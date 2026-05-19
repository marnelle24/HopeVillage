<?php

namespace App\Livewire\Member\Marketplace;

use App\Models\Location;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceItem;
use Livewire\Component;

class Browse extends Component
{
    public string $search = '';

    public string $categoryFilter = '';

    public string $locationFilter = '1';

    public function getItemsProperty()
    {
        $q = MarketplaceItem::query()
            ->with(['category', 'locations'])
            ->availableForMembers()
            ->orderBy('name');

        if ($this->search !== '') {
            $q->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }
        if ($this->categoryFilter !== '') {
            $q->where('marketplace_category_id', (int) $this->categoryFilter);
        }
        if ($this->locationFilter !== '') {
            $q->availableAtLocation((int) $this->locationFilter);
        }

        return $q->get();
    }

    public function render()
    {
        return view('livewire.member.marketplace.browse', [
            'items' => $this->items,
            'categories' => MarketplaceCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::query()->where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app', [
            'title' => __('Marketplace'),
        ]);
    }
}
