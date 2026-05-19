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
        // 1. Categories Table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
        });

        // 2. Brands Table
        Schema::table('brands', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
        });

        // 3. Products Table
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->text('description_bn')->nullable()->after('description');
            $table->text('short_description_bn')->nullable()->after('short_description');
        });

        // 4. Product Variants Table
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('attribute_name_bn')->nullable()->after('attribute_name');
            $table->string('attribute_value_bn')->nullable()->after('attribute_value');
        });

        // 5. Combo Offers Table
        Schema::table('combo_offers', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->text('description_bn')->nullable()->after('description');
        });

        // 6. Banners Table
        Schema::table('banners', function (Blueprint $table) {
            $table->string('title_bn')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('title_bn');
        });

        Schema::table('combo_offers', function (Blueprint $table) {
            $table->dropColumn(['name_bn', 'description_bn']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['attribute_name_bn', 'attribute_value_bn']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_bn', 'description_bn', 'short_description_bn']);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('name_bn');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('name_bn');
        });
    }
};
