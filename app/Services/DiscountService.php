<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;

class DiscountService
{
    /**
     * Find the best applicable discount for a product at a given quantity.
     */
    public function getApplicableDiscount(int $productId, int $quantity): ?Discount
    {
        return Discount::where('product_id', $productId)
            ->where('min_quantity', '<=', $quantity)
            ->orderByDesc('percentage')
            ->first();
    }

    /**
     * Calculate price and savings information for a cart line.
     *
     * @return array{original_total: float, discounted_total: float, savings: float, discount_percentage: ?float}
     */
    public function calculateLineWithDiscount(float $price, int $quantity, int $productId): array
    {
        $originalTotal = $price * $quantity;
        $discount = $this->getApplicableDiscount($productId, $quantity);
        
        $discountedTotal = $originalTotal;
        $discountPercentage = null;

        if ($discount) {
            $discountPercentage = (float) $discount->percentage;
            $discountedTotal = $originalTotal * (1 - ($discountPercentage / 100));
        }

        return [
            'original_total' => $originalTotal,
            'discounted_total' => $discountedTotal,
            'savings' => $originalTotal - $discountedTotal,
            'discount_percentage' => $discountPercentage,
        ];
    }

    /**
     * Calculate the total price with discount applied.
     *
     * @param float $price The price of a single product.
     * @param int $quantity The quantity of the product.
     * @param int $productId The ID of the product.
     * @return float The total price after applying the discount.
     */
    public function calculatePriceWithDiscount(float $price, int $quantity, int $productId): float
    {
        $line = $this->calculateLineWithDiscount($price, $quantity, $productId);
        return $line['discounted_total'];
    }

    /**
     * Build cart items with calculated discount-aware totals.
     *
     * @return array<int, array{product: Product, quantity: int, line_total: float, original_total: float, savings: float, applied_discount_percentage: ?float, next_discount?: ?Discount}>
     */
    public function buildCartItems(array $cart, bool $includeNextDiscount = false): array
    {
        if (empty($cart)) {
            return [];
        }

        $productIds = array_map('intval', array_keys($cart));
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->when($includeNextDiscount, fn($q) => $q->with('discounts'))
            ->get()
            ->keyBy('id');

        $cartItems = [];

        foreach ($cart as $productId => $item) {
            $product = $products->get((int) $productId);
            if (! $product) {
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity < 1) {
                continue;
            }

            $line = $this->calculateLineWithDiscount($product->price, $quantity, $product->id);

            $cartItem = [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => $line['discounted_total'],
                'original_total' => $line['original_total'],
                'savings' => $line['savings'],
                'applied_discount_percentage' => $line['discount_percentage'],
            ];

            if ($includeNextDiscount) {
                $cartItem['next_discount'] = $product->discounts
                    ->where('min_quantity', '>', $quantity)
                    ->sortBy('min_quantity')
                    ->first();
            }

            $cartItems[] = $cartItem;
        }

        return $cartItems;
    }

    /**
     * Build a full cart summary from session cart data.
     *
     * @return array{cartItems: array<int, array{product: Product, quantity: int, line_total: float, original_total: float, savings: float, applied_discount_percentage: ?float, next_discount?: ?Discount}>, subtotal: float, originalSubtotal: float, totalSavings: float}
     */
    public function summarizeCart(array $cart, bool $includeNextDiscount = false): array
    {
        $cartItems = $this->buildCartItems($cart, $includeNextDiscount);
        $subtotal = array_sum(array_column($cartItems, 'line_total'));
        $originalSubtotal = array_sum(array_column($cartItems, 'original_total'));

        return [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'originalSubtotal' => $originalSubtotal,
            'totalSavings' => $originalSubtotal - $subtotal,
        ];
    }
}