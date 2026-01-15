<?php

namespace App\Livewire\ApiDocumentation;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class Index extends Component
{
    public $selectedFile = '';
    public $markdownContent = '';
    public $availableFiles = [];

    public function mount()
    {
        $this->loadAvailableFiles();
    }

    public function updatedSelectedFile($value)
    {
        // Clear content when a new file is selected
        $this->markdownContent = '';
    }

    public function loadAvailableFiles()
    {
        // Get all .md files from the project root
        $rootPath = base_path();
        $files = File::glob($rootPath . '/*.md');
        
        $this->availableFiles = collect($files)->map(function ($file) {
            return [
                'path' => $file,
                'name' => basename($file),
            ];
        })->sortBy('name')->values()->toArray();
    }

    public function readFile()
    {
        if (empty($this->selectedFile)) {
            $this->markdownContent = '';
            session()->flash('error', 'Please select a file first.');
            return;
        }

        $this->loadMarkdownContent();
        $this->dispatch('markdown-updated');
    }

    public function loadMarkdownContent()
    {
        if (empty($this->selectedFile)) {
            $this->markdownContent = '';
            return;
        }

        // Find the selected file
        $file = collect($this->availableFiles)->firstWhere('name', $this->selectedFile);
        
        if ($file && File::exists($file['path'])) {
            $this->markdownContent = File::get($file['path']);
        } else {
            $this->markdownContent = 'File not found.';
        }
    }

    public function render()
    {
        return view('livewire.api-documentation.index')
            ->layout('components.layouts.app');
    }
}
