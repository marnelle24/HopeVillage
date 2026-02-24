<?php

namespace App\Livewire\Vouchers;

use App\Models\Merchant;
use App\Models\Voucher;
use Livewire\Component;

class Form extends Component
{
    public $voucherCode;
    public $voucherId;
    public $merchant_id = '';
    public $name = '';
    public $description = '';
    public $discount_type = 'percentage';
    public $discount_value = '';
    public $min_purchase = '';
    public $max_discount = '';
    public $valid_from = '';
    public $valid_until = '';
    public $usage_limit = '';
    public $is_active = true;
    public $showMessage = false;

    protected $rules = [
        'merchant_id' => 'required|exists:merchants,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'discount_type' => 'required|in:percentage,fixed,item',
        'discount_value' => 'required|numeric|min:0',
        'min_purchase' => 'nullable|numeric|min:0',
        'max_discount' => 'nullable|numeric|min:0',
        'valid_from' => 'nullable|date',
        'valid_until' => 'nullable|date|after_or_equal:valid_from',
        'usage_limit' => 'nullable|integer|min:1',
        'is_active' => 'boolean',
    ];

    public function mount($voucher_code = null)
    {
        $this->showMessage = session()->has('message');
        
        if ($voucher_code) {
            $this->voucherCode = $voucher_code;
            $voucher = Voucher::where('voucher_code', $voucher_code)->firstOrFail();
            $this->voucherId = $voucher->id;
            $this->merchant_id = $voucher->merchant_id;
            $this->name = $voucher->name;
            $this->description = $voucher->description;
            $this->discount_type = $voucher->discount_type;
            $this->discount_value = $voucher->discount_value;
            $this->min_purchase = $voucher->min_purchase;
            $this->max_discount = $voucher->max_discount;
            $this->valid_from = $voucher->valid_from ? $voucher->valid_from->format('Y-m-d\TH:i') : '';
            $this->valid_until = $voucher->valid_until ? $voucher->valid_until->format('Y-m-d\TH:i') : '';
            $this->usage_limit = $voucher->usage_limit;
            $this->is_active = $voucher->is_active;
        }
    }

    public function updatedDiscountType($value)
    {
        if ($value === 'item') {
            $this->discount_value = 100;
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'merchant_id' => $this->merchant_id,
            'name' => $this->name,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'min_purchase' => $this->min_purchase ?: null,
            'max_discount' => $this->max_discount ?: null,
            'valid_from' => $this->valid_from ? date('Y-m-d H:i:s', strtotime($this->valid_from)) : null,
            'valid_until' => $this->valid_until ? date('Y-m-d H:i:s', strtotime($this->valid_until)) : null,
            'usage_limit' => $this->usage_limit ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->voucherCode) {
            $voucher = Voucher::where('voucher_code', $this->voucherCode)->firstOrFail();
            $voucher->update($data);
            $message = 'Voucher updated successfully.';
        } else {
            $voucher = Voucher::create($data);
            $message = 'Voucher created successfully.';
        }

        session()->flash('message', $message);
        $this->showMessage = true;
        return redirect()->route('admin.vouchers.index');
    }

    public function render()
    {
        $merchants = Merchant::orderBy('name')->get();
        $discountTypes = [
            'percentage' => 'Percentage',
            'fixed' => 'Fixed Amount',
            'item' => 'Free Item',
        ];

        return view('livewire.vouchers.form', [
            'merchants' => $merchants,
            'discountTypes' => $discountTypes,
        ])->layout('components.layouts.app');
    }
}
