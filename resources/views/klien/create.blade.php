@extends('layouts.app')

@section('title', 'Tambah Klien')
@section('page_title', 'Tambah Klien')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('klien.index') }}">Master Klien</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Tambah Klien</span>
</nav>

<div class="page-head">
    <div>
        <h1>Tambah Klien</h1>
        <p>Lengkapi informasi perusahaan untuk mendaftarkan klien baru.</p>
    </div>
    <a class="btn btn-outline" href="{{ route('klien.index') }}">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
      <form class="card" id="form-klien" novalidate>
        <div class="f-head">
            <h2>Informasi Perusahaan</h2>
            <p>Data ini akan menjadi identitas utama klien di sistem.</p>
        </div>

        <div class="f-body">
            <!-- Nama Perusahaan -->
            <div class="field {{ $errors->has('nama') ? 'err' : '' }}">
                <label>Nama Perusahaan <em>*</em></label>
                <div class="inp">
                    <input name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama perusahaan" required autofocus>
                    <span class="ic-err"><x-icon name="alert" size="16" /></span>
                </div>
                @error('nama')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kode Klien (Otomatis) -->
            <div class="field">
                <label>Kode Klien</label>
                <div class="inp">
                    <input readonly value="Dibuat otomatis" style="background:#f8fafc; color:#64748b;">
                    <span class="ic-lock"><x-icon name="lock" size="15" /></span>
                </div>
                <div class="hint">Kode unik dibuat otomatis saat data disimpan.</div>
            </div>

            <!-- Alamat -->
            <div class="field full {{ $errors->has('alamat') ? 'err' : '' }}">
                <label>Alamat</label>
                <div class="inp">
                    <textarea name="alamat" placeholder="Contoh: Jl. Jenderal Sudirman No. 25, Tanah Abang">{{ old('alamat') }}</textarea>
                </div>
                @error('alamat')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kota -->
            <div class="field {{ $errors->has('kota') ? 'err' : '' }}">
                <label>Kota <em>*</em></label>
                <div class="inp">
                    <input name="kota" value="{{ old('kota') }}" placeholder="Contoh: Jakarta Pusat" required>
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
                    <input name="telp" value="{{ old('telp') }}" placeholder="Contoh: 021-5550-1200">
                </div>
                @error('telp')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- HP / WhatsApp -->
            <div class="field {{ $errors->has('hp') ? 'err' : '' }}">
                <label>No. HP / WA</label>
                <div class="inp">
                    <input name="hp" value="{{ old('hp') }}" placeholder="Contoh: 0812-9900-1200">
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
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="contact@perusahaan.co.id">
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
                        <option value="Aktif" {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Arsip" {{ old('status') === 'Arsip' ? 'selected' : '' }}>Arsip</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
                <div class="hint">Status awal klien baru adalah Aktif. Klien aktif dapat digunakan pada transaksi.</div>
            </div>

            <div class="f-note full">
                <x-icon name="shield" size="15" />
                Data klien hanya dapat diakses oleh pengguna yang berwenang (Admin / Account Manager).
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="{{ route('klien.index') }}">Batal</a>
                <button class="btn btn-primary" type="submit">
                    <x-icon name="check" size="16" /> Simpan Klien
                </button>
            </div>
        </div>
    </form>

    <aside class="side-col">
        <div class="s-card">
            <h3><x-icon name="info" size="16" /> Panduan pengisian</h3>
            <p>Gunakan nama perusahaan sesuai dokumen resmi agar mudah dikenali di seluruh modul.</p>
            <hr>
            <p><span class="red-dot">•</span> Nama Perusahaan dan Kota wajib diisi sebelum menyimpan.</p>
            <p>Pastikan telepon dan email perusahaan masih aktif dan format email valid.</p>
        </div>

        <div class="s-card tint">
            <h3><x-icon name="users" size="16" /> Selanjutnya, tambah kontak</h3>
            <p>Setelah Simpan berhasil, Anda akan kembali ke daftar Master Klien. Buka Detail Klien dari daftar untuk menambahkan PIC dan Direktur.</p>
            <ul>
                <li><x-icon name="check" size="14" /> Satu klien, banyak kontak</li>
                <li><x-icon name="check" size="14" /> Simpan → kembali ke daftar → buka Detail → tambah kontak</li>
            </ul>
        </div>

        <div class="s-card">
            <h3><x-icon name="users" size="16" /> Pengguna berwenang</h3>
            <p>Pendaftaran klien dapat dilakukan oleh:</p>
            <p style="color:var(--navy); font-weight:600;">Admin · Account Manager · COO · CEO</p>
        </div>
    </aside>
</div>
@endsection
@push('scripts')
<script>
const PETA = { name: 'nama', address: 'alamat', city: 'kota', phone: 'telp', mobile: 'hp', email: 'email' };

function tampilkanError(details) {
  document.querySelectorAll('.err-msg.js').forEach(e => e.remove());
  document.querySelectorAll('.field.err').forEach(e => e.classList.remove('err'));
  Object.entries(details || {}).forEach(([field, pesan]) => {
    const input = document.querySelector('[name="' + PETA[field] + '"]');
    if (!input) return;
    const box = input.closest('.field');
    box.classList.add('err');
    const msg = document.createElement('div');
    msg.className = 'err-msg js';
    msg.textContent = Array.isArray(pesan) ? pesan[0] : pesan;
    box.appendChild(msg);
  });
}

document.getElementById('form-klien').addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = e.target.querySelector('button[type=submit]');
  const label = btn.innerHTML;
  btn.disabled = true;
  btn.textContent = 'Menyimpan data klien...';
  const f = Object.fromEntries(new FormData(e.target));
  try {
   await api('POST', '/clients', {
  code: 'UJI-' + Date.now(),   // SEMENTARA, hapus setelah BE membuat kode otomatis
  name: f.nama, address: f.alamat, city: f.kota,
  phone: f.telp, mobile: f.hp, email: f.email,
});
    sessionStorage.setItem('flash', 'Data klien berhasil disimpan. Kode klien berhasil dibuat otomatis.');
    location.href = '/klien';
  } catch (err) {
    tampilkanError(err.error && err.error.details);
    if (err.error && err.error.message) alert(err.error.message);
    btn.disabled = false;
    btn.innerHTML = label;
  }
});
</script>
@endpush
