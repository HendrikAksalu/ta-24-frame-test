<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'added' => 'datetime',
            'edited' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Marker $marker) {
            $marker->added = now();
        });

        static::updating(function (Marker $marker) {
            $marker->edited = now();
        });
    }
}
