<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'reset_token')) {
                    $table->string('reset_token')->nullable()->after('address');
                }
                if (! Schema::hasColumn('users', 'reset_expires_at')) {
                    $table->dateTime('reset_expires_at')->nullable()->after('reset_token');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'reset_expires_at')) {
                    $table->dropColumn('reset_expires_at');
                }
                if (Schema::hasColumn('users', 'reset_token')) {
                    $table->dropColumn('reset_token');
                }
            });
        }
    }
};