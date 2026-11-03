<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::view('/klien', 'klien.index')->name('klien.index');
Route::view('/klien/tambah', 'klien.create')->name('klien.create');
Route::get('/klien/{kode}', fn ($kode) => view('klien.show', compact('kode')))->name('klien.show');
Route::get('/klien/{kode}/edit', fn ($kode) => view('klien.edit', compact('kode')))->name('klien.edit');

Route::view('/kontak', 'kontak.index')->name('kontak.index');
Route::view('/kontak/tambah', 'kontak.create')->name('kontak.create');
Route::get('/kontak/{id}/edit', fn ($id) => view('kontak.edit', compact('id')))->name('kontak.edit');

Route::view('/riwayat', 'riwayat.index')->name('riwayat.index');
Route::view('/riwayat/tambah', 'riwayat.create')->name('riwayat.create');

Route::view('/arsip', 'arsip.index')->name('arsip.index');
