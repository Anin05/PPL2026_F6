@extends('layouts.app')

@section('title', 'Edit Kontak')
@section('page_title', 'Edit Kontak')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('kontak.index') }}">Kontak Klien</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Edit Kontak</span>
</nav>

<div class="page-head">
    <div>
        <h1 id="page-heading">Edit Kontak</h1>
        <p id="page-subheading">Perbarui data kontak klien.</p>
    </div>
    <a class="btn btn-outline" href="{{ route('kontak.index') }}">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
    <form class="card" id="form-edit-kontak" novalidate>
        <div class="f-head">
            <h2>Informasi Kontak</h2>
            <p id="info-klien-terhubung">Memuat data kontak...</p>
        </div>

        <div class="f-body">
            <!-- Perusahaan Klien Select -->
            <div class="field full" id="field-klien">
                <label>Perusahaan Klien <em>*</em></label>
                <div class="inp">
                    <select name="client_id" id="sel-klien" required>
                        <option value="">Memuat daftar klien...</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
            </div>

            <!-- Nama Lengkap -->
            <div class="field" id="field-name">
                <label>Nama Lengkap <em>*</em></label>
                <div class="inp">
                    <input name="name" id="inp-name" placeholder="Nama lengkap kontak" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Tipe Kontak -->
            <div class="field" id="field-type">
                <label>Tipe Kontak <em>*</em></label>
                <div class="seg" id="segTipe">
                    <button type="button" id="btn-pic" onclick="setTipe('PIC')">
                        <x-icon name="check" size="14" /> PIC
                    </button>
                    <button type="button" id="btn-dir" onclick="setTipe('Direktur')">
                        <x-icon name="check" size="14" /> Direktur
                    </button>
                </div>
                <input type="hidden" name="type" id="inpTipe" value="PIC">
            </div>

            <!-- Jabatan -->
            <div class="field" id="field-position">
                <label>Jabatan</label>
                <div class="inp">
                    <input name="position" id="inp-position" placeholder="Contoh: Manajer Pengadaan">
                </div>
            </div>

            <!-- Nomor WhatsApp / Telepon -->
            <div class="field" id="field-phone">
                <label>Nomor WhatsApp / Telepon <em>*</em></label>
                <div class="inp">
                    <input name="phone" id="inp-phone" placeholder="Contoh: 0812-3456-7890" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Email -->
            <div class="field" id="field-email">
                <label>Email</label>
                <div class="inp">
                    <input name="email" id="inp-email" type="email" placeholder="nama@perusahaan.co.id">
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Status -->
            <div class="field" id="field-status">
                <label>Status Kontak</label>
                <div class="inp">
                    <select name="status" id="sel-status">
                        <option value="active">Aktif</option>
                        <option value="archived">Arsip</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
                <div class="hint">Kontak aktif dapat dipilih untuk riwayat komunikasi dan interaksi klien.</div>
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="{{ route('kontak.index') }}">Batal</a>
                <button class="btn btn-primary" type="submit" id="btn-submit">
                    <x-icon name="check" size="16" /> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <aside class="side-col">
        <div class="s-card" id="side-klien-card">
            <h3><x-icon name="building" size="16" /> Perusahaan Klien</h3>
            <div id="side-klien-nama" style="font-size:15px; font-weight:700; color:var(--navy); margin:6px 0;">-</div>
            <div class="row" style="display:flex; justify-content:space-between; align-items:center;">
                <span id="side-klien-kode">-</span>
                <span class="badge ok" id="side-klien-badge"><i></i>Aktif</span>
            </div>
            <hr style="margin:10px 0; border:0; border-top:1px solid var(--border)">
            <div class="row" style="font-size:13px; color:var(--muted);"><x-icon name="pin" size="14" /> <span id="side-klien-kota">-</span></div>
        </div>

        <div class="s-card">
            <h3><x-icon name="trash" size="16" /> Hapus Kontak</h3>
            <p>Jika kontak ini tidak lagi bekerja sama, Anda dapat menghapusnya secara permanen.</p>
            <button type="button" class="btn btn-outline btn-sm" id="btn-delete" style="color:#dc2626; border-color:#fca5a5;" onclick="hapusKontak()">
                Hapus Kontak Ini
            </button>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
const contactId = @json($id ?? '');
let clientsData = [];

function setTipe(val) {
    document.getElementById('inpTipe').value = val;
    document.getElementById('btn-pic').classList.toggle('on', val === 'PIC');
    document.getElementById('btn-dir').classList.toggle('on', val === 'Direktur');
}

