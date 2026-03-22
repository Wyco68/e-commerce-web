<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('date_time')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->decimal('total_price', 10, 2);
                $table->string('status');
                
                // Index for order history query (OrderController::index)
                $table->index(['user_id', 'date_time']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}