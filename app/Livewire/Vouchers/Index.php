<?php

namespace App\Livewire\Vouchers;

use App\Models\Merchant;
use App\Models\Voucher;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $merchantFilter = '';
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

    public function updatingMerchantFilter()
    {
        $this->resetPage();
    }

    public function edit($voucher_code)
    {
        return redirect()->route('admin.vouchers.edit', $voucher_code);
    }

    public function delete($voucher_code)
    {
        $voucher = Voucher::where('voucher_code', $voucher_code)->firstOrFail();
        $voucher->delete(); // This will perform a soft delete
        
        session()->flash('message', 'Voucher archived successfully.');
        $this->showMessage = true;
        $this->dispatch('voucher-deleted');
    }

    public function render()
    {
        $query = Voucher::with('merchant');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('voucher_code', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'pending') {
                $query->where('is_active', false);
            } else {
                $query->where('is_active', $this->statusFilter === 'active');
            }
        }

        if ($this->merchantFilter) {
            $query->where('merchant_id', $this->merchantFilter);
        }

        $vouchers = $query->orderBy('created_at', 'desc')->paginate(10);
        $merchants = Merchant::orderBy('name')->get();

        return view('livewire.vouchers.index', [
            'vouchers' => $vouchers,
            'merchants' => $merchants,
        ])->layout('components.layouts.app');
    }
}
