<?php

namespace Tests\Feature\Service;

use App\Domains\Auth\Models\User;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    // ── Public endpoints ──────────────────────────────────────────────────────

    public function test_guest_can_list_active_categories(): void
    {
        ServiceCategory::create(['name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true]);
        ServiceCategory::create(['name_ar' => 'مخفي', 'name_en' => 'Hidden', 'is_active' => false]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    public function test_guest_can_view_active_category(): void
    {
        $category = ServiceCategory::create(['name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true]);

        $this->getJson('/api/v1/categories/'.$category->slug)
            ->assertOk()
            ->assertJsonPath('data.slug', $category->slug);
    }

    public function test_guest_cannot_view_inactive_category(): void
    {
        $category = ServiceCategory::create(['name_ar' => 'مخفي', 'name_en' => 'Hidden', 'is_active' => false]);

        $this->getJson('/api/v1/categories/'.$category->slug)
            ->assertNotFound();
    }

    public function test_guest_can_list_active_services(): void
    {
        $category = ServiceCategory::create(['name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true]);
        Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'قص', 'name_en' => 'Haircut',
            'price' => 80, 'duration_minutes' => 45, 'is_active' => true,
        ]);
        Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'مخفي', 'name_en' => 'Hidden Service',
            'price' => 50, 'duration_minutes' => 30, 'is_active' => false,
        ]);

        $this->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_guest_can_view_active_service(): void
    {
        $category = ServiceCategory::create(['name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true]);
        $service = Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'قص', 'name_en' => 'Haircut',
            'price' => 80, 'duration_minutes' => 45, 'is_active' => true,
        ]);

        $this->getJson('/api/v1/services/'.$service->slug)
            ->assertOk()
            ->assertJsonPath('data.slug', $service->slug)
            ->assertJsonPath('data.price', '80.00');
    }

    public function test_guest_cannot_view_inactive_service(): void
    {
        $category = ServiceCategory::create(['name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true]);
        $service = Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'مخفي', 'name_en' => 'Disabled Service',
            'price' => 50, 'duration_minutes' => 30, 'is_active' => false,
        ]);

        $this->getJson('/api/v1/services/'.$service->slug)
            ->assertNotFound();
    }

    // ── Admin endpoints ───────────────────────────────────────────────────────

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/categories', [
                'name_ar' => 'مكياج',
                'name_en' => 'Makeup',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name_en', 'Makeup');
    }

    public function test_customer_cannot_create_category(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/v1/admin/categories', [
                'name_ar' => 'مكياج',
                'name_en' => 'Makeup',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_update_service(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = ServiceCategory::create(['name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true]);
        $service = Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'قص', 'name_en' => 'Haircut',
            'price' => 80, 'duration_minutes' => 45, 'is_active' => true,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->putJson('/api/v1/admin/services/'.$service->id, ['price' => 100])
            ->assertOk()
            ->assertJsonPath('data.price', '100.00');
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = ServiceCategory::create(['name_ar' => 'مؤقت', 'name_en' => 'Temp', 'is_active' => true]);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/v1/admin/categories/'.$category->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('service_categories', ['id' => $category->id]);
    }

    public function test_slug_auto_generated_on_category_creation(): void
    {
        $category = ServiceCategory::create(['name_ar' => 'العناية بالشعر', 'name_en' => 'Hair Care', 'is_active' => true]);

        $this->assertSame('hair-care', $category->slug);
    }

    public function test_slug_uniqueness_on_duplicate_name(): void
    {
        ServiceCategory::create(['name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true]);
        $second = ServiceCategory::create(['name_ar' => 'شعر 2', 'name_en' => 'Hair', 'is_active' => true]);

        $this->assertSame('hair-1', $second->slug);
    }

    public function test_unauthenticated_cannot_access_admin_routes(): void
    {
        $this->getJson('/api/v1/admin/categories')
            ->assertUnauthorized();
    }
}
