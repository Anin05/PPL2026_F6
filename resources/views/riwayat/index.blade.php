@extends('layouts.app')

@section('title', 'Riwayat Interaksi')
@section('page_title', 'Riwayat Interaksi')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <span class="cur">Riwayat Interaksi</span>
</nav>

<div class="page-head">
    <div>
        <h1>Riwayat Interaksi</h1>
        <p>Catat komunikasi bisnis dan pantau tindak lanjut untuk setiap klien.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('riwayat.create') }}">
        <x-icon name="plus" size="16" /> Catat Interaksi
    </a>
</div>

<section class="card filter-card">
    <div class="row" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <label class="srch grow">
            <x-icon name="search" size="16" />
            <input id="q" placeholder="Cari catatan, subjek, atau nama klien..." autocomplete="off">
        </label>

        <div class="sel fill">
            <select id="klien">
                <option value="">Semua klien</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel fill">
            <select id="kanal">
                <option value="">Semua kanal</option>
                <option value="Rapat">Rapat / Meeting</option>
                <option value="Email">Email</option>
                <option value="WhatsApp">WhatsApp</option>
                <option value="Telepon">Telepon</option>
                <option value="Kunjungan">Kunjungan</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <button type="button" id="btn-reset" class="btn btn-outline btn-sm" style="display:none;" onclick="resetFilter()">Reset</button>
    </div>
    <div class="hint" style="margin-top:8px;">Saat mencatat interaksi, pilih klien yang sudah tersimpan untuk menghubungkan rekam komunikasi secara otomatis.</div>
</section>

<div class="r-grid">
    <!-- List Column -->
    <section class="card" id="rList">
        <div class="r-tabs">
            <a class="tab on" id="tab-all" href="javascript:void(0)" onclick="switchTab('all')">
                Komunikasi tercatat <span class="chip" id="tab-all-count">0</span>
            </a>
            <a class="tab" id="tab-tl" href="javascript:void(0)" onclick="switchTab('tl')">
                Tindak lanjut <span class="chip" id="tab-tl-count">0</span>
            </a>
            <span class="sort">Terbaru lebih dahulu</span>
        </div>

        <div id="list-container">
            <div class="empty-box">Memuat riwayat interaksi...</div>
        </div>

        <!-- Pager -->
        <div class="pager" id="pager-box" style="display:none;">
            <span class="muted sm" id="info-baris"></span>
            <div class="pg-r">
                <button class="pg" id="prev" disabled aria-label="Sebelumnya"><x-icon name="chevL" size="14" /></button>
                <span class="muted sm" id="no-halaman">1 / 1</span>
                <button class="pg" id="next" disabled aria-label="Berikutnya"><x-icon name="chevR" size="14" /></button>
            </div>
        </div>
    </section>

    <!-- Detail Column -->
    <section class="card d-card" id="rDetail">
        <div id="detail-content">
            <div class="empty-box">Pilih interaksi di sebelah kiri untuk melihat detailnya.</div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
let currentTab = 'all';
let page = 1;
let lastPage = 1;
let selectedId = null;
let currentItems = [];
let searchTimer = null;

