@extends('layouts.app')

@section('title', 'Kontak Klien')
@section('page_title', 'Kontak Klien')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <span class="cur">Kontak Klien</span>
</nav>

<div class="page-head">
    <div>
        <h1>Kontak Klien</h1>
        <p>Direktori PIC dan Direktur yang terhubung dengan perusahaan klien.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('kontak.create') }}">
        <x-icon name="plus" size="16" /> Tambah Kontak
    </a>
</div>

<div class="cards-3">
    <div class="stat">
        <div>
            <div class="stat-l">Total Kontak</div>
            <div class="stat-v">
                <b id="st-total">-</b>
                <span>Kontak tersimpan</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="contact" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">PIC</div>
            <div class="stat-v">
                <b id="st-pic">-</b>
                <span>Kontak operasional</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="users" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Direktur</div>
            <div class="stat-v">
                <b id="st-dir">-</b>
                <span>Kontak pengambil keputusan</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="briefcase" size="18" /></div>
    </div>
</div>

<section class="card">
    <div class="card-head">
        <div class="ch-l">
            <h2>Daftar Kontak</h2>
            <span class="chip" id="chip-total">0 kontak</span>
        </div>
        <span class="muted sm" id="info-halaman"></span>
    </div>

    <!-- Toolbar Filters -->
    <div class="toolbar">
        <label class="srch grow">
            <x-icon name="search" size="16" />
            <input id="q" placeholder="Cari nama, jabatan, WA, atau email..." autocomplete="off">
        </label>

        <div class="sel">
            <select id="klien">
                <option value="">Semua klien</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel">
            <select id="tipe">
                <option value="">Semua tipe</option>
                <option value="PIC">PIC</option>
                <option value="Direktur">Direktur</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel">
            <select id="status">
                <option value="">Semua status</option>
                <option value="active">Aktif</option>
                <option value="archived">Arsip</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <button type="button" id="btn-reset" class="btn btn-outline btn-sm" style="display:none;" onclick="resetFilter()">Reset</button>
    </div>

    <!-- Table -->
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Perusahaan</th>
                    <th>Tipe</th>
                    <th>Jabatan</th>
                    <th>WA</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr><td colspan="8" class="empty">Memuat data kontak...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Pager -->
    <div class="pager">
        <span class="muted sm" id="info-baris"></span>
        <div class="pg-r">
            <div class="sel sm">
                <select id="per-page">
                    <option value="8">8 / halaman</option>
                    <option value="16">16 / halaman</option>
                    <option value="32">32 / halaman</option>
                </select>
                <x-icon name="chevD" size="14" />
            </div>

            <button class="pg" id="prev" disabled aria-label="Sebelumnya"><x-icon name="chevL" size="14" /></button>
            <span class="muted sm" id="no-halaman">1 / 1</span>
            <button class="pg" id="next" disabled aria-label="Berikutnya"><x-icon name="chevR" size="14" /></button>
        </div>
    </div>
    <div class="note">
        <x-icon name="info" size="14" />
        Pilih klien yang sudah tersimpan saat menambah kontak. Buka perusahaan untuk melihat tabel PIC &amp; Direktur.
    </div>
</section>
@endsection

@push('scripts')
<script>
let limit = 8;
let page = 1;
let lastPage = 1;
let searchTimer = null;

const esc = (s) => String(s ?? '-').replace(/[&<>"']/g,
    (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function formatWaLink(phone) {
    if (!phone) return '#';
    let clean = String(phone).replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) clean = '62' + clean.slice(1);
    return 'https://wa.me/' + clean;
}

async function muatKlienFilter() {
    try {
        const res = await api('GET', '/clients?limit=100&status=active');
        const sel = document.getElementById('klien');
        (res.data || []).forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.code + ' · ' + c.name;
            sel.appendChild(opt);
        });
    } catch (e) {
        console.error('Gagal memuat filter klien', e);
    }
}

