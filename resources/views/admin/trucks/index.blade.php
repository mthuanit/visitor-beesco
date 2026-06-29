@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center">
                <i class="fas fa-truck mr-3 text-blue-500"></i>
                {{ __('Quản lý Danh sách Xe Tải') }}
            </h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                {{ __('Quản lý danh sách xe tải nội bộ và biển số xe') }}
            </p>
        </div>
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

    <!-- Validation Errors -->
    @if($errors->any())
    <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 p-4 rounded-xl text-sm shadow-sm">
        <div class="flex items-center mb-2">
            <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
            <span class="font-bold">{{ __('Lỗi dữ liệu đầu vào:') }}</span>
        </div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Trucks List -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden transition-colors">
            <!-- Search & Filters -->
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <h3 class="font-bold text-slate-700 dark:text-slate-200 flex items-center shrink-0">
                    <i class="fas fa-list mr-2 text-slate-400"></i> {{ __('Danh sách xe') }}
                </h3>
                <form action="{{ route('admin.trucks.index') }}" method="GET" class="w-full sm:max-w-xs flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('Tìm biển số, tài xế, tên xe...') }}" 
                               class="w-full border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg py-2 pl-8 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                    @if($search)
                    <a href="{{ route('admin.trucks.index') }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-sm hover:bg-slate-300 transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md shadow-blue-500/10 transition-colors">
                        {{ __('Lọc') }}
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider bg-slate-50 dark:bg-slate-950/30">
                            <th class="py-4 px-6">{{ __('Biển số xe') }}</th>
                            <th class="py-4 px-6">{{ __('Tên xe') }}</th>
                            <th class="py-4 px-6 text-center">{{ __('Trạng thái') }}</th>
                            <th class="py-4 px-6 text-center">{{ __('Hành động') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 dark:divide-slate-850">
                        @forelse($trucks as $truck)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="py-4 px-6 font-mono font-black text-slate-800 dark:text-slate-100 text-base">
                                {{ $truck->license_plate }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $truck->name }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($truck->status === \App\Models\Truck::STATUS_INSIDE)
                                <span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400 rounded-full inline-block">
                                    <i class="fas fa-home mr-1"></i>{{ __('Trong Công Ty') }}
                                </span>
                                @else
                                <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400 rounded-full inline-block">
                                    <i class="fas fa-truck-moving mr-1"></i>{{ __('Đang Đi') }}
                                </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center space-x-1.5 whitespace-nowrap">
                                <button type="button" 
                                        onclick="openEditModal({{ $truck->id }}, '{{ addslashes($truck->name) }}', '{{ addslashes($truck->license_plate) }}')"
                                        class="text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 p-1.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors inline-block" 
                                        title="{{ __('Chỉnh sửa') }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="{{ route('admin.trucks.destroy', $truck->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa xe này khỏi hệ thống không?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-600 dark:hover:text-rose-450 p-1.5 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors" title="{{ __('Xóa') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-truck-moving text-4xl mb-3 opacity-40"></i>
                                    <p class="text-sm font-medium">{{ __('Không tìm thấy thông tin xe tải nào.') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($trucks->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                {{ $trucks->withQueryString()->links() }}
            </div>
            @endif
        </div>

        <!-- Right: Add Truck Form -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-6 space-y-5 transition-colors">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-slate-800 dark:text-slate-150 text-base uppercase tracking-wider flex items-center">
                    <i class="fas fa-plus-circle text-blue-500 mr-2"></i> {{ __('Thêm xe tải mới') }}
                </h3>
            </div>

            <form action="{{ route('admin.trucks.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Tên xe / Loại xe') }} <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="{{ __('Ví dụ: Xe tải Hyundai 5 tấn') }}" 
                           class="w-full border border-slate-300 dark:border-slate-850 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Biển số xe') }} <span class="text-rose-500">*</span></label>
                    <input type="text" name="license_plate" required placeholder="{{ __('Ví dụ: 29H-12345') }}" 
                           class="w-full border border-slate-300 dark:border-slate-850 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase font-mono font-bold">
                </div>


                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-[0.98] text-center flex items-center justify-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ __('LƯU THÔNG TIN XE') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Truck Modal Backdrop -->
<div id="edit-modal-backdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0" onclick="closeEditModal()"></div>

<!-- Edit Truck Modal Content -->
<div id="edit-modal" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-2xl z-50 hidden transition-all duration-300 opacity-0 scale-95 border border-slate-200 dark:border-slate-800 overflow-hidden">
    <!-- Header -->
    <div class="bg-blue-600 px-5 py-4 flex justify-between items-center text-white">
        <h3 class="font-bold flex items-center"><i class="fas fa-edit mr-2"></i> {{ __('Chỉnh sửa thông tin xe') }}</h3>
        <button onclick="closeEditModal()" class="text-white/70 hover:text-white transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    
    <!-- Form -->
    <form id="edit-truck-form" method="POST" class="p-6 space-y-4">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Tên xe / Loại xe') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="edit-name" required 
                   class="w-full border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1.5">{{ __('Biển số xe') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="license_plate" id="edit-license_plate" required 
                   class="w-full border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase font-mono font-bold">
        </div>



        <div class="pt-2 flex gap-3">
            <button type="button" onclick="closeEditModal()" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-xl transition-all">
                {{ __('Hủy bỏ') }}
            </button>
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-blue-500/20">
                {{ __('CẬP NHẬT') }}
            </button>
        </div>
    </form>
</div>

<script>
    const modalBackdrop = document.getElementById('edit-modal-backdrop');
    const modal = document.getElementById('edit-modal');
    const editForm = document.getElementById('edit-truck-form');
    
    function openEditModal(id, name, licensePlate) {
        // Set form action dynamically
        editForm.action = `/admin/trucks/${id}`;
        
        // Populate inputs
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-license_plate').value = licensePlate;
        
        // Show modal
        modalBackdrop.classList.remove('hidden');
        modal.classList.remove('hidden');
        
        // Trigger animations
        setTimeout(() => {
            modalBackdrop.classList.remove('opacity-0');
            modal.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }
    
    function closeEditModal() {
        modalBackdrop.classList.add('opacity-0');
        modal.classList.add('opacity-0', 'scale-95');
        
        // Hide completely after transition
        setTimeout(() => {
            modalBackdrop.classList.add('hidden');
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection
