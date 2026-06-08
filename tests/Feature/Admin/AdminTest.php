<?php

namespace Tests\Feature\Admin;

use App\Domains\Auth\Models\User;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/dashboard');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'total_bookings',
                    'today_bookings',
                    'top_services',
                    'average_rating',
                    'revenue_today',
                    'revenue_this_month',
                ],
            ]);
    }

    public function test_receptionist_can_access_dashboard(): void
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $this->actingAs($receptionist, 'sanctum')
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_receptionist_cannot_access_settings(): void
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $this->actingAs($receptionist, 'sanctum')
            ->getJson('/api/v1/admin/settings')
            ->assertForbidden();
    }

    public function test_receptionist_cannot_delete_service(): void
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $category = ServiceCategory::create(['name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true]);
        $service = Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'قص', 'name_en' => 'Haircut',
            'price' => 80, 'duration_minutes' => 45, 'is_active' => true,
        ]);

        $this->actingAs($receptionist, 'sanctum')
            ->deleteJson('/api/v1/admin/services/'.$service->id)
            ->assertForbidden();
    }

    public function test_customer_cannot_access_admin(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();
    }

    public function test_customer_cannot_list_admin_services(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/admin/services')
            ->assertForbidden();
    }

    public function test_customer_cannot_list_admin_bookings(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/admin/bookings')
            ->assertForbidden();
    }

    public function test_receptionist_can_list_admin_services(): void
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $this->actingAs($receptionist, 'sanctum')
            ->getJson('/api/v1/admin/services')
            ->assertOk();
    }
}
