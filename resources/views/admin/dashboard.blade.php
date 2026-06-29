@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center">
                <i class="fas fa-chart-line mr-3 text-blue-500"></i>
                {{ __('Dashboard - Khách Ra Vào') }}
            </h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                {{ __('Thống kê nhanh và lưu lượng khách ra vào công ty') }}
            </p>
        </div>
        
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center space-x-2 bg-white dark:bg-slate-900 p-1.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center px-2">
                <i class="far fa-calendar-alt text-slate-400 mr-2"></i>
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-300 focus:ring-0 cursor-pointer p-1 outline-none">
            </div>
            <span class="text-slate-300 dark:text-slate-600 font-bold">-</span>
            <div class="flex items-center px-2">
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-300 focus:ring-0 cursor-pointer p-1 outline-none">
            </div>
            <button type="submit" class="bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-600/20 dark:text-blue-400 dark:hover:bg-blue-600/30 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors">
                {{ __('Lọc') }}
            </button>
        </form>
    </div>
</div>

<div class="space-y-6">

<!-- Metrics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1 -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 flex items-center justify-between transition-colors">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">{{ __('Đang trong khu vực') }}</p>
            <h3 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $currentlyInside }}</h3>
        </div>
        <div class="w-12 h-12 bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-xl flex items-center justify-center text-xl">
            <i class="fas fa-building"></i>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 flex items-center justify-between transition-colors">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">{{ __('Đã đến trong kỳ') }}</p>
            <h3 class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $todayTotal }}</h3>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-500 dark:bg-blue-500/10 dark:text-blue-400 rounded-xl flex items-center justify-center text-xl">
            <i class="fas fa-users"></i>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 flex items-center justify-between transition-colors">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">{{ __('Đã rời đi trong kỳ') }}</p>
            <h3 class="text-3xl font-bold text-slate-700 dark:text-slate-200">{{ $recentlyLeft }}</h3>
        </div>
        <div class="w-12 h-12 bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 rounded-xl flex items-center justify-center text-xl">
            <i class="fas fa-sign-out-alt"></i>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 flex items-center justify-between transition-colors">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">{{ __('Ở lại quá lâu (>8h)') }}</p>
            <h3 class="text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $overstaying }}</h3>
        </div>
        <div class="w-12 h-12 bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400 rounded-xl flex items-center justify-center text-xl">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 transition-colors">
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('Lưu lượng khách theo giờ (Trong kỳ)') }}</h3>
        <div class="relative h-72 w-full">
            <canvas id="hourlyChart"></canvas>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 transition-colors">
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('Cơ quan/Công ty của khách') }}</h3>
        <div class="relative h-72 w-full flex justify-center items-center">
            <canvas id="factoryChart"></canvas>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Live Visitors -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 transition-colors">
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('Khách đang có mặt') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 dark:text-slate-400 uppercase bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-lg">{{ __('Khách') }}</th>
                        <th class="px-4 py-3">{{ __('Công ty') }}</th>
                        <th class="px-4 py-3">{{ __('Giờ vào') }}</th>
                        <th class="px-4 py-3 rounded-tr-lg">{{ __('Đã ở lại') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($liveVisitors as $visitor)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-200">
                                {{ $visitor->name }}
                                <div class="text-xs text-slate-500">{{ $visitor->barcode }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $visitor->company ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $visitor->checkin_time ? $visitor->checkin_time->format('H:i') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                                @if($visitor->checkin_time)
                                    @php
                                        $diff = $visitor->checkin_time->diff(now());
                                    @endphp
                                    <span class="{{ $diff->h >= 8 || $diff->days > 0 ? 'text-rose-500 font-semibold' : '' }}">
                                        {{ $diff->days > 0 ? $diff->days . 'd ' : '' }}{{ $diff->h > 0 ? $diff->h . 'h ' : '' }}{{ $diff->i }}m
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">{{ __('Không có khách nào đang trong khu vực.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 transition-colors">
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('Hoạt động mới nhất') }}</h3>
        <div class="space-y-4">
            @forelse($recentActivity as $activity)
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $activity->checkout_time && $activity->checkout_time == $activity->updated_at ? 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' : 'bg-blue-50 text-blue-500 dark:bg-blue-900/30 dark:text-blue-400' }}">
                        <i class="fas {{ $activity->checkout_time && $activity->checkout_time == $activity->updated_at ? 'fa-sign-out-alt' : 'fa-sign-in-alt' }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-800 dark:text-slate-200">
                            <span class="font-semibold">{{ $activity->name }}</span>
                            {{ $activity->checkout_time && $activity->checkout_time == $activity->updated_at ? __('vừa rời đi') : __('vừa đến') }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $activity->updated_at->diffForHumans() }} ({{ $activity->updated_at->format('H:i') }})</p>
                    </div>
                </div>
            @empty
                <p class="text-slate-500 dark:text-slate-400 text-center py-4">{{ __('Chưa có hoạt động nào.') }}</p>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? '#1e293b' : '#e2e8f0';

        // Hourly Chart
        const hourlyCtx = document.getElementById('hourlyChart');
        if (hourlyCtx) {
            const hourlyDataRaw = @json($hourlyData);
            const labels = Array.from({length: 24}, (_, i) => `${i.toString().padStart(2, '0')}:00`);
            
            new Chart(hourlyCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ __('Số khách') }}',
                        data: hourlyDataRaw,
                        borderColor: '#3b82f6', // blue-500
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                            titleColor: isDark ? '#f8fafc' : '#0f172a',
                            bodyColor: isDark ? '#cbd5e1' : '#475569',
                            borderColor: isDark ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor, maxTicksLimit: 12 }
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: { color: textColor, stepSize: 1, precision: 0 },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Factory/Company Chart
        const factoryCtx = document.getElementById('factoryChart');
        if (factoryCtx) {
            const companyDataRaw = @json($companyData);
            const factoryLabels = Object.keys(companyDataRaw);
            const factoryValues = Object.values(companyDataRaw);
            
            const backgroundColors = [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'
            ];

            new Chart(factoryCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: factoryLabels.length > 0 ? factoryLabels : ['{{ __('Chưa có dữ liệu') }}'],
                    datasets: [{
                        data: factoryValues.length > 0 ? factoryValues : [1],
                        backgroundColor: factoryValues.length > 0 ? backgroundColors : [isDark ? '#334155' : '#e2e8f0'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            enabled: factoryValues.length > 0
                        }
                    }
                }
            });
        }
    });
</script>
</div>
@endsection
