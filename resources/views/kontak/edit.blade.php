@extends('layouts.app')

@section('title', 'Edit Kontak - ' . $kontak->nama)
@section('page_title', 'Edit Kontak')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('klien.show', $kontak->klien->kode) }}">{{ $kontak->klien->nama }}</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Edit Kontak</span>
</nav>

<div class="page-head">
    <div>
        <h1>Edit Kontak</h1>
        <p>Perbarui data kontak {{ $kontak->nama }} dari {{ $kontak->klien->nama }}.</p>
    </div>
    <a class="btn btn-outline" href="{{ route('klien.show', ['kode' => $kontak->klien->kode, 'tab' => 'pic']) }}">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
    <form class="card" action="{{ route('kontak.update', $kontak->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="f-head">
            <h2>Informasi Kontak</h2>
            <p>Kontak terhubung dengan <b>{{ $kontak->klien->nama }}</b> ({{ $kontak->klien->kode }}).</p>
        </div>

        <div class="f-body">
            <!-- Nama -->
            <div class="field {{ $errors->has('nama') ? 'err' : '' }}">
                <label>Nama Lengkap <em>*</em></label>
                <div class="inp">
                    <input name="nama" value="{{ old('nama', $kontak->nama) }}" placeholder="Nama lengkap kontak" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
                @error('nama')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tipe -->
            <div class="field">
                <label>Tipe Kontak <em>*</em></label>
                <div class="seg" id="segTipe">
                    <button type="button" class="{{ old('tipe', $kontak->tipe) === 'PIC' ? 'on' : '' }}" onclick="setTipe('PIC')">
                        <x-icon name="check" size="14" /> PIC
                    </button>
                    <button type="button" class="{{ old('tipe', $kontak->tipe) === 'Direktur' ? 'on' : '' }}" onclick="setTipe('Direktur')">
                        <x-icon name="check" size="14" /> Direktur
                    </button>
                </div>
                <input type="hidden" name="tipe" id="inpTipe" value="{{ old('tipe', $kontak->tipe) }}">
            </div>

            <!-- Jabatan -->
            <div class="field {{ $errors->has('jabatan') ? 'err' : '' }}">
                <label>Jabatan</label>
                <div class="inp">
                    <input name="jabatan" value="{{ old('jabatan', $kontak->jabatan) }}" placeholder="Contoh: Manajer Pengadaan">
                </div>
                @error('jabatan')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Divisi -->
            <div class="field {{ $errors->has('divisi') ? 'err' : '' }}">
                <label>Divisi</label>
                <div class="inp">
                    <input name="divisi" value="{{ old('divisi', $kontak->divisi) }}" placeholder="Contoh: Procurement">
                </div>
                @error('divisi')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- WhatsApp -->
            <div class="field {{ $errors->has('wa') ? 'err' : '' }}">
                <label>Nomor WhatsApp <em>*</em></label>
                <div class="inp">
                    <input name="wa" value="{{ old('wa', $kontak->wa) }}" placeholder="Contoh: 0812-3456-7890" required>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
                @error('wa')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="field {{ $errors->has('email') ? 'err' : '' }}">
                <label>Email</label>
                <div class="inp">
                    <input name="email" type="email" value="{{ old('email', $kontak->email) }}" placeholder="nama@perusahaan.co.id">
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
                @error('email')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Status -->
            <div class="field">
                <label>Status</label>
                <div class="inp">
                    <select name="status">
                        <option value="Aktif" {{ old('status', $kontak->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ old('status', $kontak->status) === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
                <div class="hint">Kontak aktif tersedia untuk komunikasi bisnis.</div>
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="{{ route('klien.show', ['kode' => $kontak->klien->kode, 'tab' => 'pic']) }}">Batal</a>
                <button class="btn btn-primary" type="submit">
                    <x-icon name="check" size="16" /> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <aside class="side-col">
        <div class="s-card">
            <h3><x-icon name="building" size="16" /> Perusahaan Klien</h3>
            <div class="co-name" style="font-size:15px; font-weight:700; color:var(--navy); margin:6px 0;">{{ $kontak->klien->nama }}</div>
            <div class="row" style="display:flex; justify-content:space-between; align-items:center;">
                <span>{{ $kontak->klien->kode }}</span>
                <span class="badge {{ $kontak->klien->status === 'Aktif' ? 'ok' : 'mute' }}"><i></i>{{ $kontak->klien->status }}</span>
            </div>
            <hr style="margin:10px 0; border:0; border-top:1px solid var(--border)">
            <div class="row" style="font-size:13px; color:var(--muted);"><x-icon name="pin" size="14" /> {{ $kontak->klien->kota }}</div>
        </div>

        <div class="s-card">
            <h3><x-icon name="trash" size="16" /> Hapus Kontak</h3>
            <p>Jika kontak ini tidak lagi bekerja sama, Anda dapat menghapusnya secara permanen.</p>
            <form action="{{ route('kontak.destroy', $kontak->id) }}" method="POST" onsubmit="return confirm('Hapus kontak ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline btn-sm" style="color:#dc2626; border-color:#fca5a5;">
                    Hapus Kontak Ini
                </button>
            </form>
        </div>
    </aside>
</div>

@push('scripts')
<script>
    function setTipe(val) {
        document.getElementById('inpTipe').value = val;
        const seg = document.getElementById('segTipe');
        seg.querySelectorAll('button').forEach(btn => {
            btn.classList.toggle('on', btn.textContent.includes(val));
        });
    }
</script>
@endpush
@endsection

