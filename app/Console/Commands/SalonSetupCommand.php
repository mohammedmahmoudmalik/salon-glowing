<?php

namespace App\Console\Commands;

use App\Domains\Admin\Models\Setting;
use App\Domains\Auth\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SalonSetupCommand extends Command
{
    protected $signature = 'salon:setup';
    protected $description = 'Interactive setup wizard for a new salon installation';

    public function handle(): int
    {
        $this->info('=== Salon Setup Wizard ===');
        $this->newLine();

        $salonName   = $this->ask('Salon name (Arabic)', env('SALON_NAME', 'صالون'));
        $adminName   = $this->ask('Admin display name', env('ADMIN_NAME', 'مدير الصالون'));
        $adminEmail  = $this->ask('Admin email', env('ADMIN_EMAIL', 'admin@admin.com'));
        $adminPass   = $this->secret('Admin password (leave blank to keep current)');
        $appUrl      = $this->ask('App URL', env('APP_URL', 'http://localhost'));

        $this->newLine();
        $this->info('--- Salon working settings ---');
        $capacity    = (int) $this->ask('Max simultaneous bookings (salon_capacity)', Setting::get('salon_capacity', 3));
        $buffer      = (int) $this->ask('Buffer minutes between bookings', Setting::get('booking_buffer_minutes', 10));

        $this->newLine();
        $this->info('Summary:');
        $this->table(['Key', 'Value'], [
            ['Salon name',       $salonName],
            ['Admin name',       $adminName],
            ['Admin email',      $adminEmail],
            ['Admin password',   $adminPass ? '(updated)' : '(unchanged)'],
            ['App URL',          $appUrl],
            ['Capacity',         $capacity],
            ['Buffer minutes',   $buffer],
        ]);

        if (! $this->confirm('Apply these settings?', true)) {
            $this->warn('Setup cancelled.');
            return self::FAILURE;
        }

        $this->updateEnvFile([
            'APP_URL'        => $appUrl,
            'SALON_NAME'     => '"'.$salonName.'"',
            'ADMIN_NAME'     => '"'.$adminName.'"',
            'ADMIN_EMAIL'    => $adminEmail,
        ]);

        Setting::set('salon_name', $salonName);
        Setting::set('salon_capacity', $capacity);
        Setting::set('booking_buffer_minutes', $buffer);

        $this->upsertAdmin($adminName, $adminEmail, $adminPass ?: null);

        $this->newLine();
        $this->info('Setup complete. Run `php artisan config:clear` to reload cached config.');

        return self::SUCCESS;
    }

    private function upsertAdmin(string $name, string $email, ?string $password): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $attributes = ['name' => $name, 'is_active' => true];
        if ($password) {
            $attributes['password'] = Hash::make($password);
        }

        $user = User::updateOrCreate(['email' => $email], $attributes);
        $user->syncRoles('admin');

        $this->info("Admin user: {$email}");
    }

    private function updateEnvFile(array $data): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            $this->warn('.env file not found — skipping file update.');
            return;
        }

        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $pattern     = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
            } else {
                $content .= "\n{$replacement}";
            }
        }

        file_put_contents($envPath, $content);
    }
}
