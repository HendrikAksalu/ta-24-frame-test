<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    use \App\Traits\HasFormatedDate;

    protected $fillable = [
        'post_id',
        'author_name',
        'content',
    ];

    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
