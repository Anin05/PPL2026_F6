@extends('layouts.app')

@section('title', 'Tambah Kontak')
@section('page_title', 'Tambah Kontak')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('kontak.index') }}">Kontak Klien</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Tambah Kontak</span>
</nav>

<div class="page-head">
    <div>
        <h1>Tambah Kontak</h1>
        <p>Tambahkan PIC atau Direktur untuk perusahaan klien.</p>
    </div>
    <a class="btn btn-outline" id="btn-kembali" href="{{ route('kontak.index') }}">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
    <form class="card" id="form-kontak" novalidate>
        <div class="f-head">
            <h2>Informasi Kontak</h2>
            <p>Pilih klien yang sudah tersimpan, lalu lengkapi data kontak.</p>
        </div>

        <div class="f-body">
            <!-- Klien Select -->
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
                    <input name="name" placeholder="Masukkan nama lengkap kontak" required autofocus>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Tipe Kontak -->
            <div class="field" id="field-type">
                <label>Tipe Kontak <em>*</em></label>
                <div class="seg" id="segTipe">
                    <button type="button" class="on" onclick="setTipe('PIC')">
                        <x-icon name="check" size="14" /> PIC
                    </button>
                    <button type="button" onclick="setTipe('Direktur')">
                        <x-icon name="check" size="14" /> Direktur
                    </button>
                </div>
                <input type="hidden" name="type" id="inpTipe" value="PIC">
            </div>

            <!-- Jabatan -->
            <div class="field" id="field-position">
                <label>Jabatan</label>
                <div class="inp">
                    <input name="position" placeholder="Contoh: Manajer Pengadaan / Direktur Operasional">
                </div>
            </div>

            <!-- Nomor WhatsApp / Telepon -->
            <div class="field" id="field-phone">
                <label>Nomor WhatsApp / Telepon <em>*</em></label>
                <div class="inp">
                    <input name="phone" placeholder="Contoh: 0812-3456-7890" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Email -->
            <div class="field full" id="field-email">
                <label>Alamat Email</label>
                <div class="inp">
                    <input name="email" type="email" placeholder="nama@perusahaan.co.id">
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <div class="f-note full">
                <x-icon name="link" size="14" />
                <span>Kontak ini akan terhubung langsung dengan klien yang dipilih untuk mempermudah komunikasi dan riwayat interaksi.</span>
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="{{ route('kontak.index') }}">Batal</a>
                <button class="btn btn-primary" type="submit" id="btn-submit">
                    <x-icon name="check" size="16" /> Simpan Kontak
                </button>
            </div>
        </div>
    </form>

    <aside class="side-col">
        <div class="s-card" id="side-klien-preview" style="display:none;">
            <h3><x-icon name="building" size="16" /> Perusahaan Terpilih</h3>
            <div id="side-klien-nama" style="font-size:15px; font-weight:700; color:var(--navy); margin:6px 0;">-</div>
            <div class="row" style="display:flex; justify-content:space-between; align-items:center;">
                <span id="side-klien-kode">-</span>
                <span class="badge ok"><i></i>Aktif</span>
            </div>
            <hr style="margin:10px 0; border:0; border-top:1px solid var(--border)">
            <div class="row" style="font-size:13px; color:var(--muted);"><x-icon name="pin" size="14" /> <span id="side-klien-kota">-</span></div>
        </div>

        <div class="s-card">
            <h3><x-icon name="info" size="16" /> Pilih tipe kontak</h3>
            <div class="pt" style="font-weight:600; color:var(--navy); margin-top:8px;">PIC</div>
            <p style="margin-top:2px;">Penanggung jawab komunikasi dan koordinasi harian dengan klien. Contoh: Manajer Pengadaan, Admin Keuangan.</p>
            <hr style="margin:10px 0; border:0; border-top:1px solid var(--border)">
            <div class="pt" style="font-weight:600; color:var(--navy);">Direktur</div>
            <p style="margin-top:2px;">Pimpinan perusahaan yang menjadi kontak pengambil keputusan. Contoh: CEO / Direktur Utama, Direktur Operasional.</p>
        </div>

        <div class="s-card tint">
            <h3><x-icon name="info" size="16" /> Alur Tambah Kontak</h3>
            <ol class="steps">
                <li><span class="n">1</span>Klien tersimpan di Master Klien</li>
                <li><span class="n">2</span>Pilih perusahaan klien di formulir</li>
                <li><span class="n">3</span>Isi data kontak dan tentukan tipe (PIC / Direktur)</li>
                <li><span class="n g">4</span>Simpan kontak baru</li>
            </ol>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
let clientsData = [];
const urlParams = new URLSearchParams(window.location.search);
const preselectedClientId = urlParams.get('client_id') || urlParams.get('klien_id') || urlParams.get('clientId');
const preselectedClientCode = urlParams.get('kode') || urlParams.get('client_code');

function setTipe(val) {
    document.getElementById('inpTipe').value = val;
    const seg = document.getElementById('segTipe');
    seg.querySelectorAll('button').forEach(btn => {
        btn.classList.toggle('on', btn.textContent.includes(val));
    });
}

function updateClientPreview(selectedId) {
    const preview = document.getElementById('side-klien-preview');
    const client = clientsData.find(c => c.id === selectedId || c.code === selectedId);
    if (!client) {
        preview.style.display = 'none';
        return;
    }
    document.getElementById('side-klien-nama').textContent = client.name;
    document.getElementById('side-klien-kode').textContent = client.code;
    document.getElementById('side-klien-kota').textContent = client.city || 'Kota belum diatur';
    preview.style.display = 'block';
}

async function muatKlien() {
    const sel = document.getElementById('sel-klien');
    try {
        const res = await api('GET', '/clients?limit=100&status=active');
        clientsData = res.data || [];
        sel.innerHTML = '<option value="">Pilih perusahaan klien...</option>';
        clientsData.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.code + ' · ' + c.name;
            if (preselectedClientId === c.id || preselectedClientCode === c.code) {
                opt.selected = true;
            }
            sel.appendChild(opt);
        });

        if (sel.value) {
            updateClientPreview(sel.value);
        }
    } catch (e) {
        console.error(e);
        sel.innerHTML = '<option value="">Gagal memuat daftar klien</option>';
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

document.getElementById('form-kontak').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-submit');
    const label = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = 'Menyimpan kontak...';

    const f = Object.fromEntries(new FormData(e.target));
    try {
        await api('POST', '/contacts', {
            client_id: f.client_id,
            name: f.name,
            position: f.position,
            type: f.type,
            phone: f.phone,
            email: f.email,
        });
        sessionStorage.setItem('flash', 'Data kontak berhasil disimpan.');
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

muatKlien();
</script>
@endpush
