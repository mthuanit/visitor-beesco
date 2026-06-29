<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
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
     * Display a listing of the drivers.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Driver::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $drivers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.drivers.index', compact('drivers', 'search'));
    }

    /**
     * Store a newly created driver.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Driver::create([
            'name' => trim($request->input('name')),
            'phone' => trim($request->input('phone')),
        ]);

        return redirect()->route('admin.drivers.index')->with('success', __('Thêm tài xế mới thành công.'));
    }

    /**
     * Update the specified driver.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $driver = Driver::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $driver->update([
            'name' => trim($request->input('name')),
            'phone' => trim($request->input('phone')),
        ]);

        return redirect()->route('admin.drivers.index')->with('success', __('Cập nhật thông tin tài xế thành công.'));
    }

    /**
     * Remove the specified driver.
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $driver = Driver::findOrFail($id);
        $driver->delete();

        return redirect()->route('admin.drivers.index')->with('success', __('Xóa tài xế thành công.'));
    }
}
