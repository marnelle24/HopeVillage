<div>
    @can('can_backup_database')
        <x-slot name="header">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ __('Database Backups') }}
                </h2>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ __('Create Backup') }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ __('Creates a zipped database backup and saves it on the server disk.') }}
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="createBackup"
                            wire:loading.attr="disabled"
                            wire:target="createBackup"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-full text-white bg-orange-600 hover:bg-orange-700 disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="createBackup">{{ __('Create DB Backup') }}</span>
                            <span wire:loading wire:target="createBackup">{{ __('Creating...') }}</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Available Backups') }}</h3>

                    @if (empty($backups))
                        <div class="text-sm text-gray-500">{{ __('No backup files found yet.') }}</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('File') }}
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Size') }}
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Created At') }}
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($backups as $backup)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-800">{{ $backup['name'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('d M Y, g:i A') }}
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <button
                                                    type="button"
                                                    wire:click="downloadBackup('{{ $backup['path'] }}')"
                                                    class="inline-flex items-center cursor-pointer px-3 py-1.5 rounded-full text-sm text-white bg-slate-700 hover:bg-slate-800"
                                                >
                                                    {{ __('Download') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        @php abort(403, 'Unauthorized.'); @endphp
    @endcan
</div>
