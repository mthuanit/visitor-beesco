@extends('layouts.app')

@section('content')
<!-- Custom Styles to unify dark theme across Visitor List -->
<style>
    /* Dynamic calendar indicator style for dark mode */
    .dark input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
    
    /* Pagination dark theme overrides */
    .dark nav[role="navigation"] a, 
    .dark nav[role="navigation"] span {
        background-color: #1e293b !important; /* slate-800 */
        border-color: #334155 !important; /* slate-700 */
        color: #cbd5e1 !important; /* slate-300 */
    }
    .dark nav[role="navigation"] span[aria-current="page"] span {
        background-color: #3b82f6 !important; /* blue-500 */
        border-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    .dark nav[role="navigation"] a:hover {
        background-color: #334155 !important;
    }
</style>
@if(!request()->has('iframe'))
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center">
            <i class="fas fa-users mr-3 text-blue-500"></i>
            {{ __('Quản lý Khách Ra Vào') }}
        </h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            {{ __('Xem lịch sử, bộ lọc tìm kiếm và quản lý thông tin khách ra vào công ty') }}
        </p>
    </div>
    <div>
        <button type="button" onclick="openBackdatedModal()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-4 rounded-xl text-sm shadow-md shadow-blue-500/10 transition-colors flex items-center space-x-2">
            <i class="fas fa-plus-circle"></i>
            <span>{{ __('Đăng ký bù lịch sử') }}</span>
        </button>
    </div>
</div>
@else
<div class="flex justify-end mb-4">
    <button type="button" onclick="openBackdatedModal()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-3 rounded-lg text-xs shadow-md shadow-blue-500/10 transition-colors flex items-center space-x-1.5">
        <i class="fas fa-plus-circle"></i>
        <span>{{ __('Đăng ký bù lịch sử') }}</span>
    </button>
</div>
@endif

<!-- Alert Success/Error/Validation -->
@if(session('success'))
<div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 p-4 rounded-xl text-sm flex items-center shadow-sm mb-6">
    <i class="fas fa-check-circle mr-2.5 text-lg"></i>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 p-4 rounded-xl text-sm flex items-center shadow-sm mb-6">
    <i class="fas fa-exclamation-circle mr-2.5 text-lg"></i>
    <span>{{ session('error') }}</span>
</div>
@endif
@if($errors->any())
<div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 p-4 rounded-xl text-sm shadow-sm mb-6">
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

<div class="{{ request()->has('iframe') ? '' : 'bg-white border border-slate-200 dark:bg-slate-900 dark:border-slate-800 shadow-sm rounded-2xl' }} overflow-hidden transition-colors duration-205">
    
    @if(!request()->has('iframe'))
    <!-- Card Header -->
    <div class="p-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center transition-colors duration-205">
        <h3 class="font-bold text-slate-700 dark:text-slate-200 flex items-center shrink-0">
            <i class="fas fa-list mr-2 text-slate-400"></i> {{ __('Danh sách khách') }}
        </h3>
    </div>
    @endif
    
    <div class="{{ request()->has('iframe') ? 'p-0' : 'p-6' }}">
        <!-- Bộ lọc và Tìm kiếm -->
        <form method="GET" action="{{ route('visitors.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @if(request()->has('iframe'))
                <input type="hidden" name="iframe" value="1">
            @endif
            
            @if(auth()->check() && (!method_exists(auth()->user(), 'isFactoryAccount') || !auth()->user()->isFactoryAccount()))
            <div>
                <label class="block text-xs font-bold text-slate-555 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Xưởng') }}</label>
                <select name="factory" class="w-full bg-white border border-slate-300 text-slate-800 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-205">
                    <option value="">{{ __('Tất cả xưởng') }}</option>
                    <option value="BV" {{ request('factory') === 'BV' ? 'selected' : '' }}>BV</option>
                    <option value="LN" {{ request('factory') === 'LN' ? 'selected' : '' }}>LN</option>
                    <option value="BD" {{ request('factory') === 'BD' ? 'selected' : '' }}>BD</option>
                    <option value="PL" {{ request('factory') === 'PL' ? 'selected' : '' }}>PL</option>
                </select>
            </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-550 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Tìm kiếm') }}</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Tên, mã thẻ, CCCD, công ty...') }}" class="w-full bg-white border border-slate-300 text-slate-800 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-205">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-555 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Từ ngày') }}</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full bg-white border border-slate-300 text-slate-800 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-205">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-555 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Đến ngày') }}</label>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full bg-white border border-slate-300 text-slate-800 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-205">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-sm shadow-md shadow-blue-500/10 transition-colors flex items-center justify-center space-x-1">
                    <i class="fas fa-filter text-xs"></i> 
                    <span>{{ __('Lọc') }}</span>
                </button>
                <a href="{{ route('visitors.index') }}" class="px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-455 rounded-lg text-sm hover:bg-slate-300 transition-colors flex items-center justify-center" title="{{ __('Đặt lại bộ lọc') }}">
                    <i class="fas fa-sync-alt text-xs"></i>
                </a>
                <a href="{{ route('visitors.export', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-3.5 rounded-lg text-sm shadow-md shadow-emerald-500/10 transition-colors flex items-center justify-center" title="{{ __('Xuất Excel') }}">
                    <i class="fas fa-file-excel text-xs"></i>
                </a>
            </div>
        </form>

        <!-- Bảng Dữ Liệu -->
        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-950">
                    <tr>
                        <th class="px-2 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('STT') }}</th>
                        <th class="px-2 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Mã Thẻ') }}</th>
                        <th class="px-3 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Họ Tên Khách') }}</th>
                        <th class="px-2 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Cơ Quan') }}</th>
                        <th class="px-2 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Người Gặp') }}</th>
                        <th class="px-2 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Ảnh (Vào/Ra)') }}</th>
                        <th class="px-2 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Thời Điểm Vào') }}</th>
                        <th class="px-2 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Thời Điểm Ra') }}</th>
                        <th class="px-2 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Trạng Thái') }}</th>
                        <th class="px-2 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Chi Tiết') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                    @forelse($sessions as $index => $s)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors even:bg-slate-50/20 dark:even:bg-slate-900/50 odd:bg-white dark:odd:bg-slate-950/20">
                        <td class="px-2 py-3 whitespace-nowrap text-slate-600 dark:text-slate-400 font-medium text-center">{{ ($sessions->currentPage() - 1) * $sessions->perPage() + $loop->iteration }}</td>
                        <td class="px-2 py-3 whitespace-nowrap font-mono font-bold text-slate-700 dark:text-slate-200">{{ $s->barcode }}</td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <span class="block font-semibold text-slate-700 dark:text-slate-200">{{ $s->name }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400"><i class="fas fa-id-card mr-1 text-slate-400"></i> {{ $s->cccd }}</span>
                        </td>
                        <td class="px-2 py-3 text-slate-600 dark:text-slate-300 text-xs">{{ $s->company }}</td>
                        <td class="px-2 py-3 text-slate-600 dark:text-slate-300 text-xs">{{ $s->meet_person }}</td>
                        <td class="px-2 py-3 whitespace-nowrap">
                            <div class="flex space-x-1">
                                @if($s->portrait_photo)
                                    <img src="{{ asset('storage/' . $s->portrait_photo) }}" class="h-8 w-8 object-cover rounded border border-slate-200 dark:border-slate-700 hover:scale-150 transition-transform cursor-pointer" title="{{ __('Ảnh lúc vào') }}">
                                @elseif($s->photo)
                                    <img src="{{ asset('storage/' . $s->photo) }}" class="h-8 w-8 object-cover rounded border border-slate-200 dark:border-slate-700 hover:scale-150 transition-transform cursor-pointer" title="{{ __('Ảnh lúc vào') }}">
                                @else
                                    <div class="h-8 w-8 bg-slate-100 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500" title="{{ __('Không có ảnh vào') }}">
                                        <i class="fas fa-camera text-[10px]"></i>
                                    </div>
                                @endif

                                @if($s->portrait_photo_checkout)
                                    <img src="{{ asset('storage/' . $s->portrait_photo_checkout) }}" class="h-8 w-8 object-cover rounded border border-slate-200 dark:border-slate-700 hover:scale-150 transition-transform cursor-pointer" title="{{ __('Ảnh lúc ra') }}">
                                @elseif($s->photo_checkout)
                                    <img src="{{ asset('storage/' . $s->photo_checkout) }}" class="h-8 w-8 object-cover rounded border border-slate-200 dark:border-slate-700 hover:scale-150 transition-transform cursor-pointer" title="{{ __('Ảnh lúc ra') }}">
                                @else
                                    <div class="h-8 w-8 bg-slate-100 dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500" title="{{ __('Không có ảnh ra') }}">
                                        <i class="fas fa-camera text-[10px]"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-2 py-3 whitespace-nowrap text-slate-600 dark:text-slate-300">
                            <div>{{ $s->checkin_time->format('d/m/Y') }}</div>
                            <div class="text-xs font-semibold text-slate-500">{{ $s->checkin_time->format('H:i') }}</div>
                        </td>
                        <td class="px-2 py-3 whitespace-nowrap text-slate-600 dark:text-slate-300">
                            @if($s->checkout_time)
                                <div>{{ $s->checkout_time->format('d/m/Y') }}</div>
                                <div class="text-xs font-semibold text-slate-500">{{ $s->checkout_time->format('H:i') }}</div>
                            @else
                                --:--
                            @endif
                        </td>
                        <td class="px-2 py-3 whitespace-nowrap">
                            @if($s->checkout_time)
                                <span class="px-2 py-0.5 inline-flex text-[11px] leading-5 font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-400 border border-slate-200 dark:border-slate-700">{{ __('Đã ra') }}</span>
                            @else
                                <span class="px-2 py-0.5 inline-flex text-[11px] leading-5 font-semibold rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 shadow-sm shadow-emerald-500/5">{{ __('Trong khu vực') }}</span>
                            @endif
                        </td>
                        <td class="px-2 py-3 whitespace-nowrap text-right font-medium">
                            <a href="{{ route('visitors.show', $s->id) }}{{ request()->has('iframe') ? '?iframe=1' : '' }}" class="text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-600/40 hover:bg-blue-600 hover:border-blue-600 hover:text-white px-2.5 py-1 rounded transition-all text-[11px] inline-block">{{ __('Xem chi tiết') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-12 text-slate-500 text-lg">{{ __('Không có dữ liệu phù hợp') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="mt-6">
            {{ $sessions->appends(request()->query())->links('pagination::tailwind') }}
        </div>
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
            {{ __('Đăng ký Bù Lịch Sử Khách Ra Vào') }}
        </h3>
        <button onclick="closeBackdatedModal()" class="text-white/70 hover:text-white transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>    
    <!-- Form -->
    <form action="{{ route('admin.visitors.backdated') }}" method="POST" class="p-6 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Mã Số Thẻ') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="barcode" required value="{{ old('barcode') }}" placeholder="{{ __('Ví dụ: BV001, LN002...') }}" 
                       class="w-full border @error('barcode') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase font-mono font-bold">
                @error('barcode')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Họ và Tên') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="{{ __('Nhập họ và tên...') }}" 
                       class="w-full border @error('name') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Số CCCD / Hộ chiếu') }}</label>
                <input type="text" name="cccd" value="{{ old('cccd') }}" placeholder="{{ __('Nhập số CCCD...') }}" 
                       class="w-full border @error('cccd') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('cccd')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Số Điện Thoại') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('Nhập số điện thoại...') }}" 
                       class="w-full border @error('phone') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('phone')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Cơ quan / Công ty') }}</label>
                <input type="text" name="company" value="{{ old('company') }}" placeholder="{{ __('Nhập tên cơ quan/công ty...') }}" 
                       class="w-full border @error('company') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('company')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Người cần gặp') }}</label>
                <input type="text" name="meet_person" value="{{ old('meet_person') }}" placeholder="{{ __('Nhập người cần gặp...') }}" 
                       class="w-full border @error('meet_person') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('meet_person')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Phương Tiện') }}</label>
                <input type="text" name="vehicle" value="{{ old('vehicle') }}" placeholder="{{ __('Ví dụ: Ô tô, Xe máy...') }}" 
                       class="w-full border @error('vehicle') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('vehicle')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Thời gian vào (Check-in)') }} <span class="text-rose-500">*</span></label>
                <input type="datetime-local" name="checkin_time" value="{{ old('checkin_time') }}" required id="backdated-checkin-time"
                       class="w-full border @error('checkin_time') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('checkin_time')
                    <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Thời gian ra (Check-out)') }} <span class="text-slate-455 font-normal">({{ __('Để trống nếu khách chưa ra') }})</span></label>
                <input type="datetime-local" name="checkout_time" value="{{ old('checkout_time') }}" id="backdated-checkout-time"
                       class="w-full border @error('checkout_time') border-rose-500 dark:border-rose-500 @else border-slate-350 dark:border-slate-800 @enderror bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('checkout_time')
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
        const checkinInput = document.getElementById('backdated-checkin-time');
        const checkoutInput = document.getElementById('backdated-checkout-time');
        
        if (!checkinInput.value) {
            const now = new Date();
            const checkinVal = new Date(now.getTime() - 60 * 60 * 1000); // Default check-in: 1 hour ago
            checkinInput.value = formatDateTimeLocal(checkinVal);
        }
        if (!checkoutInput.value) {
            const now = new Date();
            checkoutInput.value = formatDateTimeLocal(now);
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
            document.querySelectorAll('#backdated-modal input').forEach(input => {
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
        @if($errors->any() && ($errors->has('barcode') || $errors->has('name') || $errors->has('cccd') || $errors->has('phone') || $errors->has('company') || $errors->has('meet_person') || $errors->has('vehicle') || $errors->has('checkin_time') || $errors->has('checkout_time')))
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
