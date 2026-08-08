<?php

namespace App\Http\Controllers;

use App\Models\Vocab;
use Illuminate\Http\Request;

class FlashcardController extends Controller
{
    public function getRandomVocab(Request $request)
    {
        $level = $request->query('level', 1);

        // Jika user memilih HSK 7 (yang mencakup level 7-9)
        $query = Vocab::query();
        if ($level == 7) {
            $query->whereIn('hsk_level', [7, 8, 9]);
        } else {
            $query->where('hsk_level', $level);
        }

        // Prioritaskan kosakata yang belum dihafal
        $vocab = (clone $query)->where('is_mastered', false)
            ->inRandomOrder()
            ->first();

        // Jika semua sudah dihafal, ambil acak dari level tersebut
        if (!$vocab) {
            $vocab = $query->inRandomOrder()->first();
        }

        return response()->json([
            'success' => true,
            'data' => $vocab
        ]);
    }

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

    public function getRandomMasteredVocab()
    {
        $vocab = Vocab::where('is_mastered', true)->inRandomOrder()->first();

        return response()->json([
            'success' => true,
            'data' => $vocab
        ]);
    }

    public function getMasteredList()
    {
        $vocabs = Vocab::where('is_mastered', true)->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $vocabs
        ]);
    }

    public function getVocabsByLevel($level)
    {
        if ($level == 7) {
            $vocabs = Vocab::where('hsk_level', '>=', 7)->orderBy('id')->get();
        } else {
            $vocabs = Vocab::where('hsk_level', $level)->orderBy('id')->get();
        }
    
        return response()->json([
            'success' => true,
            'data' => $vocabs
        ]);
    }
}