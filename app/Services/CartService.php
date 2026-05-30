<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CartService
{
    public function query(Request $request): Builder
    {
        return CartItem::query()
            ->with(['product.primaryImage', 'product.brand'])
            ->where(function (Builder $query) use ($request) {
                if ($request->user()) {
                    $query->where('user_id', $request->user()->id);
                } else {
                    $query->where('session_id', $request->session()->getId());
                }
            });
    }

    public function items(Request $request): Collection
    {
        return $this->query($request)->get();
    }

    public function add(Request $request, Product $product, int $quantity = 1): CartItem
    {
        $quantity = max(1, min($quantity, $product->stock_quantity));

        $attributes = [
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'session_id' => $request->user() ? null : $request->session()->getId(),
        ];

        $item = CartItem::query()
            ->where('product_id', $product->id)
            ->when($request->user(), fn (Builder $query) => $query->where('user_id', $request->user()->id))
            ->when(! $request->user(), fn (Builder $query) => $query->where('session_id', $request->session()->getId()))
            ->first();

        if ($item) {
            $item->update([
                'quantity' => min($product->stock_quantity, $item->quantity + $quantity),
            ]);

            return $item->refresh();
        }

        return CartItem::create($attributes + ['quantity' => $quantity]);
    }

    public function subtotal(Request $request): float
    {
        return round($this->items($request)->sum(fn (CartItem $item) => $item->line_total), 2);
    }

    public function count(Request $request): int
    {
        return (int) $this->query($request)->sum('quantity');
    }

    public function clear(Request $request): void
    {
        $this->query($request)->delete();
    }

    public function mergeGuestCartIntoUser(string $sessionId, User $user): void
    {
        $guestItems = CartItem::with('product')
            ->where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::where('user_id', $user->id)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min($guestItem->product->stock_quantity, $existing->quantity + $guestItem->quantity),
                ]);
                $guestItem->delete();
                continue;
            }

            $guestItem->update([
                'user_id' => $user->id,
                'session_id' => null,
            ]);
        }
    }
}
