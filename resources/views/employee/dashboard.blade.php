@extends('layouts.employee')

@section('content')

<div class="row">
    <div class="col-12 text-center mb-4">
        <h4 class="fw-bold mb-1">Selamat Datang,</h4>
        <h5 class="text-gold">{{ Auth::user()->name }}</h5>
        <p class="text-muted small">{{ date('l, d F Y') }}</p>
    </div>

    <!-- Attendance Card -->
    <div class="col-12">
        <div class="card p-4 text-center">
            @if(!$attendance)
                <h5 class="mb-4">Absen Masuk</h5>
                
                <form id="attendanceForm" action="{{ route('attendance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Hidden location input -->
                    <input type="hidden" name="location" id="location">
                    <input type="hidden" name="photo" id="photo">
                    
                    <div id="my_camera" class="mx-auto mb-3 rounded" style="width: 100%; max-width: 320px;"></div>
                    <div id="results" class="mx-auto mb-3" style="display:none;"></div>

                    <div class="scan-btn-wrapper d-flex justify-content-center mb-3">
                        <button type="button" onClick="take_snapshot()" class="btn btn-gold rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border: 4px solid rgba(255,255,255,0.1);">
                             <i class="fas fa-camera fa-2x"></i>
                        </button>
                    </div>
                    <p class="small text-muted mt-2">Klik tombol kamera untuk ambil foto & absen</p>
                </form>

            @elseif($attendance->status == 'present' && !$attendance->time_out)
                <h5 class="mb-4">Absen Pulang</h5>
                <div class="alert alert-success mb-4">
                    Anda masuk jam: <strong>{{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}</strong>
                </div>

                <form id="attendanceOutForm" action="{{ route('attendance.update', $attendance->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="location" id="location_out">
                    <input type="hidden" name="photo" id="photo_out">
                    
                    <div id="my_camera_out" class="mx-auto mb-3 rounded" style="width: 100%; max-width: 320px;"></div>

                    <div class="d-grid gap-2">
                        <button type="button" onClick="take_snapshot_out()" class="btn btn-danger py-3 fw-bold rounded-pill">
                            <i class="fas fa-sign-out-alt me-2"></i> AMBIL FOTO & CHECK OUT
                        </button>
                    </div>
                </form>

            @else
                <div class="py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>Selesai!</h4>
                    <p class="text-muted">Anda sudah menyelesaikan absensi hari ini.</p>
                    <div class="row mt-4">
                        <div class="col-6 border-end">
                            <small class="d-block text-muted">Masuk</small>
                            <strong>{{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="d-block text-muted">Pulang</small>
                            <strong>{{ \Carbon\Carbon::parse($attendance->time_out)->format('H:i') }}</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-12 mt-3">
        <h6 class="mb-3 fw-bold">Statistik Bulan Ini</h6>
        <div class="row g-3">
            <div class="col-6">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                            <i class="fas fa-calendar-check text-primary"></i>
                        </div>
                        <div>
                            <small class="d-block text-muted">Hadir</small>
                            <h5 class="mb-0 fw-bold">
                                {{ \App\Models\Attendance::where('user_id', Auth::id())->whereMonth('date', date('m'))->count() }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                            <i class="fas fa-clock text-danger"></i>
                        </div>
                        <div>
                            <small class="d-block text-muted">Terlambat</small>
                            <h5 class="mb-0 fw-bold">0</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Webcam.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script>
    // Initialize Webcam
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    
    // Check which camera container exists
    if(document.getElementById('my_camera')) {
        Webcam.attach('#my_camera');
    }
    if(document.getElementById('my_camera_out')) {
        Webcam.attach('#my_camera_out');
    }

    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            document.getElementById('photo').value = data_uri;
            document.getElementById('attendanceForm').submit();
        });
    }

    function take_snapshot_out() {
        Webcam.snap(function(data_uri) {
            document.getElementById('photo_out').value = data_uri;
            document.getElementById('attendanceOutForm').submit();
        });
    }

    // Simple Geolocation Script
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const loc = position.coords.latitude + "," + position.coords.longitude;
            if(document.getElementById('location')) document.getElementById('location').value = loc;
            if(document.getElementById('location_out')) document.getElementById('location_out').value = loc;
        });
    }
</script>
@endsection
