<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Support\Collection;

class CheckoutService
{
    public function totals(Collection $items, ?Coupon $coupon = null): array
    {
        $subtotal = round($items->sum(fn (CartItem $item) => $item->line_total), 2);
        $discount = $coupon?->discountFor($subtotal) ?? 0;
        $taxable = max(0, $subtotal - $discount);
        $tax = round($taxable * 0.08, 2);
        $shipping = $subtotal >= 100 || $subtotal === 0.0 ? 0.0 : 9.95;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => round($taxable + $tax + $shipping, 2),
        ];
    }
}
