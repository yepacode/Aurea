<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand_id',
        'internal_code',
        'name',
        'slug',
        'description',
        'type',
        'price',           // Precio público (lo que paga el cliente en la web)
        'compare_price',   // Precio sugerido / tachado / PVP físico
        'cost_price',      // Costo del distribuidor (lo que el negocio paga)
        'wholesale_price', // Precio para mayoristas aprobados (nullable — usa % por defecto)
        'wholesale_min_qty', // Cantidad mínima por unidad para aplicar precio mayorista
        'stock',
        'images',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyword',
        'canonical_url',
        'noindex',
        'nofollow',
        'og_type',
        'og_title',
        'og_description',
        'og_image_path',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image_path',
        'custom_schema_markup',
        'key_features',     // JSON array de bullets
        'how_to_use',       // text — instrucciones de uso
        'ingredients',      // text — lista de ingredientes (INCI)
        'suitable_for',     // string — tipo de piel/cabello/uso
        'gtin',             // EAN/UPC/GTIN-14
        'mpn',              // manufacturer part number
        'weight_value',
        'weight_unit',      // g, kg, ml, L, oz
        'country_origin',
        'is_cruelty_free',
        'is_vegan',
        'is_active',
        'is_featured',
        'badge_2x1',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price'             => 'decimal:2',
            'compare_price'     => 'decimal:2',
            'cost_price'        => 'decimal:2',
            'wholesale_price'   => 'integer',
            'wholesale_min_qty' => 'integer',
            'images'        => 'array',
            'type'          => 'array',
            'key_features'  => 'array',
            'weight_value'  => 'decimal:2',
            'noindex'       => 'boolean',
            'nofollow'      => 'boolean',
            'is_cruelty_free' => 'boolean',
            'is_vegan'      => 'boolean',
            'is_active'     => 'boolean',
            'is_featured'   => 'boolean',
            'badge_2x1'     => 'boolean',
            'sort_order'    => 'integer',
        ];
    }

    /**
     * Precio efectivo para un cliente concreto.
     *
     * - Si el cliente es mayorista APROBADO, se devuelve `wholesale_price`
     *   cuando esté definido; de lo contrario se calcula un descuento por
     *   defecto (config('wholesale.default_discount', 0.80)).
     * - Para cualquier otro cliente (invitado o cliente normal), devuelve el
     *   precio público.
     *
     * El valor se devuelve en la misma unidad que `price` (pesos, sin decimales
     * relevantes para MXN/COP redondeamos al entero más cercano).
     */
    public function priceFor(?\App\Models\Customer $customer = null): int
    {
        if ($customer && $customer->isApprovedWholesaler()) {
            if ($this->wholesale_price !== null) {
                return (int) $this->wholesale_price;
            }
            $discount = (float) config('wholesale.default_discount', 0.80);
            return (int) round((float) $this->price * $discount);
        }

        return (int) round((float) $this->price);
    }

    /**
     * Margen bruto en moneda (price - cost_price). Null si falta el costo.
     */
    public function getMarginAttribute(): ?float
    {
        if ($this->cost_price === null || $this->cost_price <= 0) return null;
        return round((float) $this->price - (float) $this->cost_price, 2);
    }

    /**
     * Margen bruto en % sobre el precio de venta. Null si falta el costo.
     */
    public function getMarginPercentAttribute(): ?float
    {
        if ($this->cost_price === null || $this->cost_price <= 0 || (float) $this->price <= 0) return null;
        return round(((float) $this->price - (float) $this->cost_price) / (float) $this->price * 100, 1);
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (! $product->slug) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // ── Relations ──

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Bundles that include this product.
     */
    public function bundles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Bundle::class, 'bundle_items')
            ->withPivot(['quantity', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Bundles currently ACTIVE that include this product.
     */
    public function activeBundles(): \Illuminate\Support\Collection
    {
        return $this->bundles()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->get();
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    // ── Reseñas / rating ──

    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->approvedReviews()->avg('rating'), 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return (int) $this->approvedReviews()->count();
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->whereJsonContains('type', $type);
    }

    /**
     * Check if this product has a given type.
     */
    public function hasType(string $type): bool
    {
        return in_array($type, $this->type ?? []);
    }

    /**
     * Check if this product has any of the given types.
     */
    public function hasAnyType(array $types): bool
    {
        return !empty(array_intersect($this->type ?? [], $types));
    }

    /**
     * True if there is any stock available (in product or in any active variant).
     */
    public function hasStock(): bool
    {
        $variantStock = $this->variants->where('is_active', true)->sum('stock');

        if ($this->variants->where('is_active', true)->count() > 0) {
            return $variantStock > 0;
        }

        return (int) $this->stock > 0;
    }

    /**
     * Total available stock (sum of active variants, or product stock if no variants).
     */
    public function availableStock(): int
    {
        if ($this->variants->where('is_active', true)->count() > 0) {
            return (int) $this->variants->where('is_active', true)->sum('stock');
        }

        return (int) $this->stock;
    }

    /** Umbral para mostrar "¡Últimas unidades!". */
    public const LOW_STOCK_THRESHOLD = 5;

    /**
     * ¿Queda poco stock? (para señal de urgencia honesta)
     */
    public function getIsLowStockAttribute(): bool
    {
        $available = $this->availableStock();

        return $available > 0 && $available <= self::LOW_STOCK_THRESHOLD;
    }

    /** Unidades disponibles (para "Solo quedan N"). */
    public function getLowStockCountAttribute(): int
    {
        return $this->availableStock();
    }

    /**
     * IDs de los productos más vendidos (por unidades en pedidos pagados).
     * Devuelve colección vacía si aún no hay ventas.
     */
    public static function bestSellerIds(int $limit = 8): \Illuminate\Support\Collection
    {
        return \App\Models\OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('order_items.product_id')
            ->orderByRaw('SUM(order_items.qty) DESC')
            ->limit($limit)
            ->pluck('order_items.product_id');
    }

    /**
     * Get the primary type label for display.
     */
    public function getTypeLabelsAttribute(): string
    {
        $labels = [
            'miopia' => 'Skincare',
            'lectura' => 'Skincare',
            'sin_graduacion' => 'Skincare',
            'toallitas' => 'Ritual',
        ];

        return collect($this->type ?? [])->map(fn ($t) => $labels[$t] ?? ucfirst($t))->join(' · ');
    }

    // ── Accessors ──

    public function getBadgeTextAttribute(): ?string
    {
        if (! $this->qualifiesFor2x1()) {
            return null;
        }

        return '2x1 · $' . number_format($this->price, 0, ',', '.') . ' c/u';
    }

    // ── 2x1 Logic ──

    /**
     * ¿Este producto entra en la promo 2x1?
     * Configurable por producto (badge_2x1) o por categoría (promo_2x1).
     */
    public function qualifiesFor2x1(): bool
    {
        return (bool) ($this->badge_2x1 || optional($this->category)->promo_2x1);
    }

    /**
     * Calculate 2x1 discount for a collection of cart items.
     * Only applies to lens products (not toallitas/accesorio) with badge_2x1 = true.
     *
     * Each item should have: 'product' (Product), 'qty' (int), 'unit_price' (float).
     *
     * Returns: ['total' => float, 'free_items' => array, 'savings' => float]
     */
    public static function calculate2x1(Collection $items): array
    {
        // Expand items by quantity into individual units, only for eligible lenses
        $units = [];

        foreach ($items as $item) {
            $product = $item['product'];

            if (! $product->qualifiesFor2x1()) {
                continue;
            }

            for ($i = 0; $i < $item['qty']; $i++) {
                $units[] = [
                    'name' => $product->name,
                    'price' => (float) $item['unit_price'],
                ];
            }
        }

        if (empty($units)) {
            return ['total' => 0, 'free_items' => [], 'savings' => 0];
        }

        // Sort by price descending — the cheaper one in each pair is free
        usort($units, fn ($a, $b) => $b['price'] <=> $a['price']);

        $total = 0;
        $freeItems = [];
        $originalTotal = array_sum(array_column($units, 'price'));

        foreach ($units as $index => $unit) {
            if (($index + 1) % 2 === 0) {
                // Every second item is free
                $freeItems[] = $unit['name'];
            } else {
                $total += $unit['price'];
            }
        }

        return [
            'total' => $total,
            'free_items' => $freeItems,
            'savings' => $originalTotal - $total,
        ];
    }
}
