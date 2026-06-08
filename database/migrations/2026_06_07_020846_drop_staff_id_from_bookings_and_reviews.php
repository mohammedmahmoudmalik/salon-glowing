<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'staff_id')) {
                $table->dropColumn('staff_id');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'staff_id')) {
                $table->dropColumn('staff_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable()->after('customer_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable()->after('customer_id');
        });
    }
};
