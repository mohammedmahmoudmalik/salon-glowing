<?php

namespace App\Livewire\Admin;

use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceTable extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'category')]
    public string $category = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        app()->setLocale(session('locale', config('app.locale')));
    }

    public function render()
    {
        app()->setLocale(session('locale', config('app.locale')));

        $query = Service::with('category')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name_ar', 'like', '%'.$this->search.'%')
                    ->orWhere('name_en', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->category) {
            $query->where('service_category_id', $this->category);
        }

        $services = $query->paginate(15);
        $categories = ServiceCategory::active()->orderBy('name_ar')->get();
        $locale = app()->getLocale();

        return view('livewire.admin.service-table', compact('services', 'categories', 'locale'));
    }
}
