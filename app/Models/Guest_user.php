<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest_user extends Model
{
    use HasFactory;

    protected $table = 'guest_user';

    protected $fillable = [
        'game_name',
        'api_token'
    ];
}
