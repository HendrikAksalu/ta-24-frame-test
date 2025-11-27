<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

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


    protected function createdAtFormated(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at?->diffForHumans()
        );
    }
    protected function updatedAtFormated(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->updated_at?->diffForHumans()
        );
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at?->diffForHumans()
        );
    }

    protected function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->updated_at?->diffForHumans()
        );
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
