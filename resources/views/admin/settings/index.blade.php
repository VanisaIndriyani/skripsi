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

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <label class="form-label fw-bold mb-0">Pilih Lokasi di Peta</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="useMyLocationBtn">
                                    <i class="fas fa-location-crosshairs me-1"></i> Lokasi Saya
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="centerMapBtn">
                                    <i class="fas fa-crosshairs me-1"></i> Center
                                </button>
                            </div>
                        </div>
                        <div class="form-text">Klik peta untuk menandai lokasi kantor. Marker bisa digeser (drag) untuk presisi.</div>
                        <div id="officeMap" class="office-map mt-2"></div>
                    </div>

                    <!-- New Location Display Section -->
                    <div id="location_preview_container" class="mb-4 d-none">
                        <div class="alert alert-light border-gold text-dark p-3 rounded shadow-sm">
                            <h6 class="fw-bold mb-2 small text-gold text-uppercase" style="letter-spacing: 1px;">Konfirmasi Lokasi</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-gold-light p-2 rounded-circle me-3">
                                    <i class="fas fa-map-marker-alt text-gold"></i>
                                </div>
                                <div>
                                    <div id="location_name" class="fw-bold mb-0">Memuat lokasi...</div>
                                    <div id="location_detail" class="text-muted small">Mencari detail alamat...</div>
                                </div>
                            </div>
                        </div>
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

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    .border-gold { border: 1px solid #D4AF37 !important; }
    .bg-gold-light { background-color: rgba(212, 175, 55, 0.1); }
    .text-gold { color: #D4AF37; }
    .office-map {
        width: 100%;
        height: 320px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    @media (max-width: 576px) {
        .office-map { height: 280px; }
    }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const latInput = document.getElementById('office_latitude');
        const lngInput = document.getElementById('office_longitude');
        const radiusInput = document.getElementById('office_radius');
        const previewContainer = document.getElementById('location_preview_container');
        const locationName = document.getElementById('location_name');
        const locationDetail = document.getElementById('location_detail');
        const useMyLocationBtn = document.getElementById('useMyLocationBtn');
        const centerMapBtn = document.getElementById('centerMapBtn');

        let timeout = null;

        function parseNum(val) {
            const n = Number(String(val || '').trim());
            return Number.isFinite(n) ? n : null;
        }

        function getLatLngFromInputs() {
            const lat = parseNum(latInput.value);
            const lng = parseNum(lngInput.value);
            if (lat === null || lng === null) return null;
            return { lat, lng };
        }

        function setInputsFromLatLng(lat, lng) {
            latInput.value = Number(lat).toFixed(6);
            lngInput.value = Number(lng).toFixed(6);
            fetchLocation();
        }

        function fetchLocation() {
            const ll = getLatLngFromInputs();
            if (!ll) {
                previewContainer.classList.add('d-none');
                return;
            }

            previewContainer.classList.remove('d-none');
            locationName.innerText = "Memuat lokasi...";
            locationDetail.innerText = "Mencari detail alamat...";

            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${ll.lat}&lon=${ll.lng}`;
                fetch(url, {
                    headers: { 'User-Agent': 'PMS-Attendance-System' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        const addr = data.address;
                        const village = addr.village || addr.suburb || addr.neighbourhood || addr.hamlet || "Desa tidak ditemukan";
                        const district = addr.city_district || addr.county || addr.city || "Kecamatan tidak ditemukan";
                        const province = addr.state || "";
                        locationName.innerText = `${String(village).toUpperCase()}`;
                        locationDetail.innerText = `Kecamatan: ${district}, ${province}`;
                    } else {
                        locationName.innerText = "Lokasi tidak ditemukan";
                        locationDetail.innerText = "Pastikan koordinat benar";
                    }
                })
                .catch(() => {
                    locationName.innerText = "Gagal memuat lokasi";
                    locationDetail.innerText = "Koneksi internet bermasalah";
                });
            }, 900);
        }

        const initial = getLatLngFromInputs() || { lat: -6.175392, lng: 106.827153 };
        const map = L.map('officeMap', { zoomControl: true }).setView([initial.lat, initial.lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const marker = L.marker([initial.lat, initial.lng], { draggable: true }).addTo(map);

        let radiusCircle = null;
        function updateRadiusCircle() {
            const ll = getLatLngFromInputs() || initial;
            const r = Math.max(10, parseNum(radiusInput.value) || 100);
            if (radiusCircle) radiusCircle.remove();
            radiusCircle = L.circle([ll.lat, ll.lng], {
                radius: r,
                color: '#D4AF37',
                weight: 2,
                fillColor: '#D4AF37',
                fillOpacity: 0.10
            }).addTo(map);
        }

        function moveMarker(lat, lng, shouldCenter = false) {
            marker.setLatLng([lat, lng]);
            if (radiusCircle) radiusCircle.setLatLng([lat, lng]);
            if (shouldCenter) map.setView([lat, lng], Math.max(map.getZoom(), 16), { animate: true });
        }

        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            moveMarker(lat, lng, false);
            setInputsFromLatLng(lat, lng);
            updateRadiusCircle();
        });

        marker.on('dragend', () => {
            const ll = marker.getLatLng();
            setInputsFromLatLng(ll.lat, ll.lng);
            updateRadiusCircle();
        });

        function syncMapToInputs(center = false) {
            const ll = getLatLngFromInputs();
            if (!ll) return;
            moveMarker(ll.lat, ll.lng, center);
            updateRadiusCircle();
        }

        latInput.addEventListener('input', () => syncMapToInputs(false));
        lngInput.addEventListener('input', () => syncMapToInputs(false));
        radiusInput.addEventListener('input', updateRadiusCircle);

        if (centerMapBtn) {
            centerMapBtn.addEventListener('click', () => syncMapToInputs(true));
        }

        if (useMyLocationBtn) {
            useMyLocationBtn.addEventListener('click', () => {
                if (!navigator.geolocation) return;
                useMyLocationBtn.disabled = true;
                navigator.geolocation.getCurrentPosition((pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    setInputsFromLatLng(lat, lng);
                    moveMarker(lat, lng, true);
                    updateRadiusCircle();
                }, () => {
                }, { enableHighAccuracy: true, timeout: 6000, maximumAge: 0 });
                setTimeout(() => { useMyLocationBtn.disabled = false; }, 1200);
            });
        }

        fetchLocation();
        updateRadiusCircle();
    });
</script>
@endsection
