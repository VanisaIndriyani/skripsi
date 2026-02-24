@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Laporan & Rekapitulasi</h3>
    <form action="{{ route('admin.reports.print') }}" method="GET" target="_blank" class="d-flex">
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="start_date" value="{{ $start_date }}">
        <input type="hidden" name="end_date" value="{{ $end_date }}">
        <button type="submit" class="btn btn-gold rounded-pill px-4 shadow-sm">
            <i class="fas fa-file-pdf me-2"></i> Export PDF
        </button>
    </form>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3 text-muted"><i class="fas fa-filter me-2"></i>Filter Laporan</h6>
        <form action="{{ route('admin.reports') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="type" class="form-label small fw-bold">Jenis Laporan</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-list"></i></span>
                    <select class="form-select bg-light" id="type" name="type" onchange="this.form.submit()">
                        <option value="attendance" {{ $type == 'attendance' ? 'selected' : '' }}>Laporan Absensi</option>
                        <option value="payroll" {{ $type == 'payroll' ? 'selected' : '' }}>Laporan Penggajian</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label small fw-bold">Tanggal Mulai</label>
                <input type="date" class="form-control bg-light" id="start_date" name="start_date" value="{{ $start_date }}" required>
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label small fw-bold">Tanggal Selesai</label>
                <input type="date" class="form-control bg-light" id="end_date" name="end_date" value="{{ $end_date }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label d-none d-md-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="fas fa-search me-2"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">
                <i class="fas {{ $type == 'attendance' ? 'fa-calendar-check text-success' : 'fa-money-bill-wave text-gold' }} me-2"></i>
                Hasil Laporan {{ $type == 'attendance' ? 'Absensi' : 'Penggajian' }}
            </h6>
            <span class="badge bg-light text-muted border">
                Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            @if($type == 'attendance')
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Karyawan</th>
                            <th>Waktu Absen</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-gold rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold small text-dark" style="width: 30px; height: 30px;">
                                        {{ substr($row->user->name, 0, 1) }}
                                    </div>
                                    {{ $row->user->name }}
                                </div>
                            </td>
                            <td><i class="far fa-clock me-1 text-muted"></i> {{ \Carbon\Carbon::parse($row->time_in)->format('H:i') }}</td>
                            <td>
                                @if($row->status == 'present') 
                                    <span class="badge bg-success-light text-success rounded-pill px-3">Hadir</span>
                                @elseif($row->status == 'late') 
                                    <span class="badge bg-warning-light text-warning rounded-pill px-3">Terlambat</span>
                                @else 
                                    <span class="badge bg-danger-light text-danger rounded-pill px-3">Absen</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-muted small">{{ $row->location_in ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" width="120" class="mb-3 opacity-50">
                                <p>Tidak ada data absensi pada periode ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Periode</th>
                            <th>Karyawan</th>
                            <th class="text-end pe-4">Total Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">
                                    @if($row->start_date && $row->end_date)
                                        {{ \Carbon\Carbon::parse($row->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($row->end_date)->format('d M Y') }}
                                    @else
                                        {{ date('F Y', mktime(0, 0, 0, $row->month, 10)) }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold small" style="width: 30px; height: 30px;">
                                        {{ substr($row->user->name, 0, 1) }}
                                    </div>
                                    {{ $row->user->name }}
                                </div>
                            </td>
                            <td class="fw-bold text-success text-end pe-4">Rp {{ number_format($row->total_salary, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" width="120" class="mb-3 opacity-50">
                                <p>Tidak ada data penggajian pada periode ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
