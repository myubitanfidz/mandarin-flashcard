<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlashcardController;

// Tampilan Utama (Hexagon)
Route::get('/', function () {
    return view('welcome');
});

// Halaman Penuh Daftar Hafal HSK
Route::get('/mastered', function () {
    return view('mastered.index');
});

// Halaman Kuis / Test Uji Hafalan
Route::get('/test', function () {
    return view('test.session');
});

// API Routes
Route::get('/api/flashcards/level/{level}', [FlashcardController::class, 'getVocabsByLevel']);
Route::get('/api/flashcards/mastered/random', [FlashcardController::class, 'getRandomMasteredVocab']);
Route::get('/api/flashcards/mastered/list', [FlashcardController::class, 'getMasteredList']);
Route::post('/api/flashcards/{id}/toggle-mastered', [FlashcardController::class, 'toggleMastered']);