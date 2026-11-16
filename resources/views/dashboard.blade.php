@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <span class="cur">Dashboard</span>
</nav>

<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <p>Ringkasan data klien, kontak, dan aktivitas komunikasi.</p>
    </div>
</div>

<div class="cards-3">
    <div class="stat">
        <div>
            <div class="stat-l">Klien Aktif</div>
            <div class="stat-v">
                <b id="st-klien-aktif">-</b>
                <span>dari <span id="st-total-klien">-</span> perusahaan</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="building" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Total Kontak</div>
            <div class="stat-v">
                <b id="st-total-kontak">-</b>
                <span>PIC &amp; Direktur</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="contact" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Tindak Lanjut</div>
            <div class="stat-v">
                <b id="st-tindak-lanjut">-</b>
                <span>menunggu dikerjakan</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="calendar" size="18" /></div>
    </div>
</div>

<section class="card">
    <div class="card-head">
        <div>
            <h2>Akses cepat</h2>
            <div class="sub">Menu yang paling sering digunakan.</div>
        </div>
    </div>
    <div class="quick">
        <a href="{{ route('klien.index') }}"><x-icon name="building" size="16" /> Master Klien</a>
        <a href="{{ route('klien.create') }}"><x-icon name="plus" size="16" /> Tambah Klien</a>
        <a href="{{ route('kontak.index') }}"><x-icon name="contact" size="16" /> Kontak Klien</a>
        <a href="{{ route('riwayat.index') }}"><x-icon name="message" size="16" /> Riwayat Interaksi</a>
        <a href="{{ route('arsip.index') }}"><x-icon name="archive" size="16" /> Arsip</a>
    </div>
</section>

<section class="card" style="margin-top: 16px;">
    <div class="card-head">
        <div>
            <h2>Interaksi terbaru</h2>
            <div class="sub">Catatan komunikasi dan tindak lanjut terbaru dengan klien.</div>
        </div>
        <a href="{{ route('riwayat.index') }}" class="btn btn-outline btn-sm">Lihat semua</a>
    </div>
    <ul class="mini-list" id="mini-list-interaksi">
        <li class="empty">Memuat interaksi...</li>
    </ul>
</section>
@endsection

@push('scripts')
<script>
const esc = (s) => String(s ?? '-').replace(/[&<>"']/g,
    (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function getIconSvg(type) {
    const t = String(type).toLowerCase();
    if (t.includes('rapat') || t.includes('meeting') || t.includes('video')) {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>';
    }
    if (t.includes('email') || t.includes('mail')) {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
    }
    if (t.includes('whatsapp') || t.includes('wa') || t.includes('chat')) {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
    }
    return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
}

async function muatDashboard() {
    try {
        const res = await api('GET', '/dashboard');
        const d = res.data;
        document.getElementById('st-klien-aktif').textContent = d.active_clients ?? 0;
        document.getElementById('st-total-klien').textContent = d.total_clients ?? 0;
        document.getElementById('st-total-kontak').textContent = d.total_contacts ?? 0;
        document.getElementById('st-tindak-lanjut').textContent = d.follow_ups ?? 0;

        const list = document.getElementById('mini-list-interaksi');
        if (!d.recent_interactions || d.recent_interactions.length === 0) {
            list.innerHTML = '<li class="empty">Belum ada interaksi tercatat.</li>';
            return;
        }

        list.innerHTML = d.recent_interactions.map(i => {
            const clientText = i.client ? (esc(i.client.code) + ' · ' + esc(i.client.name)) : 'Klien Umum';
            return '<li>'
                + '<span class="i-ic">' + getIconSvg(i.type) + '</span>'
                + '<div>'
                + '<b>' + esc(i.notes) + '</b>'
                + '<small>' + clientText + ' · ' + esc(i.type) + '</small>'
                + '</div>'
                + '<span class="dt">' + esc(i.formatted_date) + '</span>'
                + '</li>';
        }).join('');
    } catch (err) {
        console.error(err);
        document.getElementById('mini-list-interaksi').innerHTML = '<li class="empty">Gagal memuat ringkasan dashboard.</li>';
    }
}

muatDashboard();
</script>
@endpush
