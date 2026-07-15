<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Portfolio extends Model
{
    // Define fillable properties to allow mass assignment
    protected $fillable = [
        'title',
        'client_name',
        'year',
        'description',
        'slug',
        'is_featured',
        'sort_order',
        'main_image',
        'image_gallery',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Cast the JSON database column to a PHP array
            'image_gallery' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Handle deleting files when removing an image from the gallery and saving
        static::updating(function ($model) {
            if ($model->isDirty('image_gallery')) {
                $originalImages = $model->getOriginal('image_gallery');
                
                // Ensure original images is an array
                if (is_string($originalImages)) {
                    $originalImages = json_decode($originalImages, true) ?? [];
                } elseif (!is_array($originalImages)) {
                    $originalImages = [];
                }

                $currentImages = $model->image_gallery ?? [];

                // Find files that were in the original array but are not in the current array
                $deletedImages = array_diff($originalImages, $currentImages);

                // Delete the physical files from storage
                foreach ($deletedImages as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Handle main_image replacement
            if ($model->isDirty('main_image')) {
                $originalMainImage = $model->getOriginal('main_image');
                if (!empty($originalMainImage) && $originalMainImage !== $model->main_image) {
                    Storage::disk('public')->delete($originalMainImage);
                }
            }
        });

        // Handle deleting all files when the entire record is deleted
        static::deleted(function ($model) {
            // Delete all images in the gallery
            if (is_array($model->image_gallery)) {
                foreach ($model->image_gallery as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Delete the main image
            if (!empty($model->main_image)) {
                Storage::disk('public')->delete($model->main_image);
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PortfolioCategory::class, 'category_portfolio');
    }
}
