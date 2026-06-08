<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('status', 'bookings_status_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('is_hidden', 'reviews_is_hidden_index');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_is_hidden_index');
        });
    }
};
