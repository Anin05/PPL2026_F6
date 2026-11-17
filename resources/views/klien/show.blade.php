@extends('layouts.app')

@section('title', 'Detail Klien')
@section('page_title', 'Detail Klien')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('klien.index') }}">Master Klien</a>
    <x-icon name="chevR" size="12" />
    <span class="cur" id="crumb-kode">{{ $kode }}</span>
</nav>

<div class="page-head">
    <div>
        <h1 id="page-head-title">Detail Klien</h1>
        <p>Informasi perusahaan, kontak terhubung, dan riwayat komunikasi.</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a class="btn btn-outline" href="{{ route('klien.index') }}">
            <x-icon name="arrowL" size="16" /> Kembali
        </a>
    </div>
</div>

<section class="card co-card" id="card-header">
    <div class="co-top">
        <div class="co-l">
            <div class="co-ic"><x-icon name="building" size="28" /></div>
            <div>
                <div class="co-name">
                    <h2 id="view-nama">Memuat nama klien...</h2>
                    <span class="badge" id="view-badge"><i></i>-</span>
                </div>
                <div class="co-meta">
                    <span id="view-kode">{{ $kode }}</span>
                    •
                    <x-icon name="pin" size="13" />
                    <span id="view-kota">-</span>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:8px;" id="header-actions">
            <a class="btn btn-outline" id="btn-edit" href="/klien/{{ $kode }}/edit">
                <x-icon name="edit" size="16" /> Edit Klien
            </a>
            <button type="button" class="btn btn-outline" id="btn-status" onclick="toggleStatus()">
                <x-icon name="archive" size="16" /> <span id="label-status">Arsipkan</span>
            </button>
        </div>
    </div>

    <div class="co-info">
        <span><x-icon name="pin" size="14" /> <span id="info-alamat">-</span></span>
        <span><x-icon name="phone" size="14" /> <span id="info-phone">-</span></span>
        <span><x-icon name="mail" size="14" /> <span id="info-email">-</span></span>
    </div>

    <div class="tabs">
        <button type="button" class="tab on" id="tab-btn-info" onclick="switchTab('info')">
            Informasi
        </button>
        <button type="button" class="tab" id="tab-btn-pic" onclick="switchTab('pic')">
            PIC &amp; Direktur <span class="chip" id="chip-kontak">0</span>
        </button>
        <button type="button" class="tab" id="tab-btn-int" onclick="switchTab('int')">
            Riwayat Interaksi <span class="chip" id="chip-interaksi">0</span>
        </button>
    </div>
</section>

<!-- Tab 1: Informasi -->
<section class="card" id="sec-info">
    <div class="card-head">
        <div>
            <h2>Informasi Perusahaan</h2>
            <div class="sub">Data identitas klien di sistem CRM.</div>
        </div>
    </div>
    <dl class="info-grid" style="border-top: 1px solid var(--border)">
        <div><dt>Nama Perusahaan</dt><dd id="dt-nama">-</dd></div>
        <div><dt>Kode Klien</dt><dd id="dt-kode">{{ $kode }}</dd></div>
        <div><dt>Status</dt><dd id="dt-status">-</dd></div>
        <div class="span2"><dt>Alamat</dt><dd id="dt-alamat">-</dd></div>
        <div><dt>Kota</dt><dd id="dt-kota">-</dd></div>
        <div><dt>Telepon</dt><dd id="dt-phone">-</dd></div>
        <div><dt>No. HP / WA</dt><dd id="dt-mobile">-</dd></div>
        <div><dt>Email</dt><dd id="dt-email">-</dd></div>
    </dl>
</section>

<!-- Tab 2: PIC & Direktur -->
<section class="card" id="sec-pic" style="display:none;">
    <div class="card-head">
        <div>
            <h2>PIC &amp; Direktur</h2>
            <div class="sub" id="sub-kontak">Memuat kontak...</div>
        </div>
        <a class="btn btn-primary" href="/kontak/tambah">
            <x-icon name="plus" size="16" /> Tambah Kontak
        </a>
    </div>
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Jabatan</th>
                    <th>No. Telepon / WA</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="tbody-kontak">
                <tr><td colspan="6" class="empty">Memuat daftar kontak...</td></tr>
            </tbody>
        </table>
    </div>
</section>

<!-- Tab 3: Riwayat Interaksi -->
<section class="card" id="sec-int" style="display:none;">
    <div class="card-head">
        <div>
            <h2>Riwayat Interaksi</h2>
            <div class="sub" id="sub-interaksi">Memuat interaksi...</div>
        </div>
        <a class="btn btn-primary btn-sm" href="/riwayat/tambah">
            <x-icon name="plus" size="14" /> Catat Interaksi
        </a>
    </div>
    <ul class="mini-list" id="list-interaksi" style="border-bottom:0">
        <li style="padding:16px;" class="empty">Memuat catatan interaksi...</li>
    </ul>
</section>
@endsection

@push('scripts')
<script>
const KODE = @json($kode);
let clientData = null;

