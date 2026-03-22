<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('min_quantity');
            $table->decimal('percentage', 5, 2);
            
            // Index for discount lookup query (DiscountService::getApplicableDiscount)
            $table->index(['product_id', 'min_quantity']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}