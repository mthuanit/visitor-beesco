<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VisitorController extends Controller
{
    /**
     * Danh sách khách (Có tìm kiếm và lọc theo ngày)
     */
    public function index(Request $request)
    {
        $query = VisitorSession::query();

        $user = auth()->user();
        if ($user && method_exists($user, 'isFactoryAccount') && $user->isFactoryAccount()) {
            $query->where('barcode', 'LIKE', $user->factory_code . '%');
        } else {
            if ($factory = $request->input('factory')) {
                $query->where('barcode', 'LIKE', $factory . '%');
            }
        }

        // Tìm kiếm chung (tên, barcode, cccd, công ty)
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('cccd', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('meet_person', 'like', "%{$search}%");
            });
        }

        // Lọc khoảng thời gian Check-in
        // Mặc định lấy ngày hôm nay nếu không có bất kỳ tham số tìm kiếm/lọc nào
        if (!$request->has('start_date') && !$request->has('end_date') && !$request->has('search') && !$request->has('factory')) {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = Carbon::today()->format('Y-m-d');
        } else {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        if ($startDate) {
            $query->whereDate('checkin_time', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('checkin_time', '<=', $endDate);
        }

        // Sắp xếp mới nhất trước
        $query->orderBy('checkin_time', 'desc');

        // Phân trang
        $sessions = $query->paginate(20);

        return view('visitors.index', compact('sessions', 'search', 'startDate', 'endDate'));
    }

    /**
     * Helper: Lấy session được phép truy cập
     */
    private function getAuthorizedSession($id)
    {
        $query = VisitorSession::query();
        $user = auth()->user();
        
        if ($user && method_exists($user, 'isFactoryAccount') && $user->isFactoryAccount()) {
            $query->where('barcode', 'LIKE', $user->factory_code . '%');
        }
        
        return $query->findOrFail($id);
    }

    /**
     * Chi tiết khách
     */
    public function show($id)
    {
        $session = $this->getAuthorizedSession($id);
        return view('visitors.show', compact('session'));
    }

    /**
     * Chỉnh sửa thông tin khách (trong vòng 30 phút từ lúc checkin)
     */
    public function edit($id)
    {
        $session = $this->getAuthorizedSession($id);
        
        // Kiểm tra điều kiện: trong vòng 30 phút kể từ lúc checkin
        if ($session->checkin_time && $session->checkin_time->diffInMinutes(now()) > 30) {
            return redirect()->route('visitors.show', $id)->with('error', __('Chỉ có thể chỉnh sửa thông tin trong vòng 30 phút kể từ khi check-in.'));
        }

        return view('visitors.edit', compact('session'));
    }

    /**
     * Cập nhật thông tin khách
     */
    public function update(Request $request, $id)
    {
        $session = $this->getAuthorizedSession($id);

        // Kiểm tra điều kiện: trong vòng 30 phút kể từ lúc checkin
        if ($session->checkin_time && $session->checkin_time->diffInMinutes(now()) > 30) {
            return redirect()->route('visitors.show', $id)->with('error', __('Chỉ có thể chỉnh sửa thông tin trong vòng 30 phút kể từ khi check-in.'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cccd' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'meet_person' => 'nullable|string|max:255',
            'vehicle' => 'nullable|string|max:255',
        ]);

        $session->update($request->only([
            'name', 'cccd', 'phone', 'company', 'meet_person', 'vehicle'
        ]));

        return redirect()->route('visitors.show', $id)->with('success', __('Cập nhật thông tin khách thành công.'));
    }

    /**
     * Export thông tin ra file CSV
     */
    public function export(Request $request)
    {
        $query = VisitorSession::query();

        $user = auth()->user();
        if ($user && method_exists($user, 'isFactoryAccount') && $user->isFactoryAccount()) {
            $query->where('barcode', 'LIKE', $user->factory_code . '%');
        } else {
            if ($factory = $request->input('factory')) {
                $query->where('barcode', 'LIKE', $factory . '%');
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('cccd', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('meet_person', 'like', "%{$search}%");
            });
        }

        if (!$request->has('start_date') && !$request->has('end_date') && !$request->has('search') && !$request->has('factory')) {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = Carbon::today()->format('Y-m-d');
        } else {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        if ($startDate) {
            $query->whereDate('checkin_time', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('checkin_time', '<=', $endDate);
        }

        $query->orderBy('checkin_time', 'desc');

        $visitors = $query->get();

        $filename = "visitors-export-" . date('Y-m-d') . ".csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['ID', 'Mã Thẻ', 'Họ Tên', 'CCCD', 'Số ĐT', 'Công Ty', 'Gặp Ai', 'Phương Tiện', 'Check-in', 'Check-out'];

        $callback = function() use($visitors, $columns) {
            $file = fopen('php://output', 'w');
            
            // Output BOM for Excel to read UTF-8 properly
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            fputcsv($file, $columns);

            foreach ($visitors as $visitor) {
                fputcsv($file, [
                    $visitor->id,
                    $visitor->barcode,
                    $visitor->name,
                    $visitor->cccd,
                    $visitor->phone,
                    $visitor->company,
                    $visitor->meet_person,
                    $visitor->vehicle,
                    $visitor->checkin_time ? $visitor->checkin_time->format('Y-m-d H:i:s') : '',
                    $visitor->checkout_time ? $visitor->checkout_time->format('Y-m-d H:i:s') : '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Giao diện cổng điều khiển (Gate Control)
     */
    public function gate()
    {
        if (auth()->check() && method_exists(auth()->user(), 'isManagerAccount') && auth()->user()->isManagerAccount()) {
            abort(403, 'Unauthorized access. Manager is not allowed here.');
        }

        $webrtcStreamerUrl = config('services.webrtc.url', 'http://127.0.0.1:8000');
        $cameraRtspUrl = config('services.webrtc.camera_rtsp', 'rtsp://127.0.0.1:8554/live');
        $apiKey = config('services.api.key', '');
        
        return view('visitors.gate', compact('webrtcStreamerUrl', 'cameraRtspUrl', 'apiKey'));
    }

    /**
     * Store manual backdated log for a visitor.
     */
    public function storeBackdated(Request $request)
    {
        if ($request->has('barcode')) {
            $request->merge([
                'barcode' => strtoupper($request->input('barcode'))
            ]);
        }

        $request->validate([
            'barcode' => ['required', 'string', 'max:50', 'regex:/^(BV|LN|BD|PL)\d{3}$/', 'exists:cards,code'],
            'name' => 'required|string|max:255',
            'cccd' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'meet_person' => 'nullable|string|max:255',
            'vehicle' => 'nullable|string|max:255',
            'checkin_time' => 'required|date',
            'checkout_time' => 'nullable|date|after:checkin_time',
        ]);

        $barcode = strtoupper($request->input('barcode'));
        $checkoutTimeInput = $request->input('checkout_time');

        // Check factory permissions (Guard accounts)
        $user = auth()->user();
        if ($user && method_exists($user, 'isFactoryAccount') && $user->isFactoryAccount() && !str_starts_with($barcode, $user->factory_code)) {
            return redirect()->back()->withInput()->with('error', __('Thẻ này không thuộc thẩm quyền của xưởng bạn.'));
        }

        // Prevent state conflict: if registering a visitor who hasn't left yet, check card availability
        $card = \App\Models\Card::where('code', $barcode)->first();
        if (empty($checkoutTimeInput)) {
            if ($card && $card->status !== \App\Models\Card::STATUS_AVAILABLE) {
                $statusText = $card->status === \App\Models\Card::STATUS_IN_USE ? __('đang sử dụng') : __('đã báo mất');
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['barcode' => __('Thẻ :code hiện tại :status, không thể thực hiện đăng ký vào chưa ra.', ['code' => $barcode, 'status' => $statusText])]);
            }
        }

        $checkinTime = Carbon::parse($request->input('checkin_time'));
        $checkoutTime = $checkoutTimeInput ? Carbon::parse($checkoutTimeInput) : null;

        VisitorSession::create([
            'barcode' => $barcode,
            'name' => $request->input('name'),
            'cccd' => $request->input('cccd'),
            'phone' => $request->input('phone'),
            'company' => $request->input('company'),
            'meet_person' => $request->input('meet_person'),
            'vehicle' => $request->input('vehicle'),
            'checkin_time' => $checkinTime,
            'checkout_time' => $checkoutTime,
        ]);

        // Transition card state if this is an active session
        if ($checkoutTime === null && $card) {
            $card->update([
                'status' => \App\Models\Card::STATUS_IN_USE
            ]);
        }

        $dateStr = $checkinTime->format('Y-m-d');
        return redirect()->route('visitors.index', [
            'start_date' => $dateStr,
            'end_date' => $dateStr
        ])->with('success', __('Đăng ký bù lịch sử khách thành công.'));
    }
}
