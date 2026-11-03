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
    <form action="{{ route('riwayat.index') }}" method="GET" class="row">
        <input type="hidden" name="tab" value="{{ $tab }}">
        
        <label class="srch grow">
            <x-icon name="search" size="16" />
            <input name="q" placeholder="Cari subjek atau nama kontak..." value="{{ request('q') }}" autocomplete="off">
        </label>

        <div class="sel fill">
            <select name="klien" onchange="this.form.submit()">
                <option value="">Semua klien</option>
                @foreach ($klienList as $k)
                    <option value="{{ $k->kode }}" {{ request('klien') === $k->kode ? 'selected' : '' }}>
                        {{ $k->kode }} · {{ $k->nama }}
                    </option>
                @endforeach
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel fill">
            <select name="kanal" onchange="this.form.submit()">
                <option value="">Semua kanal</option>
                <option value="Rapat" {{ request('kanal') === 'Rapat' ? 'selected' : '' }}>Rapat</option>
                <option value="Email" {{ request('kanal') === 'Email' ? 'selected' : '' }}>Email</option>
                <option value="WhatsApp" {{ request('kanal') === 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                <option value="Telepon" {{ request('kanal') === 'Telepon' ? 'selected' : '' }}>Telepon</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel fill">
            <select name="periode" onchange="this.form.submit()">
                <option value="">Semua periode</option>
                <option value="late" {{ request('periode') === 'late' ? 'selected' : '' }}>17–24 Okt 2026</option>
                <option value="early" {{ request('periode') === 'early' ? 'selected' : '' }}>1–16 Okt 2026</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        @if (request()->hasAny(['q', 'klien', 'kanal', 'periode']))
            <a href="{{ route('riwayat.index', ['tab' => $tab]) }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
    </form>
    <div class="hint">Saat mencatat interaksi, pilih klien yang sudah tersimpan lalu pilih kontak dari perusahaan tersebut.</div>
</section>

<div class="r-grid">
    <!-- List Column -->
    <section class="card" id="rList">
        <div class="r-tabs">
            <a class="tab {{ $tab === 'all' ? 'on' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'all', 'page' => 1]) }}">
                Komunikasi tercatat <span class="chip">{{ $allCount }}</span>
            </a>
            <a class="tab {{ $tab === 'tl' ? 'on' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'tl', 'page' => 1]) }}">
                Tindak lanjut <span class="chip">{{ $tlCount }}</span>
            </a>
            <span class="sort">Terbaru lebih dahulu</span>
        </div>

        @forelse ($interaksis as $i)
            <div class="i-item {{ $selected && $selected->id === $i->id ? 'on' : '' }}" onclick="location.href='{{ request()->fullUrlWithQuery(['sel' => $i->id]) }}'" style="cursor: pointer;">
                <span class="i-ic">
                    @if ($i->kanal === 'Rapat') <x-icon name="video" size="15" />
                    @elseif ($i->kanal === 'Email') <x-icon name="mail" size="15" />
                    @elseif ($i->kanal === 'WhatsApp') <x-icon name="message" size="15" />
                    @else <x-icon name="phone" size="15" />
                    @endif
                </span>
                <div class="i-body">
                    <div class="i-top">
                        <b>{{ $i->subjek }}</b>
                        <span>{{ $i->tgl->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="i-co">{{ $i->klien->kode }} · {{ $i->klien->nama }} · {{ $i->kontak?->nama ?? 'Semua Kontak' }}</div>
                    <div class="i-sum">{{ $i->ringkas }}</div>
                    <div class="i-bot">
                        <span>{{ $i->kanal }} · {{ $i->jam }} WIB · Dicatat: {{ $i->oleh }}</span>
                        @if ($i->follow_tgl)
                            <span class="fu">{{ $i->follow_tgl->translatedFormat('d M') }} · {{ $i->follow_aksi }}</span>
                        @else
                            <span class="nofu">Tidak ada tindak lanjut</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-box">Tidak ada interaksi yang cocok dengan pencarian.</div>
        @endforelse

        <!-- Pager -->
        @if ($interaksis->hasPages())
            <div class="pager">
                <span class="muted sm">Menampilkan {{ $interaksis->firstItem() }}–{{ $interaksis->lastItem() }} dari {{ $interaksis->total() }}</span>
                <div class="pg-r">
                    @if ($interaksis->onFirstPage())
                        <button class="pg" disabled><x-icon name="chevL" size="14" /></button>
                    @else
                        <a class="pg" href="{{ $interaksis->previousPageUrl() }}"><x-icon name="chevL" size="14" /></a>
                    @endif

                    @foreach ($interaksis->getUrlRange(max(1, $interaksis->currentPage() - 1), min($interaksis->lastPage(), $interaksis->currentPage() + 1)) as $page => $url)
                        <a class="pg {{ $page == $interaksis->currentPage() ? 'on' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach

                    @if ($interaksis->hasMorePages())
                        <a class="pg" href="{{ $interaksis->nextPageUrl() }}"><x-icon name="chevR" size="14" /></a>
                    @else
                        <button class="pg" disabled><x-icon name="chevR" size="14" /></button>
                    @endif
                </div>
            </div>
        @endif
    </section>

    <!-- Detail Column -->
    <section class="card d-card" id="rDetail">
        @if ($selected)
            <h3>Detail Interaksi</h3>
            <span class="d-pill"><i></i>Komunikasi {{ strtolower($selected->status) }}</span>

            <div class="d-title">{{ $selected->subjek }}</div>
            <div class="d-when">
                {{ $selected->tgl->translatedFormat('d F Y') }} · {{ $selected->jam }}–{{ $selected->akhir }} WIB · {{ $selected->kanal }}
            </div>

            <div class="d-row">
                <small>Klien terkait</small>
                <b>{{ $selected->klien->kode }} · {{ $selected->klien->nama }}</b>
            </div>

            <div class="d-row d-two">
                <div>
                    <small>Kontak terkait</small>
                    <b>{{ $selected->kontak?->nama ?? '-' }}</b>
                    <div class="muted sm">{{ $selected->kontak ? "{$selected->kontak->tipe} · {$selected->kontak->jabatan}" : '' }}</div>
                </div>
                <div>
                    <small>Dicatat oleh</small>
                    <b>{{ $selected->oleh }}</b>
                </div>
            </div>

            <div class="d-row">
                <small>Ringkasan hasil</small>
                <span>{{ $selected->hasil ?? $selected->ringkas }}</span>
            </div>

            @if ($selected->follow_tgl)
                <div class="d-fu">
                    <div class="t">
                        <b>Tindak lanjut</b>
                        <span class="tg">{{ $selected->follow_status }}</span>
                    </div>
                    <div>{{ $selected->follow_detail }}</div>
                    <small>{{ $selected->follow_tgl->translatedFormat('d F Y') }} · Penanggung jawab: {{ $selected->follow_pj }}</small>
                </div>
            @endif

            <a class="d-link" href="{{ route('klien.show', ['kode' => $selected->klien->kode, 'tab' => 'pic']) }}">
                <x-icon name="building" size="14" />
                Lihat klien &amp; kontak terkait
                <x-icon name="chevR" size="14" />
            </a>
        @else
            <div class="empty-box">Pilih interaksi untuk melihat detailnya.</div>
        @endif
    </section>
</div>
@endsection