const esc = (s) => String(s ?? '-').replace(/[&<>"']/g,
    (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

async function muatDetail() {
    try {
        const res = await api('GET', '/clients/' + encodeURIComponent(KODE));
        clientData = res.data;
        const c = clientData;

        const aktif = c.status === 'active';
        document.title = 'Detail Klien - ' + c.name;
        document.getElementById('view-nama').textContent = c.name;
        document.getElementById('view-kode').textContent = c.code;
        document.getElementById('crumb-kode').textContent = c.code;
        document.getElementById('view-kota').textContent = c.city || '-';
        document.getElementById('btn-edit').href = '/klien/' + encodeURIComponent(c.code) + '/edit';

        const badge = document.getElementById('view-badge');
        badge.className = 'badge ' + (aktif ? 'ok' : 'mute');
        badge.innerHTML = '<i></i>' + (aktif ? 'Aktif' : 'Arsip');
        document.getElementById('label-status').textContent = aktif ? 'Arsipkan' : 'Pulihkan';

        document.getElementById('info-alamat').textContent = c.address || 'Alamat belum diatur';
        document.getElementById('info-phone').textContent = c.phone || c.mobile || '-';
        document.getElementById('info-email').textContent = c.email || '-';

        // Tab Info
        document.getElementById('dt-nama').textContent = c.name;
        document.getElementById('dt-kode').textContent = c.code;
        document.getElementById('dt-status').innerHTML = '<span class="badge ' + (aktif ? 'ok' : 'mute') + '"><i></i>' + (aktif ? 'Aktif' : 'Arsip') + '</span>';
        document.getElementById('dt-alamat').textContent = c.address || '-';
        document.getElementById('dt-kota').textContent = c.city || '-';
        document.getElementById('dt-phone').textContent = c.phone || '-';
        document.getElementById('dt-mobile').textContent = c.mobile || '-';
        document.getElementById('dt-email').textContent = c.email || '-';

        // Kontak
        const contacts = c.contacts || [];
        document.getElementById('chip-kontak').textContent = contacts.length;
        document.getElementById('sub-kontak').textContent = contacts.length + ' kontak terhubung dengan klien ini.';
        const tbodyK = document.getElementById('tbody-kontak');
        if (contacts.length === 0) {
            tbodyK.innerHTML = '<tr><td colspan="6" class="empty">Belum ada kontak untuk klien ini. Klik "Tambah Kontak" untuk menambahkan.</td></tr>';
        } else {
            tbodyK.innerHTML = contacts.map(k => `
                <tr>
                    <td><b>${esc(k.name)}</b></td>
                    <td><span class="tag">${esc(k.type)}</span></td>
                    <td>${esc(k.position || '-')}</td>
                    <td>${esc(k.phone || '-')}</td>
                    <td>${esc(k.email || '-')}</td>
                    <td><span class="badge ${k.status === 'active' ? 'ok' : 'mute'}"><i></i>${k.status === 'active' ? 'Aktif' : 'Arsip'}</span></td>
                </tr>
            `).join('');
        }

        // Interaksi
        const interactions = c.interactions || [];
        document.getElementById('chip-interaksi').textContent = interactions.length;
        document.getElementById('sub-interaksi').textContent = interactions.length + ' catatan interaksi untuk klien ini.';
        const listI = document.getElementById('list-interaksi');
        if (interactions.length === 0) {
            listI.innerHTML = '<li style="padding:16px; color:var(--muted); text-align:center;">Belum ada riwayat interaksi tercatat untuk klien ini.</li>';
        } else {
            listI.innerHTML = interactions.map(i => {
                const tgl = i.interaction_at ? new Date(i.interaction_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'}) : '-';
                return `
                    <li>
                        <span class="i-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></span>
                        <div>
                            <b>${esc(i.type || 'Interaksi')}</b>
                            <small>${esc(i.notes || '-')}</small>
                        </div>
                        <span class="dt">${tgl}</span>
                    </li>
                `;
            }).join('');
        }

    } catch (err) {
        alert((err.error && err.error.message) || 'Gagal memuat detail klien.');
        location.href = '/klien';
    }
}

function switchTab(tab) {
    document.querySelectorAll('.tabs .tab').forEach(t => t.classList.remove('on'));
    document.getElementById('sec-info').style.display = 'none';
    document.getElementById('sec-pic').style.display = 'none';
    document.getElementById('sec-int').style.display = 'none';

    if (tab === 'info') {
        document.getElementById('tab-btn-info').classList.add('on');
        document.getElementById('sec-info').style.display = 'block';
    } else if (tab === 'pic') {
        document.getElementById('tab-btn-pic').classList.add('on');
        document.getElementById('sec-pic').style.display = 'block';
    } else if (tab === 'int') {
        document.getElementById('tab-btn-int').classList.add('on');
        document.getElementById('sec-int').style.display = 'block';
    }
}

async function toggleStatus() {
    if (!clientData) return;
    const pesan = clientData.status === 'active' ? 'Arsipkan klien ini?' : 'Pulihkan kembali klien ini?';
    if (!confirm(pesan)) return;
    try {
        await api('PATCH', '/clients/' + clientData.id + '/status');
        await muatDetail();
    } catch (err) {
        alert((err.error && err.error.message) || 'Gagal mengubah status klien.');
    }
}

muatDetail();
</script>
@endpush
