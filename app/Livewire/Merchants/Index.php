<?php

namespace App\Livewire\Merchants;

use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all'; // 'all', 'active', 'inactive', 'pending'
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

    public function approve($merchant_code)
    {
        $merchant = Merchant::where('merchant_code', $merchant_code)->firstOrFail();
        $merchant->update(['is_active' => true]);
        
        session()->flash('message', 'Merchant approved successfully.');
        $this->showMessage = true;
    }

    public function reject($merchant_code)
    {
        $merchant = Merchant::where('merchant_code', $merchant_code)->firstOrFail();
        $merchant->delete(); // Soft delete to reject
        
        session()->flash('message', 'Merchant application rejected.');
        $this->showMessage = true;
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

        if ($this->statusFilter === 'pending') {
            $query->where('is_active', false);
        } elseif ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->onlyTrashed(); // Soft deleted merchants
        }
        // 'all' - show all non-deleted merchants (no additional filter needed)

        $merchants = $query->with('media')
            ->withCount('vouchers')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Count pending applications (non-deleted, inactive)
        $pendingCount = Merchant::where('is_active', false)->count();

        return view('livewire.merchants.index', [
            'merchants' => $merchants,
            'pendingCount' => $pendingCount,
        ])->layout('components.layouts.app');
    }
}