async function muatKontak() {
    const tbody = document.getElementById('tbody');
    const q = document.getElementById('q').value.trim();
    const clientId = document.getElementById('klien').value;
    const type = document.getElementById('tipe').value;
    const status = document.getElementById('status').value;

    const btnReset = document.getElementById('btn-reset');
    if (btnReset) {
        btnReset.style.display = (q || clientId || type || status) ? 'inline-flex' : 'none';
    }

    const params = new URLSearchParams({ limit, page });
    if (q) params.set('q', q);
    if (clientId) params.set('client_id', clientId);
    if (type) params.set('type', type);
    if (status) params.set('status', status);

    try {
        const res = await api('GET', '/contacts?' + params.toString());
        lastPage = res.meta.last_page || 1;

        if (res.meta.stats) {
            document.getElementById('st-total').textContent = res.meta.stats.total ?? 0;
            document.getElementById('st-pic').textContent = res.meta.stats.pic_count ?? 0;
            document.getElementById('st-dir').textContent = res.meta.stats.dir_count ?? 0;
        }

        document.getElementById('chip-total').textContent = (res.meta.total ?? 0) + ' kontak';
        document.getElementById('info-halaman').textContent = 'Halaman ' + (res.meta.current_page || 1) + ' dari ' + lastPage;
        document.getElementById('info-baris').textContent = 'Menampilkan total ' + (res.meta.total ?? 0) + ' kontak';
        document.getElementById('no-halaman').textContent = (res.meta.current_page || 1) + ' / ' + lastPage;

        document.getElementById('prev').disabled = page <= 1;
        document.getElementById('next').disabled = page >= lastPage;

        if (!res.data || res.data.length === 0) {
            const pesan = (q || clientId || type || status)
                ? 'Tidak ada kontak yang cocok dengan filter pencarian.'
                : 'Belum ada kontak tersimpan. Tambahkan kontak pertama untuk memulai.';
            tbody.innerHTML = '<tr><td colspan="8" class="empty">' + pesan + '</td></tr>';
            return;
        }

        tbody.innerHTML = res.data.map(k => {
            const isAktif = k.status === 'active';
            const clientName = k.client ? esc(k.client.name) : '-';
            const clientCode = k.client ? esc(k.client.code) : '';
            const clientLink = k.client ? ('/klien/' + encodeURIComponent(k.client.code) + '?tab=pic') : '#';
            const waLink = formatWaLink(k.phone);

            return '<tr>'
                + '<td><b>' + esc(k.name) + '</b></td>'
                + '<td><div class="two"><span>' + clientName + '</span><small>' + clientCode + '</small></div></td>'
                + '<td><span class="tag">' + esc(k.type) + '</span></td>'
                + '<td>' + esc(k.position) + '</td>'
                + '<td>' + esc(k.phone) + '</td>'
                + '<td>' + esc(k.email) + '</td>'
                + '<td><span class="badge ' + (isAktif ? 'ok' : 'mute') + '"><i></i>' + (isAktif ? 'Aktif' : 'Arsip') + '</span></td>'
                + '<td>'
                + '<div class="acts rel">'
                + (k.client ? '<a href="' + clientLink + '" title="Buka detail perusahaan klien"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a>' : '')
                + '<button type="button" data-dropdown-trigger aria-label="Menu">'
                + '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>'
                + '</button>'
                + '<div class="dropdown-menu">'
                + (k.phone ? '<a class="dropdown-item" href="' + waLink + '" target="_blank"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Chat WhatsApp</a>' : '')
                + (k.email ? '<a class="dropdown-item" href="mailto:' + esc(k.email) + '"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> Kirim Email</a>' : '')
                + '<a class="dropdown-item" href="/kontak/' + encodeURIComponent(k.id) + '/edit"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit Kontak</a>'
                + '<button type="button" class="dropdown-item" onclick="toggleStatus(\'' + esc(k.id) + '\', \'' + (isAktif ? 'archived' : 'active') + '\')">'
                + '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> ' + (isAktif ? 'Arsipkan' : 'Aktifkan')
                + '</button>'
                + '</div>'
                + '</div>'
                + '</td>'
                + '</tr>';
        }).join('');
    } catch (err) {
        console.error(err);
        const pesan = (err.error && err.error.message) || 'Gagal memuat data kontak.';
        tbody.innerHTML = '<tr><td colspan="8" class="empty">' + esc(pesan) + '</td></tr>';
    }
}

async function toggleStatus(id, newStatus) {
    const act = (newStatus === 'archived') ? 'mengarsipkan' : 'mengaktifkan';
    if (!confirm('Apakah Anda yakin ingin ' + act + ' kontak ini?')) return;
    try {
        await api('PATCH', '/contacts/' + id + '/status', { status: newStatus });
        await muatKontak();
    } catch (err) {
        alert((err.error && err.error.message) || 'Gagal mengubah status kontak.');
    }
}

function resetFilter() {
    document.getElementById('q').value = '';
    document.getElementById('klien').value = '';
    document.getElementById('tipe').value = '';
    document.getElementById('status').value = '';
    page = 1;
    muatKontak();
}

document.getElementById('q').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { page = 1; muatKontak(); }, 300);
});
document.getElementById('klien').addEventListener('change', () => { page = 1; muatKontak(); });
document.getElementById('tipe').addEventListener('change', () => { page = 1; muatKontak(); });
document.getElementById('status').addEventListener('change', () => { page = 1; muatKontak(); });
document.getElementById('per-page').addEventListener('change', (e) => {
    limit = parseInt(e.target.value, 10);
    page = 1;
    muatKontak();
});
document.getElementById('prev').addEventListener('click', () => { if (page > 1) { page--; muatKontak(); } });
document.getElementById('next').addEventListener('click', () => { if (page < lastPage) { page++; muatKontak(); } });

muatKlienFilter();
muatKontak();
</script>
@endpush
