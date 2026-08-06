<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vocab extends Model
{
    protected $fillable = [
        'hanzi',
        'pinyin',
        'type',
        'meaning',
        'hsk_level',
        'is_mastered',
    ];
}