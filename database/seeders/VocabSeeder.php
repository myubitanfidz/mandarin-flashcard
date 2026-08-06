<?php

namespace Database\Seeders;

use App\Models\Vocab;
use Illuminate\Database\Seeder;

class VocabSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'hanzi' => '你好',
                'pinyin' => 'nǐ hǎo',
                'type' => '代词 (Kata Ganti)',
                'meaning' => 'Halo / Apa Kabar',
                'hsk_level' => 1,
            ],
            [
                'hanzi' => '谢谢',
                'pinyin' => 'xiè xie',
                'type' => '动词 (Kata Kerja)',
                'meaning' => 'Terima kasih',
                'hsk_level' => 1,
            ],
            [
                'hanzi' => '再见',
                'pinyin' => 'zài jiàn',
                'type' => '动词 (Kata Kerja)',
                'meaning' => 'Sampai jumpa',
                'hsk_level' => 1,
            ],
            [
                'hanzi' => '中国',
                'pinyin' => 'Zhōng guó',
                'type' => '名词 (Kata Benda)',
                'meaning' => 'Tiongkok / China',
                'hsk_level' => 1,
            ],
            [
                'hanzi' => '朋友',
                'pinyin' => 'péng you',
                'type' => '名词 (Kata Benda)',
                'meaning' => 'Teman / Sahabat',
                'hsk_level' => 1,
            ],
        ];

        foreach ($samples as $data) {
            Vocab::create($data);
        }
    }
}