<?php

namespace App\Services;

use App\Models\Discount;

class DiscountService
{
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
        $discount = Discount::where('product_id', $productId)
            ->where('min_quantity', '<=', $quantity)
            ->orderByDesc('percentage')
            ->first();

        if ($discount) {
            $discountedPrice = $price * (1 - $discount->percentage / 100);
            return $discountedPrice * $quantity;
        }

        return $price * $quantity;
    }
}