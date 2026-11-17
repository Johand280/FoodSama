<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('origin')->default('phone');
            $table->string('delivery_address')->nullable();
            $table->uuid('public_token')->nullable()->unique();
            $table->uuid('idempotency_key')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('paid_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->unique(['business_id', 'idempotency_key']);
            $table->index(['business_id', 'completed_at']);
        });

        DB::table('orders')
            ->where('status', 'completed')
            ->update([
                'status' => 'delivered',
                'delivered_at' => DB::raw('COALESCE(completed_at, created_at)'),
                'paid_at' => DB::raw('COALESCE(completed_at, created_at)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['business_id', 'idempotency_key']);
            $table->dropUnique(['public_token']);
            $table->dropIndex(['business_id', 'completed_at']);
            $table->dropConstrainedForeignId('created_by_user_id');
            $table->dropConstrainedForeignId('paid_by_user_id');
            $table->dropColumn([
                'origin',
                'delivery_address',
                'public_token',
                'idempotency_key',
                'delivered_at',
                'paid_at',
                'cancellation_reason',
            ]);
        });
    }
};
