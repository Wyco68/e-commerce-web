<?php

namespace App\Rules;

use App\Models\ProductVariant;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveProductVariant implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $variant = ProductVariant::with('product')->find($value);

        if (! $variant) {
            $fail('The selected product variant is invalid.');

            return;
        }

        if (! $variant->is_active || ! $variant->product?->is_active) {
            $fail('This product is no longer available.');
        }
    }
}
