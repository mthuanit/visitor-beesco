<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    /**
     * Helper to check admin/manager authorization.
     */
    private function authorizeAdmin()
    {
        $user = auth()->user();
        if ($user && method_exists($user, 'isFactoryAccount') && $user->isFactoryAccount()) {
            abort(403, 'Unauthorized access. Only admin is allowed.');
        }
    }

    /**
     * Display a listing of the trucks.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Truck::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        // $trucks = $query->orderBy('created_at', 'desc')->paginate(15);

        // return view('admin.trucks.index', compact('trucks', 'search'));
    }

    /**
     * Store a newly created truck.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:50|unique:trucks,license_plate',
        ]);

        Truck::create([
            'name' => $request->input('name'),
            'license_plate' => strtoupper(trim($request->input('license_plate'))),
            'status' => Truck::STATUS_INSIDE,
        ]);

        return redirect()->route('admin.trucks.index')->with('success', __('Thêm xe mới thành công.'));
    }

    /**
     * Update the specified truck.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $truck = Truck::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:50|unique:trucks,license_plate,' . $id,
        ]);

        $truck->update([
            'name' => $request->input('name'),
            'license_plate' => strtoupper(trim($request->input('license_plate'))),
        ]);

        return redirect()->route('admin.trucks.index')->with('success', __('Cập nhật thông tin xe thành công.'));
    }

    /**
     * Remove the specified truck.
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $truck = Truck::findOrFail($id);
        $truck->delete();

        return redirect()->route('admin.trucks.index')->with('success', __('Xóa xe thành công.'));
    }
}
