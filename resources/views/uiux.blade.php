<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI/UX Mockup - PMS Absensi</title>
    <style>
        :root {
            --bg: #f2f2f2;
            --surface: #ffffff;
            --surface-2: #f7f7f7;
            --border: #d4d4d4;
            --text: #111111;
            --muted: #666666;
            --invert: #ffffff;
            --white: #111111;
            --shadow: rgba(0,0,0,0.08);
            --r-card: 22px;
            --r-pill: 999px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Helvetica Neue", sans-serif;
        }
        .page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 28px 20px 64px;
        }
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.2px;
        }
        .subtitle {
            color: var(--muted);
            font-size: 13px;
            margin-top: 6px;
            max-width: 70ch;
            line-height: 1.5;
        }
        .chip {
            border: 1px solid var(--border);
            border-radius: var(--r-pill);
            padding: 10px 12px;
            color: var(--muted);
            font-size: 12px;
            white-space: nowrap;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }
        @media (min-width: 980px) {
            .grid { grid-template-columns: 1fr 1fr; }
        }
        .section {
            border: 1px solid var(--border);
            border-radius: var(--r-card);
            overflow: hidden;
            background: var(--surface-2);
            box-shadow: 0 14px 40px var(--shadow);
        }
        .section.wide {
            grid-column: 1 / -1;
        }
        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
        }
        .section-title {
            font-size: 13px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            font-weight: 800;
        }
        .section-note {
            font-size: 12px;
            color: var(--muted);
        }
        .canvas {
            padding: 16px;
        }
        .device {
            width: 390px;
            height: 844px;
            margin: 0 auto;
            border: 1px solid var(--border);
            border-radius: 34px;
            padding: 18px;
            background: var(--bg);
            position: relative;
        }
        .device .safe {
            height: 100%;
            border-radius: 26px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: var(--surface-2);
        }
        .mobile {
            padding: 18px 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .logo {
            width: 88px;
            height: 88px;
            border-radius: 999px;
            border: 2px solid var(--white);
            display: grid;
            place-items: center;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .h1 { font-size: 18px; font-weight: 900; letter-spacing: 1px; }
        .p { font-size: 13px; color: var(--muted); }
        .card {
            border: 1px solid var(--border);
            border-radius: var(--r-card);
            background: var(--surface);
            padding: 16px;
        }
        .clock {
            font-size: 56px;
            font-weight: 900;
            line-height: 1;
        }
        .date {
            margin-top: 8px;
            font-size: 12px;
            letter-spacing: 1px;
            color: var(--muted);
            text-transform: uppercase;
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--border);
            border-radius: var(--r-pill);
            padding: 10px 12px;
            font-size: 12px;
            color: var(--muted);
        }
        .btn {
            border-radius: var(--r-pill);
            height: 54px;
            display: grid;
            place-items: center;
            font-weight: 900;
            letter-spacing: 0.6px;
            border: 1px solid var(--white);
            background: var(--white);
            color: var(--invert);
        }
        .btn.secondary {
            background: transparent;
            color: var(--white);
            border: 1px solid var(--white);
        }
        .btn.disabled {
            opacity: 0.4;
            pointer-events: none;
        }
        .row {
            display: flex;
            gap: 10px;
        }
        .row > * { flex: 1; }
        .camera {
            border: 2px solid var(--white);
            border-radius: 26px;
            height: 420px;
            background: linear-gradient(180deg, rgba(0,0,0,0.06), rgba(0,0,0,0.02));
            position: relative;
            overflow: hidden;
        }
        .frame {
            position: absolute;
            inset: 14px;
            border-radius: 18px;
            border: 1px dashed rgba(0,0,0,0.35);
        }
        .scanline {
            position: absolute;
            left: 0;
            right: 0;
            top: 46%;
            height: 2px;
            background: rgba(0,0,0,0.8);
            opacity: 0.45;
        }
        .status {
            border-radius: var(--r-pill);
            border: 1px solid var(--border);
            padding: 10px 12px;
            font-size: 12px;
            color: var(--muted);
            display: flex;
            justify-content: center;
            text-align: center;
        }
        .name {
            border: 1px solid var(--border);
            background: transparent;
            color: var(--white);
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-weight: 900;
            letter-spacing: 0.4px;
        }
        .desktop {
            width: 100%;
            min-height: 720px;
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            background: var(--bg);
        }
        .topbar {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 18px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-badge {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            border: 1px solid var(--white);
        }
        .nav {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
        }
        .nav .active {
            color: var(--white);
            font-weight: 900;
        }
        .profile {
            border: 1px solid var(--border);
            border-radius: var(--r-pill);
            padding: 8px 10px;
            color: var(--muted);
            font-size: 12px;
        }
        .layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: calc(720px - 64px);
        }
        .sidebar {
            border-right: 1px solid var(--border);
            background: var(--surface-2);
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .menu {
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 14px;
            display: flex;
            align-items: center;
            padding: 0 12px;
            color: var(--muted);
            font-size: 12px;
        }
        .menu.active {
            color: var(--white);
            border-color: rgba(0,0,0,0.25);
            background: rgba(0,0,0,0.04);
            font-weight: 900;
        }
        .content {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            overflow: visible;
        }
        .cards4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }
        .kpi {
            border: 1px solid var(--border);
            border-radius: 18px;
            background: var(--surface);
            padding: 14px;
        }
        .kpi .label { color: var(--muted); font-size: 11px; letter-spacing: 0.6px; text-transform: uppercase; }
        .kpi .value { margin-top: 8px; font-size: 22px; font-weight: 900; }
        .table {
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
        }
        .thead, .trow {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr 0.9fr 1.2fr 0.7fr;
            gap: 10px;
            padding: 12px 12px;
            align-items: center;
        }
        .thead {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 11px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }
        .trow {
            border-bottom: 1px solid var(--border);
            color: var(--white);
            font-size: 12px;
        }
        .trow:last-child { border-bottom: none; }
        .tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 26px;
            padding: 0 10px;
            border-radius: var(--r-pill);
            border: 1px solid rgba(0,0,0,0.35);
            font-size: 11px;
            color: var(--white);
        }
        .login-card {
            width: 420px;
            margin: 0 auto;
            margin-top: 64px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: var(--surface);
            padding: 28px;
        }
        .input {
            height: 44px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: transparent;
            display: flex;
            align-items: center;
            padding: 0 12px;
            color: var(--muted);
            font-size: 12px;
        }
        .form {
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .field .label {
            font-size: 11px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
            font-weight: 900;
        }
        .field .control {
            height: 44px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            padding: 0 12px;
            color: var(--text);
            font-size: 12px;
        }
        .field .control.placeholder {
            color: var(--muted);
        }
        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }
        .actions .btn {
            height: 46px;
        }
        .split-2 {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 12px;
        }
        .spacer { flex: 1; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div>
                <div class="title">UI/UX Mockup (Hitam–Putih)</div>
                <div class="subtitle">Halaman ini khusus untuk kebutuhan desain UI/UX. Semua tampilan dibuat statis sebagai contoh seperti layout Figma, lalu kamu bisa screenshot per menu. Akses: /uiux (hanya aktif saat APP_DEBUG=true).</div>
            </div>
            <div class="chip">Mobile 390×844 • Desktop 1440×900</div>
        </div>

        <div class="grid">
            <div class="section">
                <div class="section-head">
                    <div class="section-title">Kiosk - Landing</div>
                    <div class="section-note">Public</div>
                </div>
                <div class="canvas">
                    <div class="device">
                        <div class="safe">
                            <div class="mobile">
                                <div style="display:flex; justify-content:center; margin-top:10px;">
                                    <div class="logo">PMS</div>
                                </div>
                                <div style="text-align:center;">
                                    <div class="h1">PT PUTRA MUARA SUKSES</div>
                                    <div class="p">Sistem Absensi Kiosk</div>
                                </div>
                                <div class="card">
                                    <div class="clock">12:45</div>
                                    <div class="date">SENIN, 26 APRIL 2026</div>
                                </div>
                                <div style="display:flex; justify-content:center;">
                                    <div class="pill">Sesi Aktif: SHIFT PAGI • 08:00–12:00</div>
                                </div>
                                <div class="btn">MULAI ABSENSI</div>
                                <div class="spacer"></div>
                                <div style="text-align:center; font-size:12px; color:var(--muted);">Admin Login</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-head">
                    <div class="section-title">Kiosk - Scan</div>
                    <div class="section-note">Verifikasi + Foto</div>
                </div>
                <div class="canvas">
                    <div class="device">
                        <div class="safe">
                            <div class="mobile">
                                <div style="display:flex; justify-content:flex-end;">
                                    <div class="pill" style="padding:8px 10px;">X</div>
                                </div>
                                <div class="camera">
                                    <div class="frame"></div>
                                    <div class="scanline"></div>
                                </div>
                                <div class="card" style="display:flex; flex-direction:column; gap:10px;">
                                    <div class="status">Kedip 1x untuk verifikasi</div>
                                    <div class="name">Nama Karyawan</div>
                                    <div class="btn disabled">AMBIL FOTO</div>
                                    <div class="row">
                                        <div class="btn secondary">ULANG</div>
                                        <div class="btn disabled">KONFIRMASI</div>
                                    </div>
                                </div>
                                <div class="spacer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Auth - Login Admin</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand">
                                <div class="brand-badge"></div>
                                <div style="font-weight:900;">PMS Absensi</div>
                            </div>
                            <div class="profile">/login</div>
                        </div>
                        <div style="height: calc(520px - 64px); padding: 18px;">
                            <div class="login-card">
                                <div style="font-weight:900; font-size:18px;">Login Admin</div>
                                <div style="color:var(--muted); margin-top:6px; font-size:12px;">Masuk untuk mengelola karyawan, sesi, absensi, laporan.</div>
                                <div style="margin-top:16px; display:flex; flex-direction:column; gap:10px;">
                                    <div class="input">Email</div>
                                    <div class="input">Password</div>
                                    <div class="btn" style="margin-top:10px;">MASUK</div>
                                    <div style="text-align:center; color:var(--muted); font-size:12px;">Kembali ke Kiosk</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Dashboard</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand">
                                <div class="brand-badge"></div>
                                <div style="font-weight:900;">Admin</div>
                            </div>
                            <div class="nav">
                                <div class="active">Dashboard</div>
                                <div>Karyawan</div>
                                <div>Sesi</div>
                                <div>Absensi</div>
                                <div>Laporan</div>
                            </div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu active">Dashboard</div>
                                <div class="menu">Karyawan</div>
                                <div class="menu">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div class="cards4">
                                    <div class="kpi"><div class="label">Total Karyawan</div><div class="value">28</div></div>
                                    <div class="kpi"><div class="label">Hadir Hari Ini</div><div class="value">21</div></div>
                                    <div class="kpi"><div class="label">Sesi Aktif</div><div class="value">1</div></div>
                                    <div class="kpi"><div class="label">Terlambat</div><div class="value">3</div></div>
                                </div>
                                <div class="table">
                                    <div class="thead">
                                        <div>Nama</div><div>Jam</div><div>Status</div><div>Lokasi</div><div>Foto</div>
                                    </div>
                                    <div class="trow">
                                        <div>Budi Santoso</div><div>08:01</div><div><span class="tag">HADIR</span></div><div>Dalam radius</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>Siti Aulia</div><div>08:07</div><div><span class="tag">HADIR</span></div><div>Dalam radius</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>Rizky Putra</div><div>08:15</div><div><span class="tag">HADIR</span></div><div>Dalam radius</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Karyawan</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div class="active">Karyawan</div><div>Sesi</div><div>Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu active">Karyawan</div>
                                <div class="menu">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div style="display:flex; gap:12px; align-items:center; justify-content:space-between;">
                                    <div class="input" style="flex:1;">Cari karyawan...</div>
                                    <div class="btn" style="width:160px;">+ TAMBAH</div>
                                </div>
                                <div class="table">
                                    <div class="thead">
                                        <div>Nama</div><div>Role</div><div>HP</div><div>Status</div><div>Aksi</div>
                                    </div>
                                    <div class="trow">
                                        <div>Budi Santoso</div><div>Employee</div><div>08xxxx</div><div><span class="tag">Aktif</span></div><div><span class="tag">EDIT</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>Siti Aulia</div><div>Employee</div><div>08xxxx</div><div><span class="tag">Aktif</span></div><div><span class="tag">EDIT</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>Admin</div><div>Admin</div><div>-</div><div><span class="tag">Aktif</span></div><div><span class="tag">EDIT</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Karyawan (Tambah)</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div class="active">Karyawan</div><div>Sesi</div><div>Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu active">Karyawan</div>
                                <div class="menu">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div style="display:flex; align-items:center; justify-content:space-between;">
                                    <div>
                                        <div style="font-weight:900;">Tambah Karyawan</div>
                                        <div style="color:var(--muted); font-size:12px; margin-top:4px;">Isi data karyawan baru dan unggah foto wajah.</div>
                                    </div>
                                    <div class="btn secondary" style="width:140px;">KEMBALI</div>
                                </div>
                                <div class="split-2">
                                    <div class="card">
                                        <div style="font-weight:900;">Form Tambah</div>
                                        <div class="form">
                                            <div class="field">
                                                <div class="label">Nama</div>
                                                <div class="control placeholder">Contoh: Budi Santoso</div>
                                            </div>
                                            <div class="form-row-2">
                                                <div class="field">
                                                    <div class="label">No. HP</div>
                                                    <div class="control placeholder">08xxxxxxxxxx</div>
                                                </div>
                                                <div class="field">
                                                    <div class="label">Role</div>
                                                    <div class="control placeholder">Employee</div>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <div class="label">Foto Wajah</div>
                                                <div class="control placeholder">Upload file (jpg/png)</div>
                                            </div>
                                        </div>
                                        <div class="actions">
                                            <div class="btn secondary">BATAL</div>
                                            <div class="btn">SIMPAN</div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div style="font-weight:900;">Preview Data</div>
                                        <div style="color:var(--muted); font-size:12px; margin-top:6px;">Ringkasan data sebelum disimpan.</div>
                                        <div style="margin-top:12px; display:flex; flex-direction:column; gap:8px; font-size:12px;">
                                            <div>Nama: <strong>Budi Santoso</strong></div>
                                            <div>No. HP: <strong>08xxxxxxxxxx</strong></div>
                                            <div>Role: <strong>Employee</strong></div>
                                            <div>Foto: <strong>foto_wajah.jpg</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Karyawan (Edit)</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div class="active">Karyawan</div><div>Sesi</div><div>Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu active">Karyawan</div>
                                <div class="menu">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div style="display:flex; align-items:center; justify-content:space-between;">
                                    <div>
                                        <div style="font-weight:900;">Edit Karyawan</div>
                                        <div style="color:var(--muted); font-size:12px; margin-top:4px;">Perbarui data karyawan yang sudah ada.</div>
                                    </div>
                                    <div class="btn secondary" style="width:140px;">KEMBALI</div>
                                </div>
                                <div class="split-2">
                                    <div class="card">
                                        <div style="font-weight:900;">Form Edit</div>
                                        <div class="form">
                                            <div class="field">
                                                <div class="label">Nama</div>
                                                <div class="control">Siti Aulia</div>
                                            </div>
                                            <div class="form-row-2">
                                                <div class="field">
                                                    <div class="label">No. HP</div>
                                                    <div class="control">0812xxxxxxx</div>
                                                </div>
                                                <div class="field">
                                                    <div class="label">Role</div>
                                                    <div class="control">Employee</div>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <div class="label">Ganti Foto (Opsional)</div>
                                                <div class="control placeholder">Upload file (jpg/png)</div>
                                            </div>
                                        </div>
                                        <div class="actions">
                                            <div class="btn secondary">HAPUS</div>
                                            <div class="btn">UPDATE</div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div style="font-weight:900;">Data Saat Ini</div>
                                        <div style="color:var(--muted); font-size:12px; margin-top:6px;">Contoh data yang tersimpan.</div>
                                        <div style="margin-top:12px; display:flex; flex-direction:column; gap:8px; font-size:12px;">
                                            <div>ID: <strong>EMP-002</strong></div>
                                            <div>Nama: <strong>Siti Aulia</strong></div>
                                            <div>Status: <strong>Aktif</strong></div>
                                            <div>Foto: <strong>siti.jpg</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Sesi Kerja</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div>Karyawan</div><div class="active">Sesi</div><div>Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu">Karyawan</div>
                                <div class="menu active">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div style="display:flex; gap:12px; align-items:center; justify-content:space-between;">
                                    <div style="font-weight:900;">Daftar Sesi</div>
                                    <div class="btn" style="width:180px;">+ BUAT SESI</div>
                                </div>
                                <div class="table">
                                    <div class="thead">
                                        <div>Judul</div><div>Tanggal</div><div>Jam</div><div>Aktif</div><div>Detail</div>
                                    </div>
                                    <div class="trow">
                                        <div>Shift Pagi</div><div>26/04</div><div>08:00–12:00</div><div><span class="tag">ON</span></div><div><span class="tag">OPEN</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>Shift Sore</div><div>26/04</div><div>13:00–17:00</div><div><span class="tag">OFF</span></div><div><span class="tag">OPEN</span></div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div style="font-weight:900;">Detail Sesi (Preview)</div>
                                    <div style="color:var(--muted); font-size:12px; margin-top:6px;">List karyawan yang sudah absen pada sesi yang dipilih.</div>
                                    <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                                        <span class="tag">Budi • 08:01</span>
                                        <span class="tag">Siti • 08:07</span>
                                        <span class="tag">Rizky • 08:15</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Sesi Kerja (Buat)</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div>Karyawan</div><div class="active">Sesi</div><div>Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu">Karyawan</div>
                                <div class="menu active">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div style="display:flex; align-items:center; justify-content:space-between;">
                                    <div>
                                        <div style="font-weight:900;">Buat Sesi Kerja</div>
                                        <div style="color:var(--muted); font-size:12px; margin-top:4px;">Buat jadwal sesi absensi baru.</div>
                                    </div>
                                    <div class="btn secondary" style="width:140px;">KEMBALI</div>
                                </div>
                                <div class="split-2">
                                    <div class="card">
                                        <div style="font-weight:900;">Form Buat Sesi</div>
                                        <div class="form">
                                            <div class="field">
                                                <div class="label">Judul Sesi</div>
                                                <div class="control placeholder">Contoh: Shift Pagi</div>
                                            </div>
                                            <div class="form-row-2">
                                                <div class="field">
                                                    <div class="label">Tanggal</div>
                                                    <div class="control placeholder">YYYY-MM-DD</div>
                                                </div>
                                                <div class="field">
                                                    <div class="label">Status</div>
                                                    <div class="control placeholder">OFF (default)</div>
                                                </div>
                                            </div>
                                            <div class="form-row-2">
                                                <div class="field">
                                                    <div class="label">Jam Mulai</div>
                                                    <div class="control placeholder">08:00</div>
                                                </div>
                                                <div class="field">
                                                    <div class="label">Jam Selesai</div>
                                                    <div class="control placeholder">12:00</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="actions">
                                            <div class="btn secondary">BATAL</div>
                                            <div class="btn">SIMPAN</div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div style="font-weight:900;">Preview</div>
                                        <div style="color:var(--muted); font-size:12px; margin-top:6px;">Ringkasan sesi yang akan dibuat.</div>
                                        <div style="margin-top:12px; display:flex; flex-direction:column; gap:8px; font-size:12px;">
                                            <div>Judul: <strong>Shift Pagi</strong></div>
                                            <div>Tanggal: <strong>2026-04-26</strong></div>
                                            <div>Jam: <strong>08:00–12:00</strong></div>
                                            <div>Status: <strong>OFF</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Sesi Kerja (Edit)</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div>Karyawan</div><div class="active">Sesi</div><div>Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu">Karyawan</div>
                                <div class="menu active">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div style="display:flex; align-items:center; justify-content:space-between;">
                                    <div>
                                        <div style="font-weight:900;">Edit Sesi Kerja</div>
                                        <div style="color:var(--muted); font-size:12px; margin-top:4px;">Perbarui jadwal dan status sesi.</div>
                                    </div>
                                    <div class="btn secondary" style="width:140px;">KEMBALI</div>
                                </div>
                                <div class="split-2">
                                    <div class="card">
                                        <div style="font-weight:900;">Form Edit Sesi</div>
                                        <div class="form">
                                            <div class="field">
                                                <div class="label">Judul Sesi</div>
                                                <div class="control">Shift Sore</div>
                                            </div>
                                            <div class="form-row-2">
                                                <div class="field">
                                                    <div class="label">Tanggal</div>
                                                    <div class="control">2026-04-26</div>
                                                </div>
                                                <div class="field">
                                                    <div class="label">Aktif</div>
                                                    <div class="control">OFF</div>
                                                </div>
                                            </div>
                                            <div class="form-row-2">
                                                <div class="field">
                                                    <div class="label">Jam Mulai</div>
                                                    <div class="control">13:00</div>
                                                </div>
                                                <div class="field">
                                                    <div class="label">Jam Selesai</div>
                                                    <div class="control">17:00</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="actions">
                                            <div class="btn secondary">HAPUS</div>
                                            <div class="btn">UPDATE</div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div style="font-weight:900;">Info Sesi</div>
                                        <div style="color:var(--muted); font-size:12px; margin-top:6px;">Contoh info tambahan.</div>
                                        <div style="margin-top:12px; display:flex; flex-direction:column; gap:8px; font-size:12px;">
                                            <div>ID Sesi: <strong>WS-002</strong></div>
                                            <div>Jumlah hadir: <strong>12</strong></div>
                                            <div>Last update: <strong>26/04 12:30</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Monitoring Absensi</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div>Karyawan</div><div>Sesi</div><div class="active">Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu">Karyawan</div>
                                <div class="menu">Sesi Kerja</div>
                                <div class="menu active">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div style="display:flex; gap:12px; align-items:center;">
                                    <div class="input" style="flex:1;">Cari karyawan...</div>
                                    <div class="input" style="width:180px;">Tanggal</div>
                                </div>
                                <div class="table">
                                    <div class="thead">
                                        <div>Tanggal/Jam</div><div>Nama</div><div>Status</div><div>Lokasi</div><div>Foto</div>
                                    </div>
                                    <div class="trow">
                                        <div>26/04 08:01</div><div>Budi Santoso</div><div><span class="tag">Hadir</span></div><div>Dalam radius</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>26/04 08:07</div><div>Siti Aulia</div><div><span class="tag">Hadir</span></div><div>Dalam radius</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>26/04 08:15</div><div>Rizky Putra</div><div><span class="tag">Hadir</span></div><div>Dalam radius</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div style="font-weight:900;">Modal Foto (Preview)</div>
                                    <div style="margin-top:10px; height:140px; border:1px solid var(--border); border-radius:16px; background: linear-gradient(180deg, rgba(0,0,0,0.06), rgba(0,0,0,0.02));"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Laporan</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div>Karyawan</div><div>Sesi</div><div>Absensi</div><div class="active">Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu">Karyawan</div>
                                <div class="menu">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu active">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div class="card">
                                    <div style="font-weight:900;">Filter Laporan</div>
                                    <div style="margin-top:12px; display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px;">
                                        <div class="input">Dari Tanggal</div>
                                        <div class="input">Sampai Tanggal</div>
                                        <div class="input">Karyawan (opsional)</div>
                                    </div>
                                    <div style="margin-top:12px; display:flex; gap:10px;">
                                        <div class="btn" style="width:160px;">TAMPILKAN</div>
                                        <div class="btn secondary" style="width:160px;">EXPORT</div>
                                    </div>
                                </div>
                                <div class="table">
                                    <div class="thead">
                                        <div>Tanggal</div><div>Nama</div><div>Status</div><div>Lokasi</div><div>File</div>
                                    </div>
                                    <div class="trow">
                                        <div>26/04</div><div>Budi Santoso</div><div><span class="tag">Hadir</span></div><div>Dalam radius</div><div><span class="tag">PDF</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>26/04</div><div>Siti Aulia</div><div><span class="tag">Hadir</span></div><div>Dalam radius</div><div><span class="tag">PDF</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Settings</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div>Karyawan</div><div>Sesi</div><div>Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu">Karyawan</div>
                                <div class="menu">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu active">Settings</div>
                                <div class="menu">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div class="card">
                                    <div style="font-weight:900;">Pengaturan Kantor</div>
                                    <div style="margin-top:12px; display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px;">
                                        <div class="input">Latitude</div>
                                        <div class="input">Longitude</div>
                                        <div class="input">Radius (m)</div>
                                    </div>
                                    <div style="margin-top:12px; display:flex; gap:10px;">
                                        <div class="btn" style="width:180px;">SIMPAN</div>
                                        <div class="btn secondary" style="width:180px;">RESET</div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div style="font-weight:900;">Preview Peta (Placeholder)</div>
                                    <div style="margin-top:10px; height:180px; border:1px solid var(--border); border-radius:16px; background: rgba(0,0,0,0.03);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section wide">
                <div class="section-head">
                    <div class="section-title">Admin - Audit Logs</div>
                    <div class="section-note">Desktop</div>
                </div>
                <div class="canvas">
                    <div class="desktop">
                        <div class="topbar">
                            <div class="brand"><div class="brand-badge"></div><div style="font-weight:900;">Admin</div></div>
                            <div class="nav"><div>Dashboard</div><div>Karyawan</div><div>Sesi</div><div>Absensi</div><div>Laporan</div></div>
                            <div class="profile">Admin</div>
                        </div>
                        <div class="layout">
                            <div class="sidebar">
                                <div class="menu">Dashboard</div>
                                <div class="menu">Karyawan</div>
                                <div class="menu">Sesi Kerja</div>
                                <div class="menu">Monitoring Absensi</div>
                                <div class="menu">Laporan</div>
                                <div class="menu">Settings</div>
                                <div class="menu active">Audit Logs</div>
                            </div>
                            <div class="content">
                                <div class="table">
                                    <div class="thead">
                                        <div>Waktu</div><div>Aksi</div><div>Status</div><div>User</div><div>Detail</div>
                                    </div>
                                    <div class="trow">
                                        <div>26/04 08:01</div><div>Absensi Kiosk</div><div><span class="tag">OK</span></div><div>Budi</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>26/04 08:07</div><div>Tambah Karyawan</div><div><span class="tag">OK</span></div><div>Admin</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                    <div class="trow">
                                        <div>26/04 08:15</div><div>Aktifkan Sesi</div><div><span class="tag">OK</span></div><div>Admin</div><div><span class="tag">VIEW</span></div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div style="font-weight:900;">Detail Log (Preview)</div>
                                    <div style="margin-top:6px; color:var(--muted); font-size:12px;">Menampilkan detail aktivitas untuk kebutuhan audit.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
