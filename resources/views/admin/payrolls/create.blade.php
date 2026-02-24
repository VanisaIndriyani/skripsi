@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Generate Payroll</h3>
    <a href="{{ route('admin.payrolls') }}" class="btn btn-secondary rounded-pill px-4">
        <i class="fas fa-arrow-left me-2"></i> Kembali
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.payrolls.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="end_date" class="form-label">Tanggal Selesai</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Apa itu Generate Payroll?</h5>
            <p class="mb-0">Fitur ini berfungsi untuk <strong>menghitung total gaji karyawan</strong> secara otomatis berdasarkan sesi kerja yang telah dihadiri dalam <strong>rentang tanggal yang dipilih</strong> (Contoh: 1 Minggu).</p>
            <hr>
            <p class="mb-0 small">Pastikan semua sesi kerja dan absensi sudah diinput dengan benar sebelum melakukan generate gaji.</p>
        </div>

        <button type="submit" class="btn btn-gold rounded-pill px-5 mt-3">
            <i class="fas fa-cogs me-2"></i> Generate Payroll
        </button>
    </form>
</div>
@endsection
