<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Article extends Model
{
use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'author_name',
        'published_date',
        'image_path',
        'content',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'published_date' => 'date',
        'is_active' => 'boolean',
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
