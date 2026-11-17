@extends('layouts.app')

@section('title', 'Edit Klien')
@section('page_title', 'Edit Klien')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('klien.index') }}">Master Klien</a>
    <x-icon name="chevR" size="12" />
    <a href="/klien/{{ $kode }}" id="crumb-kode">{{ $kode }}</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Edit Klien</span>
</nav>

<div class="page-head">
    <div>
        <h1 id="page-head-title">Edit Klien</h1>
        <p id="page-head-sub">Perbarui informasi perusahaan klien.</p>
    </div>
    <a class="btn btn-outline" href="/klien/{{ $kode }}" id="btn-kembali">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
    <form class="card" id="form-edit-klien" novalidate>
        <div class="f-head">
            <h2>Informasi Perusahaan</h2>
            <p>Data ini akan menjadi identitas utama klien di sistem.</p>
        </div>

        <div class="f-body">
            <!-- Nama Perusahaan -->
            <div class="field" id="field-name">
                <label>Nama Perusahaan <em>*</em></label>
                <div class="inp">
                    <input name="name" id="inp-name" placeholder="Masukkan nama perusahaan" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Kode Klien (Readonly) -->
            <div class="field">
                <label>Kode Klien</label>
                <div class="inp">
                    <input readonly id="inp-code" value="{{ $kode }}" style="background:#f8fafc; color:#64748b;">
                    <span class="ic-lock"><x-icon name="lock" size="15" /></span>
                </div>
                <div class="hint">Kode klien bersifat permanen dan tidak dapat diubah.</div>
            </div>

            <!-- Alamat -->
            <div class="field full" id="field-address">
                <label>Alamat</label>
                <div class="inp">
                    <textarea name="address" id="inp-address" placeholder="Contoh: Jl. Jenderal Sudirman No. 25, Tanah Abang"></textarea>
                </div>
            </div>

            <!-- Kota -->
            <div class="field" id="field-city">
                <label>Kota <em>*</em></label>
                <div class="inp">
                    <input name="city" id="inp-city" placeholder="Contoh: Jakarta Pusat" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Telepon -->
            <div class="field" id="field-phone">
                <label>Telepon</label>
                <div class="inp">
                    <input name="phone" id="inp-phone" placeholder="Contoh: 021-5550-1200">
                </div>
            </div>

            <!-- HP / WhatsApp -->
            <div class="field" id="field-mobile">
                <label>No. HP / WA</label>
                <div class="inp">
                    <input name="mobile" id="inp-mobile" placeholder="Contoh: 0812-9900-1200">
                </div>
                <div class="hint">Dapat digunakan untuk kontak WhatsApp.</div>
            </div>

            <!-- Email -->
            <div class="field" id="field-email">
                <label>Email</label>
                <div class="inp">
                    <input name="email" id="inp-email" type="email" placeholder="contact@perusahaan.co.id">
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Status -->
            <div class="field full">
                <label>Status</label>
                <div class="inp">
                    <select name="status" id="sel-status">
                        <option value="active">Aktif</option>
                        <option value="archived">Arsip</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
                <div class="hint">Status menentukan apakah klien berada di direktori aktif atau arsip.</div>
            </div>

            <div class="f-note full">
                <x-icon name="shield" size="15" />
                Data klien hanya dapat diubah oleh peran berwenang (Admin, Account Manager, COO, CEO).
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="/klien/{{ $kode }}">Batal</a>
                <button class="btn btn-primary" type="submit" id="btn-submit">
                    <x-icon name="check" size="16" /> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <aside class="side-col">
        <div class="s-card">
            <h3><x-icon name="info" size="16" /> Panduan pengisian</h3>
            <p>Perubahan data klien akan langsung terhubung ke modul proyek dan kontak terkait.</p>
            <hr>
            <p><span class="red-dot">•</span> Nama Perusahaan dan Kota wajib diisi.</p>
            <p><span class="red-dot">•</span> Pastikan nomor kontak dan email perusahaan valid.</p>
        </div>

        <div class="s-card">
            <h3><x-icon name="archive" size="16" /> Status Arsip</h3>
            <p>Mengubah status ke <b>Arsip</b> akan memindahkan klien dan seluruh kontaknya ke tab Arsip tanpa menghapus data secara permanen.</p>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
const KODE = @json($kode);
let clientId = null;

async function muatDataKlien() {
    try {
        const res = await api('GET', '/clients/' + encodeURIComponent(KODE));
        const c = res.data;
        clientId = c.id;

        document.getElementById('page-head-title').textContent = 'Edit Klien - ' + c.name;
        document.getElementById('page-head-sub').textContent = 'Perbarui informasi perusahaan ' + c.name + '.';
        document.getElementById('inp-name').value = c.name || '';
        document.getElementById('inp-code').value = c.code || '';
        document.getElementById('inp-address').value = c.address || '';
        document.getElementById('inp-city').value = c.city || '';
        document.getElementById('inp-phone').value = c.phone || '';
        document.getElementById('inp-mobile').value = c.mobile || '';
        document.getElementById('inp-email').value = c.email || '';
        document.getElementById('sel-status').value = c.status || 'active';
    } catch (err) {
        alert((err.error && err.error.message) || 'Gagal memuat data klien.');
        location.href = '/klien';
    }
}

function tampilkanError(details) {
    document.querySelectorAll('.err-msg.js').forEach(e => e.remove());
    document.querySelectorAll('.field.err').forEach(e => e.classList.remove('err'));
    Object.entries(details || {}).forEach(([field, pesan]) => {
        const fieldEl = document.getElementById('field-' + field);
        if (!fieldEl) return;
        fieldEl.classList.add('err');
        const msg = document.createElement('div');
        msg.className = 'err-msg js';
        msg.textContent = Array.isArray(pesan) ? pesan[0] : pesan;
        fieldEl.appendChild(msg);
    });
}

document.getElementById('form-edit-klien').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!clientId) return;

    const btn = document.getElementById('btn-submit');
    const label = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = 'Menyimpan perubahan...';

    const f = Object.fromEntries(new FormData(e.target));

    try {
        await api('PUT', '/clients/' + clientId, {
            name: f.name,
            address: f.address,
            city: f.city,
            phone: f.phone,
            mobile: f.mobile,
            email: f.email,
            status: f.status,
        });

        sessionStorage.setItem('flash', 'Data klien ' + f.name + ' berhasil diperbarui.');
        location.href = '/klien';
    } catch (err) {
        tampilkanError(err.error && err.error.details);
        alert((err.error && err.error.message) || 'Gagal memperbarui data klien.');
        btn.disabled = false;
        btn.innerHTML = label;
    }
});

muatDataKlien();
</script>
@endpush
