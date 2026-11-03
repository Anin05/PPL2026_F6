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
                <b>{{ $totalKlien }}</b>
                <span>Perusahaan terdaftar</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="building" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Aktif</div>
            <div class="stat-v">
                <b>{{ $aktifKlien }}</b>
                <span>Di direktori aktif</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="checkCircle" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Arsip</div>
            <div class="stat-v">
                <b>{{ $arsipKlien }}</b>
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
        Pulihkan untuk mengembalikan klien ke direktori aktif. Kontak dan riwayat interaksi tetap tersimpan bersama klien.
    </div>
</div>

<section class="card">
    <div class="card-head">
        <div class="ch-l">
            <h2>Klien Diarsipkan</h2>
            <span class="chip">{{ $kliens->total() }} klien</span>
        </div>
        <span class="muted sm">Halaman {{ $kliens->currentPage() }} dari {{ $kliens->lastPage() }}</span>
    </div>

    <!-- Toolbar Filters -->
    <form action="{{ route('arsip.index') }}" method="GET" class="toolbar">
        <label class="srch grow">
            <x-icon name="search" size="16" />
            <input name="q" placeholder="Cari kode atau nama perusahaan..." value="{{ request('q') }}" autocomplete="off">
        </label>

        <div class="sel">
            <select name="kota" onchange="this.form.submit()">
                <option value="">Semua kota</option>
                @foreach ($kotas as $kota)
                    <option value="{{ $kota }}" {{ request('kota') === $kota ? 'selected' : '' }}>{{ $kota }}</option>
                @endforeach
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel">
            <select name="periode" onchange="this.form.submit()">
                <option value="">Semua periode</option>
                <option value="7" {{ request('periode') === '7' ? 'selected' : '' }}>7 hari terakhir</option>
                <option value="30" {{ request('periode') === '30' ? 'selected' : '' }}>30 hari terakhir</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel">
            <select name="alasan" onchange="this.form.submit()">
                <option value="">Semua alasan</option>
                @foreach ($reasons as $r)
                    <option value="{{ $r }}" {{ request('alasan') === $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        @if (request()->hasAny(['q', 'kota', 'periode', 'alasan']))
            <a href="{{ route('arsip.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
    </form>

    <!-- Table -->
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Kode / Perusahaan</th>
                    <th>Kota</th>
                    <th>Diarsipkan</th>
                    <th>Alasan</th>
                    <th>Kontak</th>
                    <th>Diarsipkan oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kliens as $c)
                    <tr>
                        <td>
                            <div class="two">
                                <span><b>{{ $c->nama }}</b></span>
                                <small>{{ $c->kode }}</small>
                            </div>
                        </td>
                        <td>{{ $c->kota }}</td>
                        <td>{{ $c->tgl_arsip?->translatedFormat('d M Y') ?? '-' }}</td>
                        <td>{{ $c->alasan_arsip ?? 'Diarsipkan manual' }}</td>
                        <td>
                            <span class="cnt">
                                <x-icon name="users" size="14" />
                                {{ $c->kontaks_count }}
                            </span>
                        </td>
                        <td>{{ $c->diarsipkan_oleh ?? 'Admin CRM' }}</td>
                        <td>
                            <div class="acts" style="display:flex; gap:6px;">
                                <a href="{{ route('klien.show', $c->kode) }}" class="btn btn-outline btn-sm" aria-label="Lihat detail">
                                    <x-icon name="eye" size="14" /> Detail
                                </a>
                                <form action="{{ route('klien.pulihkan', $c->kode) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm">
                                        <x-icon name="archive" size="14" /> Pulihkan
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">Tidak ada klien arsip yang cocok dengan pencarian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pager -->
    <div class="pager">
        <span class="muted sm">
            Menampilkan {{ $kliens->firstItem() ?? 0 }}–{{ $kliens->lastItem() ?? 0 }} dari {{ $kliens->total() }} klien arsip
        </span>
        <div class="pg-r">
            <div class="sel sm">
                <select onchange="location.href = this.value">
                    @foreach ([6, 12, 24] as $per)
                        <option value="{{ request()->fullUrlWithQuery(['per' => $per, 'page' => 1]) }}" {{ $kliens->perPage() == $per ? 'selected' : '' }}>
                            {{ $per }} / halaman
                        </option>
                    @endforeach
                </select>
                <x-icon name="chevD" size="14" />
            </div>

            @if ($kliens->onFirstPage())
                <button class="pg" disabled aria-label="Sebelumnya"><x-icon name="chevL" size="14" /></button>
            @else
                <a class="pg" href="{{ $kliens->previousPageUrl() }}" aria-label="Sebelumnya"><x-icon name="chevL" size="14" /></a>
            @endif

            @foreach ($kliens->getUrlRange(max(1, $kliens->currentPage() - 2), min($kliens->lastPage(), $kliens->currentPage() + 2)) as $page => $url)
                <a class="pg {{ $page == $kliens->currentPage() ? 'on' : '' }}" href="{{ $url }}">{{ $page }}</a>
            @endforeach

            @if ($kliens->hasMorePages())
                <a class="pg" href="{{ $kliens->nextPageUrl() }}" aria-label="Berikutnya"><x-icon name="chevR" size="14" /></a>
            @else
                <button class="pg" disabled aria-label="Berikutnya"><x-icon name="chevR" size="14" /></button>
            @endif
        </div>
    </div>
</section>
@endsection

