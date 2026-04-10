<?php

namespace App\Livewire\Admin\Backups;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public array $backups = [];

    public bool $isRunning = false;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('can_backup_database'), 403, 'Unauthorized.');

        $this->loadBackups();
    }

    public function createBackup(): void
    {
        abort_unless(auth()->user()?->can('can_backup_database'), 403, 'Unauthorized.');

        $this->isRunning = true;

        try {
            $exitCode = Artisan::call('backup:run', [
                '--only-db' => true,
            ]);

            if ($exitCode === 0) {
                $this->dispatch('notify', type: 'success', message: 'Database backup created successfully.');
            } else {
                $commandOutput = trim(Artisan::output());
                $message = $commandOutput !== ''
                    ? 'Backup failed: '.$commandOutput
                    : 'Backup failed. Please check logs.';

                $this->dispatch('notify', type: 'error', message: $message);
            }
        } catch (\Throwable $exception) {
            report($exception);
            $this->dispatch('notify', type: 'error', message: 'Failed to create backup. Please check logs.');
        } finally {
            $this->isRunning = false;
            $this->loadBackups();
        }
    }

    public function downloadBackup(string $path)
    {
        abort_unless(auth()->user()?->can('can_backup_database'), 403, 'Unauthorized.');

        if (! Storage::disk('local')->exists($path)) {
            $this->dispatch('notify', type: 'error', message: 'Backup file no longer exists.');

            return null;
        }

        return response()->download(
            Storage::disk('local')->path($path),
            basename($path)
        );
    }

    protected function loadBackups(): void
    {
        $disk = Storage::disk('local');

        $directory = $this->backupDirectory();

        if (! $disk->exists($directory)) {
            $this->backups = [];

            return;
        }

        $this->backups = collect($disk->files($directory))
            ->filter(fn (string $file) => str_ends_with($file, '.zip'))
            ->map(fn (string $file) => [
                'path' => $file,
                'name' => basename($file),
                'size' => $disk->size($file),
                'last_modified' => $disk->lastModified($file),
            ])
            ->sortByDesc('last_modified')
            ->values()
            ->all();
    }

    protected function backupDirectory(): string
    {
        $backupName = (string) config('backup.backup.name', 'laravel-backup');

        return Str::slug($backupName);
    }

    public function render()
    {
        return view('livewire.admin.backups.index')
            ->layout('layouts.app');
    }
}
