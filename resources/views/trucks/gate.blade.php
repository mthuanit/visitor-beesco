@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center">
                <i class="fas fa-door-open mr-3 text-blue-500"></i>
                {{ __('Kiểm soát Xe Nội Bộ') }}
            </h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                {{ __('Cho xe đi và cho xe về!') }}
            </p>
        </div>
        
        <!-- Status Indicator / Info -->
        <!-- <div class="flex items-center space-x-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-4 py-2 rounded-xl shadow-sm">
            <span class="flex items-center text-slate-500 font-semibold">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                {{ __('Hệ thống hoạt động ổn định') }}
            </span>
        </div> -->
    </div>

    <!-- Alert Success/Error -->
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 p-4 rounded-xl text-sm flex items-center shadow-sm">
        <i class="fas fa-check-circle mr-2.5 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 p-4 rounded-xl text-sm flex items-center shadow-sm">
        <i class="fas fa-exclamation-circle mr-2.5 text-lg"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors">
        <!-- Status Filter Toggles -->
        <div class="flex items-center space-x-2 bg-slate-100 dark:bg-slate-950 border border-slate-200/50 dark:border-slate-850 rounded-xl p-1">
            <a href="{{ route('trucks.gate', ['status' => 'all', 'search' => $search]) }}" 
               class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all {{ !$status || $status === 'all' ? 'bg-white dark:bg-slate-850 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                {{ __('Tất cả') }}
            </a>
            <a href="{{ route('trucks.gate', ['status' => 'inside', 'search' => $search]) }}" 
               class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'inside' ? 'bg-white dark:bg-slate-850 text-emerald-600 dark:text-emerald-450 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                {{ __('Trong công ty') }}
            </a>
            <a href="{{ route('trucks.gate', ['status' => 'outside', 'search' => $search]) }}" 
               class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'outside' ? 'bg-white dark:bg-slate-850 text-amber-600 dark:text-amber-500 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                {{ __('Đang đi') }}
            </a>
        </div>

        <!-- Search input -->
        <form action="{{ route('trucks.gate') }}" method="GET" class="flex gap-2 w-full md:max-w-xs">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('Tìm kiếm xe, tài xế...') }}" 
                       class="w-full border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-xl py-2 pl-9 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
            </div>
            @if($search)
            <a href="{{ route('trucks.gate', ['status' => $status]) }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-sm hover:bg-slate-300 transition-colors">
                <i class="fas fa-times"></i>
            </a>
            @endif
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 rounded-xl text-sm transition-colors shadow-md shadow-blue-500/10">
                {{ __('Tìm') }}
            </button>
        </form>
    </div>

    <!-- Vehicles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($trucks as $truck)
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md transition-all p-5 flex flex-col justify-between space-y-4">
            
            <!-- Truck Header & Status -->
            <div class="flex justify-between items-start">
                <div class="space-y-1">
                    <span class="block text-2xl font-black font-mono tracking-wide text-slate-850 dark:text-slate-100 uppercase border border-slate-300 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 rounded-lg px-3 py-1 shadow-sm inline-block">
                        {{ $truck->name }}
                    </span>
                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $truck->license_plate }}</h4>
                </div>
                <div>
                    @if($truck->status === \App\Models\Truck::STATUS_INSIDE)
                    <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-250 dark:border-emerald-900/50 rounded-full flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        {{ __('TRONG CÔNG TY') }}
                    </span>
                    @else
                    <span class="px-2.5 py-1 text-[10px] font-bold bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-250 dark:border-amber-900/50 rounded-full flex items-center animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                        {{ __('ĐANG ĐI') }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Driver / Trip Info -->
            <div class="bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-850 p-3 rounded-xl space-y-1 text-xs text-slate-600 dark:text-slate-400">
                @if($truck->status === \App\Models\Truck::STATUS_OUTSIDE && $truck->activeSession)
                <p><span class="font-bold text-slate-400 dark:text-slate-500 uppercase">{{ __('Tài xế chuyến') }}:</span> <span class="font-semibold text-slate-805 dark:text-slate-200">{{ $truck->activeSession->driver ? $truck->activeSession->driver->name : 'N/A' }}</span></p>
                <div class="h-px bg-slate-200 dark:bg-slate-800 my-2"></div>
                <p><span class="font-bold text-slate-400 dark:text-slate-500 uppercase">{{ __('Nơi đến') }}:</span> <span class="font-semibold text-slate-805 dark:text-slate-200">{{ $truck->activeSession->destination }}</span></p>
                <p><span class="font-bold text-slate-400 dark:text-slate-500 uppercase">{{ __('Mục đích') }}:</span> <span class="font-semibold text-slate-805 dark:text-slate-200">{{ $truck->activeSession->purpose ?: __('N/A') }}</span></p>
                <p><span class="font-bold text-slate-400 dark:text-slate-500 uppercase">{{ __('Thời gian đi') }}:</span> <span class="font-semibold text-amber-700 dark:text-amber-400">{{ $truck->activeSession->checkout_time->format('H:i d/m/Y') }}</span></p>
                @else
                <p class="text-center py-2 text-slate-400 dark:text-slate-500 italic">
                    <i class="fas fa-warehouse mr-1.5"></i>{{ __('Xe đang trong công ty') }}
                </p>
                @endif
            </div>

            <!-- Action buttons -->
            <div>
                @if($truck->status === \App\Models\Truck::STATUS_INSIDE)
                <!-- Check-out button triggers modal -->
                <button type="button" 
                        onclick="openCheckoutModal({{ $truck->id }}, '{{ addslashes($truck->license_plate) }}')"
                        class="w-full bg-blue-600 hover:bg-blue-500 active:scale-[0.98] transition-all text-white font-bold py-2.5 rounded-xl shadow-md shadow-blue-500/10 flex items-center justify-center space-x-1.5 text-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('CHO XE ĐI') }}</span>
                </button>
                @else
                <!-- Edit & Checkin Actions -->
                <div class="flex gap-2">
                    <button type="button" 
                            onclick="openEditSessionModal({{ $truck->activeSession->id }}, '{{ addslashes($truck->license_plate) }}', '{{ $truck->activeSession->driver_id }}', '{{ addslashes($truck->activeSession->destination) }}', '{{ addslashes($truck->activeSession->purpose) }}')"
                            class="w-1/3 bg-amber-500 hover:bg-amber-400 active:scale-[0.98] transition-all text-white font-bold py-2.5 rounded-xl shadow-md shadow-amber-500/10 flex items-center justify-center space-x-1.5 text-sm"
                            title="{{ __('Sửa thông tin chuyến đi') }}">
                        <i class="fas fa-edit"></i>
                        <span class="hidden sm:inline">{{ __('SỬA') }}</span>
                    </button>
                    
                    <form action="{{ route('trucks.checkin', $truck->id) }}" method="POST" onsubmit="return confirm('{{ __('Xác nhận xe đã về bến?') }}')" class="flex-1">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] transition-all text-white font-bold py-2.5 rounded-xl shadow-md shadow-emerald-500/10 flex items-center justify-center space-x-1.5 text-sm">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>{{ __('XÁC NHẬN VỀ') }}</span>
                        </button>
                    </form>
                </div>
                @endif
            </div>

        </div>
        @empty
        <div class="col-span-full py-16 text-center text-slate-500 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
            <div class="flex flex-col items-center">
                <i class="fas fa-truck-moving text-5xl mb-3 opacity-40"></i>
                <p class="font-bold text-lg text-slate-700 dark:text-slate-300">{{ __('Không có xe tải nào') }}</p>
                <p class="text-sm text-slate-400 mt-1 max-w-sm">{{ __('Hãy thông báo cho IT khởi tạo danh sách xe tải nội bộ để bắt đầu quản lý.') }}</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Checkout Modal Backdrop -->
