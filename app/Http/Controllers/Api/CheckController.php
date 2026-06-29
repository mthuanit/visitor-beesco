<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckController extends Controller
{
    /**
     * Check if a barcode is currently active (has checked in but not checked out).
     */
    public function sessionStatus($barcode)
    {
        $barcode = strtoupper($barcode);
        
        $user = auth()->user();
        if ($user && $user->isFactoryAccount() && !str_starts_with($barcode, $user->factory_code)) {
            return response()->json(['error' => 'Thẻ này không thuộc thẩm quyền của xưởng bạn.'], 403);
        }

        $card = \App\Models\Card::where('code', $barcode)->first();
        if (!$card) {
            return response()->json(['error' => 'Mã thẻ không tồn tại trong hệ thống.'], 404);
        }

        if ($card->status === \App\Models\Card::STATUS_LOST) {
            return response()->json(['error' => 'Thẻ đã báo mất/hỏng.'], 400);
        }

        if ($card->status === \App\Models\Card::STATUS_IN_USE) {
            $session = VisitorSession::where('barcode', $barcode)
                                     ->whereNull('checkout_time')
                                     ->first();
            if ($session) {
                return response()->json([
                    'active' => true,
                    'session' => $session
                ]);
            }
        }
        
        return response()->json(['active' => false]);
    }

    /**
     * Handle Check-in / Check-out request.
     */
    public function check(Request $request)
    {
        $request->validate([
            'action' => 'required|in:checkin,checkout',
            'barcode' => ['required', 'string', 'max:50', 'regex:/^(BV|LN|BD|PL)\d{3}$/'],
            'name' => 'required_if:action,checkin|string|max:255',
            'cccd' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'meet_person' => 'nullable|string|max:255',
            'vehicle' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:10240', // Max 10MB
            'photo_checkout' => 'nullable|image|max:10240',
            'portrait_photo' => 'nullable|image|max:10240',
            'portrait_photo_checkout' => 'nullable|image|max:10240',
        ]);

        $action = $request->input('action');
        $barcode = strtoupper($request->input('barcode'));
        
        $user = auth()->user();
        if ($user && $user->isFactoryAccount() && !str_starts_with($barcode, $user->factory_code)) {
            return response()->json(['error' => 'Thẻ này không thuộc thẩm quyền của xưởng bạn.'], 403);
        }
        
        // --- Kiểm tra hành động Check-out ---
        if ($action === 'checkout') {
            $session = VisitorSession::where('barcode', $barcode)
                                     ->whereNull('checkout_time')
                                     ->first();
                                     
            if ($session) {
                // Xử lý lưu ảnh check-out nếu có
                if ($request->hasFile('photo_checkout')) {
                    $session->photo_checkout = $request->file('photo_checkout')->store('photos_checkout', 'public');
                }
                if ($request->hasFile('portrait_photo_checkout')) {
                    $session->portrait_photo_checkout = $request->file('portrait_photo_checkout')->store('portrait_photos_checkout', 'public');
                }
                
                $session->checkout_time = Carbon::now();
                $session->save();

                \App\Models\Card::where('code', $barcode)->update(['status' => \App\Models\Card::STATUS_AVAILABLE]);

                return response()->json(['status' => 'checkout', 'message' => 'Check-out thành công', 'photo_checkout' => $session->photo_checkout]);
            }
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy session đang active của thẻ này'], 404);
        }

        // --- Kiểm tra hành động Check-in ---
        if ($action === 'checkin') {
            $card = \App\Models\Card::where('code', $barcode)->first();
            if (!$card) {
                return response()->json(['status' => 'error', 'message' => 'Mã thẻ không tồn tại trong hệ thống.'], 404);
            }
            if ($card->status === \App\Models\Card::STATUS_LOST) {
                return response()->json(['status' => 'error', 'message' => 'Thẻ đã báo mất/hỏng.'], 400);
            }
            if ($card->status === \App\Models\Card::STATUS_IN_USE) {
                return response()->json(['status' => 'error', 'message' => 'Thẻ này đang được sử dụng và chưa Check-out'], 400);
            }

            // Đảm bảo thẻ chưa được gắn cho ai (một thẻ = 1 session)
            $activeSession = VisitorSession::where('barcode', $barcode)
                                           ->whereNull('checkout_time')
                                           ->first();
            if ($activeSession) {
                return response()->json(['status' => 'error', 'message' => 'Thẻ này đang được sử dụng và chưa Check-out'], 400);
            }

            // Xử lý lưu ảnh
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos', 'public');
            }
            
            $portraitPhotoPath = null;
            if ($request->hasFile('portrait_photo')) {
                $portraitPhotoPath = $request->file('portrait_photo')->store('portrait_photos', 'public');
            }

            // Tạo bản ghi mới
            $session = new VisitorSession();
            $session->barcode = $barcode;
            $session->name = $request->input('name');
            $session->cccd = $request->input('cccd');
            $session->phone = $request->input('phone');
            $session->company = $request->input('company');
            $session->meet_person = $request->input('meet_person');
            $session->vehicle = $request->input('vehicle');
            $session->photo = $photoPath;
            $session->portrait_photo = $portraitPhotoPath;
            $session->checkin_time = Carbon::now();
            
            $session->save();

            $card->status = \App\Models\Card::STATUS_IN_USE;
            $card->save();

            return response()->json(['status' => 'checkin', 'message' => 'Check-in thành công']);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Hành động không hợp lệ'], 400);
    }
}
