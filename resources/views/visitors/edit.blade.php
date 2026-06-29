@extends('layouts.app')

@section('content')
<div class="{{ request()->has('iframe') ? '' : 'bg-white border-slate-200 dark:bg-slate-900 dark:border-slate-800 shadow-2xl border rounded-2xl' }} overflow-hidden max-w-4xl mx-auto text-slate-800 dark:text-slate-200 transition-colors duration-205">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950 transition-colors duration-205">
        <h2 class="text-lg font-bold tracking-wider uppercase text-slate-700 dark:text-slate-200">
            <i class="fas fa-edit mr-2 text-blue-500"></i> {{ __('CHỈNH SỬA THÔNG TIN KHÁCH') }}
        </h2>
        <a href="{{ route('visitors.show', $session->id) }}{{ request()->has('iframe') ? '?iframe=1' : '' }}" class="text-xs bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-705 dark:bg-slate-850 dark:hover:bg-slate-800 dark:border-slate-700 dark:text-slate-300 px-3.5 py-1.5 rounded-lg transition-all flex items-center space-x-1.5 shadow-md">
            <i class="fas fa-times"></i>
            <span>{{ __('Hủy bỏ') }}</span>
        </a>
    </div>
    
    <div class="{{ request()->has('iframe') ? 'px-0 py-6' : 'p-6' }}">
        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-600 border border-red-200 rounded-lg p-4 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('visitors.update', $session->id) }}{{ request()->has('iframe') ? '?iframe=1' : '' }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Phần Hình Ảnh (Chỉ xem) -->
                <div class="col-span-1 space-y-4">
                    <!-- Ảnh Lúc Vào -->
                    <div class="border border-slate-200 dark:border-slate-800 rounded-xl p-3 bg-slate-50 dark:bg-slate-950/40 text-center transition-colors duration-205">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase mb-2.5 block border-b border-slate-200 dark:border-slate-800 pb-1.5">{{ __('Ảnh Lúc Vào (In)') }}</span>
                        @if($session->photo)
                            <div class="relative group overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800">
                                <img src="{{ asset('storage/' . $session->photo) }}" alt="Ảnh lúc vào" class="w-full h-auto rounded-lg shadow-md cursor-pointer hover:scale-[1.02] transition-transform duration-200" onclick="openModal('{{ asset('storage/' . $session->photo) }}')">
                            </div>
                        @else
                            <div class="w-full h-40 flex items-center justify-center bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-500 rounded-lg border border-slate-200 dark:border-slate-800 border-dashed transition-colors duration-205">
                                <div class="text-center">
                                    <i class="fas fa-camera text-3xl mb-1.5 text-slate-400 dark:text-slate-600"></i><br>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">{{ __('KHÔNG CÓ ẢNH') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Thời gian checkin/out (Chỉ xem) -->
                    <div class="mt-6 text-center space-y-4">
                        <div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">{{ __('Trạng thái hiện tại') }}</span><br>
                            @if($session->checkout_time)
                                <span class="mt-2 inline-flex px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 rounded-full font-bold text-xs transition-colors duration-205">{{ __('Đã Rời Đi') }}</span>
                            @else
                                <span class="mt-2 inline-flex px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 rounded-full font-bold text-xs transition-colors duration-205">
                                    <i class="fas fa-circle text-[10px] text-emerald-500 mr-1.5 animate-pulse mt-0.5"></i> {{ __('Đang Trong Khu Vực') }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800">
                            <div class="mb-2">
                                <span class="block text-emerald-600 dark:text-emerald-400 text-xs font-bold">{{ __('Thời điểm vào') }}</span>
                                <span class="block text-slate-800 dark:text-slate-200 text-sm">{{ $session->checkin_time->format('d/m/Y H:i:s') }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-500 dark:text-slate-400 text-xs font-bold">{{ __('Thời điểm ra') }}</span>
                                <span class="block text-slate-800 dark:text-slate-200 text-sm">{{ $session->checkout_time ? $session->checkout_time->format('d/m/Y H:i:s') : __('Chưa Check-out') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phần Thông Tin (Có thể chỉnh sửa) -->
                <div class="col-span-2 space-y-5">
                    <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
                        <label class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Họ và tên') }}</label>
                        <input type="text" name="name" value="{{ old('name', $session->name) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 rounded-lg text-lg font-bold transition-colors duration-205 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                        
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2.5 flex items-center gap-1.5">
                            <span>{{ __('Mã thẻ (Chỉ xem):') }}</span>
                            <span class="font-mono font-bold text-blue-600 dark:text-blue-400 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 px-2.5 py-0.5 rounded">{{ $session->barcode }}</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-y-5 gap-x-6 text-sm">
                        <div>
                            <label class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Số CCCD / Hộ chiếu') }}</label>
                            <input type="text" name="cccd" value="{{ old('cccd', $session->cccd) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 rounded-lg transition-colors duration-205 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Số điện thoại') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $session->phone) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 rounded-lg transition-colors duration-205 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Cơ quan / Công ty') }}</label>
                            <input type="text" name="company" value="{{ old('company', $session->company) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 rounded-lg transition-colors duration-205 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Người cần gặp') }}</label>
                            <input type="text" name="meet_person" value="{{ old('meet_person', $session->meet_person) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 rounded-lg transition-colors duration-205 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Phương tiện di chuyển') }}</label>
                            <input type="text" name="vehicle" value="{{ old('vehicle', $session->vehicle) }}" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2 rounded-lg transition-colors duration-205 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-colors flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            {{ __('Lưu thay đổi') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Phóng to ảnh -->
<div id="imageModal" class="fixed inset-0 z-50 hidden bg-slate-950 bg-opacity-95 flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300" onclick="closeModal()">
    <div class="max-w-4xl max-h-full relative flex flex-col items-center">
        <img id="modalImg" src="" class="max-w-full max-h-[85vh] rounded-xl border border-slate-200 dark:border-slate-850 shadow-2xl object-contain transition-all duration-205">
        <button class="mt-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-850 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded-lg transition-all" onclick="closeModal()">{{ __('Đóng (ESC)') }}</button>
     </div>
</div>

<script>
    function openModal(src) {
        document.getElementById('modalImg').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    // ESC key listener to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endsection
