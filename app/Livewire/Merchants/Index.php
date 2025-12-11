<?php

namespace App\Livewire\Merchants;

use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $showMessage = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->showMessage = session()->has('message');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function edit($merchant_code)
    {
        return redirect()->route('admin.merchants.edit', $merchant_code);
    }

    public function delete($merchant_code)
    {
        $merchant = Merchant::where('merchant_code', $merchant_code)->firstOrFail();
        $merchant->delete(); // This will perform a soft delete
        
        session()->flash('message', 'Merchant archived successfully.');
        $this->showMessage = true;
        $this->dispatch('merchant-deleted');
    }

    public function render()
    {
        $query = Merchant::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('merchant_code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $merchants = $query->with('media')
            ->withCount('vouchers')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.merchants.index', [
            'merchants' => $merchants,
        ])->layout('components.layouts.app');
    }
}
