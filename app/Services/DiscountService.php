<?php

namespace App\Services;

use App\Models\Discount;

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

        if (! $discount) {
            return [
                'original_total' => $originalTotal,
                'discounted_total' => $originalTotal,
                'savings' => 0.0,
                'discount_percentage' => null,
            ];
        }

        $discountedTotal = $originalTotal * (1 - ((float) $discount->percentage / 100));

        return [
            'original_total' => $originalTotal,
            'discounted_total' => $discountedTotal,
            'savings' => $originalTotal - $discountedTotal,
            'discount_percentage' => (float) $discount->percentage,
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
}