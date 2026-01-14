<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;

class Form extends Component
{
    public $settingId;
    public $name = '';
    public $description = '';
    public $key = '';
    public $value = '';
    public $status = true;
    public $showMessage = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'key' => 'required|string|max:255|unique:settings,key',
        'value' => 'nullable|string',
        'status' => 'boolean',
    ];

    public function mount($id = null)
    {
        $this->showMessage = session()->has('message');
        
        if ($id) {
            $this->settingId = $id;
            $setting = Setting::findOrFail($id);
            $this->name = $setting->name;
            $this->description = $setting->description;
            $this->key = $setting->key;
            $this->value = $setting->value;
            $this->status = $setting->status;
            
            // Update validation rule for edit mode
            $this->rules['key'] = 'required|string|max:255|unique:settings,key,' . $id;
        }
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'key' && $this->settingId) {
            $this->rules['key'] = 'required|string|max:255|unique:settings,key,' . $this->settingId;
        }
        $this->validateOnly($propertyName);
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

        session()->flash('message', $message);
        $this->showMessage = true;
        return redirect()->route('admin.settings.index');
    }

    public function render()
    {
        return view('livewire.settings.form')->layout('components.layouts.app');
    }
}
