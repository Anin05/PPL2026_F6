@extends('layouts.app')

@section('title', 'Catat Interaksi')
@section('page_title', 'Catat Interaksi')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('riwayat.index') }}">Riwayat Interaksi</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Catat Interaksi</span>
</nav>

<div class="page-head">
    <div>
        <h1>Catat Interaksi</h1>
        <p>Rekam komunikasi bisnis dan jadwalkan tindak lanjut.</p>
    </div>
    <a class="btn btn-outline" href="{{ route('riwayat.index') }}">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
    <form class="card" id="form-interaksi" novalidate>
        <div class="f-head">
            <h2>Data Komunikasi</h2>
            <p>Catatan ini akan tersimpan pada riwayat klien yang dipilih.</p>
        </div>

        <div class="f-body">
            <!-- Klien -->
            <div class="field full" id="field-klien">
                <label>Klien Terkait <em>*</em></label>
                <div class="inp">
                    <select name="client_id" id="sel-klien" required>
                        <option value="">Memuat daftar klien...</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
            </div>

            <!-- Kanal -->
            <div class="field" id="field-type">
                <label>Kanal Komunikasi <em>*</em></label>
                <div class="inp">
                    <select name="type" id="sel-type" required>
                        <option value="Meeting">Rapat / Meeting</option>
                        <option value="Email">Email</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="Telepon">Telepon</option>
                        <option value="Kunjungan">Kunjungan</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
            </div>

            <!-- Tanggal -->
            <div class="field" id="field-date">
                <label>Tanggal Interaksi <em>*</em></label>
                <div class="inp">
                    <input name="date" id="inp-date" type="date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <!-- Waktu -->
            <div class="field" id="field-time">
                <label>Waktu Interaksi</label>
                <div class="inp">
                    <input name="time" id="inp-time" type="time" value="{{ date('H:i') }}">
                </div>
            </div>

            <!-- Catatan / Pembahasan -->
            <div class="field full" id="field-notes">
                <label>Catatan Pembahasan / Hasil Komunikasi <em>*</em></label>
                <div class="inp">
                    <textarea name="notes" id="inp-notes" rows="4" placeholder="Contoh: Pembahasan perpanjangan kontrak kerja sama dan kesepakatan jadwal demo sistem..." required></textarea>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
            </div>

            <!-- Tindak Lanjut Section -->
            <div class="field full" style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" id="chk-tindak-lanjut" onchange="toggleTindakLanjut(this.checked)">
                    <span style="font-weight:600; color:var(--navy);">Rencanakan Tindak Lanjut (Follow-up)</span>
                </label>
            </div>

            <div id="box-tindak-lanjut" class="field full" style="display:none; background:#fffbeb; border:1px solid #fde68a; padding:12px; border-radius:8px;">
                <div class="field" style="margin-bottom:8px;">
                    <label style="color:#92400e;">Tanggal Tindak Lanjut</label>
                    <div class="inp">
                        <input type="date" id="inp-tl-date">
                    </div>
                </div>
                <div class="field">
                    <label style="color:#92400e;">Catatan Rencana Tindak Lanjut</label>
                    <div class="inp">
                        <input id="inp-tl-notes" placeholder="Contoh: Kirim revisi penawaran harga dan ikuti perkembangannya">
                    </div>
                </div>
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="{{ route('riwayat.index') }}">Batal</a>
                <button class="btn btn-primary" type="submit" id="btn-submit">
                    <x-icon name="check" size="16" /> Simpan Interaksi
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
            <h3><x-icon name="info" size="16" /> Panduan Interaksi</h3>
            <p>Mencatat setiap interaksi secara berkala menjaga rekam jejak hubungan kerja sama yang akurat dengan klien.</p>
            <hr style="margin:10px 0; border:0; border-top:1px solid var(--border)">
            <p>Jika ada janji temu, pengiriman proposal, atau revisi kontrak, aktifkan opsi <b>Rencanakan Tindak Lanjut</b> agar otomatis terpantau di Dashboard.</p>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
let clientsData = [];
const urlParams = new URLSearchParams(window.location.search);
const preselectedClientId = urlParams.get('client_id') || urlParams.get('klien_id');
const preselectedClientCode = urlParams.get('kode') || urlParams.get('client_code');

function toggleTindakLanjut(checked) {
    document.getElementById('box-tindak-lanjut').style.display = checked ? 'block' : 'none';
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

document.getElementById('form-interaksi').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-submit');
    const label = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = 'Menyimpan interaksi...';

    const f = Object.fromEntries(new FormData(e.target));
    const isTl = document.getElementById('chk-tindak-lanjut').checked;
    const tlDate = document.getElementById('inp-tl-date').value;
    const tlNotes = document.getElementById('inp-tl-notes').value.trim();

    let fullNotes = f.notes;
    if (isTl) {
        fullNotes += ' (Tindak lanjut: ' + (tlNotes || 'Menunggu jadwal follow-up') + (tlDate ? ' per ' + tlDate : '') + ')';
    }

    const interactionAt = (f.date || '{{ date("Y-m-d") }}') + ' ' + (f.time ? f.time + ':00' : '09:00:00');

    try {
        await api('POST', '/interactions', {
            client_id: f.client_id,
            type: f.type,
            notes: fullNotes,
            interaction_at: interactionAt,
        });
        sessionStorage.setItem('flash', 'Catatan interaksi berhasil disimpan.');
        window.location.href = '/riwayat';
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
