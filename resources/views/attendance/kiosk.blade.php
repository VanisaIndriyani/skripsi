<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Absensi Kiosk - PT Putra Muara Sukses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="kiosk-page">
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

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
                <i class="fas fa-circle-dot text-success me-3"></i>
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

    <div class="scan-overlay" id="scanInterface">
        <div class="close-btn" onclick="closeScanner()">
            <i class="fas fa-times"></i>
        </div>

        <div class="scan-container">
            <div class="scan-header">
                <div class="scan-title">
                    <strong>Absensi Kiosk</strong>
                    <span>Face Recognition</span>
                </div>
                <div id="status-badge" class="status-badge">
                    <i class="fas fa-circle-notch fa-spin d-none" id="statusSpinner"></i>
                    <span id="status-text">Memulai...</span>
                </div>
            </div>

            <div class="video-container" id="videoContainer">
                <div class="face-guide"></div>
                <div class="camera-loading" id="cameraLoading">
                    <div class="loader-orb"></div>
                    <div class="loader-text">Menyiapkan kamera</div>
                </div>
                <video id="video" autoplay muted playsinline></video>
                <img id="captured_image" src="" alt="Captured Photo" style="display:none;">
                <div class="success-overlay" id="successOverlay">
                    <div class="success-check">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>

            <div class="scan-info-card">
                <input type="text" id="detected_name" class="form-control bg-transparent border-0 text-white text-center fs-4 fw-bold mb-3" readonly placeholder="...">

                <button type="button" class="manual-pick small fw-semibold d-none mb-2" id="manualPickBtn">
                    Tidak terdeteksi? Pilih nama
                </button>

                <div id="instructionToast" class="mini-instruction">
                    <i class="fas fa-eye"></i>
                    <span id="instructionText">Posisikan wajah di oval</span>
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

    <div class="kiosk-modal" id="manualModal" aria-hidden="true">
        <div class="kiosk-modal-backdrop" id="manualModalBackdrop"></div>
        <div class="kiosk-modal-sheet" role="dialog" aria-modal="true" aria-label="Pilih Nama Karyawan">
            <div class="kiosk-modal-header">
                <div class="kiosk-modal-title">Pilih Nama Karyawan</div>
                <button type="button" class="kiosk-close" id="manualModalClose" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <input type="search" class="form-control rounded-pill mb-3" id="manualSearch" placeholder="Cari nama...">
            <div class="kiosk-list" id="manualList"></div>
        </div>
    </div>

    <script>
        window.__KIOSK__ = {
            activeSession: @json((bool) $activeSession),
            attendedUserIds: @json($attendedUserIds ?? []),
            employees: [
                @foreach($employees as $employee)
                    @if($employee->photo)
                    { id: "{{ $employee->id }}", name: "{{ $employee->name }}", photo: "{{ asset('storage/' . $employee->photo) }}" },
                    @endif
                @endforeach
            ],
            modelUrl: "{{ asset('models') }}",
            officeLat: {{ \App\Models\Setting::get('office_latitude', 0) }},
            officeLng: {{ \App\Models\Setting::get('office_longitude', 0) }},
            officeRadius: {{ \App\Models\Setting::get('office_radius', 100) }}
        };
    </script>
</body>
</html>
