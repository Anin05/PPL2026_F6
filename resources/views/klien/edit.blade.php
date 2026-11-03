@extends('layouts.app')

@section('title', 'Edit Klien - ' . $klien->nama)
@section('page_title', 'Edit Klien')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('klien.index') }}">Master Klien</a>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('klien.show', $klien->kode) }}">{{ $klien->kode }}</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Edit Klien</span>
</nav>

<div class="page-head">
    <div>
        <h1>Edit Klien</h1>
        <p>Perbarui informasi perusahaan klien {{ $klien->nama }}.</p>
    </div>
    <a class="btn btn-outline" href="{{ route('klien.show', $klien->kode) }}">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
    <form class="card" action="{{ route('klien.update', $klien->kode) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="f-head">
            <h2>Informasi Perusahaan</h2>
            <p>Data ini akan menjadi identitas utama klien di sistem.</p>
        </div>

        <div class="f-body">
            <!-- Nama Perusahaan -->
            <div class="field {{ $errors->has('nama') ? 'err' : '' }}">
                <label>Nama Perusahaan <em>*</em></label>
                <div class="inp">
                    <input name="nama" value="{{ old('nama', $klien->nama) }}" placeholder="Masukkan nama perusahaan" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
                @error('nama')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kode Klien (Readonly) -->
            <div class="field">
                <label>Kode Klien</label>
                <div class="inp">
                    <input readonly value="{{ $klien->kode }}" style="background:#f8fafc; color:#64748b;">
                    <span class="ic-lock"><x-icon name="lock" size="15" /></span>
                </div>
                <div class="hint">Kode klien bersifat permanen.</div>
            </div>

            <!-- Alamat -->
            <div class="field full {{ $errors->has('alamat') ? 'err' : '' }}">
                <label>Alamat</label>
                <div class="inp">
                    <textarea name="alamat" placeholder="Contoh: Jl. Jenderal Sudirman No. 25, Tanah Abang">{{ old('alamat', $klien->alamat) }}</textarea>
                </div>
                @error('alamat')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kota -->
            <div class="field {{ $errors->has('kota') ? 'err' : '' }}">
                <label>Kota <em>*</em></label>
                <div class="inp">
                    <input name="kota" value="{{ old('kota', $klien->kota) }}" placeholder="Contoh: Jakarta Pusat" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
                @error('kota')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Telepon -->
            <div class="field {{ $errors->has('telp') ? 'err' : '' }}">
                <label>Telepon</label>
                <div class="inp">
                    <input name="telp" value="{{ old('telp', $klien->telp) }}" placeholder="Contoh: 021-5550-1200">
                </div>
                @error('telp')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- HP / WhatsApp -->
            <div class="field {{ $errors->has('hp') ? 'err' : '' }}">
                <label>No. HP / WA</label>
                <div class="inp">
                    <input name="hp" value="{{ old('hp', $klien->hp) }}" placeholder="Contoh: 0812-9900-1200">
                </div>
                <div class="hint">Dapat digunakan untuk kontak WhatsApp.</div>
                @error('hp')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="field {{ $errors->has('email') ? 'err' : '' }}">
                <label>Email</label>
                <div class="inp">
                    <input name="email" type="email" value="{{ old('email', $klien->email) }}" placeholder="contact@perusahaan.co.id">
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
                @error('email')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Status -->
            <div class="field full">
                <label>Status</label>
                <div class="inp">
                    <select name="status">
                        <option value="Aktif" {{ old('status', $klien->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Arsip" {{ old('status', $klien->status) === 'Arsip' ? 'selected' : '' }}>Arsip</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
                <div class="hint">Status menentukan apakah klien berada di direktori aktif atau arsip.</div>
            </div>

            <div class="f-note full">
                <x-icon name="shield" size="15" />
                Data klien hanya dapat diakses oleh pengguna yang berwenang (Admin / Account Manager).
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="{{ route('klien.show', $klien->kode) }}">Batal</a>
                <button class="btn btn-primary" type="submit">
                    <x-icon name="check" size="16" /> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <aside class="side-col">
        <div class="s-card">
            <h3><x-icon name="info" size="16" /> Panduan pengisian</h3>
            <p>Perubahan data akan langsung berdampak pada seluruh dokumen dan riwayat komunikasi terkait.</p>
            <hr>
            <p><span class="red-dot">•</span> Nama Perusahaan dan Kota wajib diisi.</p>
            <p>Pastikan nomor kontak dan email perusahaan valid.</p>
        </div>

        <div class="s-card">
            <h3><x-icon name="archive" size="16" /> Status Arsip</h3>
            <p>Mengubah status ke <b>Arsip</b> akan memindahkan klien ke tab Arsip tanpa menghapus data kontak atau riwayat komunikasinya.</p>
        </div>
    </aside>
</div>
@endsection

