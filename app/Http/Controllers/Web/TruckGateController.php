<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use App\Models\TruckSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TruckGateController extends Controller
{
    /**
     * Display the truck gate control interface.
     */
    public function index(Request $request)
    {
        if (auth()->check() && method_exists(auth()->user(), 'isManagerAccount') && auth()->user()->isManagerAccount()) {
            abort(403, 'Unauthorized access. Manager is not allowed here.');
        }

        $query = Truck::with(['activeSession.checkoutUser', 'activeSession.driver']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        $status = $request->input('status');
        if (in_array($status, ['inside', 'outside'])) {
            $query->where('status', $status);
        }

        $trucks = $query->orderBy('name', 'asc')->get();
        $drivers = \App\Models\Driver::orderBy('name', 'asc')->get();

        return view('trucks.gate', compact('trucks', 'search', 'status', 'drivers'));
    }

    /**
     * Perform Check-out (truck departs).
     */
    public function checkout(Request $request, $id)
    {
        if (auth()->check() && method_exists(auth()->user(), 'isManagerAccount') && auth()->user()->isManagerAccount()) {
            abort(403, 'Unauthorized access.');
        }

        $truck = Truck::findOrFail($id);

        if ($truck->status === Truck::STATUS_OUTSIDE) {
            return redirect()->back()->with('error', __('Xe đã rời bến trước đó.'));
        }

        $request->validate([
            'destination' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'driver_id' => 'required|exists:drivers,id',
        ]);

        // Create the session
        TruckSession::create([
            'truck_id' => $truck->id,
            'driver_id' => $request->input('driver_id'),
            'destination' => $request->input('destination'),
            'purpose' => $request->input('purpose'),
            'checkout_time' => Carbon::now(),
            'checkout_by' => auth()->id(),
        ]);

        // Update truck status
        $truck->update([
            'status' => Truck::STATUS_OUTSIDE
        ]);

        return redirect()->route('trucks.gate')->with('success', __('Cho xe đi (Check-out) thành công.'));
    }

    /**
     * Perform Check-in (truck returns).
     */
    public function checkin(Request $request, $id)
    {
        if (auth()->check() && method_exists(auth()->user(), 'isManagerAccount') && auth()->user()->isManagerAccount()) {
            abort(403, 'Unauthorized access.');
        }

        $truck = Truck::findOrFail($id);

        if ($truck->status === Truck::STATUS_INSIDE) {
            return redirect()->back()->with('error', __('Xe đang ở trong công ty.'));
        }

        $activeSession = $truck->activeSession;
        if (!$activeSession) {
            // Fix status mismatch if no active session
            $truck->update(['status' => Truck::STATUS_INSIDE]);
            return redirect()->route('trucks.gate')->with('success', __('Đã khôi phục trạng thái xe về công ty.'));
        }

        // Close the session
        $activeSession->update([
            'checkin_time' => Carbon::now(),
            'checkin_by' => auth()->id(),
        ]);

        // Update truck status
        $truck->update([
            'status' => Truck::STATUS_INSIDE
        ]);

        return redirect()->route('trucks.gate')->with('success', __('Cho xe về (Check-in) thành công.'));
    }

    /**
     * Update an active truck session (edit driver, destination, purpose).
     */
    public function updateSession(Request $request, $id)
    {
        if (auth()->check() && method_exists(auth()->user(), 'isManagerAccount') && auth()->user()->isManagerAccount()) {
            abort(403, 'Unauthorized access.');
        }

        $session = TruckSession::findOrFail($id);

        // Only allow updating if the truck hasn't returned yet
        if ($session->checkin_time !== null) {
            return redirect()->back()->with('error', __('Không thể sửa chuyến đi đã hoàn thành.'));
        }

        $request->validate([
            'destination' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $session->update([
            'driver_id' => $request->input('driver_id'),
            'destination' => $request->input('destination'),
            'purpose' => $request->input('purpose'),
        ]);

        return redirect()->route('trucks.gate')->with('success', __('Cập nhật thông tin chuyến đi thành công.'));
    }
}
