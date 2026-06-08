<?php

namespace Database\Seeders;

use App\Domains\Admin\Models\Setting;
use App\Domains\Auth\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FreshInstallSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedAdminUser();
        $this->seedSettings();
        $this->seedSocialSettings();
    }

    private function seedRoles(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
    }

    private function seedAdminUser(): void
    {
        $email    = env('ADMIN_EMAIL', 'admin@admin.com');
        $password = env('ADMIN_PASSWORD', 'admin');
        $name     = env('ADMIN_NAME', 'مدير الصالون');

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name'      => $name,
                'password'  => Hash::make($password),
                'is_active' => true,
            ]
        );
        $admin->syncRoles('admin');

        $this->command->info('');
        $this->command->info('=== Admin Credentials ===');
        $this->command->info("Email   : {$email}");
        $this->command->info("Password: {$password}");
        $this->command->info('');
        $this->command->warn('⚠  Change the admin password from the dashboard after first login.');
        $this->command->info('');

        $this->seedOwnerUser();
    }

    private function seedOwnerUser(): void
    {
        $email    = env('OWNER_EMAIL', 'owner@salon.test');
        $password = env('OWNER_PASSWORD', 'owner');
        $name     = env('OWNER_NAME', 'صاحب الصالون');

        $owner = User::updateOrCreate(
            ['email' => $email],
            [
                'name'      => $name,
                'password'  => Hash::make($password),
                'is_active' => true,
            ]
        );
        $owner->syncRoles('owner');

        $this->command->info('=== Owner Credentials ===');
        $this->command->info("Email   : {$email}");
        $this->command->info("Password: {$password}");
        $this->command->info('');
        $this->command->warn('⚠  Change the owner password from the dashboard after first login.');
        $this->command->info('');
    }

    private function seedSettings(): void
    {
        $salonName = env('SALON_NAME', 'صالون');

        $settings = [
            // ── Booking rules ─────────────────────────────────────────────
            [
                'key'   => 'salon_working_hours',
                'value' => [
                    'open'         => '09:00',
                    'close'        => '21:00',
                    'working_days' => [0, 1, 2, 3, 4], // Sun–Thu
                ],
            ],
            ['key' => 'booking_buffer_minutes',    'value' => 10],
            ['key' => 'cancellation_cutoff_hours', 'value' => 3],
            ['key' => 'salon_capacity',            'value' => 3],

            // ── Country / Currency ────────────────────────────────────────
            ['key' => 'salon_country', 'value' => env('SALON_COUNTRY', 'SA')],

            // ── Branding ──────────────────────────────────────────────────
            ['key' => 'salon_name', 'value' => $salonName],
            ['key' => 'site_logo',  'value' => ''],

            // ── Colors (defaults) ─────────────────────────────────────────
            ['key' => 'primary_color',         'value' => '#B76E79'],
            ['key' => 'primary_color_light',   'value' => '#c98a93'],
            ['key' => 'primary_color_dark',    'value' => '#9a5a64'],
            ['key' => 'soft_pink_color',       'value' => '#F2A7BB'],
            ['key' => 'soft_pink_light_color', 'value' => '#f7c8d5'],
            ['key' => 'secondary_color',       'value' => '#F5F0E8'],
            ['key' => 'secondary_color_dark',  'value' => '#E8E0CC'],
            ['key' => 'salon_text_color',      'value' => '#3d2b2f'],

            // ── Hero texts ────────────────────────────────────────────────
            ['key' => 'hero_title_ar',    'value' => $salonName],
            ['key' => 'hero_title_en',    'value' => $salonName],
            ['key' => 'hero_subtitle_ar', 'value' => 'احجزي موعدك الآن'],
            ['key' => 'hero_subtitle_en', 'value' => 'Book your appointment now'],
            ['key' => 'hero_cta_ar',      'value' => 'احجزي الآن'],
            ['key' => 'hero_cta_en',      'value' => 'Book Now'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }

    private function seedSocialSettings(): void
    {
        $socials = [
            ['key' => 'social_whatsapp',  'value' => ['url' => '', 'is_active' => false]],
            ['key' => 'social_instagram', 'value' => ['url' => '', 'is_active' => false]],
            ['key' => 'social_snapchat',  'value' => ['url' => '', 'is_active' => false]],
            ['key' => 'social_tiktok',    'value' => ['url' => '', 'is_active' => false]],
            ['key' => 'social_twitter',   'value' => ['url' => '', 'is_active' => false]],
            ['key' => 'social_facebook',  'value' => ['url' => '', 'is_active' => false]],
            ['key' => 'google_maps_url',  'value' => ['url' => '', 'is_active' => false]],
        ];

        foreach ($socials as $item) {
            Setting::updateOrCreate(['key' => $item['key']], ['value' => $item['value']]);
        }
    }
}
