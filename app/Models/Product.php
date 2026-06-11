<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;

class Product extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'summary',
        'description',
        'price',
        'compare_at_price',
        'cost_price',
        'stock_quantity',
        'low_stock_threshold',
        'rating',
        'reviews_count',
        'is_featured',
        'is_active',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $appends = ['discount_percent', 'image_url'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'rating' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true)->oldest('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(function (Builder $query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('summary', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('sku', 'like', $term);
                });
            })
            ->when($request->filled('category'), function (Builder $query) use ($request) {
                $query->whereHas('category', fn (Builder $category) => $category->where('slug', $request->category));
            })
            ->when($request->filled('brand'), function (Builder $query) use ($request) {
                $query->whereHas('brand', fn (Builder $brand) => $brand->where('slug', $request->brand));
            })
            ->when($request->filled('min_price'), fn (Builder $query) => $query->where('price', '>=', $request->float('min_price')))
            ->when($request->filled('max_price'), fn (Builder $query) => $query->where('price', '<=', $request->float('max_price')))
            ->when($request->filled('rating'), fn (Builder $query) => $query->where('rating', '>=', $request->integer('rating')));
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->compare_at_price || $this->compare_at_price <= $this->price) {
            return 0;
        }

        return (int) round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->primaryImage?->image_url
            ?? 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80';
    }

    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function syncRating(): void
    {
        $stats = $this->approvedReviews()
            ->selectRaw('COUNT(*) as aggregate_count, COALESCE(AVG(rating), 0) as aggregate_rating')
            ->first();

        $this->forceFill([
            'reviews_count' => (int) $stats->aggregate_count,
            'rating' => round((float) $stats->aggregate_rating, 2),
        ])->save();
    }
}
