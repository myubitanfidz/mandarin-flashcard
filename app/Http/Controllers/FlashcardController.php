<?php

namespace App\Http\Controllers;

use App\Models\Vocab;
use Illuminate\Http\Request;

class FlashcardController extends Controller
{
    /**
     * Ambil 1 kosakata acak yang belum dihafal.
     */
    public function getRandomVocab()
    {
        // Prioritaskan kosakata yang belum dihafal
        $vocab = Vocab::where('is_mastered', false)
            ->inRandomOrder()
            ->first();

        // Jika semua sudah dihafal, ambil acak dari seluruh data
        if (!$vocab) {
            $vocab = Vocab::inRandomOrder()->first();
        }

        return response()->json([
            'success' => true,
            'data' => $vocab
        ]);
    }

    /**
     * Perbarui status hafal/belum hafal kosakata.
     */
    public function toggleMastered(Request $request, $id)
    {
        $vocab = Vocab::findOrFail($id);
        
        $request->validate([
            'is_mastered' => 'required|boolean',
        ]);

        $vocab->update([
            'is_mastered' => $request->is_mastered,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status hafalan berhasil diperbarui.',
            'data' => $vocab
        ]);
    }
}