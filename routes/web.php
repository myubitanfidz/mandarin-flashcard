<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlashcardController;

Route::get('/', function () {
    return view('welcome');
});

// Endpoint API Flashcard
Route::get('/api/flashcards/random', [FlashcardController::class, 'getRandomVocab']);
Route::post('/api/flashcards/{id}/toggle-mastered', [FlashcardController::class, 'toggleMastered']);