@extends('layouts.app')

@section('content')
<div class="space-y-6 relative">
    <!-- Header & Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center">
                <i class="fas fa-id-card mr-3 text-blue-500"></i>
                {{ __('Quản lý Thẻ: ') }} {{ $factory }}
            </h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                {{ __('Sơ đồ trực quan trạng thái thẻ của xưởng ') }} {{ $factory }}
            </p>
        </div>
        
        <!-- Factory Filter -->
        @if(!auth()->user()->isFactoryAccount())
        <div class="flex items-center space-x-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg p-1 shadow-sm">
            @foreach(['BV', 'LN', 'BD', 'PL'] as $fac)
                <a href="{{ route('cards.index', ['factory' => $fac]) }}" class="px-4 py-1.5 rounded-md text-sm font-semibold transition-colors {{ $factory === $fac ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                    {{ $fac }}
                </a>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Stats Grid (Reduced to 3 columns) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm flex items-center justify-between transition-transform hover:scale-[1.02]">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Tổng số thẻ') }} ({{ $factory }})</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">{{ number_format($stats['total']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-500">
                <i class="fas fa-layer-group text-lg"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm flex items-center justify-between transition-transform hover:scale-[1.02]">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Đang trống (Sẵn sàng)') }}</p>
                <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-500">{{ number_format($stats['available']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-500">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm flex items-center justify-between transition-transform hover:scale-[1.02]">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Đang sử dụng') }}</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['in_use']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-500">
                <i class="fas fa-user-clock text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Unified Card Grid Container -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
            <h3 class="font-bold text-slate-700 dark:text-slate-200 flex items-center">
                <i class="fas fa-th mr-2 text-slate-400"></i> {{ __('Sơ đồ Thẻ') }}
            </h3>
            <div class="flex items-center space-x-4 text-xs font-medium">
                <span class="flex items-center text-amber-600 dark:text-amber-400"><span class="w-3 h-3 rounded-full bg-amber-500 mr-1.5"></span> Trống</span>
                <span class="flex items-center text-emerald-600 dark:text-emerald-400"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-1.5"></span> Đang dùng</span>
            </div>
        </div>
        
        <div class="p-6">
            @if($cards->count() > 0)
                <div class="grid grid-cols-5 md:grid-cols-10 gap-3">
                    @foreach($cards as $card)
                        @if($card->status === \App\Models\Card::STATUS_IN_USE)
                            <!-- In Use Card (Emerald) -->
                            <button type="button" 
                                    onclick="showVisitorModal('{{ $card->code }}', '{{ addslashes($card->activeSession?->name) }}', '{{ addslashes($card->activeSession?->company) }}', '{{ addslashes($card->activeSession?->meet_person) }}', '{{ $card->activeSession ? \Carbon\Carbon::parse($card->activeSession->checkin_time)->format('H:i d/m/Y') : '' }}', '{{ $card->activeSession ? route('visitors.show', $card->activeSession->id) : '#' }}')"
                                    class="relative aspect-square flex flex-col items-center justify-center rounded-xl border-2 border-emerald-400 dark:border-emerald-500/50 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 shadow-sm hover:shadow-md hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all active:scale-95 group overflow-hidden">
                                <span class="font-mono font-black text-sm relative z-10">{{ $card->code }}</span>
                                <i class="fas fa-user-check text-[10px] mt-1 opacity-70 relative z-10"></i>
                                <!-- Tooltip hint -->
                                <div class="absolute inset-0 bg-emerald-600 flex items-center justify-center text-white font-bold opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                    <i class="fas fa-search"></i>
                                </div>
                            </button>
                        @else
                            <!-- Available Card (Amber) -->
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl border border-amber-200 dark:border-amber-800/50 bg-amber-50/50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-500/70 opacity-80 cursor-default">
                                <span class="font-mono font-bold text-sm">{{ $card->code }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center text-slate-500 py-12">
                    <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                    <p>{{ __('Chưa có thẻ nào được cấu hình cho xưởng này.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Visitor Info Modal Backdrop -->
<div id="visitor-modal-backdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0" onclick="closeVisitorModal()"></div>

<!-- Visitor Info Modal Content -->
<div id="visitor-modal" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-sm bg-white dark:bg-slate-900 rounded-2xl shadow-2xl z-50 hidden transition-all duration-300 opacity-0 scale-95 border border-slate-200 dark:border-slate-800 overflow-hidden">
    <!-- Header -->
    <div class="bg-emerald-500 px-5 py-4 flex justify-between items-center text-white">
        <h3 class="font-bold flex items-center"><i class="fas fa-id-card mr-2"></i> {{ __('Đang giữ thẻ: ') }} <span id="modal-card-code" class="ml-1 font-mono font-black"></span></h3>
        <button onclick="closeVisitorModal()" class="text-white/70 hover:text-white transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    
    <!-- Body -->
    <div class="p-6 flex flex-col items-center text-center">
        
        <!-- Info -->
        <h4 id="modal-name" class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-1"></h4>
        <p id="modal-company" class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-4"></p>
        
        <div class="w-full bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 flex flex-col gap-2 border border-slate-100 dark:border-slate-800 mb-6">
            <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-slate-400 uppercase">{{ __('Vào gặp ai') }}</span>
                <span id="modal-meet" class="text-sm font-bold text-slate-700 dark:text-slate-300"></span>
            </div>
            <div class="w-full h-px bg-slate-200 dark:bg-slate-700/50"></div>
            <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-slate-400 uppercase">{{ __('Giờ vào') }}</span>
                <span id="modal-time" class="text-sm font-bold text-slate-700 dark:text-slate-300"></span>
            </div>
        </div>
        
        <!-- Actions -->
        <a id="modal-link" href="#" class="w-full block bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold py-3 rounded-xl transition-colors shadow-lg shadow-slate-500/20">
            {{ __('Xem Chi Tiết Lượt Khách') }}
        </a>
    </div>
</div>

<script>
    const modalBackdrop = document.getElementById('visitor-modal-backdrop');
    const modal = document.getElementById('visitor-modal');
    
    function showVisitorModal(code, name, company, meetPerson, time, detailUrl) {
        // Populate data
        document.getElementById('modal-card-code').textContent = code;
        document.getElementById('modal-name').textContent = name || '{{ __("Không có tên") }}';
        document.getElementById('modal-company').textContent = company || '{{ __("Khách vãng lai") }}';
        document.getElementById('modal-meet').textContent = meetPerson || '{{ __("Không rõ") }}';
        document.getElementById('modal-time').textContent = time || 'N/A';
        document.getElementById('modal-link').href = detailUrl;
        
        // Show modal
        modalBackdrop.classList.remove('hidden');
        modal.classList.remove('hidden');
        
        // Trigger animations (small delay for display:block to apply)
        setTimeout(() => {
            modalBackdrop.classList.remove('opacity-0');
            modal.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }
    
    function closeVisitorModal() {
        modalBackdrop.classList.add('opacity-0');
        modal.classList.add('opacity-0', 'scale-95');
        
        // Wait for transition before hiding completely
        setTimeout(() => {
            modalBackdrop.classList.add('hidden');
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection
