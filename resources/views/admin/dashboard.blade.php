@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Dashboard Overview</h3>
    <span class="text-muted">{{ date('l, d F Y') }}</span>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Card 1: Total Employees -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Total Karyawan</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $totalEmployees }}</h2>
                    </div>
                    <div class="icon-box bg-gold-light text-gold rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(212, 175, 55, 0.15);">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-success small fw-bold"><i class="fas fa-arrow-up"></i> Aktif</span>
                    <span class="text-muted small ms-1">Terdaftar di sistem</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Attendance Today -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Hadir Hari Ini</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $totalPresentToday }}</h2>
                    </div>
                    <div class="icon-box bg-success-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.15);">
                        <i class="fas fa-user-check fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 5px;">
                        @php $percentage = $totalEmployees > 0 ? ($totalPresentToday / $totalEmployees) * 100 : 0; @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="text-muted small mt-1 d-block">{{ round($percentage) }}% Kehadiran</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Payroll -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Total Penggajian</h6>
                        <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</h4>
                    </div>
                    <div class="icon-box bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(13, 110, 253, 0.15);">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small">Akumulasi Total</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4">
    <!-- Attendance Chart -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-line me-2 text-gold"></i>Tren Kehadiran (7 Hari Terakhir)</h6>
            </div>
           <div class="card-body">
    <div style="height:300px;">
        <canvas id="attendanceChart"></canvas>
    </div>
</div>
        </div>
    </div>

    <!-- Payroll Summary Chart -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-pie me-2 text-primary"></i>Ringkasan Penggajian</h6>
            </div>
           <div style="height:300px;">
    <canvas id="payrollChart"></canvas>
</div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attendance Chart
    const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctxAttendance, {
        type: 'line',
        data: {
            labels: {!! json_encode($attendanceLabels) !!},
            datasets: [{
                label: 'Jumlah Hadir',
                data: {!! json_encode($attendanceData) !!},
                borderColor: '#D4AF37',
                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#D4AF37',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Payroll Chart
    const ctxPayroll = document.getElementById('payrollChart').getContext('2d');
    new Chart(ctxPayroll, {
        type: 'bar',
        data: {
            labels: {!! json_encode($payrollLabels) !!},
            datasets: [{
                label: 'Total Gaji (Rp)',
                data: {!! json_encode($payrollData) !!},
                backgroundColor: '#0d6efd',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID'); // Format as IDR
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection
