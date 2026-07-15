<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    // 1. Biarkan 'value' mengembalikan data aslinya untuk Filament
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value
        );
    }

    // 2. Buat accessor baru (misal: value_url) untuk Frontend/API
    protected function valueUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $realValue = $attributes['value'] ?? null;
                
                if (!$realValue) {
                    return null;
                }

                $mediaKeys = ['hero_image', 'home_video'];

                if (isset($attributes['key']) && in_array($attributes['key'], $mediaKeys)) {
                    if (filter_var($realValue, FILTER_VALIDATE_URL)) {
                        return $realValue;
                    }
                    return asset('storage/' . $realValue);
                }

                return $realValue;
            }
        );
    }
}
