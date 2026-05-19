<?php

namespace App\Livewire\Member\Merchants;

use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Merchant::query()
            ->where('is_active', true)
            ->with('media');

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                    ->orWhere('description', 'like', $s)
                    ->orWhere('city', 'like', $s)
                    ->orWhere('province', 'like', $s)
                    ->orWhere('address', 'like', $s);
            });
        }

        $merchants = $query
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.member.merchants.index', [
            'merchants' => $merchants,
        ]);
    }
}
