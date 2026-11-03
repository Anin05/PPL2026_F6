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
                <b>{{ $totalKontak }}</b>
                <span>Kontak tersimpan</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="contact" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">PIC</div>
            <div class="stat-v">
                <b>{{ $picCount }}</b>
                <span>Kontak operasional</span>
            </div>
        </div>
        <div class="stat-ic"><x-icon name="users" size="18" /></div>
    </div>
    <div class="stat">
        <div>
            <div class="stat-l">Direktur</div>
            <div class="stat-v">
                <b>{{ $dirCount }}</b>
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
            <span class="chip">{{ $kontaks->total() }} kontak</span>
        </div>
        <span class="muted sm">Halaman {{ $kontaks->currentPage() }} dari {{ $kontaks->lastPage() }}</span>
    </div>

    <!-- Toolbar Filters -->
    <form action="{{ route('kontak.index') }}" method="GET" class="toolbar">
        <label class="srch grow">
            <x-icon name="search" size="16" />
            <input name="q" placeholder="Cari nama, jabatan, WA, atau email..." value="{{ request('q') }}" autocomplete="off">
        </label>

        <div class="sel">
            <select name="klien" onchange="this.form.submit()">
                <option value="">Semua klien</option>
                @foreach ($clients as $c)
                    <option value="{{ $c->kode }}" {{ request('klien') === $c->kode ? 'selected' : '' }}>
                        {{ $c->kode }} · {{ $c->nama }}
                    </option>
                @endforeach
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel">
            <select name="tipe" onchange="this.form.submit()">
                <option value="">Semua tipe</option>
                <option value="PIC" {{ request('tipe') === 'PIC' ? 'selected' : '' }}>PIC</option>
                <option value="Direktur" {{ request('tipe') === 'Direktur' ? 'selected' : '' }}>Direktur</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        <div class="sel">
            <select name="status" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <x-icon name="chevD" size="14" />
        </div>

        @if (request()->hasAny(['q', 'klien', 'tipe', 'status']))
            <a href="{{ route('kontak.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
    </form>

    <!-- Table -->
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Perusahaan</th>
                    <th>Tipe</th>
                    <th>Jabatan / Divisi</th>
                    <th>WA</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kontaks as $k)
                    <tr>
                        <td><b>{{ $k->nama }}</b></td>
                        <td>
                            <div class="two">
                                <span>{{ $k->klien->nama }}</span>
                                <small>{{ $k->klien->kode }}</small>
                            </div>
                        </td>
                        <td><span class="tag">{{ $k->tipe }}</span></td>
                        <td>
                            <div class="two">
                                <span>{{ $k->jabatan ?? '-' }}</span>
                                <small>{{ $k->divisi ?? '-' }}</small>
                            </div>
                        </td>
                        <td>{{ $k->wa }}</td>
                        <td>{{ $k->email ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $k->status === 'Aktif' ? 'ok' : 'mute' }}">
                                <i></i>{{ $k->status }}
                            </span>
                        </td>
                        <td>
                            <div class="acts rel">
                                <a href="{{ route('klien.show', ['kode' => $k->klien->kode, 'tab' => 'pic']) }}" title="Buka detail perusahaan klien">
                                    <x-icon name="ext" size="15" />
                                </a>
                                <button type="button" data-dropdown-trigger aria-label="Menu">
                                    <x-icon name="more" size="16" />
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ $k->wa_link }}" target="_blank">
                                        <x-icon name="message" size="14" /> Chat WhatsApp
                                    </a>
                                    @if ($k->email)
                                        <a class="dropdown-item" href="mailto:{{ $k->email }}">
                                            <x-icon name="mail" size="14" /> Kirim Email
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('kontak.edit', $k->id) }}">
                                        <x-icon name="edit" size="14" /> Edit Kontak
                                    </a>
                                    <form action="{{ route('kontak.toggle', $k->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <x-icon name="check" size="14" /> {{ $k->status === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">Tidak ada kontak yang cocok dengan filter pencarian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pager -->
    <div class="pager">
        <span class="muted sm">
            Menampilkan {{ $kontaks->firstItem() ?? 0 }}–{{ $kontaks->lastItem() ?? 0 }} dari {{ $kontaks->total() }} kontak
        </span>
        <div class="pg-r">
            <div class="sel sm">
                <select onchange="location.href = this.value">
                    @foreach ([8, 16, 32] as $per)
                        <option value="{{ request()->fullUrlWithQuery(['per' => $per, 'page' => 1]) }}" {{ $kontaks->perPage() == $per ? 'selected' : '' }}>
                            {{ $per }} / halaman
                        </option>
                    @endforeach
                </select>
                <x-icon name="chevD" size="14" />
            </div>

            @if ($kontaks->onFirstPage())
                <button class="pg" disabled aria-label="Sebelumnya"><x-icon name="chevL" size="14" /></button>
            @else
                <a class="pg" href="{{ $kontaks->previousPageUrl() }}" aria-label="Sebelumnya"><x-icon name="chevL" size="14" /></a>
            @endif

            @foreach ($kontaks->getUrlRange(max(1, $kontaks->currentPage() - 2), min($kontaks->lastPage(), $kontaks->currentPage() + 2)) as $page => $url)
                <a class="pg {{ $page == $kontaks->currentPage() ? 'on' : '' }}" href="{{ $url }}">{{ $page }}</a>
            @endforeach

            @if ($kontaks->hasMorePages())
                <a class="pg" href="{{ $kontaks->nextPageUrl() }}" aria-label="Berikutnya"><x-icon name="chevR" size="14" /></a>
            @else
                <button class="pg" disabled aria-label="Berikutnya"><x-icon name="chevR" size="14" /></button>
            @endif
        </div>
    </div>
    <div class="note">
        <x-icon name="info" size="14" />
        Pilih klien yang sudah tersimpan saat menambah kontak. Buka perusahaan untuk melihat tabel PIC &amp; Direktur.
    </div>
</section>
@endsection

