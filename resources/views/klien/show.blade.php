@extends('layouts.app')

@section('title', 'Detail Klien - ' . $klien->nama)
@section('page_title', 'Detail Klien')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('klien.index') }}">Master Klien</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Detail Klien</span>
</nav>

<div class="page-head">
    <div>
        <h1>Detail Klien</h1>
        <p>Informasi perusahaan dan kontak yang terhubung dengan klien.</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a class="btn btn-outline" href="{{ route('klien.index') }}">
            <x-icon name="arrowL" size="16" /> Kembali
        </a>
    </div>
</div>

<section class="card co-card">
    <div class="co-top">
        <div class="co-l">
            <div class="co-ic"><x-icon name="building" size="28" /></div>
            <div>
                <div class="co-name">
                    <h2>{{ $klien->nama }}</h2>
                    <span class="badge {{ $klien->status === 'Aktif' ? 'ok' : 'mute' }}">
                        <i></i>{{ $klien->status }}
                    </span>
                </div>
                <div class="co-meta">
                    <span>{{ $klien->kode }}</span>
                    •
                    <x-icon name="pin" size="13" />
                    <span>{{ $klien->kota }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:8px;">
            <a class="btn btn-outline" href="{{ route('klien.edit', $klien->kode) }}">
                <x-icon name="edit" size="16" /> Edit Klien
            </a>
            @if ($klien->status === 'Aktif')
                <form action="{{ route('klien.arsipkan', $klien->kode) }}" method="POST" onsubmit="return confirm('Pindahkan klien ke arsip?')">
                    @csrf
                    <button type="submit" class="btn btn-outline">
                        <x-icon name="archive" size="16" /> Arsipkan
                    </button>
                </form>
            @else
                <form action="{{ route('klien.pulihkan', $klien->kode) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline">
                        <x-icon name="archive" size="16" /> Pulihkan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="co-info">
        <span><x-icon name="pin" size="14" /> {{ $klien->alamat ?? 'Alamat belum diatur' }}</span>
        <span><x-icon name="phone" size="14" /> {{ $klien->telp ?? '-' }}</span>
        <span><x-icon name="mail" size="14" /> {{ $klien->email ?? '-' }}</span>
    </div>

    <div class="tabs">
        <a class="tab {{ $tab === 'info' ? 'on' : '' }}" href="{{ route('klien.show', ['kode' => $klien->kode, 'tab' => 'info']) }}">
            Informasi
        </a>
        <a class="tab {{ $tab === 'pic' ? 'on' : '' }}" href="{{ route('klien.show', ['kode' => $klien->kode, 'tab' => 'pic']) }}">
            PIC &amp; Direktur <span class="chip">{{ $kontaks->count() }}</span>
        </a>
        <a class="tab {{ $tab === 'int' ? 'on' : '' }}" href="{{ route('klien.show', ['kode' => $klien->kode, 'tab' => 'int']) }}">
            Riwayat Interaksi <span class="chip">{{ $interaksis->count() }}</span>
        </a>
    </div>
</section>

@if ($tab === 'info')
    <!-- Tab Informasi -->
    <section class="card">
        <div class="card-head">
            <div>
                <h2>Informasi Perusahaan</h2>
                <div class="sub">Data identitas klien di sistem.</div>
            </div>
        </div>
        <dl class="info-grid" style="border-top: 1px solid var(--border)">
            <div><dt>Nama Perusahaan</dt><dd>{{ $klien->nama }}</dd></div>
            <div><dt>Kode Klien</dt><dd>{{ $klien->kode }}</dd></div>
            <div><dt>Status</dt><dd><span class="badge {{ $klien->status === 'Aktif' ? 'ok' : 'mute' }}"><i></i>{{ $klien->status }}</span></dd></div>
            <div class="span2"><dt>Alamat</dt><dd>{{ $klien->alamat ?? '-' }}</dd></div>
            <div><dt>Kota</dt><dd>{{ $klien->kota }}</dd></div>
            <div><dt>Telepon</dt><dd>{{ $klien->telp ?? '-' }}</dd></div>
            <div><dt>No. HP / WA</dt><dd>{{ $klien->hp ?? '-' }}</dd></div>
            <div><dt>Email</dt><dd>{{ $klien->email ?? '-' }}</dd></div>
            @if ($klien->status === 'Arsip')
                <div class="span2">
                    <dt>Alasan diarsipkan</dt>
                    <dd>{{ $klien->alasan_arsip }} · {{ $klien->tgl_arsip?->translatedFormat('d M Y') }} (Oleh: {{ $klien->diarsipkan_oleh }})</dd>
                </div>
            @endif
        </dl>
    </section>
@elseif ($tab === 'int')
    <!-- Tab Riwayat Interaksi -->
    <section class="card">
        <div class="card-head">
            <div>
                <h2>Riwayat Interaksi</h2>
                <div class="sub">{{ $interaksis->count() }} interaksi tercatat untuk klien ini.</div>
            </div>
            <div style="display:flex; gap:8px;">
                <a class="btn btn-primary btn-sm" href="{{ route('riwayat.create', ['klien_kode' => $klien->kode]) }}">
                    <x-icon name="plus" size="14" /> Catat Interaksi
                </a>
                <a class="btn btn-outline btn-sm" href="{{ route('riwayat.index', ['klien' => $klien->kode]) }}">
                    Lihat di Riwayat
                </a>
            </div>
        </div>
        @if ($interaksis->isNotEmpty())
            <ul class="mini-list" style="border-bottom:0">
                @foreach ($interaksis as $i)
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
                            <small>{{ $i->kanal }} · {{ $i->kontak?->nama ?? 'Semua Kontak' }} · Dicatat oleh: {{ $i->oleh }}</small>
                            @if ($i->ringkas)
                                <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $i->ringkas }}</div>
                            @endif
                        </div>
                        <span class="dt">{{ $i->tgl->translatedFormat('d M Y') }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="empty-box" style="border-top:1px solid var(--border)">Belum ada interaksi tercatat untuk klien ini.</div>
        @endif
    </section>
@else
    <!-- Tab PIC & Direktur -->
    <section class="card">
        <div class="card-head">
            <div>
                <h2>PIC &amp; Direktur</h2>
                <div class="sub">{{ $kontaks->count() }} kontak · {{ $picCount }} PIC · {{ $dirCount }} Direktur</div>
            </div>
            <a class="btn btn-primary" href="{{ route('kontak.create', ['klien_kode' => $klien->kode]) }}">
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
                        <th>Divisi</th>
                        <th>WA</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kontaks as $k)
                        <tr>
                            <td>
                                <div class="cell-co">
                                    <span class="avatar round">{{ $k->initials }}</span>
                                    <b>{{ $k->nama }}</b>
                                </div>
                            </td>
                            <td><span class="tag">{{ $k->tipe }}</span></td>
                            <td>{{ $k->jabatan ?? '-' }}</td>
                            <td>{{ $k->divisi ?? '-' }}</td>
                            <td>{{ $k->wa }}</td>
                            <td>{{ $k->email ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $k->status === 'Aktif' ? 'ok' : 'mute' }}">
                                    <i></i>{{ $k->status }}
                                </span>
                            </td>
                            <td>
                                <div class="acts rel">
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
                            <td colspan="8" class="empty">Belum ada kontak untuk klien ini. Klik "Tambah Kontak" untuk menambahkan PIC atau Direktur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="note">
            <x-icon name="info" size="14" />
            Satu klien dapat memiliki lebih dari satu PIC dan Direktur.
        </div>
    </section>
@endif
@endsection

