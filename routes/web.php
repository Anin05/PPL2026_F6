<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/klien');
Route::get('/dashboard', fn () => redirect()->route('klien.index'))->name('dashboard');

Route::view('/klien', 'klien.index')->name('klien.index');
Route::view('/klien/tambah', 'klien.create')->name('klien.create');
Route::get('/klien/{kode}', fn ($kode) => view('klien.show', compact('kode')))->name('klien.show');
Route::get('/klien/{kode}/edit', fn ($kode) => view('klien.edit', compact('kode')))->name('klien.edit');

Route::get('/kontak', fn () => view('belum_tersedia', ['judul' => 'Kontak Klien']))->name('kontak.index');
Route::get('/riwayat', fn () => view('belum_tersedia', ['judul' => 'Riwayat Interaksi']))->name('riwayat.index');
Route::get('/arsip', fn () => view('belum_tersedia', ['judul' => 'Arsip']))->name('arsip.index');