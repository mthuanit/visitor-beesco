<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VisitorSession;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user && method_exists($user, 'isFactoryAccount') && $user->isFactoryAccount()) {
            abort(403, 'Unauthorized access. Only admin is allowed.');
        }

        $startDateRaw = $request->input('start_date');
        $endDateRaw = $request->input('end_date');
        
        try {
            $start = $startDateRaw ? Carbon::parse($startDateRaw)->startOfDay() : Carbon::today()->startOfDay();
            $end = $endDateRaw ? Carbon::parse($endDateRaw)->endOfDay() : Carbon::today()->endOfDay();
            $startDate = $start->format('Y-m-d');
            $endDate = $end->format('Y-m-d');
        } catch (\Exception $e) {
            $start = Carbon::today()->startOfDay();
            $end = Carbon::today()->endOfDay();
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = Carbon::today()->format('Y-m-d');
        }

        // Thống kê nhanh (Cards)
        // Số người Đang trong khu vực (thực tế lúc này, không phụ thuộc filter)
        $currentlyInside = VisitorSession::whereNull('checkout_time')->count();
        
        // Tổng số khách trong khoảng thời gian đã chọn
        $todayTotal = VisitorSession::whereBetween('checkin_time', [$start, $end])->count();
        
        // Số người đã rời đi trong khoảng thời gian
        $recentlyLeft = VisitorSession::whereBetween('checkout_time', [$start, $end])->count();
        
        // Số người vào trong khoảng thời gian này và ở lại quá lâu (chưa ra)
        $overstaying = VisitorSession::whereNull('checkout_time')
            ->whereBetween('checkin_time', [$start, Carbon::now()->subHours(8)])
            ->count();

        // Lấy dữ liệu theo khoảng thời gian để vẽ biểu đồ
        $sessions = VisitorSession::whereBetween('checkin_time', [$start, $end])->get();
        
        $hourlyData = array_fill(0, 24, 0);
        $companyDataRaw = [];

        foreach ($sessions as $session) {
            // Tính số khách theo giờ (tổng gộp của các ngày)
            if ($session->checkin_time) {
                $hour = $session->checkin_time->format('G'); // 0-23
                $hourlyData[(int)$hour]++;
            }
            // Tính số khách theo cơ quan/công ty
            $company = trim($session->company);
            $company = $company ? $company : 'Cá nhân/Khác';
            if (!isset($companyDataRaw[$company])) {
                $companyDataRaw[$company] = 0;
            }
            $companyDataRaw[$company]++;
        }

        // Lọc top 5 công ty, còn lại gom vào nhóm "Khác"
        arsort($companyDataRaw);
        $companyData = array_slice($companyDataRaw, 0, 5, true);
        $otherCount = array_sum(array_slice($companyDataRaw, 5));
        
        if ($otherCount > 0) {
            if (isset($companyData['Cá nhân/Khác'])) {
                $companyData['Cá nhân/Khác'] += $otherCount;
            } else {
                $companyData['Cá nhân/Khác'] = $otherCount;
            }
        }

        // Bảng 1: Khách đang có mặt (Live Visitors) - luôn là thực tế hiện tại
        $liveVisitors = VisitorSession::whereNull('checkout_time')
            ->orderBy('checkin_time', 'desc')
            ->take(10)
            ->get();

        // Bảng 2: Hoạt động mới nhất trong khoảng thời gian
        $recentActivity = VisitorSession::whereBetween('updated_at', [$start, $end])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'currentlyInside', 
            'todayTotal', 
            'recentlyLeft', 
            'overstaying', 
            'hourlyData', 
            'companyData',
            'liveVisitors',
            'recentActivity',
            'startDate',
            'endDate'
        ));
    }
}
