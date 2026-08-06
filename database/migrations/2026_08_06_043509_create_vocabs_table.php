<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabs', function (Blueprint $table) {
            $table->id();
            $table->string('hanzi');        // Karakter Mandarin, contoh: 你好
            $table->string('pinyin');       // Pinyin dengan nada, contoh: nǐ hǎo
            $table->string('type');         // Jenis kata, contoh: 代词 (Pronoun)
            $table->text('meaning');        // Terjemahan Bahasa Indonesia
            $table->integer('hsk_level')->default(1); // HSK Level (1-6)
            $table->boolean('is_mastered')->default(false); // Status Hafal/Belum
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabs');
    }
};