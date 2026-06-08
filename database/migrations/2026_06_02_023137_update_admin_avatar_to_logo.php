<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'admin@admin.com')
            ->update(['avatar' => 'logos/logo_tJ1aIM0wBO88.png']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'admin@admin.com')
            ->update(['avatar' => null]);
    }
};
