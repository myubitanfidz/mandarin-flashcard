<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlashcardController;

Route::get('/', function () {
    return view('welcome');
});

// Endpoint API Flashcard
Route::get('/api/flashcards/random', [FlashcardController::class, 'getRandomVocab']);
Route::post('/api/flashcards/{id}/toggle-mastered', [FlashcardController::class, 'toggleMastered']);
Route::get('/api/flashcards/mastered/random', [FlashcardController::class, 'getRandomMasteredVocab']);
Route::get('/api/flashcards/mastered/list', [FlashcardController::class, 'getMasteredList']);
Route::get('/api/flashcards/level/{level}', [FlashcardController::class, 'getVocabsByLevel']);