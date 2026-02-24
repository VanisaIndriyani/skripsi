@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Daftar Karyawan</h3>
    <button type="button" class="btn btn-gold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
        <i class="fas fa-plus me-2"></i> Tambah Karyawan
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-light border-start-0" placeholder="Cari nama karyawan...">
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <span class="text-muted small">Total: <strong>{{ $employees->count() }}</strong> Karyawan</span>
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="employeeTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Karyawan</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" class="rounded-circle me-3" style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #eee;">
                                @else
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3" style="width: 45px; height: 45px;">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $employee->name }}</div>
                                    <small class="text-muted">{{ $employee->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $employee->phone_number ?? '-' }}</td>
                        <td><span class="badge bg-success-light text-success rounded-pill px-3">Aktif</span></td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $employee->id }}" title="Edit Karyawan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm btn-delete" title="Hapus Karyawan">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Karyawan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body pt-4">
                                        <div class="mb-3 text-center">
                                            @if($employee->photo)
                                                <img src="{{ asset('storage/' . $employee->photo) }}" class="rounded-circle mb-3 shadow-sm" width="100" height="100" style="object-fit: cover;">
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                                            <input type="text" class="form-control" name="name" value="{{ $employee->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">No. HP (Opsional)</label>
                                            <input type="text" class="form-control" name="phone_number" value="{{ $employee->phone_number }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Ganti Foto</label>
                                            <input type="file" class="form-control" name="photo" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0 pb-4">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-gold rounded-pill px-4">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" width="150" class="mb-3 opacity-50">
                            <p>Belum ada data karyawan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">No. HP (Opsional)</label>
                        <input type="text" class="form-control" name="phone_number" placeholder="Contoh: 08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Upload Foto Wajah (Wajib)</label>
                        <input type="file" class="form-control" name="photo" accept="image/*" required>
                        <small class="text-muted d-block mt-1">Foto ini akan digunakan untuk sistem deteksi wajah.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold rounded-pill px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('#employeeTable tbody tr');

        tableRows.forEach(row => {
            let name = row.querySelector('td:nth-child(1)').innerText.toLowerCase();
            if (name.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection
