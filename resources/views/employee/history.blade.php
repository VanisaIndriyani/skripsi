@extends('layouts.employee')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h4 class="fw-bold mb-3">Riwayat Absensi</h4>
        
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($attendance->date)->format('d M') }}</span>
                                <small class="d-block text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('Y') }}</small>
                            </td>
                            <td>
                                <span class="text-success">{{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}</span>
                            </td>
                            <td>
                                @if($attendance->time_out)
                                    <span class="text-danger">{{ \Carbon\Carbon::parse($attendance->time_out)->format('H:i') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($attendance->status == 'present')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($attendance->status == 'late')
                                    <span class="badge bg-warning text-dark">Terlambat</span>
                                @else
                                    <span class="badge bg-danger">Absen</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <p class="mb-0">Belum ada riwayat absensi.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