function updateClientPreview(selectedId) {
    const client = clientsData.find(c => c.id === selectedId || c.code === selectedId);
    if (!client) return;
    document.getElementById('side-klien-nama').textContent = client.name;
    document.getElementById('side-klien-kode').textContent = client.code;
    document.getElementById('side-klien-kota').textContent = client.city || 'Kota belum diatur';
    const isAktif = client.status === 'active';
    const badge = document.getElementById('side-klien-badge');
    badge.className = 'badge ' + (isAktif ? 'ok' : 'mute');
    badge.innerHTML = '<i></i>' + (isAktif ? 'Aktif' : 'Arsip');
}

async function muatData() {
    try {
        const [klienRes, kontakRes] = await Promise.all([
            api('GET', '/clients?limit=100'),
            api('GET', '/contacts/' + encodeURIComponent(contactId)),
        ]);

        clientsData = klienRes.data || [];
        const selKlien = document.getElementById('sel-klien');
        selKlien.innerHTML = '<option value="">Pilih perusahaan klien...</option>';
        clientsData.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.code + ' · ' + c.name;
            selKlien.appendChild(opt);
        });

        const k = kontakRes.data;
        document.getElementById('page-heading').textContent = 'Edit Kontak - ' + k.name;
        document.getElementById('page-subheading').textContent = 'Perbarui data kontak ' + k.name + ' dari ' + (k.client ? k.client.name : 'klien') + '.';
        document.getElementById('info-klien-terhubung').textContent = 'Kontak terhubung dengan ' + (k.client ? k.client.name + ' (' + k.client.code + ')' : '-');

        selKlien.value = k.client_id;
        document.getElementById('inp-name').value = k.name || '';
        document.getElementById('inp-position').value = k.position || '';
        document.getElementById('inp-phone').value = k.phone || '';
        document.getElementById('inp-email').value = k.email || '';
        document.getElementById('sel-status').value = k.status || 'active';
        setTipe(k.type || 'PIC');

        if (k.client) {
            document.getElementById('side-klien-nama').textContent = k.client.name;
            document.getElementById('side-klien-kode').textContent = k.client.code;
            document.getElementById('side-klien-kota').textContent = k.client.city || '-';
        }
    } catch (err) {
        console.error(err);
        alert((err.error && err.error.message) || 'Gagal memuat data kontak.');
        window.location.href = '/kontak';
    }
}

document.getElementById('sel-klien').addEventListener('change', (e) => {
    updateClientPreview(e.target.value);
});

function tampilkanError(details) {
    document.querySelectorAll('.err-msg.js').forEach(e => e.remove());
    document.querySelectorAll('.field.err').forEach(e => e.classList.remove('err'));
    
    Object.entries(details || {}).forEach(([field, pesan]) => {
        const input = document.querySelector('[name="' + field + '"]');
        if (!input) return;
        const box = input.closest('.field');
        if (!box) return;
        box.classList.add('err');
        const msg = document.createElement('div');
        msg.className = 'err-msg js';
        msg.textContent = Array.isArray(pesan) ? pesan[0] : pesan;
        box.appendChild(msg);
    });
}

document.getElementById('form-edit-kontak').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-submit');
    const label = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = 'Menyimpan perubahan...';

    const f = Object.fromEntries(new FormData(e.target));
    try {
        await api('PUT', '/contacts/' + encodeURIComponent(contactId), {
            client_id: f.client_id,
            name: f.name,
            position: f.position,
            type: f.type,
            phone: f.phone,
            email: f.email,
            status: f.status,
        });
        sessionStorage.setItem('flash', 'Perubahan data kontak berhasil disimpan.');
        window.location.href = '/kontak';
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = label;
        if (err.error && err.error.details) {
            tampilkanError(err.error.details);
        }
        if (err.error && err.error.message) {
            alert(err.error.message);
        }
    }
});

async function hapusKontak() {
    if (!confirm('Apakah Anda yakin ingin menghapus kontak ini secara permanen?')) return;
    const btn = document.getElementById('btn-delete');
    btn.disabled = true;
    btn.textContent = 'Menghapus...';
    try {
        await api('DELETE', '/contacts/' + encodeURIComponent(contactId));
        sessionStorage.setItem('flash', 'Kontak berhasil dihapus.');
        window.location.href = '/kontak';
    } catch (err) {
        btn.disabled = false;
        btn.textContent = 'Hapus Kontak Ini';
        alert((err.error && err.error.message) || 'Gagal menghapus kontak.');
    }
}

muatData();
</script>
@endpush
