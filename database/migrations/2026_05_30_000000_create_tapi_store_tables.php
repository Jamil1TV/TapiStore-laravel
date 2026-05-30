<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('full_name', 100);
                $table->string('email', 150)->unique();
                $table->string('password');
                $table->enum('role', ['customer', 'admin'])->default('customer');
                $table->string('phone', 20)->nullable();
                $table->text('address')->nullable();
                $table->string('reset_token')->nullable();
                $table->dateTime('reset_expires_at')->nullable();
                $table->dateTime('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 120)->unique();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
            });
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
                $table->string('name', 200);
                $table->string('slug', 220)->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->decimal('discount_price', 10, 2)->nullable();
                $table->integer('stock_quantity')->default(0);
                $table->string('image')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->dateTime('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('cart')) {
            Schema::create('cart', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->dateTime('added_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('full_name', 100);
                $table->string('email', 150);
                $table->string('phone', 20)->nullable();
                $table->text('address');
                $table->string('city', 100)->nullable();
                $table->decimal('total_amount', 10, 2);
                $table->enum('payment_method', ['cod', 'credit_card'])->default('cod');
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
                $table->enum('order_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
                $table->dateTime('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
            });
        }

        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->tinyInteger('rating');
                $table->text('comment')->nullable();
                $table->dateTime('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('wishlist')) {
            Schema::create('wishlist', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->dateTime('added_at')->useCurrent();
                $table->unique(['user_id', 'product_id'], 'unique_wish');
            });
        }

        if (! Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('email', 150);
                $table->string('subject', 200);
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->dateTime('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('wishlist');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
    }
};