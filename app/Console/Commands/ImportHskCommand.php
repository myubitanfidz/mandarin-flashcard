<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vocab;
use Illuminate\Support\Facades\File;

class ImportHskCommand extends Command
{
    /**
     * Nama perintah yang akan dijalankan di terminal:
     * Contoh: php artisan hsk:import 1
     */
    protected $signature = 'hsk:import {level=1 : Level HSK yang ingin diimpor (1-7)}';

    protected $description = 'Impor dataset kosakata HSK dari file JSON ke database';

    public function handle()
    {
        $level = $this->argument('level');
        $filePath = database_path("data/hsk{$level}.json");

        if (!File::exists($filePath)) {
            $this->error("File dataset tidak ditemukan: database/data/hsk{$level}.json");
            $this->info("Pastikan Anda sudah menaruh file JSON dataset di folder database/data/");
            return 1;
        }

        $this->info("Membaca file HSK Level {$level}...");
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (!$data || !is_array($data)) {
            $this->error("Format JSON tidak valid!");
            return 1;
        }

        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        $count = 0;
        foreach ($data as $item) {
            Vocab::updateOrCreate(
                [
                    'hanzi' => $item['hanzi'],
                    'hsk_level' => $item['hsk_level'] ?? $level
                ],
                [
                    'pinyin' => $item['pinyin'] ?? '',
                    'type' => $item['type'] ?? '',
                    'meaning' => $item['meaning'] ?? '',
                    'is_mastered' => false,
                ]
            );
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Berhasil mengimpor {$count} kosakata HSK Level {$level} ke database!");

        return 0;
    }
}