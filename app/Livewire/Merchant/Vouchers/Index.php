<?php

namespace App\Livewire\Merchant\Vouchers;

use App\Models\Voucher;
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

    public function edit($voucher_code)
    {
        return redirect()->route('merchant.vouchers.edit', $voucher_code);
    }

    public function delete($voucher_code)
    {
        $merchant = auth()->user()->currentMerchant();
        if (!$merchant) {
            abort(403, 'No merchant associated with your account. Please contact an administrator.');
        }

        if (!$merchant->is_active) {
            abort(403, 'Your merchant account is pending approval. You cannot delete vouchers until your account is approved.');
        }

        $voucher = Voucher::where('voucher_code', $voucher_code)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();
        
        $voucher->delete(); // This will perform a soft delete
        
        session()->flash('message', 'Voucher archived successfully.');
        $this->showMessage = true;
        $this->dispatch('voucher-deleted');
    }

    public function render()
    {
        $merchant = auth()->user()->currentMerchant();
        
        if (!$merchant) {
            abort(403, 'No merchant associated with your account. Please contact an administrator.');
        }

        $query = Voucher::where('merchant_id', $merchant->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('voucher_code', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $vouchers = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.merchant.vouchers.index', [
            'vouchers' => $vouchers,
            'merchant' => $merchant,
        ])->layout('components.layouts.app');
    }
}
