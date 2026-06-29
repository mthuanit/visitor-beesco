@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center">
                <i class="fas fa-chart-line mr-3 text-blue-500"></i>
                {{ __('Activity') }}
            </h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                {{ __('Thống kê nhanh và lịch sử hành trình chi tiết của xe nội bộ') }}
            </p>
        </div>
        <div>
            <button type="button" onclick="openBackdatedModal()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-4 rounded-xl text-sm shadow-md shadow-blue-500/10 transition-colors flex items-center space-x-2">
                <i class="fas fa-plus-circle"></i>
                <span>{{ __('Đăng ký bù lịch sử') }}</span>
            </button>
        </div>
    </div>

    <!-- Alert Success/Error/Validation -->
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 p-4 rounded-xl text-sm flex items-center shadow-sm mb-4">
        <i class="fas fa-check-circle mr-2.5 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 p-4 rounded-xl text-sm flex items-center shadow-sm mb-4">
        <i class="fas fa-exclamation-circle mr-2.5 text-lg"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 p-4 rounded-xl text-sm shadow-sm mb-4">
        <div class="font-bold mb-1.5 flex items-center text-rose-600 dark:text-rose-455">
            <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
            {{ __('Đã xảy ra lỗi nhập liệu. Vui lòng kiểm tra lại thông tin trong modal đăng ký.') }}
        </div>
        <ul class="list-disc pl-5 space-y-0.5 text-xs opacity-90">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Total Trucks -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all hover:scale-[1.01]">
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Tổng số xe') }}</p>
                <h3 class="text-3xl font-extrabold text-slate-850 dark:text-slate-100">{{ number_format($totalTrucks) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-500">
                <i class="fas fa-truck text-xl"></i>
            </div>
        </div>

        <!-- Card 2: Currently Inside -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all hover:scale-[1.01]">
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Đang ở công ty') }}</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-450">{{ number_format($currentlyInside) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-500">
                <i class="fas fa-warehouse text-xl"></i>
            </div>
        </div>

        <!-- Card 3: Currently Outside -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all hover:scale-[1.01]">
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Đang di chuyển') }}</p>
                <h3 class="text-3xl font-extrabold text-amber-600 dark:text-amber-500">{{ number_format($currentlyOutside) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-500">
                <i class="fas fa-truck-moving text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Filters & Logs Table Container -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden transition-colors">
        
        <!-- Filter Form Header -->
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
            <form action="{{ route('admin.trucks.dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <!-- Date from -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1.5">{{ __('Từ ngày') }}</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full border border-slate-300 dark:border-slate-850 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date to -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1.5">{{ __('Đến ngày') }}</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full border border-slate-300 dark:border-slate-850 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1.5">{{ __('Từ khóa tìm kiếm') }}</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('Tìm biển số, tài xế, nơi đến...') }}" 
                               class="w-full border border-slate-300 dark:border-slate-850 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg py-2.5 pl-8 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Submit buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-sm shadow-md shadow-blue-500/10 transition-colors flex items-center justify-center space-x-1">
                        <i class="fas fa-filter text-xs"></i>
                        <span>{{ __('Lọc') }}</span>
                    </button>
                    
                    @if($startDate || $endDate || $search)
                    <a href="{{ route('admin.trucks.dashboard') }}" class="px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-455 rounded-lg text-sm hover:bg-slate-300 transition-colors flex items-center justify-center" title="{{ __('Đặt lại') }}">
                        <i class="fas fa-sync-alt text-xs"></i>
                    </a>
                    @endif

                    <a href="{{ route('admin.trucks.export', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-3.5 rounded-lg text-sm shadow-md shadow-emerald-500/10 transition-colors flex items-center justify-center" title="{{ __('Xuất Excel/CSV') }}">
                        <i class="fas fa-file-excel text-xs"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider bg-slate-50 dark:bg-slate-950/30">
                        <th class="py-4 px-6">{{ __('Biển số xe') }}</th>
                        <th class="py-4 px-6">{{ __('Tài xế') }}</th>
                        <th class="py-4 px-6">{{ __('Nơi đến') }}</th>
                        <th class="py-4 px-6">{{ __('Mục đích') }}</th>
                        <th class="py-4 px-6">{{ __('Giờ Đi') }}</th>
                        <th class="py-4 px-6">{{ __('Giờ Về') }}</th>
                        <th class="py-4 px-6">{{ __('Tổng thời gian đi') }}</th>
                        <th class="py-4 px-6 text-center">{{ __('Trạng thái') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150 dark:divide-slate-850 text-sm">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                        <td class="py-4 px-6">
                            <span class="font-mono font-black text-slate-850 dark:text-slate-100 text-base">
                                {{ $session->truck ? $session->truck->license_plate : __('N/A') }}
                            </span>
                            @if($session->truck)
                            <span class="block text-slate-400 dark:text-slate-500 text-[10px]">{{ $session->truck->name }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-700 dark:text-slate-300">
                            {{ $session->driver ? $session->driver->name : __('N/A') }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-700 dark:text-slate-300">
                            {{ $session->destination }}
                        </td>
                        <td class="py-4 px-6 text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $session->purpose }}">
                            {{ $session->purpose ?: '-' }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="block font-bold text-slate-700 dark:text-slate-300">{{ $session->checkout_time->format('H:i d/m/Y') }}</span>
                            <span class="block text-slate-400 text-[10px] mt-0.5"><i class="fas fa-user-check text-[9px] mr-1 text-slate-350"></i>{{ $session->checkoutUser ? $session->checkoutUser->name : 'System' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($session->checkin_time)
                            <span class="block font-bold text-slate-700 dark:text-slate-300">{{ $session->checkin_time->format('H:i d/m/Y') }}</span>
                            <span class="block text-slate-400 text-[10px] mt-0.5"><i class="fas fa-user-check text-[9px] mr-1 text-slate-350"></i>{{ $session->checkinUser ? $session->checkinUser->name : 'System' }}</span>
                            @else
                            <span class="text-slate-400 dark:text-slate-500 italic text-xs">{{ __('Chưa về') }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-600 dark:text-slate-300 font-medium">
                            @php
                                $endTime = $session->checkin_time ?? now();
                                $diff = $session->checkout_time->diff($endTime);
                                $duration = '';
                                if ($diff->d > 0) {
                                    $duration .= $diff->d . ' ngày ';
                                }
                                if ($diff->h > 0) {
                                    $duration .= $diff->h . ' giờ ';
                                }
                                if ($diff->i > 0) {
                                    $duration .= $diff->i . ' phút';
                                }
                                if (empty($duration)) {
                                    $duration = 'Dưới 1 phút';
                                }
                            @endphp
                            <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs flex items-center w-max">
                                <i class="far fa-clock mr-1.5 text-slate-400"></i>{{ trim($duration) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($session->checkin_time)
                            <span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400 rounded-full inline-block">
                                <i class="fas fa-check-circle mr-1"></i>{{ __('Đã hoàn thành') }}
                            </span>
                            @else
                            <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400 rounded-full inline-block animate-pulse">
                                <i class="fas fa-truck-moving mr-1"></i>{{ __('Đang đi') }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-history text-4xl mb-3 opacity-40"></i>
                                <p class="text-sm font-medium">{{ __('Không tìm thấy lịch sử chuyến đi nào.') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($sessions->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
            {{ $sessions->withQueryString()->links() }}
        </div>
        @endif

    </div>
</div>

<!-- Backdated Modal Backdrop -->
<div id="backdated-modal-backdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0" onclick="closeBackdatedModal()"></div>

<!-- Backdated Modal Content -->
<div id="backdated-modal" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl z-50 hidden transition-all duration-300 opacity-0 scale-95 border border-slate-200 dark:border-slate-800 overflow-hidden text-slate-800 dark:text-slate-200">
    <!-- Header -->
    <div class="bg-blue-600 px-5 py-4 flex justify-between items-center text-white">
        <h3 class="font-bold flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> 
            {{ __('Đăng ký Bù Lịch Sử Xe Tải Ra Vào') }}
        </h3>
        <button onclick="closeBackdatedModal()" class="text-white/70 hover:text-white transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    
    <!-- Form -->
    <form action="{{ route('admin.trucks.backdated') }}" method="POST" class="p-6 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Chọn Xe Tải') }} <span class="text-rose-500">*</span></label>
                <select name="truck_id" required class="w-full border @error('truck_id') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- {{ __('Chọn xe tải') }} --</option>
                    @foreach($trucks as $truck)
                        <option value="{{ $truck->id }}" {{ old('truck_id') == $truck->id ? 'selected' : '' }}>{{ $truck->license_plate }} ({{ $truck->name }})</option>
                    @endforeach
                </select>
                @error('truck_id')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Chọn Tài Xế') }}</label>
                <select name="driver_id" class="w-full border @error('driver_id') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- {{ __('Chọn tài xế') }} --</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }} @if($driver->phone) ({{ $driver->phone }}) @endif</option>
                    @endforeach
                </select>
                @error('driver_id')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Nơi đến / Điểm đến') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="destination" required value="{{ old('destination') }}" placeholder="{{ __('Ví dụ: Kho Hà Nội, Xưởng B...') }}" 
                       class="w-full border @error('destination') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('destination')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-555 dark:text-slate-400 uppercase mb-1.5">{{ __('Mục đích sử dụng') }}</label>
                <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="{{ __('Ví dụ: Vận chuyển linh kiện...') }}" 
                       class="w-full border @error('purpose') border-rose-500 dark:border-rose-500 @else border-slate-355 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('purpose')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Thời gian đi (Check-out)') }} <span class="text-rose-500">*</span></label>
                <input type="datetime-local" name="checkout_time" value="{{ old('checkout_time') }}" required id="backdated-checkout-time"
                       class="w-full border @error('checkout_time') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('checkout_time')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Thời gian về (Check-in)') }} <span class="text-slate-455 font-normal">({{ __('Để trống nếu xe chưa về') }})</span></label>
                <input type="datetime-local" name="checkin_time" value="{{ old('checkin_time') }}" id="backdated-checkin-time"
                       class="w-full border @error('checkin_time') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('checkin_time')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="pt-4 flex gap-3">
            <button type="button" onclick="closeBackdatedModal()" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-xl transition-all">
                {{ __('Hủy bỏ') }}
            </button>
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-blue-500/20">
                {{ __('ĐĂNG KÝ BÙ') }}
            </button>
        </div>
    </form>
</div>

<script>
    const backdatedModalBackdrop = document.getElementById('backdated-modal-backdrop');
    const backdatedModal = document.getElementById('backdated-modal');
    
    function openBackdatedModal() {
        // Set default times only if inputs are empty (to preserve old values on validation error)
        const checkoutInput = document.getElementById('backdated-checkout-time');
        const checkinInput = document.getElementById('backdated-checkin-time');
        
        if (!checkoutInput.value) {
            const now = new Date();
            const checkoutVal = new Date(now.getTime() - 60 * 60 * 1000); // Default check-out: 1 hour ago
            checkoutInput.value = formatDateTimeLocal(checkoutVal);
        }
        if (!checkinInput.value) {
            const now = new Date();
            checkinInput.value = formatDateTimeLocal(now);
        }
        
        // Show modal
        backdatedModalBackdrop.classList.remove('hidden');
        backdatedModal.classList.remove('hidden');
        
        // Trigger animations
        setTimeout(() => {
            backdatedModalBackdrop.classList.remove('opacity-0');
            backdatedModal.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }
    
    function closeBackdatedModal() {
        backdatedModalBackdrop.classList.add('opacity-0');
        backdatedModal.classList.add('opacity-0', 'scale-95');
        
        // Hide completely after transition
        setTimeout(() => {
            backdatedModalBackdrop.classList.add('hidden');
            backdatedModal.classList.add('hidden');
            
            // Clear errors and input values if user cancels
            @if(!$errors->any())
            document.querySelectorAll('#backdated-modal select, #backdated-modal input').forEach(input => {
                if (input.type !== 'hidden' && input.type !== 'submit') {
                    input.value = '';
                }
            });
            @endif
        }, 300);
    }

    function formatDateTimeLocal(date) {
        const tzOffset = date.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(date - tzOffset)).toISOString().slice(0, -1);
        return localISOTime.substring(0, 16);
    }

    // Auto-reopen modal if validation errors exist for this form
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any() && ($errors->has('truck_id') || $errors->has('driver_id') || $errors->has('destination') || $errors->has('purpose') || $errors->has('checkout_time') || $errors->has('checkin_time')))
            // We open the modal without resetting inputs to preserve the Laravel old() values
            backdatedModalBackdrop.classList.remove('hidden');
            backdatedModal.classList.remove('hidden');
            setTimeout(() => {
                backdatedModalBackdrop.classList.remove('opacity-0');
                backdatedModal.classList.remove('opacity-0', 'scale-95');
            }, 10);
        @endif
    });
</script>
@endsection
