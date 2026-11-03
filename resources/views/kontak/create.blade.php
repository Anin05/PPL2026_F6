@extends('layouts.app')

@section('title', 'Tambah Kontak')
@section('page_title', 'Tambah Kontak')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    @if ($selectedKlien)
        <a href="{{ route('klien.index') }}">Master Klien</a>
        <x-icon name="chevR" size="12" />
        <a href="{{ route('klien.show', $selectedKlien->kode) }}">{{ $selectedKlien->nama }}</a>
        <x-icon name="chevR" size="12" />
        <a href="{{ route('klien.show', ['kode' => $selectedKlien->kode, 'tab' => 'pic']) }}">PIC &amp; Direktur</a>
    @else
        <a href="{{ route('kontak.index') }}">Kontak Klien</a>
    @endif
    <x-icon name="chevR" size="12" />
    <span class="cur">Tambah Kontak</span>
</nav>

<div class="page-head">
    <div>
        <h1>Tambah Kontak</h1>
        <p>Tambahkan PIC atau Direktur untuk perusahaan klien.</p>
    </div>
    <a class="btn btn-outline" href="{{ $selectedKlien ? route('klien.show', ['kode' => $selectedKlien->kode, 'tab' => 'pic']) : route('kontak.index') }}">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
    <form class="card" action="{{ route('kontak.store') }}" method="POST">
        @csrf
        <div class="f-head">
            <h2>Informasi Kontak</h2>
            <p>
                @if ($selectedKlien)
                    Kontak akan terhubung langsung dengan <b>{{ $selectedKlien->nama }}</b> ({{ $selectedKlien->kode }}).
                @else
                    Pilih klien yang sudah tersimpan, lalu lengkapi data kontak.
                @endif
            </p>
        </div>

        <div class="f-body">
            @if ($selectedKlien)
                <input type="hidden" name="klien_id" value="{{ $selectedKlien->id }}">
            @else
                <div class="field full {{ $errors->has('klien_id') ? 'err' : '' }}">
                    <label>Klien <em>*</em></label>
                    <div class="inp">
                        <select name="klien_id" required>
                            <option value="">Pilih klien...</option>
                            @foreach ($clients as $c)
                                <option value="{{ $c->id }}" {{ old('klien_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->kode }} · {{ $c->nama }}
                                </option>
                            @endforeach
                        </select>
                        <x-icon name="chevD" size="15" class="chev" />
                    </div>
                    @error('klien_id')
                        <div class="err-msg">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <!-- Nama -->
            <div class="field {{ $errors->has('nama') ? 'err' : '' }}">
                <label>Nama Lengkap <em>*</em></label>
                <div class="inp">
                    <input name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap kontak" required autofocus>
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
                    <button type="button" class="{{ old('tipe', 'PIC') === 'PIC' ? 'on' : '' }}" onclick="setTipe('PIC')">
                        <x-icon name="check" size="14" /> PIC
                    </button>
                    <button type="button" class="{{ old('tipe') === 'Direktur' ? 'on' : '' }}" onclick="setTipe('Direktur')">
                        <x-icon name="check" size="14" /> Direktur
                    </button>
                </div>
                <input type="hidden" name="tipe" id="inpTipe" value="{{ old('tipe', 'PIC') }}">
            </div>

            <!-- Jabatan -->
            <div class="field {{ $errors->has('jabatan') ? 'err' : '' }}">
                <label>Jabatan</label>
                <div class="inp">
                    <input name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Manajer Pengadaan">
                </div>
                @error('jabatan')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Divisi -->
            <div class="field {{ $errors->has('divisi') ? 'err' : '' }}">
                <label>Divisi</label>
                <div class="inp">
                    <input name="divisi" value="{{ old('divisi') }}" placeholder="Contoh: Procurement">
                </div>
                @error('divisi')
                    <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- WhatsApp -->
            <div class="field {{ $errors->has('wa') ? 'err' : '' }}">
                <label>Nomor WhatsApp <em>*</em></label>
                <div class="inp">
                    <input name="wa" value="{{ old('wa') }}" placeholder="Contoh: 0812-3456-7890" required>
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
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="nama@perusahaan.co.id">
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
                        <option value="Aktif" {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ old('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
                <div class="hint">Kontak aktif tersedia untuk komunikasi bisnis.</div>
            </div>

            <div class="hint" style="align-self:center; margin:0">
                Pastikan nomor WhatsApp dan email merupakan kontak kerja yang aktif.
            </div>

            <div class="f-note full">
                <x-icon name="link" size="14" />
                <span>{{ $selectedKlien ? "Kontak ini hanya terhubung dengan klien {$selectedKlien->kode}." : "Kontak ini hanya terhubung dengan klien yang dipilih." }}</span>
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="{{ $selectedKlien ? route('klien.show', ['kode' => $selectedKlien->kode, 'tab' => 'pic']) : route('kontak.index') }}">Batal</a>
                <button class="btn btn-primary" type="submit">
                    <x-icon name="check" size="16" /> {{ $selectedKlien ? 'Simpan & Kembali ke PIC & Direktur' : 'Simpan Kontak' }}
                </button>
            </div>
        </div>
    </form>

    <aside class="side-col">
        @if ($selectedKlien)
            <div class="s-card">
                <h3><x-icon name="building" size="16" /> Perusahaan Klien</h3>
                <div class="co-name" style="font-size:15px; font-weight:700; color:var(--navy); margin:6px 0;">{{ $selectedKlien->nama }}</div>
                <div class="row" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>{{ $selectedKlien->kode }}</span>
                    <span class="badge {{ $selectedKlien->status === 'Aktif' ? 'ok' : 'mute' }}"><i></i>{{ $selectedKlien->status }}</span>
                </div>
                <hr style="margin:10px 0; border:0; border-top:1px solid var(--border)">
                <div class="row" style="font-size:13px; color:var(--muted);"><x-icon name="pin" size="14" /> {{ $selectedKlien->kota }}</div>
                <div class="row" style="font-size:13px; color:var(--muted); margin-top:4px;"><x-icon name="users" size="14" /> {{ $selectedKlien->kontaks()->count() }} kontak tersimpan</div>
            </div>
        @endif

        <div class="s-card">
            <h3><x-icon name="info" size="16" /> Pilih tipe kontak</h3>
            <div class="pt" style="font-weight:600; color:var(--navy); margin-top:8px;">PIC</div>
            <p style="margin-top:2px;">Penanggung jawab komunikasi dan koordinasi harian dengan klien. Contoh: Manajer Pengadaan, Admin Keuangan.</p>
            <hr style="margin:10px 0; border:0; border-top:1px solid var(--border)">
            <div class="pt" style="font-weight:600; color:var(--navy);">Direktur</div>
            <p style="margin-top:2px;">Pimpinan perusahaan yang menjadi kontak pengambil keputusan. Contoh: CEO / Direktur Utama, Direktur Operasional.</p>
        </div>

        <div class="s-card tint">
            <h3><x-icon name="info" size="16" /> Alur Tambah Kontak</h3>
            <ol class="steps">
                <li><span class="n">1</span>Klien tersimpan di Master Klien</li>
                <li><span class="n">2</span>Buka Detail Klien → Tab PIC &amp; Direktur</li>
                <li><span class="n">3</span>Isi formulir kontak di halaman ini</li>
                <li><span class="n g">4</span>Simpan kontak baru</li>
            </ol>
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

