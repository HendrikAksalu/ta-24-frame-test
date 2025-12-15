<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFormatedDate;

class Author extends Model
{
    use HasFactory;
    use HasFormatedDate;

    protected $guarded = ['id'];

    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
        'date_of_birth_formatted',
    ];

    protected function dateOfBirthFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date_of_birth
                ? \Carbon\Carbon::parse($this->date_of_birth)->toFormattedDateString()
                : null
        );
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
