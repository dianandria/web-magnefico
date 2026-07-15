<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Expertise extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                // Ambil dari raw database path
                $path = $attributes['image_path'] ?? null;
                
                if (!$path) {
                    return null;
                }

                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    return $path;
                }

                return asset('storage/' . $path); 
            }
        );
    }
}
