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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #D4AF37;
            --dark-bg: #121212;
            --card-bg: #1e1e1e;
        }
        body {
            background-color: var(--dark-bg);
            font-family: 'Poppins', sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 20px 0;
        }
        
        /* Landing Page Styles */
        .landing-container {
            text-align: center;
            width: 100%;
            max-width: 600px;
            padding: 20px;
            animation: fadeIn 1s ease-in-out;
        }
        
        .logo-landing {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            margin-bottom: 20px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }
        
        .clock-widget {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
        }
        
        .date-widget {
            font-size: 1.2rem;
            color: #aaa;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .start-btn {
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .start-btn:hover {
            background-color: var(--primary-color);
            color: #000;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.5);
            transform: scale(1.05);
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modal / Scanning UI */
        .scan-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.95);
            z-index: 9999;
            display: none; /* Hidden by default */
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-y: auto; /* Enable vertical scroll if needed */
            padding: 20px 0;
        }

        .scan-card {
            width: 90%;
            max-width: 450px;
            background: #1e1e1e;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            position: relative;
            border: 1px solid #333;
            margin: auto; /* Center vertically with flex and scroll */
        }

        .video-wrapper {
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            background: #000;
            position: relative;
            aspect-ratio: 3/4; /* Portrait optimized */
            border: 2px solid var(--primary-color);
        }

        video, #captured_image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #captured_image { display: none; }

        .scan-status {
            margin-top: 15px;
            font-weight: 600;
            min-height: 24px;
        }

        .btn-gold {
            background-color: var(--primary-color);
            color: #000;
            border: 1px solid var(--primary-color);
            font-weight: 600;
        }
        .btn-gold:hover {
            background-color: #bfa140;
            color: #000;
        }
        .btn-outline-gold {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            font-weight: 600;
        }
        .btn-outline-gold:hover {
            background-color: var(--primary-color);
            color: #000;
        }

        /* Mobile Optimization */
        @media (max-width: 768px) {
            .scan-card {
                width: 100%;
                height: 100%;
                max-width: none;
                border-radius: 0;
                border: none;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 30px;
            }

            .video-wrapper {
                border-radius: 20px;
                aspect-ratio: 3/4;
                width: 100%;
                max-height: 60vh;
            }

            .close-scan {
                top: 20px;
                right: 20px;
                background: rgba(0,0,0,0.5);
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .start-btn {
                width: 80%;
                padding: 18px;
            }
            
            .clock-widget {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>

    <!-- Main Landing Screen -->
    <div class="landing-container" id="landingPage">
        <img src="{{ asset('img/logo.jpeg') }}" alt="Logo PMS" class="logo-landing">
        
        <h2 class="fw-bold text-white mb-1">PT PUTRA MUARA SUKSES</h2>
        <p class="text-secondary mb-5">Selamat Datang di Kiosk Absensi</p>

        <div class="clock-widget" id="clock">00:00</div>
        <div class="date-widget" id="date">SENIN, 01 JANUARI 2026</div>

        @if($activeSession)
            <div class="mb-5">
                <div class="card bg-dark border-secondary d-inline-block px-4 py-2" style="border-radius: 50px; border: 1px solid #333;">
                    <div class="d-flex align-items-center">
                        <div class="bg-gold rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-clock text-dark"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="text-warning fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; color: #FFD700 !important;">Sesi Aktif: {{ $activeSession->title }}</h6>
                            <small class="text-warning" style="color: #FFD700 !important;">
                                {{ \Carbon\Carbon::parse($activeSession->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($activeSession->end_time)->format('H:i') }} WIB
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="start-btn pulse-animation" onclick="openScanner()">
                <i class="fas fa-fingerprint me-2"></i> Tap to Start
            </button>
        @else
            <div class="alert alert-dark border-secondary text-secondary d-inline-block px-4">
                <i class="fas fa-lock me-2"></i> Belum ada sesi aktif
            </div>
        @endif

        <div class="mt-5">
            <a href="{{ route('login') }}" class="text-secondary small text-decoration-none opacity-50 hover-opacity-100">
                <i class="fas fa-user-shield"></i> Admin Login
            </a>
        </div>
    </div>

    <!-- Scanning Interface (Overlay) -->
    <div class="scan-overlay" id="scanInterface">
        <div class="scan-card">
            <button class="close-scan" onclick="closeScanner()"><i class="fas fa-times"></i></button>
            
            <h5 class="mb-3 text-white">Verifikasi Wajah</h5>
            
            <div class="video-wrapper">
                <video id="video" autoplay muted playsinline></video>
                <img id="captured_image" src="" alt="Captured Photo">
            </div>

            <div class="scan-status text-warning" id="status-text">Memuat kamera...</div>
            <input type="text" id="detected_name" class="form-control bg-dark border-secondary text-white text-center mt-2 mb-3" readonly placeholder="Menunggu wajah...">

            <form id="attendanceForm" action="{{ route('attendance.storePublic') }}" method="POST">
                @csrf
                <input type="hidden" name="location" id="location">
                <input type="hidden" name="photo" id="photo">
                <input type="hidden" name="user_id" id="user_id">

                <div class="d-grid gap-2">
                    <button type="button" id="captureBtn" onClick="captureAndDetect()" class="btn btn-light rounded-pill fw-bold">
                        <i class="fas fa-camera"></i> AMBIL FOTO
                    </button>
                    <div id="actionButtons" class="d-none d-flex gap-2">
                        <button type="button" onClick="resetCamera()" class="btn btn-outline-gold w-50 rounded-pill">Ulang</button>
                        <button type="button" id="submitBtn" onClick="submitAttendance()" class="btn btn-gold w-50 rounded-pill" disabled>Absen Sekarang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alert Messages (Toast) -->
    @if(session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: "{{ session('success') ? 'success' : 'error' }}",
                    title: "{{ session('success') ? 'Berhasil!' : 'Gagal!' }}",
                    text: "{{ session('success') ?? session('error') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top',
                    timerProgressBar: true
                }).then(() => {
                    // Auto-redirect back to clean state after success
                    if ("{{ session('success') }}") {
                        window.location.href = "{{ route('attendance.kiosk') }}";
                    }
                });
            });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- Clock Logic (Always Active) ---
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('clock').innerText = `${hours}:${minutes}`;
            
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('date').innerText = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    
    @if($activeSession)
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        // --- Camera & Face Logic ---
        const video = document.getElementById('video');
        const capturedImage = document.getElementById('captured_image');
        const statusText = document.getElementById('status-text');
        const detectedNameInput = document.getElementById('detected_name');
        const userIdInput = document.getElementById('user_id');
        const submitBtn = document.getElementById('submitBtn');
        const captureBtn = document.getElementById('captureBtn');
        const actionButtons = document.getElementById('actionButtons');
        
        let stream = null;
        let labeledFaceDescriptors = [];
        let faceMatcher = null;
        const MODEL_URL = "{{ asset('models') }}";

        const employees = [
            @foreach($employees as $employee)
                @if($employee->photo)
                {
                    id: "{{ $employee->id }}",
                    name: "{{ $employee->name }}",
                    photo: "{{ asset('storage/' . $employee->photo) }}"
                },
                @endif
            @endforeach
        ];

        // Pre-load models in background
        if (typeof faceapi !== 'undefined') {
            loadModels();
        }

        function loadModels() {
            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL)
            ]).then(async () => {
                console.log("Models loaded");
                labeledFaceDescriptors = await loadLabeledImages();
                if(labeledFaceDescriptors.length > 0) {
                    faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.45);
                }
            }).catch(err => console.error(err));
        }

        async function loadLabeledImages() {
            return Promise.all(
                employees.map(async (employee) => {
                    const descriptions = [];
                    try {
                        const img = await faceapi.fetchImage(employee.photo);
                        const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                        if(detections) {
                            descriptions.push(detections.descriptor);
                            return new faceapi.LabeledFaceDescriptors(employee.name, descriptions);
                        }
                    } catch(e) { console.error(e); }
                    return null;
                })
            ).then(results => results.filter(r => r !== null));
        }

        // --- UI Interactions ---
        function openScanner() {
            document.getElementById('landingPage').style.display = 'none';
            document.getElementById('scanInterface').style.display = 'flex';
            startVideo();
        }

        function closeScanner() {
            stopVideo();
            document.getElementById('scanInterface').style.display = 'none';
            document.getElementById('landingPage').style.display = 'block';
            resetCamera();
        }

        function startVideo() {
            statusText.innerText = "Mengakses kamera...";
            statusText.className = "scan-status text-warning";
            
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                    .then(s => {
                        stream = s;
                        video.srcObject = stream;
                        video.play();
                        statusText.innerText = "Silahkan posisikan wajah Anda";
                        statusText.className = "scan-status text-white";
                    })
                    .catch(err => {
                        statusText.innerText = "Gagal akses kamera: " + err.message;
                        statusText.className = "scan-status text-danger";
                    });
            }
        }

        function stopVideo() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }
        }

        async function captureAndDetect() {
            if (!faceMatcher) {
                statusText.innerText = "Sistem AI belum siap. Tunggu sebentar...";
                return;
            }

            // Capture
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg');

            // UI Update
            video.style.display = 'none';
            capturedImage.src = dataUrl;
            capturedImage.style.display = 'block';
            captureBtn.classList.add('d-none'); // Hide capture button ONLY
            actionButtons.classList.remove('d-none'); // Show action buttons
            
            document.getElementById('photo').value = dataUrl;
            
            // Detection
            statusText.innerText = "Menganalisa...";
            statusText.className = "scan-status text-warning";

            const img = document.createElement('img');
            img.src = dataUrl;
            
            img.onload = async () => {
                try {
                    const detection = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                    if (detection) {
                        const result = faceMatcher.findBestMatch(detection.descriptor);
                        if (result.label !== 'unknown') {
                            const employee = employees.find(e => e.name === result.label);
                            if (employee) {
                                detectedNameInput.value = employee.name;
                                userIdInput.value = employee.id;
                                submitBtn.disabled = false;
                                statusText.innerText = "Identitas Terkonfirmasi ✅";
                                statusText.className = "scan-status text-success";
                            }
                        } else {
                            handleScanError("Wajah tidak dikenali ❌");
                        }
                    } else {
                        handleScanError("Wajah tidak ditemukan ❌");
                    }
                } catch (e) {
                    handleScanError("Error sistem ⚠️");
                }
            };
        }

        function handleScanError(msg) {
            detectedNameInput.value = msg;
            userIdInput.value = "";
            submitBtn.disabled = true;
            statusText.innerText = "Silahkan coba lagi";
            statusText.className = "scan-status text-danger";
        }

        function resetCamera() {
            video.style.display = 'block';
            capturedImage.style.display = 'none';
            captureBtn.classList.remove('d-none');
            actionButtons.classList.add('d-none');
            detectedNameInput.value = "";
            userIdInput.value = "";
            submitBtn.disabled = true;
            statusText.innerText = "Silahkan posisikan wajah Anda";
            statusText.className = "scan-status text-white";
        }

        function submitAttendance() {
            if(!userIdInput.value) return;
            document.getElementById('attendanceForm').submit();
        }

        // Geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                document.getElementById('location').value = pos.coords.latitude + "," + pos.coords.longitude;
            });
        }
    </script>
    @endif
</body>
</html>
