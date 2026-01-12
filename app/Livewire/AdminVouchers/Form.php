<?php

namespace App\Livewire\AdminVouchers;

use App\Models\AdminVoucher;
use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public $voucherCode;
    public $voucherId;
    public $name = '';
    public $description = '';
    public $points_cost = 0;
    public $amount_cost = 0;
    public $valid_from = '';
    public $valid_until = '';
    public $usage_limit = '';
    public $is_active = true;
    public $selectedMerchants = [];
    public $showMessage = false;
    public $voucherImage;
    public $existingVoucherImage = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'points_cost' => 'required|integer|min:0',
        'amount_cost' => 'nullable|numeric|min:0',
        'valid_from' => 'nullable|date',
        'valid_until' => 'nullable|date|after_or_equal:valid_from',
        'usage_limit' => 'nullable|integer|min:1',
        'is_active' => 'boolean',
        'selectedMerchants' => 'required|array|min:1',
        'selectedMerchants.*' => 'exists:merchants,id',
        'voucherImage' => 'nullable|image|max:2048',
    ];

    public function mount($voucher_code = null)
    {
        $this->showMessage = session()->has('message');
        
        if ($voucher_code) {
            $this->voucherCode = $voucher_code;
            $adminVoucher = AdminVoucher::where('voucher_code', $voucher_code)->firstOrFail();
            $this->voucherId = $adminVoucher->id;
            $this->name = $adminVoucher->name;
            $this->description = $adminVoucher->description;
            $this->points_cost = $adminVoucher->points_cost;
            $this->amount_cost = $adminVoucher->amount_cost ?? 0;
            $this->valid_from = $adminVoucher->valid_from ? $adminVoucher->valid_from->format('Y-m-d\TH:i') : '';
            $this->valid_until = $adminVoucher->valid_until ? $adminVoucher->valid_until->format('Y-m-d\TH:i') : '';
            $this->usage_limit = $adminVoucher->usage_limit;
            $this->is_active = $adminVoucher->is_active;
            $this->selectedMerchants = $adminVoucher->merchants()->pluck('merchants.id')->toArray();
            $this->existingVoucherImage = $adminVoucher->image_url;
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
            'name' => $this->name,
            'description' => $this->description,
            'points_cost' => $this->points_cost,
            'amount_cost' => $this->amount_cost ?? 0,
            'valid_from' => $this->valid_from ? date('Y-m-d H:i:s', strtotime($this->valid_from)) : null,
            'valid_until' => $this->valid_until ? date('Y-m-d H:i:s', strtotime($this->valid_until)) : null,
            'usage_limit' => $this->usage_limit ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->voucherCode) {
            $adminVoucher = AdminVoucher::where('voucher_code', $this->voucherCode)->firstOrFail();
            $adminVoucher->update($data);
            $adminVoucher->merchants()->sync($this->selectedMerchants);
            $message = 'Admin voucher updated successfully.';
        } else {
            $data['created_by'] = auth()->id();
            $adminVoucher = AdminVoucher::create($data);
            $adminVoucher->merchants()->attach($this->selectedMerchants);
            $message = 'Admin voucher created successfully.';
        }

        // Handle voucher image upload
        if ($this->voucherImage) {
            // Clear existing image
            $adminVoucher->clearMediaCollection('image');
            
            // Add new image
            $adminVoucher->addMedia($this->voucherImage->getRealPath())
                ->usingName($adminVoucher->name . ' - Image')
                ->toMediaCollection('image');
        }

        session()->flash('message', $message);
        $this->showMessage = true;
        return redirect()->route('admin.admin-vouchers.index');
    }

    public function removeVoucherImage()
    {
        if ($this->voucherCode) {
            $adminVoucher = AdminVoucher::where('voucher_code', $this->voucherCode)->first();
            if ($adminVoucher) {
                $adminVoucher->clearMediaCollection('image');
                $this->existingVoucherImage = null;
                $this->dispatch('voucher-image-removed');
            }
        }
    }

    public function render()
    {
        $merchants = Merchant::orderBy('name')->get();

        return view('livewire.admin-vouchers.form', [
            'merchants' => $merchants,
        ])->layout('components.layouts.app');
    }
}