const esc = (s) => String(s ?? '-').replace(/[&<>"']/g,
    (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function getIconSvg(type) {
    const t = String(type).toLowerCase();
    if (t.includes('rapat') || t.includes('meeting') || t.includes('video')) {
        return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>';
    }
    if (t.includes('email') || t.includes('mail')) {
        return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
    }
    if (t.includes('whatsapp') || t.includes('wa') || t.includes('chat')) {
        return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
    }
    if (t.includes('kunjungan')) {
        return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    }
    return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
}

function switchTab(tab) {
    currentTab = tab;
    page = 1;
    document.getElementById('tab-all').classList.toggle('on', tab === 'all');
    document.getElementById('tab-tl').classList.toggle('on', tab === 'tl');
    muatRiwayat();
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
        console.error(e);
    }
}

async function muatRiwayat() {
    const list = document.getElementById('list-container');
    const q = document.getElementById('q').value.trim();
    const clientId = document.getElementById('klien').value;
    const kanal = document.getElementById('kanal').value;

    const btnReset = document.getElementById('btn-reset');
    if (btnReset) {
        btnReset.style.display = (q || clientId || kanal) ? 'inline-flex' : 'none';
    }

    const params = new URLSearchParams({ limit: 10, page, tab: currentTab });
    if (q) params.set('q', q);
    if (clientId) params.set('client_id', clientId);
    if (kanal) params.set('type', kanal);

    try {
        const res = await api('GET', '/interactions?' + params.toString());
        currentItems = res.data || [];
        lastPage = res.meta.last_page || 1;

        if (res.meta.stats) {
            document.getElementById('tab-all-count').textContent = res.meta.stats.all_count ?? 0;
            document.getElementById('tab-tl-count').textContent = res.meta.stats.tl_count ?? 0;
        }

        const pagerBox = document.getElementById('pager-box');
        if (res.meta.total > 0) {
            pagerBox.style.display = 'flex';
            document.getElementById('info-baris').textContent = 'Total ' + res.meta.total + ' interaksi';
            document.getElementById('no-halaman').textContent = (res.meta.current_page || 1) + ' / ' + lastPage;
            document.getElementById('prev').disabled = page <= 1;
            document.getElementById('next').disabled = page >= lastPage;
        } else {
            pagerBox.style.display = 'none';
        }

        if (currentItems.length === 0) {
            list.innerHTML = '<div class="empty-box">Tidak ada riwayat interaksi yang cocok.</div>';
            renderDetail(null);
            return;
        }

        list.innerHTML = currentItems.map(i => {
            const clientName = i.client ? esc(i.client.name) : 'Klien';
            const clientCode = i.client ? esc(i.client.code) : '-';
            const isOn = selectedId === i.id;

            return '<div class="i-item ' + (isOn ? 'on' : '') + '" onclick="pilihInteraksi(\'' + esc(i.id) + '\')" style="cursor:pointer;">'
                + '<span class="i-ic">' + getIconSvg(i.type) + '</span>'
                + '<div class="i-body">'
                + '<div class="i-top"><b>' + esc(i.notes).slice(0, 50) + (i.notes.length > 50 ? '...' : '') + '</b><span>' + esc(i.date_formatted) + '</span></div>'
                + '<div class="i-co">' + clientCode + ' · ' + clientName + '</div>'
                + '<div class="i-sum">' + esc(i.notes) + '</div>'
                + '<div class="i-bot">'
                + '<span>' + esc(i.type) + ' · ' + esc(i.time_formatted) + ' WIB</span>'
                + (i.is_follow_up ? '<span class="fu" style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600;">Tindak Lanjut</span>' : '<span class="nofu">Selesai</span>')
                + '</div>'
                + '</div>'
                + '</div>';
        }).join('');

        // Select first item if none selected or previous selection no longer in list
        if (!selectedId || !currentItems.some(x => x.id === selectedId)) {
            pilihInteraksi(currentItems[0].id);
        } else {
            const found = currentItems.find(x => x.id === selectedId);
            renderDetail(found);
        }
    } catch (err) {
        console.error(err);
        list.innerHTML = '<div class="empty-box">Gagal memuat riwayat interaksi.</div>';
        renderDetail(null);
    }
}

function pilihInteraksi(id) {
    selectedId = id;
    document.querySelectorAll('.i-item').forEach(el => el.classList.remove('on'));
    const target = currentItems.find(x => x.id === id);
    renderDetail(target);

    // mark active class in list
    const items = document.querySelectorAll('.i-item');
    currentItems.forEach((x, idx) => {
        if (x.id === id && items[idx]) items[idx].classList.add('on');
    });
}

function renderDetail(item) {
    const detailBox = document.getElementById('detail-content');
    if (!item) {
        detailBox.innerHTML = '<div class="empty-box">Pilih interaksi untuk melihat detailnya.</div>';
        return;
    }

    const clientName = item.client ? esc(item.client.name) : '-';
    const clientCode = item.client ? esc(item.client.code) : '-';
    const clientLink = item.client ? ('/klien/' + encodeURIComponent(item.client.code)) : '#';

    detailBox.innerHTML = '<h3>Detail Interaksi</h3>'
        + '<span class="d-pill"><i></i>Komunikasi ' + esc(item.type) + '</span>'
        + '<div class="d-title" style="margin-top:12px; font-size:16px; font-weight:700; color:var(--navy);">' + esc(item.notes) + '</div>'
        + '<div class="d-when" style="font-size:13px; color:var(--muted); margin:4px 0 16px 0;">'
        + esc(item.date_formatted) + ' · ' + esc(item.time_formatted) + ' WIB · ' + esc(item.type)
        + '</div>'
        + '<div class="d-row" style="padding:10px 0; border-top:1px solid var(--border);"><small style="display:block; font-size:11px; color:var(--muted); text-transform:uppercase;">Klien Terkait</small><b>' + clientCode + ' · ' + clientName + '</b></div>'
        + '<div class="d-row" style="padding:10px 0; border-top:1px solid var(--border);"><small style="display:block; font-size:11px; color:var(--muted); text-transform:uppercase;">Catatan / Hasil Pembahasan</small><div style="margin-top:4px; font-size:13.5px; line-height:1.6; color:var(--navy);">' + esc(item.notes) + '</div></div>'
        + (item.is_follow_up ? '<div class="d-fu" style="margin-top:14px; background:#fffbeb; border:1px solid #fde68a; padding:12px; border-radius:8px;"><div class="t" style="display:flex; justify-content:space-between; font-weight:700; color:#92400e;"><b>Status: Perlu Tindak Lanjut</b></div><small style="color:#b45309; display:block; margin-top:4px;">Interaksi ini terjadwal atau memerlukan follow up dengan klien.</small></div>' : '')
        + '<div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">'
        + '<a class="d-link" href="' + clientLink + '" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:var(--primary); text-decoration:none;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M9 8h1"/><path d="M9 12h1"/><path d="M9 16h1"/><path d="M14 8h1"/><path d="M14 12h1"/><path d="M14 16h1"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/></svg> Buka Detail Klien</a>'
        + '<button type="button" class="btn btn-outline btn-sm" style="color:#dc2626; border-color:#fca5a5;" onclick="hapusInteraksi(\'' + esc(item.id) + '\')">Hapus Catatan</button>'
        + '</div>';
}

async function hapusInteraksi(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus catatan interaksi ini?')) return;
    try {
        await api('DELETE', '/interactions/' + encodeURIComponent(id));
        selectedId = null;
        await muatRiwayat();
    } catch (err) {
        alert((err.error && err.error.message) || 'Gagal menghapus interaksi.');
    }
}

function resetFilter() {
    document.getElementById('q').value = '';
    document.getElementById('klien').value = '';
    document.getElementById('kanal').value = '';
    page = 1;
    muatRiwayat();
}

document.getElementById('q').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { page = 1; muatRiwayat(); }, 300);
});
document.getElementById('klien').addEventListener('change', () => { page = 1; muatRiwayat(); });
document.getElementById('kanal').addEventListener('change', () => { page = 1; muatRiwayat(); });
document.getElementById('prev').addEventListener('click', () => { if (page > 1) { page--; muatRiwayat(); } });
document.getElementById('next').addEventListener('click', () => { if (page < lastPage) { page++; muatRiwayat(); } });

muatKlienFilter();
muatRiwayat();
</script>
@endpush
