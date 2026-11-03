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
                <b>{{ $klienAktif }}</b>
                <span>dari {{ $totalKlien }} perusahaan</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="building" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Total Kontak</div>
            <div class="stat-v">
                <b>{{ $totalKontak }}</b>
                <span>PIC &amp; Direktur</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="contact" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Tindak Lanjut</div>
            <div class="stat-v">
                <b>{{ $tindakLanjut }}</b>
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
    <ul class="mini-list">
        @forelse ($interaksiTerbaru as $i)
            <li>
                <span class="i-ic">
                    @if ($i->kanal === 'Rapat') <x-icon name="video" size="14" />
                    @elseif ($i->kanal === 'Email') <x-icon name="mail" size="14" />
                    @elseif ($i->kanal === 'WhatsApp') <x-icon name="message" size="14" />
                    @else <x-icon name="phone" size="14" />
                    @endif
                </span>
                <div>
                    <b>{{ $i->subjek }}</b>
                    <small>{{ $i->klien->kode }} · {{ $i->klien->nama }} · {{ $i->kontak?->nama ?? 'Umum' }}</small>
                </div>
                <span class="dt">{{ $i->tgl->translatedFormat('d M Y') }}</span>
            </li>
        @empty
            <li class="empty">Belum ada interaksi tercatat.</li>
        @endforelse
    </ul>
</section>
@endsection

