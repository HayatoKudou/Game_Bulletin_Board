<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = [
        'reply_id',
        'user_id',
        'user_name',
        'platform',
        'comment',
        'report',
        'tag_vc_yes',
        'tag_vc_no',
        'tag_cooperation',
        'tag_friend',
        'tag_clan',
        'tag_rank',
        'tag_quick',
        'tag_event',
        'tag_seriously',
        'tag_society',
        'tag_student',
    ];
}
