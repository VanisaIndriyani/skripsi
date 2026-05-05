@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Manajemen Penggajian</h3>
        <p class="text-muted small mb-0">Kelola dan cetak slip gaji karyawan harian.</p>
    </div>
    <a href="{{ route('admin.payrolls.create') }}" class="btn btn-gold rounded-pill px-4 shadow-sm">
        <i class="fas fa-plus-circle me-2"></i> Generate Gaji Baru
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-light border-start-0" placeholder="Cari nama karyawan...">
                </div>
            </div>
            <div class="col-md-4">
                <select id="periodFilter" class="form-select bg-light">
                    <option value="">Semua Periode</option>
                    @php
                        $periods = $payrolls->getCollection()->map(function($p) {
                            if($p->start_date && $p->end_date) {
                                return \Carbon\Carbon::parse($p->start_date)->format('d M') . ' - ' . \Carbon\Carbon::parse($p->end_date)->format('d M Y');
                            }
                            return date('F Y', mktime(0, 0, 0, $p->month, 10));
                        })->unique();
                    @endphp
                    @foreach($periods as $period)
                        <option value="{{ $period }}">{{ $period }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                <span class="badge bg-light text-dark border py-2 px-3">
                    <i class="fas fa-database me-1 text-gold"></i> Total: <strong>{{ $payrolls->total() }}</strong> Data
                </span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3 border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="payrollTable">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Karyawan</th>
                        <th>Periode Pembayaran</th>
                        <th class="text-center">Sesi Kerja</th>
                        <th>Total Diterima</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-gold-light text-gold fw-bold d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px;">
                                    {{ substr($payroll->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $payroll->user->name }}</div>
                                    <small class="text-muted"><i class="fas fa-id-badge me-1"></i> #{{ str_pad($payroll->user->id, 4, '0', STR_PAD_LEFT) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($payroll->start_date && $payroll->end_date)
                                <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($payroll->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($payroll->end_date)->format('d M Y') }}</div>
                                <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> Rentang Waktu</small>
                            @else
                                <div class="fw-medium text-dark">{{ date('F Y', mktime(0, 0, 0, $payroll->month, 10)) }}</div>
                                <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> Bulanan</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-light text-info rounded-pill px-3">
                                <i class="fas fa-briefcase me-1"></i> {{ $payroll->session_count }} Sesi
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</div>
                            <small class="text-muted">Nett Pay</small>
                        </td>
                        <td>
                            @if($payroll->status == 'paid')
                                <span class="badge bg-success-light text-success rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> Lunas</span>
                            @else
                                <span class="badge bg-warning-light text-warning rounded-pill px-3"><i class="fas fa-clock me-1"></i> Pending</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.payrolls.print', $payroll->id) }}" target="_blank" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm p-2" title="Cetak Slip Gaji">
                                    <i class="fas fa-print fa-fw"></i>
                                </a>
                                <button class="btn btn-sm btn-light text-info rounded-circle shadow-sm p-2" title="Detail">
                                    <i class="fas fa-info-circle fa-fw"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" width="180" class="mb-3 opacity-50">
                                <h5 class="text-muted fw-normal">Belum ada data penggajian.</h5>
                                <p class="text-muted small">Klik tombol "Generate Gaji" untuk membuat slip gaji baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end px-4 py-3">
            {{ $payrolls->links() }}
        </div>
    </div>
</div>

<style>
    .bg-gold-light { background-color: rgba(212, 175, 55, 0.1); }
    .bg-info-light { background-color: rgba(13, 202, 240, 0.1); }
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
    .text-gold { color: #D4AF37; }
    .btn-gold { background-color: #D4AF37; color: white; border: none; }
    .btn-gold:hover { background-color: #B8962E; color: white; }
    
    .table thead th {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr {
        transition: all 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: rgba(212, 175, 55, 0.02);
    }
</style>

<script>
    // Search Functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('#payrollTable tbody tr');

        tableRows.forEach(row => {
            let text = row.innerText.toLowerCase();
            if (text.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Period Filter
    document.getElementById('periodFilter').addEventListener('change', function() {
        let filterValue = this.value;
        let tableRows = document.querySelectorAll('#payrollTable tbody tr');

        tableRows.forEach(row => {
            if (filterValue === '') {
                row.style.display = '';
            } else {
                let periodText = row.cells[1].innerText;
                if (periodText.includes(filterValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
</script>
@endsection
