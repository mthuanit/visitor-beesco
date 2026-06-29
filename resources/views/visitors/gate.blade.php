@extends('layouts.app')

@section('content')
<!-- Custom Styles for Premium Gate Control UI -->
<style>
    @keyframes pulse-soft {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.05); }
    }
    @keyframes laser-sweep {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }
    .pulse-glow {
        animation: pulse-soft 2s infinite ease-in-out;
    }
    .laser-line {
        animation: laser-sweep 2.5s infinite linear;
    }
    /* Dynamic slide-in toast */
    .toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
    }
    .toast-card {
        transform: translateX(120%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
    }
    .toast-card.show {
        transform: translateX(0);
    }
</style>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center">
            <i class="fas fa-door-open mr-3 text-blue-500"></i>
            {{ __('Kiểm soát Khách Ra Vào') }}
        </h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            {{ __('Check in / out!') }}
        </p>
    </div>
</div>

<!-- Main Container Wrapper -->
<div class="flex flex-col justify-center py-0.5">
    <!-- Main Container -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- Left Column: Camera Console (CCTV & Capture) -->
    <div class="lg:col-span-8 bg-white border-slate-200 text-slate-800 dark:bg-slate-900 rounded-2xl border dark:border-slate-800 shadow-2xl overflow-hidden dark:text-slate-100 transition-colors duration-205">
        
        <!-- Console Header -->
        <div class="px-6 lg:px-8 py-4 bg-slate-50 border-b border-slate-200 dark:bg-slate-950 dark:border-slate-800 flex justify-between items-center transition-colors duration-205">
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 bg-red-500 rounded-full pulse-glow" id="camera-status-dot"></div>
                <h3 class="font-bold tracking-wide text-sm uppercase text-slate-700 dark:text-slate-300">CCTV CONSOLE</h3>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono" id="camera-status-text">
                    {{ __('Đang khởi tạo...') }}
                </div>
            </div>
        </div>

        <!-- Video Player Frame -->
        <div class="px-6 lg:px-8 py-4 bg-slate-50 dark:bg-slate-900">
            <div class="relative bg-black aspect-[16/9] flex items-center justify-center overflow-hidden rounded-xl group shadow-inner">
                <!-- Main Webcam Feed (Portrait) -->
                <video id="webcam-feed" class="w-full h-full object-cover transition-all duration-300" autoplay playsinline muted></video>
                <canvas id="capture-webcam-canvas" class="hidden"></canvas>
                
                <!-- PiP IP Camera Feed (Panorama) -->
                <div id="pip-container" style="width: 33.3333%;" class="absolute top-4 left-4 aspect-[16/9] bg-black border-2 border-slate-700 rounded-lg overflow-hidden shadow-xl z-10 cursor-move transition-all duration-300 group/pip">
                    <video id="camera-feed" class="w-full h-full object-cover" autoplay playsinline muted></video>
                    <canvas id="capture-canvas" class="hidden"></canvas>
                    
                    <!-- Camera Loading Overlay for IP Camera -->
                    <div id="camera-loading" class="absolute inset-0 bg-slate-950 bg-opacity-80 flex flex-col items-center justify-center space-y-2 transition-opacity duration-300">
                        <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-slate-400 text-[10px] font-medium text-center leading-tight">{{ __('Tải IP Cam...') }}</span>
                    </div>

                    <!-- Resize Handle -->
                    <div id="pip-resizer" class="absolute bottom-0 right-0 w-6 h-6 cursor-nwse-resize z-20 flex items-end justify-end p-1 opacity-0 group-hover/pip:opacity-100 transition-opacity bg-gradient-to-tl from-black/50 to-transparent">
                        <svg class="w-3 h-3 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 21h6v-6M21 21l-7-7"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls Bar -->
        <div class="px-6 lg:px-8 py-5 bg-slate-50 border-t border-slate-200 dark:bg-slate-950 dark:border-slate-800 space-y-4 transition-colors duration-205">
            
            <!-- Capture and Preview Panel -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-6 pt-2">
                <button type="button" id="btn-capture" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:scale-95 transition-all text-white font-semibold py-3 px-5 rounded-xl shadow-lg shadow-blue-500/20 flex items-center justify-center space-x-2">
                    <i class="fas fa-camera text-lg"></i>
                    <span>{{ __('CHỤP ẢNH KHÁCH VÀO') }}</span>
                </button>

                <!-- Captured Image Preview Box -->
                <div class="flex items-center space-x-3 bg-white p-2 rounded-xl border border-slate-200 dark:bg-slate-900 dark:border-slate-800 w-full sm:w-auto min-w-[200px] transition-colors duration-205">
                    <div class="w-14 h-14 rounded-lg bg-slate-100 border border-slate-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden flex items-center justify-center text-slate-400 dark:text-slate-500 shrink-0 relative">
                        <img id="portrait-photo-preview" class="w-full h-full object-cover hidden" alt="Portrait Preview">
                        <img id="photo-preview" class="absolute bottom-0 right-0 w-1/2 h-1/2 object-cover border border-slate-300 rounded-sm hidden" alt="Panorama Preview">
                        <i id="no-photo-preview" class="fas fa-user-circle text-2xl"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">{{ __('Ảnh đã chụp') }}</span>
                        <span id="photo-status" class="block text-[10px] text-red-500 dark:text-red-400 font-medium">{{ __('Chưa có ảnh') }}</span>
                    </div>
                    <button type="button" id="btn-clear-photo" class="hidden text-slate-400 hover:text-red-500 dark:hover:text-red-400 p-1 ml-auto transition-colors">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Right Column: Guard Action Panel -->
    <div class="lg:col-span-4 flex flex-col space-y-6">
        
        <!-- Card Input Card -->
        <div class="bg-white border-slate-200 dark:bg-slate-900 dark:border-slate-800 shadow-xl border rounded-2xl p-6 lg:p-8 space-y-4 transition-colors duration-205">
            <label for="barcode-input" class="block text-sm font-bold text-slate-700 dark:text-slate-350 uppercase tracking-wider">{{ __('Mã Số Thẻ') }}</label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <input type="text" id="barcode-input" autofocus autocomplete="off" class="w-full border-2 border-slate-200 focus:border-blue-500 bg-white text-slate-800 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 font-mono font-bold text-xl rounded-xl py-3 pl-10 pr-3 focus:outline-none transition-all uppercase" placeholder="{{ __('Nhập mã số thẻ...') }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                        <i class="fas fa-id-card text-lg"></i>
                    </div>
                </div>
                <button type="button" id="btn-check-barcode" class="bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold px-6 rounded-xl transition-all flex items-center justify-center">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Form State View Card -->
        <div class="bg-white border-slate-200 dark:bg-slate-900 dark:border-slate-800 shadow-xl border rounded-2xl overflow-hidden min-h-[320px] flex flex-col transition-colors duration-205">
            
            <!-- State Headers -->
            <div id="form-header" class="px-6 lg:px-8 py-4 border-b border-slate-100 bg-slate-50 dark:border-slate-850 dark:bg-slate-900/40 flex items-center justify-between transition-colors duration-205">
                <h3 class="font-bold text-slate-700 dark:text-slate-300 uppercase text-sm" id="form-title">{{ __('Trạng thái chờ') }}</h3>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-350" id="form-badge">IDLE</span>
            </div>

            <!-- Content Area -->
            <div class="p-6 lg:p-8 flex-1 flex flex-col justify-between" id="form-content">
                
                <!-- 1. IDLE STATE VIEW -->
                <div id="state-idle" class="flex-1 flex flex-col items-center justify-center text-center space-y-6 py-10">
                    <div class="relative w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center text-blue-500 dark:text-blue-400">
                        <i class="fas fa-keyboard text-5xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-slate-200 text-lg">{{ __('Sẵn sàng nhập thẻ') }}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xs">{{ __('Nhập mã số thẻ bằng bàn phím để tiến hành Check-in/Check-out.') }}</p>
                    </div>
                </div>

                <!-- 2. CHECK-IN FORM -->
                <form id="state-checkin" class="hidden flex-1 flex flex-col justify-between space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1">{{ __('Họ và tên khách') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full border border-slate-300 bg-white text-slate-850 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1">{{ __('Số CCCD / Hộ chiếu') }}</label>
                            <input type="text" name="cccd" class="w-full border border-slate-300 bg-white text-slate-850 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1">{{ __('Số điện thoại') }}</label>
                            <input type="tel" name="phone" class="w-full border border-slate-300 bg-white text-slate-850 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1">{{ __('Cơ quan / Công ty') }}</label>
                            <input type="text" name="company" class="w-full border border-slate-300 bg-white text-slate-850 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1">{{ __('Người cần gặp') }}</label>
                            <input type="text" name="meet_person" class="w-full border border-slate-300 bg-white text-slate-850 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1">{{ __('Phương tiện di chuyển') }}</label>
                            <input type="text" name="vehicle" class="w-full border border-slate-300 bg-white text-slate-850 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="{{ __('Ví dụ: ĐI BỘ/60A-12345') }}">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] transition-all text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-500/20 text-center flex items-center justify-center space-x-2 mt-4">
                        <i class="fas fa-check-circle text-lg"></i>
                        <span>{{ __('LƯU VÀ CHO KHÁCH VÀO') }}</span>
                    </button>
                </form>

                <!-- 3. CHECK-OUT VIEW -->
                <div id="state-checkout" class="hidden flex-1 flex flex-col justify-between space-y-4">
                    <div class="bg-slate-55 dark:bg-slate-950 shadow-inner p-3 rounded-xl border border-slate-150 dark:border-slate-800 flex gap-4 transition-colors duration-205">
                        <!-- Visitor Avatar (Captured Check-in Photo) -->
                        <div class="flex gap-2">
                            <div class="w-24 h-24 rounded-lg bg-slate-100 border border-slate-200 dark:bg-slate-900 dark:border-slate-800 overflow-hidden flex items-center justify-center shrink-0 shadow-sm transition-colors duration-205">
                                <img id="checkout-visitor-portrait" class="w-full h-full object-cover" src="" alt="Portrait">
                            </div>
                            <div class="w-24 h-24 rounded-lg bg-slate-100 border border-slate-200 dark:bg-slate-900 dark:border-slate-800 overflow-hidden flex items-center justify-center shrink-0 shadow-sm transition-colors duration-205">
                                <img id="checkout-visitor-photo" class="w-full h-full object-cover" src="" alt="Panorama">
                            </div>
                        </div>
                        
                        <!-- Guest Info Details -->
                        <div class="flex-1 space-y-1.5 text-sm">
                            <h4 class="font-bold text-slate-850 dark:text-slate-100 text-base" id="checkout-visitor-name">Nguyễn Văn A</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-350"><span class="font-semibold text-slate-500 dark:text-slate-450">{{ __('Số CCCD / Hộ chiếu') }}:</span> <span id="checkout-visitor-cccd">1234567890</span></p>
                            <p class="text-xs text-slate-600 dark:text-slate-350"><span class="font-semibold text-slate-500 dark:text-slate-450">{{ __('Số điện thoại') }}:</span> <span id="checkout-visitor-phone">0987654321</span></p>
                            <p class="text-xs text-slate-600 dark:text-slate-350"><span class="font-semibold text-slate-500 dark:text-slate-450">{{ __('Cơ quan / Công ty') }}:</span> <span id="checkout-visitor-company">Company</span></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs bg-slate-55 border border-slate-150 dark:bg-slate-950 dark:border-slate-800 p-3.5 rounded-xl transition-colors duration-205">
                        <div>
                            <span class="block text-slate-400 dark:text-slate-500 font-bold uppercase mb-0.5">{{ __('Người cần gặp') }}</span>
                            <span class="text-slate-850 dark:text-slate-200 font-medium" id="checkout-visitor-meet">Host</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 dark:text-slate-500 font-bold uppercase mb-0.5">{{ __('Phương tiện') }}</span>
                            <span class="text-slate-850 dark:text-slate-200 font-medium" id="checkout-visitor-vehicle">Vehicle</span>
                        </div>
                        <div class="md:col-span-2">
                            <span class="block text-slate-400 dark:text-slate-500 font-bold uppercase mb-0.5">{{ __('Thời điểm vào (Check-in)') }}</span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-bold" id="checkout-visitor-checkin-time">05/06/2026 09:30:15</span>
                        </div>
                    </div>

                    <button type="button" id="btn-confirm-checkout" class="w-full bg-rose-600 hover:bg-rose-500 active:scale-[0.98] transition-all text-white font-bold py-3 rounded-xl shadow-lg shadow-rose-500/20 text-center flex items-center justify-center space-x-2">
                        <i class="fas fa-door-open text-lg"></i>
                        <span>{{ __('XÁC NHẬN CHO KHÁCH RA') }}</span>
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>
</div>

<!-- Toast Notifications UI -->
<div class="toast-container">
    <div id="toast" class="toast-card bg-slate-900 border border-slate-800 text-white rounded-xl shadow-2xl p-4 flex items-center space-x-3 min-w-[320px] max-w-md opacity-0">
        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" id="toast-icon-container">
            <i class="fas text-base" id="toast-icon"></i>
        </div>
        <div class="flex-1">
            <h5 class="font-bold text-sm" id="toast-title">{{ __('Thông báo') }}</h5>
            <p class="text-xs text-slate-400 mt-0.5" id="toast-message">{{ __('Nội dung chi tiết ở đây.') }}</p>
        </div>
        <button type="button" onclick="hideToast()" class="text-slate-400 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>



<!-- Javascript Controller -->
<script>
    // Configuration values from backend
    const webrtcStreamerUrl = "{{ $webrtcStreamerUrl }}";
    const cameraRtspUrl = "{{ $cameraRtspUrl }}";
    const apiKey = "{{ $apiKey }}";

    // Localization helper
    const currentLocale = "{{ app()->getLocale() }}";
    const translations = {
        en: {
            "Đang khởi tạo...": "Initializing...",
            "Đang kết nối Camera IP...": "Connecting to IP Camera...",
            "Chưa cấu hình địa chỉ RTSP cho camera.": "IP Camera RTSP address not configured.",
            "Camera IP RTSP - Live": "IP Camera RTSP - Live",
            "Không thể tải webrtcstreamer.js từ ": "Failed to load webrtcstreamer.js from ",
            "Không thể tải adapter.min.js từ ": "Failed to load adapter.min.js from ",
            "Lỗi kết nối WebRTC-Streamer. Hãy chắc chắn máy chủ WebRTC-Streamer đang hoạt động.": "WebRTC-Streamer connection failed. Make sure the WebRTC-Streamer server is running.",
            "Camera chưa khởi tạo thành công hoặc luồng video bị lỗi.": "Camera not initialized or video stream error.",
            "Vui lòng chụp ảnh khách hàng trước khi check-in.": "Please capture check-in photo before saving.",
            "Khách hàng Check-in thành công!": "Visitor checked in successfully!",
            "Khách hàng Check-out thành công!": "Visitor checked out successfully!",
            "Đã chụp ảnh check-in!": "Check-in photo captured!",
            "Vui lòng nhập mã số thẻ.": "Please enter card code.",
            "Lỗi kiểm tra thẻ: ": "Card check error: ",
            "Lỗi kiểm tra thẻ:": "Card check error:",
            "Lỗi xảy ra trong quá trình check-in.": "An error occurred during check-in.",
            "Lỗi mạng hoặc server: ": "Network or server error: ",
            "Lỗi mạng hoặc server:": "Network or server error:",
            "Lỗi xảy ra trong quá trình check-out.": "An error occurred during check-out.",
            "Thành công": "Success",
            "Cảnh báo": "Warning",
            "Lỗi": "Error",
            "Chưa có ảnh": "No photo yet",
            "Đang tải luồng video...": "Loading video stream...",
            "Không tìm thấy mã thẻ!": "Card code not found!",
            "Có lỗi xảy ra, vui lòng thử lại.": "An error occurred, please try again.",
            "LƯU VÀ CHO KHÁCH VÀO": "SAVE & CHECK-IN",
            "XÁC NHẬN CHO KHÁCH RA (CHECK-OUT)": "CONFIRM CHECK-OUT",
            "Trạng thái chờ": "Idle State",
            "Sẵn sàng nhập thẻ": "Ready to Scan Card",
            "Nhập mã số thẻ bằng bàn phím để tiến hành Check-in/Check-out.": "Enter card number using keyboard to perform Check-in/Check-out.",
            "Đang tải...": "Loading...",
            "IDLE": "IDLE",
            "CHECKING": "CHECKING",
            "CHECK-IN": "CHECK-IN",
            "CHECK-OUT": "CHECK-OUT",
            "Đã sẵn sàng": "Ready",
            "Đang kiểm tra...": "Checking...",
            "Lỗi truy vấn thông tin thẻ": "Failed to query card details.",
            "Thẻ": "Card",
            "THẺ TRỐNG": "VACANT",
            "ĐANG ACTIVE": "ACTIVE",
            "Không có": "None",
            "Không rõ": "Unknown",
            "Đang xử lý...": "Processing...",
            "Đang xác nhận ra...": "Confirming checkout...",
            "Thông báo": "Notification",
            "Thẻ này đang được sử dụng và chưa Check-out": "This card is already checked in and has not checked out yet.",
            "Không tìm thấy session đang active của thẻ này": "Active session for this card not found.",
            "Hành động không hợp lệ": "Invalid action.",
            "Không thể tải webrtcstreamer.js từ ": "Failed to load webrtcstreamer.js from ",
            "Không thể tải adapter.min.js từ ": "Failed to load adapter.min.js from "
        }
    };

    function __(key) {
        if (currentLocale === 'en' && translations.en[key]) {
            return translations.en[key];
        }
        return key;
    }

    // Application state
    let appState = 'idle'; // 'idle', 'checkin', 'checkout'
    let currentBarcode = '';
    let currentSession = null;
    let rtcStreamer = null;
    let capturedBlob = null;
    let capturedPortraitBlob = null;

    // Toast Timer
    let toastTimeout = null;

    // Script loading flag
    let scriptsLoaded = false;

    // DOM Elements
    const videoFeed = document.getElementById('camera-feed');
    const webcamFeed = document.getElementById('webcam-feed');
    const cameraStatusDot = document.getElementById('camera-status-dot');
    const cameraStatusText = document.getElementById('camera-status-text');
    const cameraLoading = document.getElementById('camera-loading');
    const btnCapture = document.getElementById('btn-capture');
    const photoPreview = document.getElementById('photo-preview');
    const portraitPhotoPreview = document.getElementById('portrait-photo-preview');
    const noPhotoPreview = document.getElementById('no-photo-preview');
    const photoStatus = document.getElementById('photo-status');
    const btnClearPhoto = document.getElementById('btn-clear-photo');

    const barcodeInput = document.getElementById('barcode-input');
    const btnCheckBarcode = document.getElementById('btn-check-barcode');
    const formTitle = document.getElementById('form-title');
    const formBadge = document.getElementById('form-badge');
    const formHeader = document.getElementById('form-header');

    // State Views
    const stateIdle = document.getElementById('state-idle');
    const stateCheckin = document.getElementById('state-checkin');
    const stateCheckout = document.getElementById('state-checkout');

    // Checkout Details elements
    const checkoutVisitorPhoto = document.getElementById('checkout-visitor-photo');
    const checkoutVisitorPortrait = document.getElementById('checkout-visitor-portrait');
    const checkoutVisitorName = document.getElementById('checkout-visitor-name');
    const checkoutVisitorCccd = document.getElementById('checkout-visitor-cccd');
    const checkoutVisitorPhone = document.getElementById('checkout-visitor-phone');
    const checkoutVisitorCompany = document.getElementById('checkout-visitor-company');
    const checkoutVisitorMeet = document.getElementById('checkout-visitor-meet');
    const checkoutVisitorVehicle = document.getElementById('checkout-visitor-vehicle');
    const checkoutVisitorCheckinTime = document.getElementById('checkout-visitor-checkin-time');
    const btnConfirmCheckout = document.getElementById('btn-confirm-checkout');

    // -------------------------------------------------------------
    // Initial Setup
    // -------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize camera
        initCamera();
        initWebcam();

        // Barcode scanning / Checking
        barcodeInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkBarcode();
            }
        });
        btnCheckBarcode.addEventListener('click', checkBarcode);

        // Capturing photos
        btnCapture.addEventListener('click', capturePhoto);
        btnClearPhoto.addEventListener('click', clearPhoto);

        // Handle Check-in form submit
        stateCheckin.addEventListener('submit', handleCheckinSubmit);

        // Handle Check-out button click
        btnConfirmCheckout.addEventListener('click', handleCheckoutSubmit);

        // PiP Draggable Logic
        const pip = document.getElementById('pip-container');
        const pipParent = pip.parentElement;
        const pipResizer = document.getElementById('pip-resizer');
        
        let isDragging = false;
        let isResizing = false;
        let startX, startY, initialTop, initialLeft, initialWidth;

        pipResizer.addEventListener('mousedown', (e) => {
            isResizing = true;
            startX = e.clientX;
            initialWidth = pip.getBoundingClientRect().width;
            pip.style.transition = 'none';
            e.stopPropagation(); // Do not trigger drag
            e.preventDefault();
        });

        pip.addEventListener('mousedown', (e) => {
            if (isResizing) return;
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            
            const rect = pip.getBoundingClientRect();
            const parentRect = pipParent.getBoundingClientRect();
            
            initialLeft = rect.left - parentRect.left;
            initialTop = rect.top - parentRect.top;
            
            pip.style.bottom = 'auto';
            pip.style.right = 'auto';
            pip.style.left = `${initialLeft}px`;
            pip.style.top = `${initialTop}px`;
            pip.style.transition = 'none';
            e.preventDefault();
        });

        document.addEventListener('mousemove', (e) => {
            if (isResizing) {
                const dx = e.clientX - startX;
                let newWidth = initialWidth + dx;
                
                // Constraints
                const maxW = pipParent.getBoundingClientRect().width * 0.8;
                if (newWidth < 120) newWidth = 120;
                if (newWidth > maxW) newWidth = maxW;
                
                pip.style.width = `${newWidth}px`;
                return;
            }

            if (!isDragging) return;
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            pip.style.left = `${initialLeft + dx}px`;
            pip.style.top = `${initialTop + dy}px`;
        });

        document.addEventListener('mouseup', () => {
            if (isResizing) {
                isResizing = false;
                pip.style.transition = 'all 0.3s ease';
                return;
            }

            if (!isDragging) return;
            isDragging = false;
            pip.style.transition = 'all 0.3s ease';
            
            const parentRect = pipParent.getBoundingClientRect();
            const pipRect = pip.getBoundingClientRect();
            
            const pipCenterX = pipRect.left - parentRect.left + pipRect.width / 2;
            const pipCenterY = pipRect.top - parentRect.top + pipRect.height / 2;
            
            const isLeft = pipCenterX < parentRect.width / 2;
            const isTop = pipCenterY < parentRect.height / 2;
            
            const padding = 16; // tailwind bottom-4 = 1rem = 16px
            
            pip.style.left = isLeft ? `${padding}px` : 'auto';
            pip.style.right = isLeft ? 'auto' : `${padding}px`;
            pip.style.top = isTop ? `${padding}px` : 'auto';
            pip.style.bottom = isTop ? 'auto' : `${padding}px`;
        });

    });

    // -------------------------------------------------------------
    // Camera Streaming & Logic
    // -------------------------------------------------------------
    function initCamera() {
        stopCamera();
        startIpCamera();
    }

    function initWebcam() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { aspectRatio: 16/9 } })
                .then(function(stream) {
                    webcamFeed.srcObject = stream;
                })
                .catch(function(error) {
                    console.error("Lỗi truy cập Webcam USB: ", error);
                    showToast('error', "Không thể truy cập Webcam USB. Vui lòng cấp quyền.");
                });
        } else {
            showToast('error', "Trình duyệt không hỗ trợ getUserMedia.");
        }
    }

    function showCameraStatus(status, text) {
        cameraStatusText.textContent = text;
        cameraStatusDot.className = 'w-3 h-3 rounded-full';
        
        if (status === 'loading') {
            cameraStatusDot.classList.add('bg-amber-500', 'pulse-glow');
            cameraLoading.style.opacity = '1';
            cameraLoading.classList.remove('pointer-events-none');
        } else if (status === 'streaming') {
            cameraStatusDot.classList.add('bg-emerald-500');
            cameraLoading.style.opacity = '0';
            cameraLoading.classList.add('pointer-events-none');
        } else if (status === 'error') {
            cameraStatusDot.classList.add('bg-rose-500');
            cameraLoading.style.opacity = '1';
            cameraLoading.classList.remove('pointer-events-none');
            // Show custom icon inside loading screen to tell error
            cameraLoading.innerHTML = `
                <div class="text-rose-500 text-3xl mb-2"><i class="fas fa-exclamation-triangle"></i></div>
                <span class="text-slate-400 text-sm font-medium text-center px-4">${text}</span>
            `;
        }
    }

    function loadWebRTCStreamerScripts(url) {
        return new Promise((resolve, reject) => {
            if (window.WebRtcStreamer) {
                resolve();
                return;
            }
            
            // Re-render loader inside camera Loading screen to show progress
            cameraLoading.innerHTML = `
                <svg class="animate-spin h-10 w-10 text-blue-500 mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-slate-400 text-xs font-mono">Loading WebRTC dependencies...</span>
            `;

            const adapterScript = document.createElement('script');
            adapterScript.src = `${url}/libs/adapter.min.js`;
            
            adapterScript.onload = () => {
                const streamerScript = document.createElement('script');
                streamerScript.src = `${url}/webrtcstreamer.js`;
                
                streamerScript.onload = () => {
                    scriptsLoaded = true;
                    resolve();
                };
                streamerScript.onerror = () => reject(new Error(__('Không thể tải webrtcstreamer.js từ ') + url));
                document.head.appendChild(streamerScript);
            };
            adapterScript.onerror = () => reject(new Error(__('Không thể tải adapter.min.js từ ') + url + '/libs'));
            document.head.appendChild(adapterScript);
        });
    }

    function startIpCamera() {
        showCameraStatus('loading', __('Đang kết nối Camera IP...'));
        
        if (!cameraRtspUrl) {
            showCameraStatus('error', __('Chưa cấu hình địa chỉ RTSP cho camera.'));
            return;
        }

        loadWebRTCStreamerScripts(webrtcStreamerUrl)
            .then(() => {
                rtcStreamer = new WebRtcStreamer("camera-feed", webrtcStreamerUrl);
                rtcStreamer.connect(cameraRtspUrl);
                showCameraStatus('streaming', __('Camera IP RTSP - Live'));
            })
            .catch(err => {
                console.error(err);
                showCameraStatus('error', __('Lỗi kết nối WebRTC-Streamer. Hãy chắc chắn máy chủ WebRTC-Streamer đang hoạt động.'));
                showToast('error', __(err.message));
            });
    }

    function stopCamera() {
        if (rtcStreamer) {
            try {
                rtcStreamer.disconnect();
            } catch(e) {
                console.error('Error disconnecting WebRTC streamer:', e);
            }
            rtcStreamer = null;
        }
        videoFeed.srcObject = null;
    }

    // -------------------------------------------------------------
    // Photo Capture (Canvas)
    // -------------------------------------------------------------
    function capturePhotoBlob(videoEl, canvasId) {
        return new Promise((resolve) => {
            if (!videoEl.videoWidth) {
                resolve(null);
                return;
            }

            const canvas = document.getElementById(canvasId);
            canvas.width = videoEl.videoWidth;
            canvas.height = videoEl.videoHeight;
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

            canvas.toBlob((blob) => {
                resolve(blob);
            }, 'image/jpeg', 0.9);
        });
    }

    async function capturePhoto() {
        const ipBlob = await capturePhotoBlob(videoFeed, 'capture-canvas');
        const webcamBlob = await capturePhotoBlob(webcamFeed, 'capture-webcam-canvas');

        if (ipBlob || webcamBlob) {
            if (ipBlob) {
                capturedBlob = ipBlob;
                photoPreview.src = URL.createObjectURL(ipBlob);
                photoPreview.classList.remove('hidden');
            }
            if (webcamBlob) {
                capturedPortraitBlob = webcamBlob;
                portraitPhotoPreview.src = URL.createObjectURL(webcamBlob);
                portraitPhotoPreview.classList.remove('hidden');
            }
            
            noPhotoPreview.classList.add('hidden');
            
            photoStatus.textContent = __('Đã sẵn sàng');
            photoStatus.className = 'block text-[10px] text-emerald-500 font-bold';
            btnClearPhoto.classList.remove('hidden');

            showToast('success', __('Đã chụp ảnh check-in!'));
        } else {
            showToast('error', __('Camera chưa khởi tạo thành công hoặc luồng video bị lỗi.'));
        }
    }

    function clearPhoto() {
        capturedBlob = null;
        capturedPortraitBlob = null;
        photoPreview.src = '';
        photoPreview.classList.add('hidden');
        portraitPhotoPreview.src = '';
        portraitPhotoPreview.classList.add('hidden');
        noPhotoPreview.classList.remove('hidden');
        photoStatus.textContent = __('Chưa có ảnh');
        photoStatus.className = 'block text-[10px] text-red-400 font-medium';
        btnClearPhoto.classList.add('hidden');
    }

    // -------------------------------------------------------------
    // Barcode Checker (Logic & API call)
    // -------------------------------------------------------------
    function checkBarcode() {
        const barcode = barcodeInput.value.trim().toUpperCase();
        if (!barcode) {
            showToast('warning', __('Vui lòng nhập mã số thẻ.'));
            barcodeInput.focus();
            return;
        }

        const barcodePattern = /^(BV|LN|BD|PL)\d{3}$/;
        if (!barcodePattern.test(barcode)) {
            showToast('error', __('Mã thẻ không hợp lệ. Vui lòng nhập đúng định dạng (VD: BV001)!'));
            barcodeInput.focus();
            return;
        }

        currentBarcode = barcode;
        
        // Display intermediate state
        formTitle.textContent = __('Đang kiểm tra...');
        formBadge.textContent = __('CHECKING');
        formBadge.className = 'px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
        
        const headers = {};
        if (apiKey) {
            headers['X-API-KEY'] = apiKey;
        }

        fetch(`/api/session/${encodeURIComponent(barcode)}`, {
            method: 'GET',
            headers: headers
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || err.message || __('Lỗi truy vấn thông tin thẻ'));
                }).catch(() => {
                    throw new Error(__('Lỗi truy vấn thông tin thẻ'));
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.active) {
                // Card is checked in, set to Checkout state
                currentSession = data.session;
                setFormState('checkout');
            } else {
                // Card is empty, set to Check-in state
                currentSession = null;
                setFormState('checkin');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('error', __('Lỗi kiểm tra thẻ: ') + __(err.message));
            resetToIdle();
        });
    }

    // -------------------------------------------------------------
    // Form State Transition Manager
    // -------------------------------------------------------------
    function setFormState(state) {
        appState = state;
        
        // Hide all views
        stateIdle.classList.add('hidden');
        stateCheckin.classList.add('hidden');
        stateCheckout.classList.add('hidden');
        
        if (state === 'idle') {
            formTitle.textContent = __('Trạng thái chờ');
            formBadge.textContent = __('IDLE');
            formBadge.className = 'px-2 py-0.5 rounded-full text-xs font-bold bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-350';
            formHeader.className = 'px-6 py-4 border-b border-slate-100 bg-slate-50 dark:border-slate-850 dark:bg-slate-900/40 flex items-center justify-between transition-colors duration-205';
            stateIdle.classList.remove('hidden');
        } 
        else if (state === 'checkin') {
            formTitle.textContent = `Check-in: ${__('Thẻ')} ${currentBarcode}`;
            formBadge.textContent = __('THẺ TRỐNG');
            formBadge.className = 'px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
            formHeader.className = 'px-6 py-4 border-b border-blue-100 bg-blue-50/50 dark:border-blue-900/20 dark:bg-blue-950/20 flex items-center justify-between transition-colors duration-205';
            
            // Clear check-in inputs
            stateCheckin.reset();
            stateCheckin.classList.remove('hidden');
            
            // Focus on first input
            setTimeout(() => {
                const firstInput = stateCheckin.querySelector('input[name="name"]');
                if (firstInput) firstInput.focus();
            }, 100);
        } 
        else if (state === 'checkout') {
            formTitle.textContent = `Check-out: ${__('Thẻ')} ${currentBarcode}`;
            formBadge.textContent = __('ĐANG ACTIVE');
            formBadge.className = 'px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-450';
            formHeader.className = 'px-6 py-4 border-b border-emerald-100 bg-emerald-50/50 dark:border-emerald-900/20 dark:bg-emerald-950/20 flex items-center justify-between transition-colors duration-205';
            
            // Bind session details
            checkoutVisitorName.textContent = currentSession.name || 'N/A';
            checkoutVisitorCccd.textContent = currentSession.cccd || __('Không có');
            checkoutVisitorPhone.textContent = currentSession.phone || __('Không có');
            checkoutVisitorCompany.textContent = currentSession.company || __('Không có');
            checkoutVisitorMeet.textContent = currentSession.meet_person || __('Không rõ');
            checkoutVisitorVehicle.textContent = currentSession.vehicle || __('Không có');
            
            // Format checkin time
            if (currentSession.checkin_time) {
                const d = new Date(currentSession.checkin_time);
                checkoutVisitorCheckinTime.textContent = d.toLocaleString(currentLocale === 'en' ? 'en-US' : 'vi-VN');
            } else {
                checkoutVisitorCheckinTime.textContent = 'N/A';
            }

            // Set Checkin Photo preview
            if (currentSession.photo) {
                checkoutVisitorPhoto.src = `/storage/${currentSession.photo}`;
                checkoutVisitorPhoto.classList.remove('hidden');
            } else {
                checkoutVisitorPhoto.src = '';
                checkoutVisitorPhoto.classList.add('hidden');
            }
            if (currentSession.portrait_photo) {
                checkoutVisitorPortrait.src = `/storage/${currentSession.portrait_photo}`;
                checkoutVisitorPortrait.classList.remove('hidden');
            } else {
                checkoutVisitorPortrait.src = '';
                checkoutVisitorPortrait.classList.add('hidden');
            }

            stateCheckout.classList.remove('hidden');
        }
    }

    function resetToIdle() {
        currentBarcode = '';
        currentSession = null;
        barcodeInput.value = '';
        clearPhoto();
        setFormState('idle');
        barcodeInput.focus();
    }

    // -------------------------------------------------------------
    // API Check-in / Check-out Submit Handling
    // -------------------------------------------------------------
    function handleCheckinSubmit(e) {
        e.preventDefault();
        
        // Photo is highly recommended, let's warn if missing
        if (!capturedBlob) {
            showToast('warning', __('Vui lòng chụp ảnh khách hàng trước khi check-in.'));
            return;
        }

        const formData = new FormData(stateCheckin);
        formData.append('action', 'checkin');
        formData.append('barcode', currentBarcode);
        
        // Attach photo file
        if (capturedBlob) {
            formData.append('photo', capturedBlob, 'photo.jpg');
        }
        if (capturedPortraitBlob) {
            formData.append('portrait_photo', capturedPortraitBlob, 'portrait_photo.jpg');
        }

        const headers = {};
        if (apiKey) {
            headers['X-API-KEY'] = apiKey;
        }

        showLoadingBtn(stateCheckin.querySelector('button[type="submit"]'), __('Đang xử lý...'));

        fetch('/api/check', {
            method: 'POST',
            headers: headers,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingBtn(stateCheckin.querySelector('button[type="submit"]'), __('LƯU VÀ CHO KHÁCH VÀO'));
            if (data.status === 'checkin') {
                showToast('success', __('Khách hàng Check-in thành công!'));
                resetToIdle();
            } else {
                showToast('error', __(data.message || 'Lỗi xảy ra trong quá trình check-in.'));
            }
        })
        .catch(err => {
            hideLoadingBtn(stateCheckin.querySelector('button[type="submit"]'), __('LƯU VÀ CHO KHÁCH VÀO'));
            console.error(err);
            showToast('error', __('Lỗi mạng hoặc server: ') + err.message);
        });
    }

    async function handleCheckoutSubmit() {
        showLoadingBtn(btnConfirmCheckout, __('Đang xác nhận ra...'));

        const formData = new FormData();
        formData.append('action', 'checkout');
        formData.append('barcode', currentBarcode);

        // Auto-capture checkout photo right on checkout button click
        try {
            const checkoutIpBlob = await capturePhotoBlob(videoFeed, 'capture-canvas');
            const checkoutWebcamBlob = await capturePhotoBlob(webcamFeed, 'capture-webcam-canvas');
            if (checkoutIpBlob) {
                formData.append('photo_checkout', checkoutIpBlob, 'photo_checkout.jpg');
            }
            if (checkoutWebcamBlob) {
                formData.append('portrait_photo_checkout', checkoutWebcamBlob, 'portrait_photo_checkout.jpg');
            }
        } catch (e) {
            console.error('Error auto-capturing checkout photo:', e);
        }

        const headers = {};
        if (apiKey) {
            headers['X-API-KEY'] = apiKey;
        }

        fetch('/api/check', {
            method: 'POST',
            headers: headers,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingBtn(btnConfirmCheckout, __('XÁC NHẬN CHO KHÁCH RA (CHECK-OUT)'));
            if (data.status === 'checkout') {
                showToast('success', __('Khách hàng Check-out thành công!'));
                resetToIdle();
            } else {
                showToast('error', __(data.message || 'Lỗi xảy ra trong quá trình check-out.'));
            }
        })
        .catch(err => {
            hideLoadingBtn(btnConfirmCheckout, __('XÁC NHẬN CHO KHÁCH RA (CHECK-OUT)'));
            console.error(err);
            showToast('error', __('Lỗi mạng hoặc server: ') + err.message);
        });
    }

    // Helper to style loading buttons
    function showLoadingBtn(btn, text) {
        btn.disabled = true;
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>${text}</span>
        `;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    }

    function hideLoadingBtn(btn, text) {
        btn.disabled = false;
        btn.innerHTML = btn.dataset.originalText || text;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
    }

    // -------------------------------------------------------------
    // Toast Notification Manager
    // -------------------------------------------------------------
    function showToast(type, message) {
        const toast = document.getElementById('toast');
        const iconContainer = document.getElementById('toast-icon-container');
        const icon = document.getElementById('toast-icon');
        const title = document.getElementById('toast-title');
        const msg = document.getElementById('toast-message');

        // Reset styling
        iconContainer.className = 'w-8 h-8 rounded-full flex items-center justify-center shrink-0';
        icon.className = 'fas text-base';

        if (type === 'success') {
            iconContainer.classList.add('bg-emerald-100', 'text-emerald-600');
            icon.classList.add('fa-check-circle');
            title.textContent = __('Thành công');
        } else if (type === 'error') {
            iconContainer.classList.add('bg-rose-100', 'text-rose-600');
            icon.classList.add('fa-exclamation-circle');
            title.textContent = __('Lỗi');
        } else if (type === 'warning') {
            iconContainer.classList.add('bg-amber-100', 'text-amber-600');
            icon.classList.add('fa-exclamation-triangle');
            title.textContent = __('Cảnh báo');
        } else {
            iconContainer.classList.add('bg-blue-100', 'text-blue-600');
            icon.classList.add('fa-info-circle');
            title.textContent = __('Thông báo');
        }

        msg.textContent = message;

        // Show toast with slide-in animation
        toast.classList.add('show');
        toast.style.opacity = '1';

        // Clear existing timeout
        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }

        // Auto hide after 4 seconds
        toastTimeout = setTimeout(() => {
            hideToast();
        }, 4000);
    }

    function hideToast() {
        const toast = document.getElementById('toast');
        toast.classList.remove('show');
        toast.style.opacity = '0';
    }
</script>
@endsection