<div id="checkout-modal-backdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0" onclick="closeCheckoutModal()"></div>

<!-- Checkout Modal Content -->
<div id="checkout-modal" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-2xl z-50 hidden transition-all duration-300 opacity-0 scale-95 border border-slate-200 dark:border-slate-800 overflow-hidden">
    <!-- Header -->
    <div class="bg-blue-600 px-5 py-4 flex justify-between items-center text-white">
        <h3 class="font-bold flex items-center">
            <i class="fas fa-sign-out-alt mr-2"></i> 
            {{ __('Check-out Xe Tải: ') }} <span id="modal-truck-plate" class="ml-1 font-mono font-black border border-white/20 bg-white/10 px-2 py-0.5 rounded"></span>
        </h3>
        <button onclick="closeCheckoutModal()" class="text-white/70 hover:text-white transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    
    <!-- Form -->
    <form id="checkout-truck-form" method="POST" class="p-6 space-y-4">
        @csrf
        
        <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Chọn tài xế chuyến đi') }} <span class="text-rose-500">*</span></label>
            <select name="driver_id" required class="w-full border border-slate-350 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- {{ __('Chọn tài xế') }} --</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }} @if($driver->phone) ({{ $driver->phone }}) @endif</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Nơi đến / Điểm đến') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="destination" required placeholder="{{ __('Ví dụ: Kho Hà Nội, Xưởng B,...') }}" 
                   class="w-full border border-slate-350 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Mục đích sử dụng xe') }}</label>
            <input type="text" name="purpose" placeholder="{{ __('Ví dụ: Vận chuyển linh kiện, bảo dưỡng...') }}" 
                   class="w-full border border-slate-355 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="pt-2 flex gap-3">
            <button type="button" onclick="closeCheckoutModal()" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-xl transition-all">
                {{ __('Hủy bỏ') }}
            </button>
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-blue-500/20">
                {{ __('CHO XE ĐI') }}
            </button>
        </div>
    </form>
    </form>
