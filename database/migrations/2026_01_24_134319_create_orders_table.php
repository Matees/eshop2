<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();

            $table->string('status')->default(OrderStatus::Pending);

            $table->string('email');
            $table->string('phone')->nullable();

            $table->string('address_line_one');
            $table->string('address_line_two');
            $table->string('address_line_three');

            $table->decimal('total');
            $table->decimal('subtotal');
            $table->decimal('tax_amount')->default(0);
            $table->decimal('discount_amount')->default(0);

            $table->text('note')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
