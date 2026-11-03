@extends('layouts.app')

@section('title', 'Catat Interaksi')
@section('page_title', 'Catat Interaksi')

@section('content')
<nav class="crumb">
    <span>CRM Klien &amp; Kontak</span>
    <x-icon name="chevR" size="12" />
    <a href="{{ route('riwayat.index') }}">Riwayat Interaksi</a>
    <x-icon name="chevR" size="12" />
    <span class="cur">Catat Interaksi</span>
</nav>

<div class="page-head">
    <div>
        <h1>Catat Interaksi</h1>
        <p>Rekam komunikasi bisnis dan jadwalkan tindak lanjut.</p>
    </div>
    <a class="btn btn-outline" href="{{ route('riwayat.index') }}">
        <x-icon name="arrowL" size="16" /> Kembali
    </a>
</div>

<div class="form-grid">
    <form class="card" action="{{ route('riwayat.store') }}" method="POST">
        @csrf
        <div class="f-head">
            <h2>Data Komunikasi</h2>
            <p>Catatan ini akan tersimpan pada riwayat klien dan kontak yang dipilih.</p>
        </div>

        <div class="f-body">
            <!-- Klien -->
            <div class="field {{ $errors->has('klien_id') ? 'err' : '' }}">
                <label>Klien Terkait <em>*</em></label>
                <div class="inp">
                    <select name="klien_id" id="selKlien" required onchange="filterContacts()">
                        <option value="">Pilih klien...</option>
                        @foreach ($clients as $c)
                            <option value="{{ $c->id }}" data-kode="{{ $c->kode }}" {{ (old('klien_id') == $c->id || ($selectedKlien && $selectedKlien->id == $c->id)) ? 'selected' : '' }}>
                                {{ $c->kode }} · {{ $c->nama }}
                            </option>
                        @endforeach
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
                @error('klien_id') <div class="err-msg">{{ $message }}</div> @enderror
            </div>

            <!-- Kontak Terkait -->
            <div class="field">
                <label>Kontak Terkait</label>
                <div class="inp">
                    <select name="kontak_id" id="selKontak">
                        <option value="">Pilih kontak (opsional)...</option>
                        @if ($selectedKlien)
                            @foreach ($selectedKlien->kontaks as $kt)
                                <option value="{{ $kt->id }}" {{ old('kontak_id') == $kt->id ? 'selected' : '' }}>
                                    {{ $kt->nama }} ({{ $kt->tipe }} - {{ $kt->jabatan }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
            </div>

            <!-- Subjek -->
            <div class="field full {{ $errors->has('subjek') ? 'err' : '' }}">
                <label>Subjek Komunikasi <em>*</em></label>
                <div class="inp">
                    <input name="subjek" value="{{ old('subjek') }}" placeholder="Contoh: Pembahasan perpanjangan kontrak kerja sama" required>
                </div>
                @error('subjek') <div class="err-msg">{{ $message }}</div> @enderror
            </div>

            <!-- Kanal -->
            <div class="field">
                <label>Kanal Komunikasi <em>*</em></label>
                <div class="inp">
                    <select name="kanal" required>
                        <option value="Rapat" {{ old('kanal') === 'Rapat' ? 'selected' : '' }}>Rapat</option>
                        <option value="Email" {{ old('kanal') === 'Email' ? 'selected' : '' }}>Email</option>
                        <option value="WhatsApp" {{ old('kanal') === 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="Telepon" {{ old('kanal') === 'Telepon' ? 'selected' : '' }}>Telepon</option>
                    </select>
                    <x-icon name="chevD" size="15" class="chev" />
                </div>
            </div>

            <!-- Tanggal -->
            <div class="field">
                <label>Tanggal <em>*</em></label>
                <div class="inp">
                    <input name="tgl" type="date" value="{{ old('tgl', date('Y-m-d')) }}" required>
                </div>
            </div>

            <!-- Waktu Mulai & Selesai -->
            <div class="field">
                <label>Waktu Mulai</label>
                <div class="inp">
                    <input name="jam" value="{{ old('jam', '09.00') }}" placeholder="09.00">
                </div>
            </div>

            <div class="field">
                <label>Waktu Selesai</label>
                <div class="inp">
                    <input name="akhir" value="{{ old('akhir', '10.00') }}" placeholder="10.00">
                </div>
            </div>

            <!-- Ringkasan Singkat -->
            <div class="field full">
                <label>Ringkasan Pembahasan</label>
                <div class="inp">
                    <textarea name="ringkas" placeholder="Catat poin-poin utama diskusi...">{{ old('ringkas') }}</textarea>
                </div>
            </div>

            <!-- Hasil Kesepakatan -->
            <div class="field full">
                <label>Hasil / Kesepakatan</label>
                <div class="inp">
                    <textarea name="hasil" placeholder="Hasil keputusan atau kesepakatan yang dicapai...">{{ old('hasil') }}</textarea>
                </div>
            </div>

            <!-- Tindak Lanjut Section -->
            <div class="field full" style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                <h3 style="font-size: 15px; color: var(--navy); margin-bottom: 8px;">Rencana Tindak Lanjut (Opsional)</h3>
            </div>

            <div class="field">
                <label>Tanggal Tindak Lanjut</label>
                <div class="inp">
                    <input name="follow_tgl" type="date" value="{{ old('follow_tgl') }}">
                </div>
            </div>

            <div class="field">
                <label>Aksi Tindak Lanjut</label>
                <div class="inp">
                    <input name="follow_aksi" value="{{ old('follow_aksi') }}" placeholder="Contoh: Kirim penawaran revisi">
                </div>
            </div>

            <div class="field full">
                <label>Detail Tindak Lanjut</label>
                <div class="inp">
                    <textarea name="follow_detail" placeholder="Instruksi atau catatan tindak lanjut...">{{ old('follow_detail') }}</textarea>
                </div>
            </div>
        </div>

        <div class="f-foot">
            <div class="req">* Wajib diisi</div>
            <div class="btns">
                <a class="btn btn-outline" href="{{ route('riwayat.index') }}">Batal</a>
                <button class="btn btn-primary" type="submit">
                    <x-icon name="check" size="16" /> Simpan Interaksi
                </button>
            </div>
        </div>
    </form>

    <aside class="side-col">
        <div class="s-card">
            <h3><x-icon name="info" size="16" /> Panduan Interaksi</h3>
            <p>Mencatat setiap interaksi secara berkala menjaga rekam jejak hubungan kerja sama yang akurat dengan klien.</p>
            <hr>
            <p>Jika ada janji temu, pengiriman proposal, atau revisi kontrak, aktifkan bagian <b>Rencana Tindak Lanjut</b> agar otomatis terpantau.</p>
        </div>
    </aside>
</div>

@push('scripts')
<script>
    const clientsData = @json($clients->map(fn($c) => ['id' => $c->id, 'kontaks' => $c->kontaks]));

    function filterContacts() {
        const klienId = document.getElementById('selKlien').value;
        const selKontak = document.getElementById('selKontak');
        selKontak.innerHTML = '<option value="">Pilih kontak (opsional)...</option>';

        if (!klienId) return;

        const c = clientsData.find(x => x.id == klienId);
        if (c && c.kontaks) {
            c.kontaks.forEach(kt => {
                const opt = document.createElement('option');
                opt.value = kt.id;
                opt.textContent = `${kt.nama} (${kt.tipe} - ${kt.jabatan || 'Staf'})`;
                selKontak.appendChild(opt);
            });
        }
    }
</script>
@endpush
@endsection

