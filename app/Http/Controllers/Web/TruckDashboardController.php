<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use App\Models\TruckSession;
use App\Models\Driver;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TruckDashboardController extends Controller
{

    /**
     * Build the query for sessions based on request parameters.
     */
    private function buildSessionsQuery(Request $request)
    {
        $query = TruckSession::with(['truck', 'driver', 'checkoutUser', 'checkinUser']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('destination', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhereHas('truck', function ($tQ) use ($search) {
                      $tQ->where('license_plate', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('driver', function ($dQ) use ($search) {
                      $dQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $startDateRaw = $request->input('start_date');
        $endDateRaw = $request->input('end_date');

        if ($startDateRaw) {
            $query->whereDate('checkout_time', '>=', $startDateRaw);
        }
        if ($endDateRaw) {
            $query->whereDate('checkout_time', '<=', $endDateRaw);
        }

        return $query;
    }

    public function index(Request $request)
    {
        // 1. Filter dates (defaults to today if no parameters and no search)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        if (!$request->has('start_date') && !$request->has('end_date') && !$request->has('search')) {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = Carbon::today()->format('Y-m-d');
            $request->merge(['start_date' => $startDate, 'end_date' => $endDate]);
        }

        // 2. Quick Stats based on selected dates
        $totalTrucks = Truck::count();
        
        $outsideQuery = TruckSession::whereNull('checkin_time');
        if ($startDate) {
            $outsideQuery->whereDate('checkout_time', '>=', $startDate);
        }
        if ($endDate) {
            $outsideQuery->whereDate('checkout_time', '<=', $endDate);
        }
        $currentlyOutside = $outsideQuery->distinct('truck_id')->count('truck_id');
        $currentlyInside = $totalTrucks - $currentlyOutside;

        // 3. Build & get sessions query
        $query = $this->buildSessionsQuery($request);
        $sessions = $query->orderBy('checkout_time', 'desc')->paginate(20);

        $search = $request->input('search');

        // Get trucks and drivers for backdated modal
        $trucks = Truck::orderBy('license_plate', 'asc')->get();
        $drivers = Driver::orderBy('name', 'asc')->get();

        return view('admin.trucks.dashboard', compact(
            'totalTrucks',
            'currentlyOutside',
            'currentlyInside',
            'sessions',
            'startDate',
            'endDate',
            'search',
            'trucks',
            'drivers'
        ));
    }

    /**
     * Export logs to CSV.
     */
    public function export(Request $request)
    {

        $query = $this->buildSessionsQuery($request);
        $sessions = $query->orderBy('checkout_time', 'desc')->get();

        $filename = "truck-logs-export-" . date('Y-m-d') . ".csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['ID', 'Biển Số', 'Tên Xe', 'Tài Xế', 'Nơi Đến', 'Mục Đích', 'Giờ Đi', 'Giờ Về', 'Tổng Thời Gian Đi'];

        $callback = function() use($sessions, $columns) {
            $file = fopen('php://output', 'w');
            
            // Output BOM for Excel to read UTF-8 properly
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            fputcsv($file, $columns);

            foreach ($sessions as $session) {
                $duration = 'Chưa về';
                if ($session->checkin_time) {
                    $diff = $session->checkout_time->diff($session->checkin_time);
                    $duration = ($diff->days > 0 ? $diff->days . 'd ' : '') . ($diff->h > 0 ? $diff->h . 'h ' : '') . $diff->i . 'm';
                }

                fputcsv($file, [
                    $session->id,
                    $session->truck ? $session->truck->license_plate : '',
                    $session->truck ? $session->truck->name : '',
                    $session->driver ? $session->driver->name : '',
                    $session->destination,
                    $session->purpose,
                    $session->checkout_time ? $session->checkout_time->format('Y-m-d H:i:s') : '',
                    $session->checkin_time ? $session->checkin_time->format('Y-m-d H:i:s') : '',
                    $duration,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store manual backdated log for a truck.
     */
    public function storeBackdated(Request $request)
    {
        $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'destination' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'checkout_time' => 'required|date',
            'checkin_time' => 'nullable|date|after:checkout_time',
        ]);

        $truckId = $request->input('truck_id');
        $checkinTimeInput = $request->input('checkin_time');

        // Tìm xe tải
        $truck = \App\Models\Truck::find($truckId);

        // Ngăn chặn xung đột: nếu đăng ký xe đi chưa về, check xem xe hiện tại đang ở trong công ty không
        if (empty($checkinTimeInput)) {
            if ($truck && $truck->status !== \App\Models\Truck::STATUS_INSIDE) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['truck_id' => __('Xe tải :plate hiện tại đang di chuyển bên ngoài, không thể đăng ký thêm chuyến đi chưa về.', ['plate' => $truck->license_plate])]);
            }
        }

        $checkoutTime = Carbon::parse($request->input('checkout_time'));
        $checkinTime = $checkinTimeInput ? Carbon::parse($checkinTimeInput) : null;
        $checkinBy = $checkinTimeInput ? auth()->id() : null;

        TruckSession::create([
            'truck_id' => $truckId,
            'driver_id' => $request->input('driver_id'),
            'destination' => $request->input('destination'),
            'purpose' => $request->input('purpose'),
            'checkout_time' => $checkoutTime,
            'checkin_time' => $checkinTime,
            'checkout_by' => auth()->id(),
            'checkin_by' => $checkinBy,
        ]);

        // Cập nhật trạng thái xe tải nếu xe chưa về
        if ($checkinTime === null && $truck) {
            $truck->update([
                'status' => \App\Models\Truck::STATUS_OUTSIDE
            ]);
        }

        $dateStr = $checkoutTime->format('Y-m-d');
        return redirect()->route('admin.trucks.dashboard', [
            'start_date' => $dateStr,
            'end_date' => $dateStr
        ])->with('success', __('Đăng ký bù lịch sử xe tải thành công.'));
    }
}
