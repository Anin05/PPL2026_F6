@extends('layouts.app')

@section('title', 'Arsip Klien')
@section('page_title', 'Arsip')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <span class="cur">Arsip</span>
</nav>

<div class="page-head">
    <div>
        <h1>Arsip</h1>
        <p>Tinjau klien yang diarsipkan dan pulihkan saat kerja sama berlanjut.</p>
    </div>
</div>

<div class="cards-3">
    <div class="stat">
        <div>
            <div class="stat-l">Total Klien</div>
            <div class="stat-v">
                <b id="st-total">-</b>
                <span>Perusahaan terdaftar</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="building" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Aktif</div>
            <div class="stat-v">
                <b id="st-aktif">-</b>
                <span>Di direktori aktif</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="check" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Arsip</div>
            <div class="stat-v">
                <b id="st-arsip">-</b>
                <span>Data tetap tersimpan</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="archive" size="18" /></div>
    </div>
</div>

<div class="banner">
    <x-icon name="info" size="18" />
    <div>
        <b>Arsip bukan penghapusan permanen</b>
        Pulihkan untuk mengembalikan klien ke direktori aktif. Kontak dan riwayat interaksi tetap tersimpan aman bersama klien.
    </div>
</div>

<section class="card">
    <div class="card-head">
        <div class="ch-l">
            <h2>Klien Diarsipkan</h2>
            <span class="chip" id="chip-total">0 klien</span>
        </div>
        <span class="muted sm" id="info-halaman"></span>
    </div>

    <!-- Toolbar Filters -->
    <div class="toolbar">
        <label class="srch grow">
            <x-icon name="search" size="16" />
            <input id="q" placeholder="Cari kode atau nama perusahaan..." autocomplete="off">
        </label>

        <div class="sel">
            <select id="kota">
                <option value="">Semua kota</option>
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
                    <th>Kode / Perusahaan</th>
                    <th>Kota</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Kontak Terkait</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr><td colspan="7" class="empty">Memuat data arsip klien...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Pager -->
    <div class="pager">
        <span class="muted sm" id="info-baris"></span>
        <div class="pg-r">
            <div class="sel sm">
                <select id="per-page">
                    <option value="6">6 / halaman</option>
                    <option value="12">12 / halaman</option>
                    <option value="24">24 / halaman</option>
                </select>
                <x-icon name="chevD" size="14" />
            </div>

            <button class="pg" id="prev" disabled aria-label="Sebelumnya"><x-icon name="chevL" size="14" /></button>
            <span class="muted sm" id="no-halaman">1 / 1</span>
            <button class="pg" id="next" disabled aria-label="Berikutnya"><x-icon name="chevR" size="14" /></button>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let limit = 6;
let page = 1;
let lastPage = 1;
let searchTimer = null;
let citiesLoaded = false;

const esc = (s) => String(s ?? '-').replace(/[&<>"']/g,
    (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

async function muatKotaFilter() {
    if (citiesLoaded) return;
    try {
        const res = await api('GET', '/clients?limit=100');
        const cities = new Set();
        (res.data || []).forEach(c => { if (c.city) cities.add(c.city); });
        const sel = document.getElementById('kota');
        cities.forEach(city => {
            const opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            sel.appendChild(opt);
        });
        citiesLoaded = true;
    } catch (e) {
        console.error(e);
    }
}

async function muatArsip() {
    const tbody = document.getElementById('tbody');
    const q = document.getElementById('q').value.trim();
    const kota = document.getElementById('kota').value;

    const btnReset = document.getElementById('btn-reset');
    if (btnReset) {
        btnReset.style.display = (q || kota) ? 'inline-flex' : 'none';
    }

    const params = new URLSearchParams({ limit, page, status: 'archived' });
    if (q) params.set('q', q);
    if (kota) params.set('city', kota);

    try {
        const res = await api('GET', '/clients?' + params.toString());
        lastPage = res.meta.last_page || 1;

        if (res.meta.stats) {
            document.getElementById('st-total').textContent = res.meta.stats.total ?? 0;
            document.getElementById('st-aktif').textContent = res.meta.stats.active ?? 0;
            document.getElementById('st-arsip').textContent = res.meta.stats.archived ?? 0;
        }

        document.getElementById('chip-total').textContent = (res.meta.total ?? 0) + ' klien arsip';
        document.getElementById('info-halaman').textContent = 'Halaman ' + (res.meta.current_page || 1) + ' dari ' + lastPage;
        document.getElementById('info-baris').textContent = 'Menampilkan total ' + (res.meta.total ?? 0) + ' klien arsip';
        document.getElementById('no-halaman').textContent = (res.meta.current_page || 1) + ' / ' + lastPage;

        document.getElementById('prev').disabled = page <= 1;
        document.getElementById('next').disabled = page >= lastPage;

        if (!res.data || res.data.length === 0) {
            const pesan = (q || kota)
                ? 'Tidak ada klien arsip yang cocok dengan filter pencarian.'
                : 'Belum ada klien di arsip. Klien yang diarsipkan dari Master Klien akan muncul di sini.';
            tbody.innerHTML = '<tr><td colspan="7" class="empty">' + pesan + '</td></tr>';
            return;
        }

        tbody.innerHTML = res.data.map(c => {
            const contactsCount = c.contacts_count ?? 0;

            return '<tr>'
                + '<td><div class="two"><span><b>' + esc(c.name) + '</b></span><small>' + esc(c.code) + '</small></div></td>'
                + '<td>' + esc(c.city) + '</td>'
                + '<td>' + esc(c.phone) + '</td>'
                + '<td>' + esc(c.email) + '</td>'
                + '<td><span class="cnt" style="display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:600;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> ' + contactsCount + ' kontak</span></td>'
                + '<td><span class="badge mute"><i></i>Arsip</span></td>'
                + '<td>'
                + '<div class="acts" style="display:flex; gap:6px;">'
                + '<a href="/klien/' + encodeURIComponent(c.code) + '" class="btn btn-outline btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Detail</a>'
                + '<button type="button" class="btn btn-outline btn-sm" onclick="pulihkan(\'' + esc(c.id) + '\')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg> Pulihkan</button>'
                + '</div>'
                + '</td>'
                + '</tr>';
        }).join('');
    } catch (err) {
        console.error(err);
        const pesan = (err.error && err.error.message) || 'Gagal memuat arsip klien.';
        tbody.innerHTML = '<tr><td colspan="7" class="empty">' + esc(pesan) + '</td></tr>';
    }
}

async function pulihkan(id) {
    if (!confirm('Pulihkan klien ini dan kembalikan ke daftar aktif?')) return;
    try {
        await api('PATCH', '/clients/' + encodeURIComponent(id) + '/status', { status: 'active' });
        await muatArsip();
    } catch (err) {
        alert((err.error && err.error.message) || 'Gagal memulihkan klien.');
    }
}

function resetFilter() {
    document.getElementById('q').value = '';
    document.getElementById('kota').value = '';
    page = 1;
    muatArsip();
}

document.getElementById('q').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { page = 1; muatArsip(); }, 300);
});
document.getElementById('kota').addEventListener('change', () => { page = 1; muatArsip(); });
document.getElementById('per-page').addEventListener('change', (e) => {
    limit = parseInt(e.target.value, 10);
    page = 1;
    muatArsip();
});
document.getElementById('prev').addEventListener('click', () => { if (page > 1) { page--; muatArsip(); } });
document.getElementById('next').addEventListener('click', () => { if (page < lastPage) { page++; muatArsip(); } });

muatKotaFilter();
muatArsip();
</script>
@endpush