</div>

<!-- Edit Session Modal Content -->
<div id="edit-session-modal" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-2xl z-50 hidden transition-all duration-300 opacity-0 scale-95 border border-slate-200 dark:border-slate-800 overflow-hidden">
    <!-- Header -->
    <div class="bg-amber-500 px-5 py-4 flex justify-between items-center text-white">
        <h3 class="font-bold flex items-center">
            <i class="fas fa-edit mr-2"></i> 
            {{ __('Sửa Chuyến Đi: ') }} <span id="modal-edit-truck-plate" class="ml-1 font-mono font-black border border-white/20 bg-white/10 px-2 py-0.5 rounded"></span>
        </h3>
        <button onclick="closeEditSessionModal()" class="text-white/70 hover:text-white transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    
    <!-- Form -->
    <form id="edit-session-form" method="POST" class="p-6 space-y-4">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Tài xế chuyến đi') }} <span class="text-rose-500">*</span></label>
            <select name="driver_id" id="edit-driver-id" required class="w-full border border-slate-350 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">-- {{ __('Chọn tài xế') }} --</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }} @if($driver->phone) ({{ $driver->phone }}) @endif</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Nơi đến / Điểm đến') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="destination" id="edit-destination" required placeholder="{{ __('Ví dụ: Kho Hà Nội, Xưởng B,...') }}" 
                   class="w-full border border-slate-350 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Mục đích sử dụng xe') }}</label>
            <input type="text" name="purpose" id="edit-purpose" placeholder="{{ __('Ví dụ: Vận chuyển linh kiện, bảo dưỡng...') }}" 
                   class="w-full border border-slate-355 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div class="pt-2 flex gap-3">
            <button type="button" onclick="closeEditSessionModal()" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-xl transition-all">
                {{ __('Hủy bỏ') }}
            </button>
            <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-400 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-amber-500/20">
                {{ __('LƯU THAY ĐỔI') }}
            </button>
        </div>
    </form>
</div>

<script>
    const checkoutModalBackdrop = document.getElementById('checkout-modal-backdrop');
    const checkoutModal = document.getElementById('checkout-modal');
    const checkoutForm = document.getElementById('checkout-truck-form');
    
    function openCheckoutModal(id, plate) {
        // Set form action dynamically
        checkoutForm.action = `/trucks/${id}/checkout`;
        
        // Populate modal data
        document.getElementById('modal-truck-plate').textContent = plate;
        
        // Show modal
        checkoutModalBackdrop.classList.remove('hidden');
        checkoutModal.classList.remove('hidden');
        
        // Trigger animations
        setTimeout(() => {
            checkoutModalBackdrop.classList.remove('opacity-0');
            checkoutModal.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }
    
    function closeCheckoutModal() {
        checkoutModalBackdrop.classList.add('opacity-0');
        checkoutModal.classList.add('opacity-0', 'scale-95');
        
        // Hide completely after transition
        setTimeout(() => {
            checkoutModalBackdrop.classList.add('hidden');
            checkoutModal.classList.add('hidden');
        }, 300);
    }

    const editModal = document.getElementById('edit-session-modal');
    const editForm = document.getElementById('edit-session-form');

    function openEditSessionModal(sessionId, plate, driverId, destination, purpose) {
        editForm.action = `/trucks/session/${sessionId}`;
        
        document.getElementById('modal-edit-truck-plate').textContent = plate;
        document.getElementById('edit-driver-id').value = driverId;
        document.getElementById('edit-destination').value = destination;
        document.getElementById('edit-purpose').value = purpose;
        
        checkoutModalBackdrop.classList.remove('hidden');
        editModal.classList.remove('hidden');
        
        setTimeout(() => {
            checkoutModalBackdrop.classList.remove('opacity-0');
            editModal.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeEditSessionModal() {
        checkoutModalBackdrop.classList.add('opacity-0');
        editModal.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            checkoutModalBackdrop.classList.add('hidden');
            editModal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection
