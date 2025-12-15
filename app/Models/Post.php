<?php

namespace App\Models;

use App\Traits\HasFormatedDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    use HasFormatedDate;

    protected $fillable = [
        'title',
        'content',
        'author_id',
        'published',
    ];

    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
