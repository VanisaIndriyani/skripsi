@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Monitoring Absensi</h3>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-light border-start-0" placeholder="Cari karyawan...">
                </div>
            </div>
            <div class="col-md-4">
                <input type="date" id="dateFilter" class="form-control bg-light" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                <span class="text-muted small">Total: <strong>{{ $attendances->count() }}</strong> Absensi</span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="attendanceTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tanggal & Waktu</th>
                        <th>Karyawan</th>
                        <th>Bukti Foto</th>
                        <th>Status Kehadiran</th>
                        <th class="text-end pe-4">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                    <tr data-date="{{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}">
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</div>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }} WIB
                            </small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-gold rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold text-dark shadow-sm" style="width: 40px; height: 40px; font-size: 1rem;">
                                    {{ substr($attendance->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $attendance->user->name }}</div>
                                    <small class="text-muted">ID: #{{ str_pad($attendance->user->id, 5, '0', STR_PAD_LEFT) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($attendance->photo_in)
                                <img src="{{ asset('storage/' . $attendance->photo_in) }}" 
                                     class="rounded border shadow-sm" 
                                     style="width: 60px; height: 60px; object-fit: cover; cursor: pointer; transition: transform 0.2s;"
                                     onmouseover="this.style.transform='scale(1.1)'"
                                     onmouseout="this.style.transform='scale(1)'"
                                     onclick="showPhotoModal('{{ asset('storage/' . $attendance->photo_in) }}', '{{ $attendance->user->name }}', '{{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}')">
                            @else
                                <span class="badge bg-light text-muted border">No Photo</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->status == 'present')
                                <span class="badge bg-success-light text-success rounded-pill px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> Hadir
                                </span>
                            @elseif($attendance->status == 'late')
                                <span class="badge bg-warning-light text-warning rounded-pill px-3 py-2">
                                    <i class="fas fa-exclamation-circle me-1"></i> Terlambat
                                </span>
                            @else
                                <span class="badge bg-danger-light text-danger rounded-pill px-3 py-2">
                                    <i class="fas fa-times-circle me-1"></i> Absen
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($attendance->location_in)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $attendance->location_in }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-map-marker-alt me-1"></i> Maps
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" width="150" class="mb-3 opacity-50">
                            <p>Belum ada data absensi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Single Photo Modal -->
<div class="modal fade" id="globalPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-secondary shadow-lg">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white fw-bold" id="photoModalTitle">Bukti Absensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0 bg-black position-relative">
                <img id="photoModalImage" src="" class="img-fluid" style="max-height: 80vh;">
                <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-dark text-white d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.7);">
                    <span id="photoModalName" class="fw-bold"></span>
                    <span id="photoModalTime" class="badge bg-gold text-dark"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Search & Filter Functionality
    const searchInput = document.getElementById('searchInput');
    const dateFilter = document.getElementById('dateFilter');
    const tableRows = document.querySelectorAll('#attendanceTable tbody tr');

    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const filterDate = dateFilter.value;

        tableRows.forEach(row => {
            const name = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
            const rowDate = row.getAttribute('data-date');
            
            // Check matches
            const matchesSearch = name.includes(searchText);
            // If filterDate is empty, show all dates. If set, must match exactly.
            const matchesDate = !filterDate || rowDate === filterDate;

            if (matchesSearch && matchesDate) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    dateFilter.addEventListener('change', filterTable);

    // Photo Modal Logic
    function showPhotoModal(imageSrc, name, time) {
        document.getElementById('photoModalImage').src = imageSrc;
        document.getElementById('photoModalTitle').innerText = 'Foto Absensi - ' + name;
        document.getElementById('photoModalName').innerText = name;
        document.getElementById('photoModalTime').innerText = 'Pukul ' + time;
        
        const modal = new bootstrap.Modal(document.getElementById('globalPhotoModal'));
        modal.show();
    }
</script>

<style>
    .bg-gradient-dark {
        background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    }
</style>
@endsection
