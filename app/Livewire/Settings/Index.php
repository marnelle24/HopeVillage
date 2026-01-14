<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $showMessage = false;

    // Form properties
    public $settingId;
    public $name = '';
    public $description = '';
    public $key = '';
    public $value = '';
    public $status = true;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'key' => 'required|string|max:255|unique:settings,key',
        'value' => 'nullable|string',
        'status' => 'boolean',
    ];

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

    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        $this->settingId = $setting->id;
        $this->name = $setting->name;
        $this->description = $setting->description;
        $this->key = $setting->key;
        $this->value = $setting->value;
        $this->status = $setting->status;
        
        // Update validation rule for edit mode
        $this->rules['key'] = 'required|string|max:255|unique:settings,key,' . $id;
        
        // Scroll to form
        $this->dispatch('scroll-to-form');
    }

    public function createNew()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->settingId = null;
        $this->name = '';
        $this->description = '';
        $this->key = '';
        $this->value = '';
        $this->status = true;
        $this->resetErrorBag();
        $this->rules['key'] = 'required|string|max:255|unique:settings,key';
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'key' && $this->settingId) {
            $this->rules['key'] = 'required|string|max:255|unique:settings,key,' . $this->settingId;
        }
        if (in_array($propertyName, ['name', 'description', 'key', 'value', 'status'])) {
            $this->validateOnly($propertyName);
        }
    }

    public function save()
    {
        if ($this->settingId) {
            // For edit mode, validate all fields except key (key is disabled)
            $this->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'value' => 'nullable|string',
                'status' => 'boolean',
            ]);
            
            $setting = Setting::findOrFail($this->settingId);
            $setting->update([
                'name' => $this->name,
                'description' => $this->description,
                'value' => $this->value ?? null,
                'status' => $this->status,
            ]);
            $message = 'Setting updated successfully.';
        } else {
            // For create mode, validate all fields
            $this->validate();
            
            Setting::create([
                'name' => $this->name,
                'description' => $this->description,
                'key' => $this->key,
                'value' => $this->value ?? null,
                'status' => $this->status,
                'added_by' => auth()->id(),
            ]);
            $message = 'Setting created successfully.';
        }

        $this->showMessage = true;
        $this->resetForm();
        $this->resetPage();
        session()->flash('message', $message);
    }

    public function delete($id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete(); // This will perform a soft delete
        
        // If deleting the currently edited setting, reset form
        if ($this->settingId == $id) {
            $this->resetForm();
        }
        
        session()->flash('message', 'Setting deleted successfully.');
        $this->showMessage = true;
        $this->resetPage();
    }

    public function render()
    {
        $query = Setting::with(['addedBy']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('key', 'like', '%' . $this->search . '%')
                  ->orWhere('value', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter === 'active');
        }

        $settings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.settings.index', [
            'settings' => $settings,
        ])->layout('components.layouts.app');
    }
}
