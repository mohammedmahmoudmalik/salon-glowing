<?php

namespace Tests\Feature\Offer;

use App\Domains\Offer\Models\Offer;
use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferTest extends TestCase
{
    use RefreshDatabase;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $category = ServiceCategory::create([
            'name_ar' => 'شعر', 'name_en' => 'Hair', 'is_active' => true,
        ]);

        $this->service = Service::create([
            'service_category_id' => $category->id,
            'name_ar' => 'قص شعر', 'name_en' => 'Haircut',
            'price' => 100.00, 'duration_minutes' => 60, 'is_active' => true,
        ]);
    }

    private function createOffer(array $overrides = []): Offer
    {
        $offer = Offer::create(array_merge([
            'title_ar' => 'عرض تجريبي',
            'title_en' => 'Test Offer',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'is_active' => true,
        ], $overrides));

        $offer->services()->attach($this->service->id);

        return $offer;
    }

    // ─── percentage ──────────────────────────────────────────────────────────

    public function test_percentage_offer_applies_correctly(): void
    {
        // 20% off 100 → final 80
        $this->createOffer(['discount_type' => 'percentage', 'discount_value' => 20]);

        $this->getJson('/api/v1/services/'.$this->service->slug)
            ->assertOk()
            ->assertJsonPath('data.original_price', '100.00')
            ->assertJsonPath('data.final_price', 80);
    }

    // ─── fixed ───────────────────────────────────────────────────────────────

    public function test_fixed_offer_applies_correctly(): void
    {
        // 30 fixed off 100 → final 70
        $this->createOffer(['discount_type' => 'fixed', 'discount_value' => 30]);

        $this->getJson('/api/v1/services/'.$this->service->slug)
            ->assertOk()
            ->assertJsonPath('data.original_price', '100.00')
            ->assertJsonPath('data.final_price', 70);
    }

    // ─── multiple offers: best wins ───────────────────────────────────────────

    public function test_multiple_offers_best_applies(): void
    {
        // 20% off 100 → saves 20 → final 80
        $this->createOffer(['discount_type' => 'percentage', 'discount_value' => 20]);
        // 35 fixed off 100 → saves 35 → final 65 (better for customer)
        $this->createOffer(['discount_type' => 'fixed', 'discount_value' => 35]);

        $this->getJson('/api/v1/services/'.$this->service->slug)
            ->assertOk()
            ->assertJsonPath('data.final_price', 65);
    }

    // ─── expired offer: ignored ───────────────────────────────────────────────

    public function test_expired_offer_not_applied(): void
    {
        $this->createOffer([
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'ends_at' => now()->subDay()->toDateTimeString(),
        ]);

        $this->getJson('/api/v1/services/'.$this->service->slug)
            ->assertOk()
            ->assertJsonPath('data.final_price', 100)
            ->assertJsonPath('data.active_offer', null);
    }
}
