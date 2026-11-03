@extends('layouts.app')

@section('title', 'Master Klien')
@section('page_title', 'Master Klien')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <span class="cur">Master Klien</span>
</nav>

<div class="page-head">
    <div>
        <h1>Master Klien</h1>
        <p>Kelola data perusahaan klien dan seluruh kontak bisnis Anda.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('klien.create') }}">Tambah Klien</a>
</div>

<div class="cards-3">
    <div class="stat"><div><div class="stat-l">Total Klien</div>
        <div class="stat-v"><b id="st-total">-</b><span>Perusahaan terdaftar</span></div></div></div>
    <div class="stat"><div><div class="stat-l">Aktif</div>
        <div class="stat-v"><b id="st-aktif">-</b></div></div></div>
    <div class="stat"><div><div class="stat-l">Arsip</div>
        <div class="stat-v"><b id="st-arsip">-</b><span>Data tersimpan aman</span></div></div></div>
</div>

<section class="card">
    <div class="card-head">
        <div class="ch-l"><h2>Daftar Klien</h2><span class="chip" id="chip-total">0 klien</span></div>
        <span class="muted sm" id="info-halaman"></span>
    </div>

    <div class="toolbar">
        <label class="srch grow">
            <input id="q" placeholder="Cari kode atau nama perusahaan..." autocomplete="off">
        </label>
        <div class="sel">
            <select id="status">
                <option value="">Semua status</option>
                <option value="active">Aktif</option>
                <option value="archived">Arsip</option>
            </select>
        </div>
    </div>

    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr><th>Kode</th><th>Nama Perusahaan</th><th>Kota</th><th>Telepon</th><th>Email</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody id="tbody">
                <tr><td colspan="7" class="empty">Memuat data klien...</td></tr>
            </tbody>
        </table>
    </div>

    <div class="pager">
        <span class="muted sm" id="info-baris"></span>
        <div class="pg-r">
            <button class="pg" id="prev">&lsaquo;</button>
            <span class="muted sm" id="no-halaman"></span>
            <button class="pg" id="next">&rsaquo;</button>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const LIMIT = 8;
let page = 1, lastPage = 1, timer = null;

const esc = (s) => String(s ?? '-').replace(/[&<>"']/g,
    (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

async function muatStatistik() {
    const [semua, aktif, arsip] = await Promise.all([
        api('GET', '/clients?limit=1'),
        api('GET', '/clients?limit=1&status=active'),
        api('GET', '/clients?limit=1&status=archived'),
    ]);
    document.getElementById('st-total').textContent = semua.meta.total;
    document.getElementById('st-aktif').textContent = aktif.meta.total;
    document.getElementById('st-arsip').textContent = arsip.meta.total;
}

async function muatDaftar() {
    const tbody = document.getElementById('tbody');
    const params = new URLSearchParams({ limit: LIMIT, page });
    const q = document.getElementById('q').value.trim();
    const st = document.getElementById('status').value;
    if (q) params.set('q', q);
    if (st) params.set('status', st);

    try {
        const res = await api('GET', '/clients?' + params.toString());
        lastPage = res.meta.last_page;
        document.getElementById('chip-total').textContent = res.meta.total + ' klien';
        document.getElementById('info-halaman').textContent = 'Halaman ' + res.meta.current_page + ' dari ' + res.meta.last_page;
        document.getElementById('info-baris').textContent = 'Total ' + res.meta.total + ' klien';
        document.getElementById('no-halaman').textContent = res.meta.current_page + ' / ' + res.meta.last_page;
        document.getElementById('prev').disabled = page <= 1;
        document.getElementById('next').disabled = page >= lastPage;

        if (res.data.length === 0) {
            const kosong = (q || st)
                ? 'Data klien tidak ditemukan.'
                : 'Belum ada klien terdaftar. Tambahkan klien pertama untuk memulai.';
            tbody.innerHTML = '<tr><td colspan="7" class="empty">' + kosong + '</td></tr>';
            return;
        }

        tbody.innerHTML = res.data.map((c) => {
            const aktif = c.status === 'active';
            return '<tr>'
                + '<td>' + esc(c.code) + '</td>'
                + '<td><b>' + esc(c.name) + '</b></td>'
                + '<td>' + esc(c.city) + '</td>'
                + '<td>' + esc(c.phone) + '</td>'
                + '<td>' + esc(c.email) + '</td>'
                + '<td><span class="badge ' + (aktif ? 'ok' : 'mute') + '"><i></i>' + (aktif ? 'Aktif' : 'Arsip') + '</span></td>'
                + '<td><a class="btn btn-outline btn-sm" href="/klien/' + encodeURIComponent(c.code) + '">Lihat</a> '
                + '<a class="btn btn-outline btn-sm" href="/klien/' + encodeURIComponent(c.code) + '/edit">Edit</a> '
                + (aktif ? '<button class="btn btn-outline btn-sm" onclick="arsipkan(\'' + esc(c.id) + '\')">Arsipkan</button>' : '')
                + '</td></tr>';
        }).join('');
    } catch (err) {
        const pesan = (err.error && err.error.message) || 'Gagal memuat data klien.';
        tbody.innerHTML = '<tr><td colspan="7" class="empty">' + esc(pesan) + '</td></tr>';
    }
}

async function arsipkan(id) {
    if (!confirm('Arsipkan klien ini?')) return;
    try {
        await api('PATCH', '/clients/' + id + '/status');
        await Promise.all([muatDaftar(), muatStatistik()]);
    } catch (err) {
        alert((err.error && err.error.message) || 'Gagal mengarsipkan klien.');
    }
}

document.getElementById('q').addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => { page = 1; muatDaftar(); }, 300);
});
document.getElementById('status').addEventListener('change', () => { page = 1; muatDaftar(); });
document.getElementById('prev').addEventListener('click', () => { if (page > 1) { page--; muatDaftar(); } });
document.getElementById('next').addEventListener('click', () => { if (page < lastPage) { page++; muatDaftar(); } });

muatStatistik();
muatDaftar();
</script>
@endpush