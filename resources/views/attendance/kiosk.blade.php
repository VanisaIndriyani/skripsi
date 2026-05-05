<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Absensi Kiosk - PT Putra Muara Sukses</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #D4AF37;
            --primary-light: #f1d27a;
            --dark-bg: #0f0f0f;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        
        body {
            box-sizing: border-box; /* Add this line */
            background: radial-gradient(circle at top right, #1a1a1a, #0a0a0a);
            font-family: 'Outfit', sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        /* Background Decorations */
        .bg-glow {
            position: fixed;
            width: 300px;
            height: 300px;
            background: var(--primary-color);
            filter: blur(150px);
            opacity: 0.1;
            z-index: -1;
            border-radius: 50%;
        }
        
        .bg-glow-1 { top: -100px; right: -100px; }
        .bg-glow-2 { bottom: -100px; left: -100px; }

        /* Landing Page Styles */
        .landing-container {
            text-align: center;
            width: 100%;
            max-width: 600px;
            z-index: 1;
        }
        
        .logo-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 30px;
        }
        
        .logo-landing {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            padding: 5px;
            background: var(--dark-bg);
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.3);
            object-fit: cover;
        }
        
        .company-name {
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 5px;
            background: linear-gradient(to right, #fff, var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .clock-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 30px;
            border-radius: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .clock-widget {
            font-size: 5rem;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            margin-bottom: 10px;
        }
        
        .date-widget {
            font-size: 1.1rem;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        
        .session-info {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.2);
            padding: 15px 25px;
            border-radius: 100px;
            display: inline-flex;
            align-items: center;
            margin-bottom: 40px;
        }
        
        .start-btn {
            width: 250px;
            height: 70px;
            background: var(--primary-color);
            color: #000;
            border: none;
            border-radius: 100px;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
            margin: 0 auto;
        }
        
        .start-btn:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4);
            background: var(--primary-light);
        }

        /* Scanning Overlay */
        .scan-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transform: scale(0.985);
            transition: opacity 220ms ease, transform 220ms ease;
        }

        .scan-overlay.is-open {
            opacity: 1;
            transform: scale(1);
        }
        
        .scan-container {
            width: 100%;
            max-width: 350px; /* Adjusted from 480px to 350px */
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            aspect-ratio: 3/4;
            border-radius: 40px;
            overflow: hidden;
            border: 4px solid var(--primary-color);
            background: #111;
            box-shadow: 0 0 50px rgba(212, 175, 55, 0.2);
            transition: box-shadow 220ms ease, border-color 220ms ease, transform 220ms ease;
        }

        .video-container.is-detecting {
            border-color: rgba(212, 175, 55, 0.9);
            box-shadow: 0 0 55px rgba(212, 175, 55, 0.35);
        }

        .video-container.is-locked {
            border-color: rgba(212, 175, 55, 1);
            box-shadow: 0 0 70px rgba(212, 175, 55, 0.55);
        }

        .video-container.is-verified {
            border-color: rgba(34, 197, 94, 0.95);
            box-shadow: 0 0 80px rgba(34, 197, 94, 0.5);
            transform: scale(1.01);
        }

        .face-guide {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .face-guide::before {
            content: "";
            width: 72%;
            height: 62%;
            border-radius: 999px;
            border: 2px dashed rgba(212, 175, 55, 0.55);
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.28) inset;
            backdrop-filter: blur(0px);
        }

        .video-container.is-verified .face-guide::before {
            border-color: rgba(34, 197, 94, 0.65);
        }

        .success-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at center, rgba(34, 197, 94, 0.22), rgba(0, 0, 0, 0.1));
            opacity: 0;
            transform: scale(0.98);
            pointer-events: none;
            z-index: 6;
            transition: opacity 220ms ease, transform 220ms ease;
        }

        .success-overlay.is-visible {
            opacity: 1;
            transform: scale(1);
        }

        .success-check {
            width: 92px;
            height: 92px;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.18);
            border: 2px solid rgba(34, 197, 94, 0.7);
            box-shadow: 0 0 35px rgba(34, 197, 94, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-check i {
            font-size: 2.4rem;
            color: rgba(34, 197, 94, 0.95);
        }
        
        video, #captured_image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }
        
        .scan-frame {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='400' height='500' viewBox='0 0 400 500' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 20H20V40' stroke='%23D4AF37' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M360 20H380V40' stroke='%23D4AF37' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M40 480H20V460' stroke='%23D4AF37' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M360 480H380V460' stroke='%23D4AF37' stroke-width='4' stroke-linecap='round'/%3E%3C/svg%3E") center/contain no-repeat;
            pointer-events: none;
            opacity: 0.5;
        }
        
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, transparent, var(--primary-color), transparent);
            box-shadow: 0 0 15px var(--primary-color);
            animation: scanMove 3s infinite ease-in-out;
            z-index: 2;
        }
        
        @keyframes scanMove {
            0% { top: 10%; }
            50% { top: 90%; }
            100% { top: 10%; }
        }
        
        .scan-info-card {
            background: rgba(15, 15, 15, 0.8); /* Darker and more opaque */
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5); /* Stronger shadow */
        }

        .status-badge #status-text {
            font-size: 1rem;
            letter-spacing: 0.2px;
        }

        .liveness-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 12px 12px;
        }

        .liveness-meta {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 8px;
        }

        .liveness-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.78);
        }

        .liveness-value {
            font-weight: 700;
            font-size: 0.85rem;
            color: rgba(212, 175, 55, 0.95);
        }

        .progress.liveness-progress {
            height: 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.07);
            overflow: hidden;
        }

        .progress-bar.liveness-bar {
            border-radius: 999px;
            transition: width 180ms ease;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .close-btn {
            position: absolute;
            top: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10000;
        }

        .btn-gold {
            background-color: var(--primary-color);
            color: #000;
            border: none;
            font-weight: 700;
            font-family: 'Outfit', sans-serif; /* Add this line */
            transition: all 0.3s;
        }
        
        .btn-gold:hover {
            background-color: var(--primary-light);
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }
        
        .btn-gold:disabled {
            background-color: #333;
            color: #666;
            opacity: 0.7;
        }

        /* Mobile Adjustments */
        @media (max-width: 576px) {
            .clock-widget { font-size: 4rem; }
            .scan-container {
                padding: 10px;
                margin: auto;
                min-height: 100vh;
                justify-content: center;
            }
            .video-container { border-radius: 30px; }
            .scan-info-card { padding: 15px; } /* Adjusted padding for smaller screens */
            .scan-overlay {
                overflow-y: auto; /* Allow scrolling on small screens */
                align-items: flex-start; /* Align to top instead of center */
                padding: 40px 15px; /* More vertical padding */
            }
            #detected_name,
            .liveness-card {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <!-- Landing Page -->
    <div class="landing-container" id="landingPage">
        <div class="logo-wrapper">
            <img src="{{ asset('img/logo.jpeg') }}" alt="Logo PMS" class="logo-landing">
        </div>
        
        <h2 class="company-name">PT PUTRA MUARA SUKSES</h2>
        <p class="text-secondary mb-4">Sistem Absensi Kiosk Pintar</p>

        <div class="clock-container">
            <div class="clock-widget" id="clock">00:00</div>
            <div class="date-widget" id="date">MEMUAT TANGGAL...</div>
        </div>

        @if($activeSession)
            <div class="session-info">
                <i class="fas fa-circle-dot text-success me-3 pulse-animation"></i>
                <div class="text-start">
                    <span class="d-block text-gold small fw-bold text-uppercase">Sesi Berjalan</span>
                    <span class="fw-bold">{{ $activeSession->title }}</span>
                    <span class="mx-2 opacity-25">|</span>
                    <span class="small opacity-75">{{ \Carbon\Carbon::parse($activeSession->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($activeSession->end_time)->format('H:i') }} WIB</span>
                </div>
            </div>
            
            <button class="start-btn" onclick="openScanner()">
                <i class="fas fa-camera-retro fa-lg"></i>
                MULAI ABSENSI
            </button>
        @else
            <div class="alert alert-dark bg-dark border-secondary rounded-pill px-4 mb-5">
                <i class="fas fa-lock me-2 text-gold"></i> Belum ada sesi aktif saat ini
            </div>
        @endif

        <div class="mt-5">
            <a href="{{ route('login') }}" class="text-secondary small text-decoration-none opacity-50 hover-opacity-100">
                <i class="fas fa-shield-halved me-1"></i> Admin Login
            </a>
        </div>
    </div>

    <!-- Scanning Overlay -->
    <div class="scan-overlay" id="scanInterface">
        <div class="close-btn" onclick="closeScanner()">
            <i class="fas fa-times"></i>
        </div>

        <div class="scan-container">
            <div class="video-container" id="videoContainer">
                <div class="scan-line" id="scanLine"></div>
                <div class="scan-frame"></div>
                <div class="face-guide"></div>
                <video id="video" autoplay muted playsinline></video>
                <img id="captured_image" src="" alt="Captured Photo">
                <div class="success-overlay" id="successOverlay">
                    <div class="success-check">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>

            <div class="scan-info-card">
                <div id="status-badge" class="status-badge bg-warning text-dark">
                    <i class="fas fa-camera"></i>
                    <span id="status-text">Memulai Kamera...</span>
                    <span class="spinner-border spinner-border-sm ms-2 d-none" id="statusSpinner" role="status" aria-hidden="true"></span>
                </div>
                
                <input type="text" id="detected_name" class="form-control bg-transparent border-0 text-white text-center fs-4 fw-bold mb-3" readonly placeholder="...">
                
                <div class="liveness-card mb-3">
                    <div class="liveness-meta">
                        <div class="liveness-title">Liveness</div>
                        <div class="liveness-value" id="livenessValue">0%</div>
                    </div>
                    <div class="progress liveness-progress">
                        <div class="progress-bar liveness-bar bg-warning" id="livenessBar" style="width: 0%"></div>
                    </div>
                </div>

                <form id="attendanceForm" action="{{ route('attendance.storePublic') }}" method="POST">
                    @csrf
                    <input type="hidden" name="location" id="location">
                    <input type="hidden" name="photo" id="photo">
                    <input type="hidden" name="user_id" id="user_id">

                    <div class="d-grid gap-2">
                        <button type="button" id="captureBtn" onClick="captureAndDetect()" class="btn btn-gold rounded-pill py-3 w-100">
                            <i class="fas fa-check me-2"></i> ABSEN SEKARANG
                        </button>
                        <div id="actionButtons" class="d-none d-flex gap-2">
                            <button type="button" onClick="resetCamera()" class="btn btn-outline-light w-50 rounded-pill py-3">ULANG</button>
                            <button type="button" id="submitBtn" onClick="submitAttendance()" class="btn btn-gold w-50 rounded-pill py-3" disabled>KONFIRMASI</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- Clock Logic ---
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('clock').innerText = `${hours}:${minutes}`;
            
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('date').innerText = now.toLocaleDateString('id-ID', options).toUpperCase();
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    
    @if($activeSession)
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const video = document.getElementById('video');
        const capturedImage = document.getElementById('captured_image');
        const statusText = document.getElementById('status-text');
        const statusBadge = document.getElementById('status-badge');
        const detectedNameInput = document.getElementById('detected_name');
        const userIdInput = document.getElementById('user_id');
        const submitBtn = document.getElementById('submitBtn');
        const captureBtn = document.getElementById('captureBtn');
        const actionButtons = document.getElementById('actionButtons');
        const scanLine = document.getElementById('scanLine');
        const videoContainer = document.getElementById('videoContainer');
        const statusSpinner = document.getElementById('statusSpinner');
        const livenessBar = document.getElementById('livenessBar');
        const livenessValue = document.getElementById('livenessValue');
        const successOverlay = document.getElementById('successOverlay');
        const scanInterface = document.getElementById('scanInterface');

        const attendedUserIds = @json($attendedUserIds ?? []);

        const MODEL_URL = "{{ asset('models') }}";

        const DETECTION_INTERVAL_MS = 90;
        const VIDEO_INPUT_SIZE = 96;
        const VIDEO_SCORE_THRESHOLD = 0.55;
        const PHOTO_INPUT_SIZE = 160;

        const MATCH_THRESHOLD = 0.48;
        const MAX_ACCEPT_DISTANCE = 0.44;
        const STABLE_FRAMES_REQUIRED = 2;

        const MIN_DETECTION_SCORE = 0.6;
        const LIVENESS_TIMEOUT_MS = 10000;
        const MIN_LIVENESS_DURATION_MS = 1500;
        const REQUIRED_BLINKS = 2;
        const BLINK_LOW_THRESHOLD = 0.20;
        const BLINK_HIGH_THRESHOLD = 0.24;
        const YAW_TURN_THRESHOLD = 0.18;
        const HEAD_STABLE_FRAMES_REQUIRED = 2;
        const FACE_MISSING_RESET_FRAMES = 8;
        const UNKNOWN_RESET_FRAMES = 8;
        const INSTRUCTION_HOLD_MS = 900;

        const videoDetectorOptions = new faceapi.TinyFaceDetectorOptions({
            inputSize: VIDEO_INPUT_SIZE,
            scoreThreshold: VIDEO_SCORE_THRESHOLD
        });

        const photoDetectorOptions = new faceapi.TinyFaceDetectorOptions({
            inputSize: PHOTO_INPUT_SIZE,
            scoreThreshold: VIDEO_SCORE_THRESHOLD
        });

        let stream = null;

        let isFaceSystemReady = false;
        let isProcessing = false;
        let isDetecting = false;

        let labeledFaceDescriptors = [];
        let faceMatcher = null;
        let modelsPromise = null;
        let matcherPromise = null;

        let rafHandle = null;
        let lastDetectionAt = 0;

        let isLivenessVerified = false;
        let isAlreadyAttended = false;
        let candidateEmployee = null;
        let currentMatchLabel = null;
        let stableMatchFrames = 0;
        let livenessStartedAt = null;
        let livenessChallengeStartedAt = null;
        let blinkCount = 0;
        let blinkState = 'open';
        let requiredBlinks = REQUIRED_BLINKS;
        let livenessSequence = [];
        let livenessStepIndex = 0;
        let headStableFrames = 0;
        let missingFaceFrames = 0;
        let unknownFaceFrames = 0;
        let lastShownName = "";

        setCaptureReady(false);
        setLivenessProgress(0, false);

        const OFFICE_LAT = {{ \App\Models\Setting::get('office_latitude', 0) }};
        const OFFICE_LNG = {{ \App\Models\Setting::get('office_longitude', 0) }};
        const MAX_RADIUS = {{ \App\Models\Setting::get('office_radius', 100) }};

        function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
            var R = 6371;
            var dLat = deg2rad(lat2-lat1);
            var dLon = deg2rad(lon2-lon1); 
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            return R * c;
        }

        function deg2rad(deg) { return deg * (Math.PI/180) }

        const employees = [
            @foreach($employees as $employee)
                @if($employee->photo)
                { id: "{{ $employee->id }}", name: "{{ $employee->name }}", photo: "{{ asset('storage/' . $employee->photo) }}" },
                @endif
            @endforeach
        ];

        if (typeof faceapi !== 'undefined') {
            ensureModelsLoaded();
            ensureMatcherReady();
        }

        function hashString(input) {
            let hash = 5381;
            for (let i = 0; i < input.length; i++) {
                hash = ((hash << 5) + hash) ^ input.charCodeAt(i);
            }
            return (hash >>> 0).toString(16);
        }

        function getDescriptorsCacheKey() {
            return 'pms_face_descriptors_v2_' + hashString(JSON.stringify(employees));
        }

        function ensureModelsLoaded() {
            if (modelsPromise) return modelsPromise;
            modelsPromise = Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]).then(() => {
                isFaceSystemReady = true;
            }).catch(err => {
                console.error(err);
                updateStatus("Gagal memuat model", "danger", 0, true);
            });
            return modelsPromise;
        }

        function ensureMatcherReady() {
            if (matcherPromise) return matcherPromise;
            matcherPromise = ensureModelsLoaded().then(async () => {
                labeledFaceDescriptors = await loadLabeledDescriptorsFromCacheOrCompute();
                if (labeledFaceDescriptors.length > 0) {
                    faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, MATCH_THRESHOLD);
                }
            });
            return matcherPromise;
        }

        async function loadLabeledDescriptorsFromCacheOrCompute() {
            const cacheKey = getDescriptorsCacheKey();
            try {
                const cached = localStorage.getItem(cacheKey);
                if (cached) {
                    const parsed = JSON.parse(cached);
                    return parsed.map(item => new faceapi.LabeledFaceDescriptors(
                        item.label,
                        item.descriptors.map(d => new Float32Array(d))
                    ));
                }
            } catch (e) {}

            const results = [];
            for (let i = 0; i < employees.length; i++) {
                const employee = employees[i];
                updateStatus(`Menyiapkan data karyawan ${i + 1}/${employees.length}...`, "info", 0, true);
                const descriptions = [];
                try {
                    const img = await faceapi.fetchImage(employee.photo);
                    const canvas = document.createElement('canvas');
                    const maxSide = 240;
                    const ratio = img.width && img.height ? Math.min(1, maxSide / Math.max(img.width, img.height)) : 1;
                    canvas.width = Math.max(1, Math.round((img.width || maxSide) * ratio));
                    canvas.height = Math.max(1, Math.round((img.height || maxSide) * ratio));
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    const detections = await faceapi
                        .detectSingleFace(canvas, photoDetectorOptions)
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (detections && detections.descriptor) {
                        descriptions.push(detections.descriptor);
                        results.push(new faceapi.LabeledFaceDescriptors(employee.name, descriptions));
                    }
                } catch (e) {
                    console.error(e);
                }
            }

            try {
                const toCache = results.map(ld => ({
                    label: ld.label,
                    descriptors: ld.descriptors.map(d => Array.from(d))
                }));
                localStorage.setItem(cacheKey, JSON.stringify(toCache));
            } catch (e) {}

            return results;
        }

        function openScanner() {
            Swal.fire({
                title: 'Validasi Lokasi',
                text: 'Sedang mengecek posisi GPS Anda...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    document.getElementById('location').value = lat + "," + lng;

                    const distance = getDistanceFromLatLonInKm(lat, lng, OFFICE_LAT, OFFICE_LNG) * 1000;
                    
                    if (distance > MAX_RADIUS) {
                        Swal.fire({ icon: 'error', title: 'Diluar Jangkauan', text: `Jarak Anda ${Math.round(distance)}m. Maksimum radius ${MAX_RADIUS}m.` });
                    } else {
                        Swal.close();
                        document.getElementById('landingPage').classList.add('d-none');
                        scanInterface.style.display = 'flex';
                        requestAnimationFrame(() => scanInterface.classList.add('is-open'));
                        startVideo();
                    }
                }, error => {
                    Swal.fire({ icon: 'error', title: 'GPS Gagal', text: 'Mohon aktifkan izin lokasi di browser Anda.' });
                }, { enableHighAccuracy: true, timeout: 5000 });
            }
        }

        function closeScanner() {
            stopVideo();
            stopScanning();
            scanInterface.classList.remove('is-open');
            setTimeout(() => { scanInterface.style.display = 'none'; }, 230);
            document.getElementById('landingPage').classList.remove('d-none');
            resetCamera();
        }

        function startVideo() {
            updateStatus("Inisialisasi...", "warning");
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({
                    audio: false,
                    video: {
                        facingMode: { ideal: "user" },
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        frameRate: { ideal: 30, max: 30 }
                    }
                })
                    .then(s => {
                        stream = s;
                        video.srcObject = stream;
                        video.onloadedmetadata = () => {
                            const playPromise = video.play();
                            if (playPromise && typeof playPromise.catch === 'function') {
                                playPromise.catch(() => {});
                            }
                        };
                        video.onplaying = () => {
                            updateStatus("Siap Memindai", "info", 0, true);
                            setVideoState('detecting');
                            initLivenessChallenge();
                            ensureMatcherReady();
                            startScanning();
                        };
                    })
                    .catch(err => updateStatus("Kamera Error", "danger"));
            }
        }

        function stopVideo() {
            if (stream) stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
            stream = null;
            stopScanning();
        }

        let statusHoldUntil = 0;
        let lastStatusText = "";
        let lastStatusType = "";

        function updateStatus(text, type, holdMs = 0, force = false) {
            const now = Date.now();
            if (!force && now < statusHoldUntil) return;
            if (text === lastStatusText && type === lastStatusType) return;
            if (text === "") {
                if (statusBadge) statusBadge.style.display = "none";
                if (statusSpinner) statusSpinner.classList.add('d-none');
                lastStatusText = text;
                lastStatusType = type;
                statusHoldUntil = holdMs > 0 ? now + holdMs : 0;
                return;
            }
            if (statusBadge) statusBadge.style.display = "inline-flex";
            statusText.innerText = text;
            statusBadge.className = `status-badge bg-${type} ${type === 'warning' ? 'text-dark' : 'text-white'}`;
            lastStatusText = text;
            lastStatusType = type;
            statusHoldUntil = holdMs > 0 ? now + holdMs : 0;
            const shouldSpin = type === 'info' || text.includes('Memuat') || text.includes('Menyiapkan') || text.includes('Memverifikasi');
            if (statusSpinner) statusSpinner.classList.toggle('d-none', !shouldSpin);
        }

        function setCaptureReady(ready) {
            captureBtn.disabled = !ready;
        }

        function setLivenessProgress(percent, ok = false) {
            const p = Math.max(0, Math.min(100, Math.round(percent)));
            if (livenessBar) livenessBar.style.width = `${p}%`;
            if (livenessValue) livenessValue.textContent = `${p}%`;
            if (livenessBar) {
                livenessBar.classList.toggle('bg-success', ok);
                livenessBar.classList.toggle('bg-warning', !ok);
            }
        }

        function setVideoState(state) {
            if (!videoContainer) return;
            videoContainer.classList.toggle('is-detecting', state === 'detecting');
            videoContainer.classList.toggle('is-locked', state === 'locked');
            videoContainer.classList.toggle('is-verified', state === 'verified');
        }

        function playSuccessBeep() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const o = ctx.createOscillator();
                const g = ctx.createGain();
                o.type = 'sine';
                o.frequency.value = 880;
                g.gain.value = 0.0001;
                o.connect(g);
                g.connect(ctx.destination);
                o.start();
                g.gain.exponentialRampToValueAtTime(0.12, ctx.currentTime + 0.02);
                g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.18);
                o.stop(ctx.currentTime + 0.2);
                setTimeout(() => ctx.close(), 350);
            } catch (e) {}
        }

        function isFaceCentered(detections) {
            try {
                const box = detections.detection.box;
                const videoWidth = video.videoWidth;
                const videoHeight = video.videoHeight;
                if (!videoWidth || !videoHeight) return false;

                const centerX = videoWidth / 2;
                const centerY = videoHeight / 2;

                const faceCenterX = box.x + box.width / 2;
                const faceCenterY = box.y + box.height / 2;

                const toleranceX = videoWidth * 0.2;
                const toleranceY = videoHeight * 0.2;

                return (
                    Math.abs(faceCenterX - centerX) < toleranceX &&
                    Math.abs(faceCenterY - centerY) < toleranceY
                );
            } catch (e) {
                return false;
            }
        }

        function initLivenessChallenge() {
            const headStep = Math.random() < 0.5 ? 'left' : 'right';
            livenessSequence = Math.random() < 0.5 ? ['blink', headStep] : [headStep, 'blink'];
            livenessStepIndex = 0;
            headStableFrames = 0;

            requiredBlinks = REQUIRED_BLINKS;
            blinkCount = 0;
            blinkState = 'open';
            livenessStartedAt = null;
            livenessChallengeStartedAt = Date.now();
            setLivenessProgress(0, false);
            if (successOverlay) successOverlay.classList.remove('is-visible');
        }

        function getYaw(landmarks) {
            const leftEye = landmarks.getLeftEye();
            const rightEye = landmarks.getRightEye();
            if (!leftEye?.length || !rightEye?.length) return 0;

            const leftCenter = averagePoints(leftEye);
            const rightCenter = averagePoints(rightEye);
            const midEye = { x: (leftCenter.x + rightCenter.x) / 2, y: (leftCenter.y + rightCenter.y) / 2 };
            const interEye = distance(leftCenter, rightCenter);
            const nose = landmarks.getNose();
            const noseTip = nose && nose.length ? nose[Math.min(3, nose.length - 1)] : midEye;
            if (!interEye) return 0;
            return (noseTip.x - midEye.x) / interEye;
        }

        function getCurrentInstructionText() {
            const step = livenessSequence[livenessStepIndex] || 'blink';
            if (step === 'blink') return `Kedipkan mata ${REQUIRED_BLINKS}x`;
            if (step === 'left') return 'Lihat kiri';
            if (step === 'right') return 'Lihat kanan';
            return 'Arahkan wajah ke kamera';
        }

        function updateLiveness(landmarks) {
            const now = Date.now();
            if (!livenessStartedAt) livenessStartedAt = now;
            if (livenessStartedAt && now - livenessStartedAt > LIVENESS_TIMEOUT_MS) {
                initLivenessChallenge();
                livenessStartedAt = now;
            }

            if (!livenessSequence.length) initLivenessChallenge();

            const step = livenessSequence[livenessStepIndex] || 'blink';
            updateStatus(getCurrentInstructionText(), "warning", INSTRUCTION_HOLD_MS);
            setVideoState('locked');

            if (step === 'blink') {
                const avgEAR = getAvgEAR(landmarks);
                updateBlinkState(avgEAR);
                if (blinkCount >= requiredBlinks) {
                    livenessStepIndex += 1;
                    headStableFrames = 0;
                }
                const stepProgress = Math.min(1, blinkCount / requiredBlinks);
                const overall = ((livenessStepIndex + stepProgress) / Math.max(1, livenessSequence.length)) * 100;
                setLivenessProgress(overall, false);
            } else {
                const yaw = getYaw(landmarks);
                const ok =
                    (step === 'left' && yaw <= -YAW_TURN_THRESHOLD) ||
                    (step === 'right' && yaw >= YAW_TURN_THRESHOLD);

                if (ok) headStableFrames += 1;
                else headStableFrames = 0;

                if (headStableFrames >= HEAD_STABLE_FRAMES_REQUIRED) {
                    livenessStepIndex += 1;
                    headStableFrames = 0;
                    blinkCount = 0;
                    blinkState = 'open';
                }
                const stepProgress = Math.min(1, headStableFrames / HEAD_STABLE_FRAMES_REQUIRED);
                const overall = ((livenessStepIndex + stepProgress) / Math.max(1, livenessSequence.length)) * 100;
                setLivenessProgress(overall, false);
            }

            if (livenessStepIndex < livenessSequence.length) return;

            if (!livenessChallengeStartedAt) livenessChallengeStartedAt = now;
            if (now - livenessChallengeStartedAt < MIN_LIVENESS_DURATION_MS) {
                updateStatus("Memverifikasi...", "info", INSTRUCTION_HOLD_MS);
                setVideoState('locked');
                return;
            }

            verifyLiveness();
        }

        function startScanning() {
            stopScanning();
            lastDetectionAt = 0;
            rafHandle = requestAnimationFrame(scanLoop);
        }

        function stopScanning() {
            if (rafHandle) cancelAnimationFrame(rafHandle);
            rafHandle = null;
        }

        async function scanLoop(ts) {
            if (!stream) {
                rafHandle = null;
                return;
            }

            if (!lastDetectionAt || ts - lastDetectionAt >= DETECTION_INTERVAL_MS) {
                lastDetectionAt = ts;
                await runDetectionFrame();
            }

            rafHandle = requestAnimationFrame(scanLoop);
        }

        async function runDetectionFrame() {
            if (isProcessing) return;
            if (isDetecting) return;

            if (!isFaceSystemReady) {
                setCaptureReady(false);
                if (!isLivenessVerified) updateStatus("Memuat model wajah...", "info", 0, true);
                setVideoState('detecting');
                ensureModelsLoaded();
                return;
            }

            if (!faceMatcher) {
                setCaptureReady(false);
                if (!isLivenessVerified) updateStatus("Menyiapkan data karyawan...", "info", 0, true);
                setVideoState('detecting');
                ensureMatcherReady();
                return;
            }

            isDetecting = true;
            let detections = null;
            try {
                detections = await faceapi
                    .detectSingleFace(video, videoDetectorOptions)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
            } finally {
                isDetecting = false;
            }

            if (!detections) {
                setCaptureReady(false);
                missingFaceFrames += 1;
                if (!isLivenessVerified && candidateEmployee && missingFaceFrames >= 3) {
                    initLivenessChallenge();
                }
                if (missingFaceFrames >= FACE_MISSING_RESET_FRAMES) resetMatchState();
                if (!isLivenessVerified) {
                    if (detectedNameInput) detectedNameInput.value = "";
                    updateStatus("Arahkan wajah ke kamera", "info");
                    setVideoState('detecting');
                }
                return;
            }

            if (detections.detection && detections.detection.score < MIN_DETECTION_SCORE) {
                setCaptureReady(false);
                updateStatus("Arahkan wajah ke kamera", "warning", INSTRUCTION_HOLD_MS);
                setVideoState('detecting');
                return;
            }

            const result = faceMatcher.findBestMatch(detections.descriptor);
            if (result.label === 'unknown' || result.distance > MATCH_THRESHOLD) {
                setCaptureReady(false);
                unknownFaceFrames += 1;
                if (!isLivenessVerified && candidateEmployee && unknownFaceFrames >= 3) {
                    initLivenessChallenge();
                }
                if (unknownFaceFrames >= UNKNOWN_RESET_FRAMES) resetMatchState();
                if (!isLivenessVerified) {
                    if (detectedNameInput) detectedNameInput.value = "";
                    updateStatus("Wajah Tidak Dikenal", "warning", INSTRUCTION_HOLD_MS);
                    setVideoState('detecting');
                }
                return;
            }

            missingFaceFrames = 0;
            unknownFaceFrames = 0;

            if (currentMatchLabel === result.label) {
                stableMatchFrames += 1;
            } else {
                currentMatchLabel = result.label;
                stableMatchFrames = 1;
            }

            if (stableMatchFrames < STABLE_FRAMES_REQUIRED) {
                if (!isLivenessVerified) updateStatus("Mendeteksi wajah...", "info");
                setVideoState('detecting');
                return;
            }

            const matchedEmployee = employees.find(e => e.name === result.label);
            if (!matchedEmployee) {
                resetMatchState();
                if (!isLivenessVerified) updateStatus("Wajah Tidak Dikenal", "warning", INSTRUCTION_HOLD_MS);
                return;
            }

            if (matchedEmployee.name !== lastShownName) {
                if (detectedNameInput) detectedNameInput.value = matchedEmployee.name;
                lastShownName = matchedEmployee.name;
            }

            if (!candidateEmployee || candidateEmployee.id !== matchedEmployee.id) {
                candidateEmployee = matchedEmployee;
                isAlreadyAttended = attendedUserIds.includes(String(matchedEmployee.id));
                initLivenessChallenge();
            }

            if (isLivenessVerified) return;

            if (isAlreadyAttended) {
                setCaptureReady(false);
                submitBtn.disabled = true;
                updateStatus("Sudah absen hari ini", "secondary", INSTRUCTION_HOLD_MS);
                stopScanning();
                return;
            }

            setCaptureReady(false);

            if (result.distance > MAX_ACCEPT_DISTANCE) {
                updateStatus("Akurasi rendah, hadapkan wajah ke kamera", "warning", INSTRUCTION_HOLD_MS);
                if (!isLivenessVerified && candidateEmployee) {
                    initLivenessChallenge();
                }
                setVideoState('detecting');
                return;
            }

            if (!isFaceCentered(detections)) {
                if (!isLivenessVerified) {
                    initLivenessChallenge();
                    updateStatus("Posisikan wajah di dalam lingkaran", "warning", INSTRUCTION_HOLD_MS);
                }
                setVideoState('detecting');
                return;
            }

            if (!isLivenessVerified) updateStatus("", "info");
            updateLiveness(detections.landmarks);
        }

        async function captureAndDetect() {
            if (!isLivenessVerified || !userIdInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Verifikasi dulu',
                    text: "Ikuti instruksi liveness dulu.",
                });
                return;
            }

            isProcessing = true;
            stopScanning();
            
            const canvas = document.createElement('canvas');
            const maxWidth = 640;
            const srcWidth = video.videoWidth;
            const srcHeight = video.videoHeight;
            const ratio = srcWidth ? Math.min(1, maxWidth / srcWidth) : 1;
            canvas.width = Math.round(srcWidth * ratio);
            canvas.height = Math.round(srcHeight * ratio);
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.72);

            video.style.display = 'none';
            capturedImage.src = dataUrl;
            capturedImage.style.display = 'block';
            captureBtn.classList.add('d-none');
            actionButtons.classList.remove('d-none');
            document.getElementById('photo').value = dataUrl;

            submitBtn.disabled = false;
            updateStatus("Foto Siap Dikonfirmasi", "success");
        }

        function resetCamera() {
            isProcessing = false;
            video.style.display = 'block';
            capturedImage.style.display = 'none';
            captureBtn.classList.remove('d-none');
            actionButtons.classList.add('d-none');
            if (detectedNameInput) detectedNameInput.value = "";
            userIdInput.value = "";
            submitBtn.disabled = true;
            scanLine.style.animationPlayState = 'running';
            isLivenessVerified = false;
            isAlreadyAttended = false;
            candidateEmployee = null;
            resetMatchState();
            initLivenessChallenge();
            updateStatus("Siap Memindai", "info");
            setVideoState('detecting');
            if (scanInterface && scanInterface.style.display !== 'none') startScanning();
        }

        function submitAttendance() {
            if (!isLivenessVerified || !userIdInput.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Verifikasi belum lengkap',
                    text: "Ikuti instruksi liveness dulu.",
                });
                return;
            }
            
            Swal.fire({
                title: 'Menyimpan Absensi',
                text: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            document.getElementById('attendanceForm').submit();
        }

        function resetMatchState() {
            currentMatchLabel = null;
            stableMatchFrames = 0;
            missingFaceFrames = 0;
            unknownFaceFrames = 0;
            lastShownName = "";
            if (!isLivenessVerified) {
                isAlreadyAttended = false;
                candidateEmployee = null;
                if (detectedNameInput) detectedNameInput.value = "";
                userIdInput.value = "";
                submitBtn.disabled = true;
                scanLine.style.animationPlayState = 'running';
                initLivenessChallenge();
            }
        }

        function resetLivenessState(keepStartTime = false) {
            isLivenessVerified = false;
            if (!keepStartTime) livenessStartedAt = null;
            blinkCount = 0;
            blinkState = 'open';
            requiredBlinks = REQUIRED_BLINKS;
            livenessChallengeStartedAt = null;
            livenessSequence = [];
            livenessStepIndex = 0;
            headStableFrames = 0;
            submitBtn.disabled = true;
            setCaptureReady(false);
            userIdInput.value = "";
            scanLine.style.animationPlayState = 'running';
        }

        function verifyLiveness() {
            if (!candidateEmployee) return;
            isLivenessVerified = true;
            if (detectedNameInput) detectedNameInput.value = candidateEmployee.name;
            userIdInput.value = candidateEmployee.id;
            submitBtn.disabled = true;
            setCaptureReady(true);
            scanLine.style.animationPlayState = 'paused';
            updateStatus("Wajah terverifikasi", "success");
            setVideoState('verified');
            setLivenessProgress(100, true);
            if (successOverlay) successOverlay.classList.add('is-visible');
            playSuccessBeep();
            stopScanning();
        }

        function getAvgEAR(landmarks) {
            const leftEye = landmarks.getLeftEye();
            const rightEye = landmarks.getRightEye();
            const leftEAR = getEAR(leftEye);
            const rightEAR = getEAR(rightEye);
            return (leftEAR + rightEAR) / 2;
        }

        function getEAR(eye) {
            const vertical1 = distance(eye[1], eye[5]);
            const vertical2 = distance(eye[2], eye[4]);
            const horizontal = distance(eye[0], eye[3]);
            if (!horizontal) return 0;
            return (vertical1 + vertical2) / (2 * horizontal);
        }

        function updateBlinkState(avgEAR) {
            if (blinkState === 'open' && avgEAR < BLINK_LOW_THRESHOLD) {
                blinkState = 'closed';
            } else if (blinkState === 'closed' && avgEAR > BLINK_HIGH_THRESHOLD) {
                blinkState = 'open';
                blinkCount += 1;
            }
        }

        function averagePoints(points) {
            let x = 0;
            let y = 0;
            for (let i = 0; i < points.length; i++) {
                x += points[i].x;
                y += points[i].y;
            }
            const len = points.length || 1;
            return { x: x / len, y: y / len };
        }

        function distance(a, b) {
            const dx = a.x - b.x;
            const dy = a.y - b.y;
            return Math.sqrt(dx * dx + dy * dy);
        }
    </script>
    @endif
</body>
</html>
