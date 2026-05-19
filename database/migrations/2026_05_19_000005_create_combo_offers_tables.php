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
        // 1. Create combo_offers table
        Schema::create('combo_offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Create combo_offer_products pivot table
        Schema::create('combo_offer_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_offer_id')->constrained('combo_offers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // 3. Modify cart_items table
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('combo_offer_id')->nullable()->after('variant_id')->constrained('combo_offers')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->change();
        });

        // 4. Modify order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('combo_offer_id')->nullable()->after('variant_id')->constrained('combo_offers')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['combo_offer_id']);
            $table->dropColumn('combo_offer_id');
            $table->foreignId('product_id')->nullable(false)->change();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['combo_offer_id']);
            $table->dropColumn('combo_offer_id');
            $table->foreignId('product_id')->nullable(false)->change();
        });

        Schema::dropIfExists('combo_offer_products');
        Schema::dropIfExists('combo_offers');
    }
};
