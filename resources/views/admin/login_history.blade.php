@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center">
                <i class="fas fa-history text-blue-500 mr-3"></i>
                {{ __('Lịch sử Đăng nhập') }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Quản lý và giám sát các lượt truy cập vào hệ thống') }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-md border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 bg-slate-50 dark:bg-slate-800/50 dark:text-slate-400 uppercase border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">{{ __('Tài khoản') }}</th>
                        <th scope="col" class="px-6 py-4 font-semibold">{{ __('Thời gian') }}</th>
                        <th scope="col" class="px-6 py-4 font-semibold w-1/3">{{ __('Thiết bị / Trình duyệt') }}</th>
                        <th scope="col" class="px-6 py-4 font-semibold">IP Address</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">{{ __('Trạng thái') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse($histories as $index => $history)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors {{ $index % 2 === 0 ? 'bg-white dark:bg-slate-900' : 'bg-slate-50/50 dark:bg-slate-800/10' }}">
                        <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">
                            {{ $history->username }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                            {{ $history->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">
                            {{ $history->user_agent }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                            {{ $history->ip_address }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($history->is_success)
                                <i class="fas fa-check text-emerald-500"></i>
                            @else
                                <i class="fas fa-times text-rose-500"></i>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            {{ __('Chưa có dữ liệu đăng nhập nào.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($histories->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
