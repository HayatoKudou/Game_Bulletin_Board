<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apex_player extends Model
{
    use HasFactory;

    protected $table = 'apex_player';

    protected $fillable = [
        'user_id',
        'platformSlug',
        'platformUserId',
        'avatarUrl',
        'level',
        'kills',
        'damage',
        'rankScore',
        'rankScore_iconUrl',
    ];
}
