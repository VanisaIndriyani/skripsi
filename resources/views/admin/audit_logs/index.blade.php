@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Audit Log Aktivitas</h3>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <form action="{{ route('admin.audit_logs') }}" method="GET" class="row align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari aktivitas atau nama admin..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-gold w-100">Cari</button>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <span class="text-muted small">Total: <strong>{{ $logs->total() }}</strong> Aktivitas</span>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>Admin/Sistem</th>
                        <th>Aktivitas</th>
                        <th>Keterangan</th>
                        <th class="text-end pe-4">Informasi Perangkat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <small class="text-muted d-block">{{ $log->created_at->format('d/m/Y') }}</small>
                                <span class="fw-bold">{{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-user-shield text-secondary" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <span class="fw-medium text-dark">{{ $log->user ? $log->user->name : 'System' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-gold text-dark">{{ $log->activity }}</span>
                            </td>
                            <td>
                                <p class="mb-0 text-muted small" style="max-width: 300px;">{{ $log->description }}</p>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex flex-column align-items-end">
                                    <code class="small text-primary">{{ $log->ip_address }}</code>
                                    <small class="text-muted truncate" style="max-width: 200px;" title="{{ $log->user_agent }}">
                                        {{ Str::limit($log->user_agent, 30) }}
                                    </small>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-history fa-3x mb-3 opacity-20"></i>
                                    <p>Belum ada aktivitas yang tercatat</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($logs->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
