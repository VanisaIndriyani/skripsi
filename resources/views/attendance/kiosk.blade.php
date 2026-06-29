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
        <div class="landing-hero-card">
        <div class="logo-wrapper">
            <div class="logo-ring"></div>
            <img src="{{ asset('img/logo.jpeg') }}" alt="Logo PMS" class="logo-landing">
        </div>

        <div class="landing-copy">
            <h2 class="company-name">PT PUTRA MUARA SUKSES</h2>
        </div>

        <div class="clock-container">
            <div class="clock-header">
                <span class="clock-label">Waktu Sekarang</span>
                <span class="clock-status">Realtime</span>
            </div>
            <div class="clock-main">
                <div class="clock-widget" id="clock">00:00</div>
                <div class="date-widget" id="date">MEMUAT TANGGAL...</div>
            </div>
        </div>

        @if($activeSession)
            <div class="session-info">
                <div class="session-dot-wrap">
                    <span class="session-dot"></span>
                </div>
                <div class="text-start session-copy">
                    <span class="d-block text-gold small fw-bold text-uppercase">Sesi Aktif</span>
                    <span class="session-title">{{ $activeSession->title }}</span>
                    <span class="session-time">{{ \Carbon\Carbon::parse($activeSession->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($activeSession->end_time)->format('H:i') }} WIB</span>
                </div>
            </div>

            <button class="start-btn" onclick="openScanner()">
                <span class="start-btn-icon"><i class="fas fa-camera-retro fa-lg"></i></span>
                <span class="start-btn-copy">
                    <strong>Mulai Absensi</strong>
                </span>
            </button>

            @if(session('success') || session('error'))
                <div class="kiosk-result-card {{ session('success') ? 'is-success' : 'is-error' }}">
                    <div class="kiosk-result-icon">
                        <i class="fas {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                    </div>
                    <div class="kiosk-result-copy">
                        <span class="kiosk-result-title">{{ session('success') ? 'Verifikasi Berhasil' : 'Verifikasi Gagal' }}</span>
                        <span class="kiosk-result-message">{{ session('success') ?? session('error') }}</span>
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-dark bg-dark border-secondary rounded-pill px-4 mb-5">
                <i class="fas fa-lock me-2 text-gold"></i> Belum ada sesi aktif saat ini
            </div>
        @endif

        <div class="landing-footer mt-5">
            <a href="{{ route('login') }}" class="text-secondary small text-decoration-none opacity-50 hover-opacity-100 admin-link">
                <i class="fas fa-shield-halved me-1"></i> Admin Login
            </a>
        </div>
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
                </div>
                <div id="status-badge" class="status-badge">
                    <i class="fas fa-circle-notch fa-spin d-none" id="statusSpinner"></i>
                    <span id="status-text">Memulai...</span>
                </div>
            </div>

            <div class="camera-stage">
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
                <div class="face-status" id="faceStatus" data-state="detecting">
                    <span class="face-status-dot"></span>
                    <span id="faceStatusText">Wajah belum terdeteksi</span>
                </div>
            </div>

            <div class="scan-info-card">
                <div class="identity-panel">
                    <input type="text" id="detected_name" class="form-control bg-transparent border-0 text-white text-center fs-4 fw-bold" readonly placeholder="">
                </div>

                <button type="button" class="manual-pick small fw-semibold d-none mb-2" id="manualPickBtn">
                    Tidak terdeteksi? Pilih nama
                </button>

                <div class="instruction-panel" id="instructionPanel" aria-live="polite">
                    <div class="instruction-rule"></div>
                    <div class="instruction-step-title" id="instructionStepLabel">Verifikasi</div>
                    <div class="instruction-panel-body">
                        <div class="instruction-copy">
                            <div class="instruction-text" id="instructionText">Posisikan wajah di dalam bingkai.</div>
                            <div class="instruction-hint" id="instructionHint"></div>
                        </div>
                    </div>
                    <div class="instruction-rule"></div>
                    <div class="instruction-status-chip" id="instructionStatusChip">
                        <span class="instruction-status-dot"></span>
                        <span id="instructionStatusLabel">Scan</span>
                    </div>
                </div>

                <div class="progress-panel">
                    <div class="progress-panel-header">
                        <span class="progress-percent" id="progressPercent">0%</span>
                    </div>
                    <div class="progress-track" aria-hidden="true">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="progress-meta">
                        <span id="progressStepText">Langkah 1 dari 3</span>
                        <span id="progressStatusText">Scan</span>
                    </div>
                </div>

                <form id="attendanceForm" action="{{ route('attendance.storePublic') }}" method="POST">
                    @csrf
                    <input type="hidden" name="location" id="location">
                    <input type="hidden" name="photo" id="photo">
                    <input type="hidden" name="user_id" id="user_id">

                    <div class="action-row">
                        <button type="button" onClick="resetCamera()" class="btn btn-outline-light rounded-pill py-3 action-btn action-btn-secondary">← Ulang</button>
                        <button type="button" id="captureBtn" onClick="captureAndDetect()" class="btn btn-gold rounded-pill py-3 action-btn">
                            Absen Sekarang
                        </button>
                        <button type="button" id="submitBtn" onClick="submitAttendance()" class="btn btn-gold rounded-pill py-3 action-btn d-none" disabled>
                            Konfirmasi
                        </button>
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
