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
            .scan-container { padding: 10px; margin: auto; } /* Added margin: auto */
            .video-container { border-radius: 30px; }
            .scan-info-card { padding: 15px; } /* Adjusted padding for smaller screens */
            .scan-overlay {
                overflow-y: auto; /* Allow scrolling on small screens */
                align-items: flex-start; /* Align to top instead of center */
                padding: 40px 15px; /* More vertical padding */
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
            <div class="video-container">
                <div class="scan-line" id="scanLine"></div>
                <div class="scan-frame"></div>
                <video id="video" autoplay muted playsinline></video>
                <img id="captured_image" src="" alt="Captured Photo">
            </div>

            <div class="scan-info-card">
                <div id="status-badge" class="status-badge bg-warning text-dark">
                    <i class="fas fa-camera"></i>
                    <span id="status-text">Memulai Kamera...</span>
                </div>
                
                <input type="text" id="detected_name" class="form-control bg-transparent border-0 text-white text-center fs-4 fw-bold mb-3" readonly placeholder="...">

                <form id="attendanceForm" action="{{ route('attendance.storePublic') }}" method="POST">
                    @csrf
                    <input type="hidden" name="location" id="location">
                    <input type="hidden" name="photo" id="photo">
                    <input type="hidden" name="user_id" id="user_id">

                    <div class="d-grid gap-2">
                        <button type="button" id="captureBtn" onClick="captureAndDetect()" class="btn btn-gold rounded-pill py-3">
                            <i class="fas fa-camera me-2"></i> AMBIL FOTO
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
        
        let stream = null;
        let labeledFaceDescriptors = [];
        let faceMatcher = null;
        let isFaceSystemReady = false;
        let isDetecting = false;
        let detectionInterval = null;
        let isProcessing = false;
        let isLivenessVerified = false;
        let isAlreadyAttended = false;
        let candidateEmployee = null;
        let currentMatchLabel = null;
        let stableMatchFrames = 0;
        let livenessStartedAt = null;
        let blinkCount = 0;
        let blinkState = 'open';
        let missingFaceFrames = 0;
        let unknownFaceFrames = 0;

        const attendedUserIds = @json($attendedUserIds ?? []);

        const MATCH_THRESHOLD = 0.48;
        const STABLE_FRAMES_REQUIRED = 2;
        const MIN_FACE_SCORE = 0.45;
        const REQUIRED_BLINKS = 1;
        const LIVENESS_TIMEOUT_MS = 15000;
        const BLINK_LOW_THRESHOLD = 0.21;
        const BLINK_HIGH_THRESHOLD = 0.23;
        const FACE_MISSING_RESET_FRAMES = 10;
        const UNKNOWN_RESET_FRAMES = 10;
        const INSTRUCTION_HOLD_MS = 2500;
        const BLINK_INSTRUCTION_TEXT = `Kedip ${REQUIRED_BLINKS}x untuk verifikasi`;
        const MODEL_URL = "{{ asset('models') }}";

        setCaptureReady(false);

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

        if (typeof faceapi !== 'undefined') loadModels();

        function loadModels() {
            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]).then(async () => {
                labeledFaceDescriptors = await loadLabeledImages();
                if(labeledFaceDescriptors.length > 0) {
                    faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, MATCH_THRESHOLD);
                }
                isFaceSystemReady = true;
            }).catch(err => console.error(err));
        }

        async function loadLabeledImages() {
            return Promise.all(
                employees.map(async (employee) => {
                    const descriptions = [];
                    try {
                        const img = await faceapi.fetchImage(employee.photo);
                        const detections = await faceapi
                            .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: MIN_FACE_SCORE }))
                            .withFaceLandmarks()
                            .withFaceDescriptor();
                        if(detections) {
                            descriptions.push(detections.descriptor);
                            return new faceapi.LabeledFaceDescriptors(employee.name, descriptions);
                        }
                    } catch(e) { console.error(e); }
                    return null;
                })
            ).then(results => results.filter(r => r !== null));
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
                        document.getElementById('scanInterface').style.display = 'flex';
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
            document.getElementById('scanInterface').style.display = 'none';
            document.getElementById('landingPage').classList.remove('d-none');
            resetCamera();
        }

        function startVideo() {
            updateStatus("Inisialisasi...", "warning");
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                    .then(s => {
                        stream = s;
                        video.srcObject = stream;
                        video.play();
                        updateStatus("Siap Memindai", "info");
                        video.onplay = () => startScanning();
                    })
                    .catch(err => updateStatus("Kamera Error", "danger"));
            }
        }

        function stopVideo() {
            if (stream) stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
        }

        let statusHoldUntil = 0;
        let lastStatusText = "";
        let lastStatusType = "";

        function updateStatus(text, type, holdMs = 0, force = false) {
            const now = Date.now();
            if (!force && now < statusHoldUntil) return;
            if (text === lastStatusText && type === lastStatusType) return;
            statusText.innerText = text;
            statusBadge.className = `status-badge bg-${type} ${type === 'warning' ? 'text-dark' : 'text-white'}`;
            lastStatusText = text;
            lastStatusType = type;
            statusHoldUntil = holdMs > 0 ? now + holdMs : 0;
        }

        function setCaptureReady(ready) {
            captureBtn.disabled = !ready;
        }

        function startScanning() {
            if (detectionInterval) clearInterval(detectionInterval);
            detectionInterval = setInterval(async () => {
                if (isProcessing) return;
                if (isDetecting) return;
                if (!isFaceSystemReady) {
                    setCaptureReady(false);
                    if (!isLivenessVerified) updateStatus("Memuat model wajah...", "info", 0, true);
                    return;
                }
                if (!faceMatcher) {
                    setCaptureReady(false);
                    if (!isLivenessVerified) updateStatus("Data wajah karyawan belum ada", "warning", 0, true);
                    return;
                }
                isDetecting = true;
                let detections = null;
                try {
                    detections = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 128, scoreThreshold: MIN_FACE_SCORE }))
                        .withFaceLandmarks()
                        .withFaceDescriptor();
                } finally {
                    isDetecting = false;
                }

                if (!detections) {
                    setCaptureReady(false);
                    missingFaceFrames += 1;
                    if (missingFaceFrames >= FACE_MISSING_RESET_FRAMES) resetMatchState();
                    if (!isLivenessVerified) {
                        if (candidateEmployee) updateStatus(BLINK_INSTRUCTION_TEXT, "warning", INSTRUCTION_HOLD_MS);
                        else updateStatus("Arahkan wajah ke kamera", "info");
                    }
                    return;
                }

                const result = faceMatcher.findBestMatch(detections.descriptor);
                if (result.label === 'unknown' || result.distance > MATCH_THRESHOLD) {
                    setCaptureReady(false);
                    unknownFaceFrames += 1;
                    if (unknownFaceFrames >= UNKNOWN_RESET_FRAMES) resetMatchState();
                    if (!candidateEmployee) detectedNameInput.value = "Mencari wajah...";
                    if (!isLivenessVerified) {
                        if (candidateEmployee) updateStatus(BLINK_INSTRUCTION_TEXT, "warning", INSTRUCTION_HOLD_MS);
                        else updateStatus("Wajah Tidak Dikenal", "warning");
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
                    if (!isLivenessVerified) updateStatus("Mengunci identitas...", "info");
                    return;
                }

                const matchedEmployee = employees.find(e => e.name === result.label);
                if (!matchedEmployee) {
                    resetMatchState();
                    if (!isLivenessVerified) updateStatus("Wajah Tidak Dikenal", "warning");
                    return;
                }

                if (!candidateEmployee || candidateEmployee.id !== matchedEmployee.id) {
                    candidateEmployee = matchedEmployee;
                    detectedNameInput.value = "";
                    isAlreadyAttended = attendedUserIds.includes(String(matchedEmployee.id));
                    resetLivenessState();
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
                const landmarks = detections.landmarks;
                const now = Date.now();
                if (!livenessStartedAt) livenessStartedAt = now;
                if (livenessStartedAt && now - livenessStartedAt > LIVENESS_TIMEOUT_MS) {
                    resetLivenessState();
                    livenessStartedAt = now;
                }

                updateStatus(BLINK_INSTRUCTION_TEXT, "warning", INSTRUCTION_HOLD_MS);
                const avgEAR = getAvgEAR(landmarks);
                updateBlinkState(avgEAR);
                if (blinkCount >= REQUIRED_BLINKS) verifyLiveness();
            }, 100);
        }

        function stopScanning() {
            if (detectionInterval) clearInterval(detectionInterval);
        }

        async function captureAndDetect() {
            if (!isLivenessVerified || !userIdInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Verifikasi dulu',
                    text: BLINK_INSTRUCTION_TEXT + ".",
                });
                return;
            }

            isProcessing = true;
            stopScanning();
            
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg');

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
            detectedNameInput.value = "";
            userIdInput.value = "";
            submitBtn.disabled = true;
            scanLine.style.animationPlayState = 'running';
            isLivenessVerified = false;
            isAlreadyAttended = false;
            candidateEmployee = null;
            resetMatchState();
            resetLivenessState(true);
            updateStatus("Siap Memindai", "info");
            startScanning();
        }

        function submitAttendance() {
            if (!isLivenessVerified || !userIdInput.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Verifikasi belum lengkap',
                    text: BLINK_INSTRUCTION_TEXT + ".",
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
            if (!isLivenessVerified) {
                isAlreadyAttended = false;
                candidateEmployee = null;
                detectedNameInput.value = "";
                userIdInput.value = "";
                submitBtn.disabled = true;
                scanLine.style.animationPlayState = 'running';
                resetLivenessState();
            }
        }

        function resetLivenessState(keepStartTime = false) {
            isLivenessVerified = false;
            if (!keepStartTime) livenessStartedAt = null;
            blinkCount = 0;
            blinkState = 'open';
            submitBtn.disabled = true;
            setCaptureReady(false);
            userIdInput.value = "";
            scanLine.style.animationPlayState = 'running';
        }

        function verifyLiveness() {
            if (!candidateEmployee) return;
            isLivenessVerified = true;
            detectedNameInput.value = candidateEmployee.name;
            userIdInput.value = candidateEmployee.id;
            submitBtn.disabled = true;
            setCaptureReady(true);
            scanLine.style.animationPlayState = 'paused';
            updateStatus("Wajah Terverifikasi - Silakan Ambil Foto", "success");
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


        function averagePoint(points) {
            const sum = points.reduce((acc, p) => ({ x: acc.x + p.x, y: acc.y + p.y }), { x: 0, y: 0 });
            return { x: sum.x / points.length, y: sum.y / points.length };
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
