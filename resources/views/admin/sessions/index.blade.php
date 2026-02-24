@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Manajemen Sesi Kerja</h3>
    <button type="button" class="btn btn-gold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createSessionModal">
        <i class="fas fa-plus me-2"></i> Buat Sesi Baru
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-light border-start-0" placeholder="Cari sesi kerja...">
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <span class="text-muted small">Total: <strong>{{ $sessions->count() }}</strong> Sesi</span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        @if($errors->any())
            <div class="alert alert-danger m-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="sessionTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tanggal & Waktu</th>
                        <th>Informasi Sesi</th>
                        <th>Peserta</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $session->date->format('d M Y') }}</div>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $session->title }}</div>
                            <span class="badge bg-light text-dark border mt-1">Rp {{ number_format($session->wage, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info-light text-info rounded-pill">
                                    <i class="fas fa-users me-1"></i> {{ $session->attendances_count }} Hadir
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($session->is_active)
                                <span class="badge bg-success-light text-success rounded-pill px-3">Aktif</span>
                            @else
                                <span class="badge bg-secondary-light text-secondary rounded-pill px-3">Selesai</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" onclick="showAttendees({{ $session->id }})" title="Lihat Peserta">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <form action="{{ route('admin.sessions.toggle', $session->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm rounded-circle shadow-sm {{ $session->is_active ? 'btn-success text-white' : 'btn-light text-muted' }}" 
                                            title="{{ $session->is_active ? 'Nonaktifkan Sesi' : 'Aktifkan Sesi' }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.sessions.destroy', $session->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm btn-delete" title="Hapus Sesi">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" width="150" class="mb-3 opacity-50">
                            <p>Belum ada sesi kerja.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Session Modal -->
<div class="modal fade" id="createSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Sesi Kerja Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.sessions.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Sesi</label>
                        <input type="text" class="form-control" name="title" required placeholder="Contoh: Bongkar Muatan Truk A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Tanggal</label>
                        <input type="date" class="form-control" name="date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Jam Mulai</label>
                            <input type="time" class="form-control" name="start_time" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Jam Selesai</label>
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Upah Per Sesi (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" class="form-control" name="wage" required placeholder="150000">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold rounded-pill px-4">Simpan Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attendees Modal -->
<div class="modal fade" id="attendeesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Daftar Hadir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div id="attendeesList" class="list-group list-group-flush">
                    <!-- List will be populated by JS -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-gold" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Search Functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('#sessionTable tbody tr');

        tableRows.forEach(row => {
            let text = row.innerText.toLowerCase();
            if (text.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Show Attendees
    function showAttendees(sessionId) {
        const modal = new bootstrap.Modal(document.getElementById('attendeesModal'));
        const listContainer = document.getElementById('attendeesList');
        
        listContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-gold" role="status"></div>
                <p class="mt-2 text-muted small">Memuat data...</p>
            </div>
        `;
        
        modal.show();

        fetch(`/admin/sessions/${sessionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.attendees.length === 0) {
                    listContainer.innerHTML = `
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-user-slash fa-2x mb-3 opacity-50"></i>
                            <p>Belum ada karyawan yang absen di sesi ini.</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                data.attendees.forEach(attendee => {
                    html += `
                        <div class="list-group-item d-flex align-items-center py-3 border-0 border-bottom">
                            <img src="${attendee.photo || 'https://ui-avatars.com/api/?name=' + attendee.name}" 
                                 class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">${attendee.name}</h6>
                                <small class="text-muted"><i class="far fa-clock me-1"></i> Absen: ${attendee.time_in}</small>
                            </div>
                            <span class="badge bg-success-light text-success rounded-pill">Hadir</span>
                        </div>
                    `;
                });
                listContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                listContainer.innerHTML = '<p class="text-danger text-center">Gagal memuat data.</p>';
            });
    }
</script>
@endsection
