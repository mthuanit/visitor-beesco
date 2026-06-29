@extends('layouts.app')

@section('content')
<div class="{{ request()->has('iframe') ? '' : 'bg-white border-slate-200 dark:bg-slate-900 dark:border-slate-800 shadow-2xl border rounded-2xl' }} overflow-hidden max-w-4xl mx-auto text-slate-800 dark:text-slate-200 transition-colors duration-205">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950 transition-colors duration-205">
        <h2 class="text-lg font-bold tracking-wider uppercase text-slate-700 dark:text-slate-200">
            <i class="fas fa-info-circle mr-2 text-blue-500"></i> {{ __('CHI TIẾT KHÁCH RA VÀO') }}
        </h2>
        <div class="flex items-center space-x-3">
            @if($session->checkin_time && $session->checkin_time->diffInMinutes(now()) <= 30)
                <a href="{{ route('visitors.edit', $session->id) }}{{ request()->has('iframe') ? '?iframe=1' : '' }}" class="text-xs bg-blue-100 hover:bg-blue-200 border border-blue-300 text-blue-700 dark:bg-blue-900/50 dark:hover:bg-blue-800/60 dark:border-blue-700/50 dark:text-blue-300 px-3.5 py-1.5 rounded-lg transition-all flex items-center space-x-1.5 shadow-md">
                    <i class="fas fa-edit"></i>
                    <span>{{ __('Chỉnh sửa') }}</span>
                </a>
            @endif
            <a href="{{ route('visitors.index') }}{{ request()->has('iframe') ? '?iframe=1' : '' }}" class="text-xs bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-705 dark:bg-slate-850 dark:hover:bg-slate-800 dark:border-slate-700 dark:text-slate-300 px-3.5 py-1.5 rounded-lg transition-all flex items-center space-x-1.5 shadow-md">
                <i class="fas fa-arrow-left"></i>
                <span>{{ __('Quay lại') }}</span>
            </a>
        </div>
    </div>
    
    <div class="{{ request()->has('iframe') ? 'px-0 py-6' : 'p-6' }}">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Phần Hình Ảnh -->
            <div class="col-span-1 space-y-4">
                <!-- Ảnh Lúc Vào -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-xl p-3 bg-slate-50 dark:bg-slate-950/40 text-center transition-colors duration-205">
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase mb-2.5 block border-b border-slate-200 dark:border-slate-800 pb-1.5">{{ __('Ảnh Lúc Vào (In)') }}</span>
                    @if($session->photo || $session->portrait_photo)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @if($session->portrait_photo)
                            <div class="relative group overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800">
                                <img src="{{ asset('storage/' . $session->portrait_photo) }}" alt="Ảnh chân dung lúc vào" class="w-full aspect-video object-cover rounded-lg shadow-md cursor-pointer hover:scale-[1.02] transition-transform duration-200" onclick="openModal('{{ asset('storage/' . $session->portrait_photo) }}')">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity pointer-events-none">
                                    <i class="fas fa-search-plus text-white text-lg"></i>
                                </div>
                            </div>
                            @endif
                            @if($session->photo)
                            <div class="relative group overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800">
                                <img src="{{ asset('storage/' . $session->photo) }}" alt="Ảnh toàn cảnh lúc vào" class="w-full aspect-video object-cover rounded-lg shadow-md cursor-pointer hover:scale-[1.02] transition-transform duration-200" onclick="openModal('{{ asset('storage/' . $session->photo) }}')">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity pointer-events-none">
                                    <i class="fas fa-search-plus text-white text-lg"></i>
                                </div>
                            </div>
                            @endif
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

                <!-- Ảnh Lúc Ra -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-xl p-3 bg-slate-50 dark:bg-slate-950/40 text-center transition-colors duration-205">
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase mb-2.5 block border-b border-slate-200 dark:border-slate-800 pb-1.5">{{ __('Ảnh Lúc Ra (Out)') }}</span>
                    @if($session->photo_checkout || $session->portrait_photo_checkout)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @if($session->portrait_photo_checkout)
                            <div class="relative group overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800">
                                <img src="{{ asset('storage/' . $session->portrait_photo_checkout) }}" alt="Ảnh chân dung lúc ra" class="w-full aspect-video object-cover rounded-lg shadow-md cursor-pointer hover:scale-[1.02] transition-transform duration-200" onclick="openModal('{{ asset('storage/' . $session->portrait_photo_checkout) }}')">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity pointer-events-none">
                                    <i class="fas fa-search-plus text-white text-lg"></i>
                                </div>
                            </div>
                            @endif
                            @if($session->photo_checkout)
                            <div class="relative group overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800">
                                <img src="{{ asset('storage/' . $session->photo_checkout) }}" alt="Ảnh toàn cảnh lúc ra" class="w-full aspect-video object-cover rounded-lg shadow-md cursor-pointer hover:scale-[1.02] transition-transform duration-200" onclick="openModal('{{ asset('storage/' . $session->photo_checkout) }}')">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity pointer-events-none">
                                    <i class="fas fa-search-plus text-white text-lg"></i>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="w-full h-40 flex items-center justify-center bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-500 rounded-lg border border-slate-200 dark:border-slate-800 border-dashed transition-colors duration-205">
                            <div class="text-center">
                                <i class="fas fa-camera text-3xl mb-1.5 text-slate-400 dark:text-slate-600"></i><br>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">{{ __('CHƯA CÓ ẢNH') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6 text-center">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">{{ __('Trạng thái hiện tại') }}</span><br>
                    @if($session->checkout_time)
                        <span class="mt-2 inline-flex px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 rounded-full font-bold text-xs transition-colors duration-205">{{ __('Đã Rời Đi') }}</span>
                    @else
                        <span class="mt-2 inline-flex px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 rounded-full font-bold text-xs transition-colors duration-205">
                            <i class="fas fa-circle text-[10px] text-emerald-500 mr-1.5 animate-pulse mt-0.5"></i> {{ __('Đang Trong Khu Vực') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Phần Thông Tin -->
            <div class="col-span-2 space-y-5">
                <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-slate-100 transition-colors duration-205">{{ $session->name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 flex items-center gap-1.5 transition-colors duration-205">
                        <span>{{ __('Mã thẻ:') }}</span>
                        <span class="font-mono font-bold text-blue-600 dark:text-blue-400 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 px-2.5 py-0.5 rounded transition-colors duration-205">{{ $session->barcode }}</span>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-y-5 gap-x-6 text-sm">
                    
                    <div>
                        <span class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Số CCCD / Hộ chiếu') }}</span>
                        <span class="block text-slate-800 dark:text-slate-200 font-medium bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-850 px-3 py-2.5 rounded-lg transition-colors duration-205">{{ $session->cccd ?? __('Chưa cập nhật') }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Số điện thoại') }}</span>
                        <span class="block text-slate-800 dark:text-slate-200 font-medium bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-850 px-3 py-2.5 rounded-lg transition-colors duration-205">{{ $session->phone ?? __('Chưa cập nhật') }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Cơ quan / Công ty') }}</span>
                        <span class="block text-slate-800 dark:text-slate-200 font-medium bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-850 px-3 py-2.5 rounded-lg transition-colors duration-205">{{ $session->company ?? __('Chưa cập nhật') }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Người cần gặp') }}</span>
                        <span class="block text-slate-800 dark:text-slate-200 font-medium bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-850 px-3 py-2.5 rounded-lg transition-colors duration-205">{{ $session->meet_person ?? __('Chưa cập nhật') }}</span>
                    </div>

                    <div class="col-span-2">
                        <span class="block text-slate-500 text-xs uppercase font-bold tracking-wider mb-1">{{ __('Phương tiện di chuyển') }}</span>
                        <span class="block text-slate-800 dark:text-slate-200 font-medium bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-850 px-3 py-2.5 rounded-lg transition-colors duration-205">
                            <i class="fas fa-car-side opacity-50 mr-1.5 text-blue-600 dark:text-blue-400"></i>{{ $session->vehicle ?? __('Đi bộ') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-sm mt-8 p-4 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 transition-colors duration-205">
                    <div>
                        <span class="block text-emerald-600 dark:text-emerald-400 text-xs uppercase font-bold"><i class="fas fa-sign-in-alt mr-1"></i> {{ __('Thời điểm vào (Check-in)') }}</span>
                        <span class="block text-slate-800 dark:text-slate-200 font-bold text-base mt-1.5 transition-colors duration-205">{{ $session->checkin_time->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 dark:text-slate-400 text-xs uppercase font-bold"><i class="fas fa-sign-out-alt mr-1"></i> {{ __('Thời điểm ra (Check-out)') }}</span>
                        <span class="block text-slate-800 dark:text-slate-200 font-bold text-base mt-1.5 transition-colors duration-205">{{ $session->checkout_time ? $session->checkout_time->format('d/m/Y H:i:s') : __('Chưa Check-out') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

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