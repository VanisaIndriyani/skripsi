@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Pengaturan Lokasi Kantor</h3>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="office_latitude" class="form-label fw-bold">Latitude Kantor</label>
                        <input type="text" class="form-control" id="office_latitude" name="office_latitude" value="{{ old('office_latitude', $settings['office_latitude']) }}" required>
                        <div class="form-text">Contoh: -6.175392 (Monas)</div>
                        @error('office_latitude')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="office_longitude" class="form-label fw-bold">Longitude Kantor</label>
                        <input type="text" class="form-control" id="office_longitude" name="office_longitude" value="{{ old('office_longitude', $settings['office_longitude']) }}" required>
                        <div class="form-text">Contoh: 106.827153 (Monas)</div>
                        @error('office_longitude')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="office_radius" class="form-label fw-bold">Radius Maksimum (Meter)</label>
                        <input type="number" class="form-control" id="office_radius" name="office_radius" value="{{ old('office_radius', $settings['office_radius']) }}" required min="10">
                        <div class="form-text">Jarak maksimum yang diizinkan untuk absensi.</div>
                        @error('office_radius')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-gold rounded-pill px-4 fw-bold">
                            <i class="fas fa-save me-2"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-gold text-dark">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                <p class="small mb-0">
                    Pastikan koordinat Latitude dan Longitude akurat sesuai lokasi kantor Anda. Anda bisa mendapatkan koordinat ini melalui Google Maps.
                </p>
                <hr class="border-dark opacity-25">
                <p class="small mb-0">
                    <strong>Cara mendapatkan koordinat:</strong><br>
                    1. Buka Google Maps<br>
                    2. Klik kanan pada lokasi kantor<br>
                    3. Klik pada angka koordinat yang muncul untuk menyalinnya.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
