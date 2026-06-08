<?php

namespace App\Livewire;

use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ServiceQuickSetup extends Component
{
    public array $categories = [];

    public function mount(): void
    {
        $this->categories = [$this->blankCategory()];
    }

    public function addCategory(): void
    {
        $this->categories[] = $this->blankCategory();
    }

    public function removeCategory(int $catIndex): void
    {
        array_splice($this->categories, $catIndex, 1);

        if (empty($this->categories)) {
            $this->categories = [$this->blankCategory()];
        }
    }

    public function addService(int $catIndex): void
    {
        $this->categories[$catIndex]['services'][] = $this->blankService();
    }

    public function removeService(int $catIndex, int $svcIndex): void
    {
        array_splice($this->categories[$catIndex]['services'], $svcIndex, 1);

        if (empty($this->categories[$catIndex]['services'])) {
            $this->categories[$catIndex]['services'] = [$this->blankService()];
        }
    }

    public function save(): void
    {
        $this->validate([
            'categories'                              => ['required', 'array', 'min:1'],
            'categories.*.name_ar'                   => ['required', 'string', 'max:100'],
            'categories.*.name_en'                   => ['required', 'string', 'max:100'],
            'categories.*.services'                  => ['required', 'array', 'min:1'],
            'categories.*.services.*.name_ar'        => ['required', 'string', 'max:150'],
            'categories.*.services.*.name_en'        => ['required', 'string', 'max:150'],
            'categories.*.services.*.price'          => ['required', 'numeric', 'min:0'],
            'categories.*.services.*.duration_minutes' => ['required', 'integer', 'min:5'],
        ]);

        $categoriesCreated = 0;
        $servicesCreated = 0;

        DB::transaction(function () use (&$categoriesCreated, &$servicesCreated) {
            foreach ($this->categories as $catData) {
                $category = ServiceCategory::firstOrCreate(
                    ['name_en' => $catData['name_en']],
                    ['name_ar' => $catData['name_ar'], 'is_active' => true]
                );

                if ($category->wasRecentlyCreated) {
                    $categoriesCreated++;
                }

                foreach ($catData['services'] as $svcData) {
                    $service = Service::firstOrCreate(
                        [
                            'service_category_id' => $category->id,
                            'name_en'             => $svcData['name_en'],
                        ],
                        [
                            'name_ar'          => $svcData['name_ar'],
                            'price'            => $svcData['price'],
                            'duration_minutes' => $svcData['duration_minutes'],
                            'is_active'        => true,
                        ]
                    );

                    if ($service->wasRecentlyCreated) {
                        $servicesCreated++;
                    }
                }
            }
        });

        session()->flash('success', __('web.setup_save_success', [
            'categories' => $categoriesCreated,
            'services'   => $servicesCreated,
        ]));

        $this->categories = [$this->blankCategory()];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.service-quick-setup');
    }

    private function blankCategory(): array
    {
        return [
            'name_ar'  => '',
            'name_en'  => '',
            'services' => [$this->blankService()],
        ];
    }

    private function blankService(): array
    {
        return [
            'name_ar'          => '',
            'name_en'          => '',
            'price'            => '',
            'duration_minutes' => 30,
        ];
    }
}
