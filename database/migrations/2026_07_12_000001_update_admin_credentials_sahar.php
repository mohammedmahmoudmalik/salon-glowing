<?php

use App\Domains\Auth\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Update the salon admin account:
     *   name     => سحر البدري
     *   phone    => sahar   (login username — the web login treats non-email input as phone)
     *   password => sahar
     *
     * Login is performed by typing "sahar" / "sahar" on the admin login form.
     */
    public function up(): void
    {
        // Prefer the existing admin-role user; fall back to previously seeded admin emails.
        $admin = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->orderBy('id')->first()
            ?? User::whereIn('email', ['mohammedmalik995@gmail.com', 'admin@admin.com'])->first();

        // Fresh database (no admin yet): the FreshInstallSeeder is responsible for creating one.
        if (! $admin) {
            return;
        }

        $admin->name = 'سحر البدري';
        $admin->phone = 'sahar';
        $admin->password = Hash::make('sahar');
        $admin->is_active = true;
        $admin->save();

        if (! $admin->hasRole('admin')) {
            $admin->syncRoles('admin');
        }
    }

    public function down(): void
    {
        // Credential change is not reversibly restorable; no-op.
    }
};
