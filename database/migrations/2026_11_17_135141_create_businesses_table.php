<?php

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
        Schema::create('businesses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('phone');
            $table->string('email');
            $table->string('address')->nullable();
            $table->string('tax_id')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->unsignedBigInteger('order_counter')->default(0);
            $table->unsignedBigInteger('receipt_counter')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
