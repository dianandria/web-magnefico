<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PortfolioCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Get the portfolios associated with the category.
     */
    public function portfolios(): BelongsToMany
    {
        return $this->belongsToMany(Portfolio::class, 'category_portfolio');
    }
}
