@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Data Penggajian</h3>
    <a href="{{ route('admin.payrolls.create') }}" class="btn btn-gold rounded-pill px-4">
        <i class="fas fa-plus me-2"></i> Generate Gaji
    </a>
</div>

<div class="card border-0 shadow-sm">
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
                        $periods = $payrolls->map(function($p) {
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
                <span class="text-muted small">Total: <strong>{{ $payrolls->count() }}</strong> Data</span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="payrollTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Periode Gaji</th>
                        <th>Karyawan</th>
                        <th>Total Terima</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                    <tr>
                        <td class="ps-4">
                            @if($payroll->start_date && $payroll->end_date)
                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($payroll->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($payroll->end_date)->format('d M Y') }}</span>
                            @else
                                <span class="fw-bold text-dark">{{ date('F Y', mktime(0, 0, 0, $payroll->month, 10)) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-gold rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold text-dark shadow-sm" style="width: 40px; height: 40px; font-size: 1rem;">
                                    {{ substr($payroll->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $payroll->user->name }}</div>
                                    <small class="text-muted">ID: #{{ str_pad($payroll->user->id, 5, '0', STR_PAD_LEFT) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-success fs-6">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</div>
                            <small class="text-muted">Lunas</small>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.payrolls.print', $payroll->id) }}" target="_blank" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" title="Cetak Slip Gaji">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" width="150" class="mb-3 opacity-50">
                            <p>Belum ada data penggajian.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('periodFilter').addEventListener('change', filterTable);

    function filterTable() {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const selectedPeriod = document.getElementById('periodFilter').value;
        const tableRows = document.querySelectorAll('#payrollTable tbody tr');

        tableRows.forEach(row => {
            const period = row.querySelector('td:nth-child(1)').innerText.trim();
            const name = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
            
            const matchesSearch = name.includes(searchText);
            const matchesPeriod = !selectedPeriod || period === selectedPeriod;

            if (matchesSearch && matchesPeriod) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
