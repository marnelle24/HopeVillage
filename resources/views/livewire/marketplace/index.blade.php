<div>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-between items-center gap-3">
                <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                    {{ __('Marketplace items') }}
                </h2>
                <div class="flex flex-wrap items-center gap-2">
                    @can('marketplace.edit')
                        <a href="{{ route('admin.marketplace.cashier') }}" class="text-sm bg-green-600 hover:bg-green-700 text-white transition-all duration-300 py-2 px-3 rounded-full font-medium hover:text-green-100">
                            {{ __('Cashier Checkout') }}
                        </a>
                    @endcan
                    {{-- @if (auth()->user()?->canAccessAdminMarketplace())
                        <a href="{{ route('admin.marketplace.orders') }}" class="text-sm bg-orange-500 hover:bg-orange-600 text-white transition-all duration-300 py-2 px-3 rounded-full font-medium hover:text-orange-200">
                            {{ __('Confirm Orders') }}
                        </a>
                    @endif --}}
                    @can('marketplace.create')
                        <a href="{{ route('admin.marketplace.create') }}" class="flex items-center gap-1 text-sm bg-orange-600 hover:bg-orange-700 text-white transition-all duration-300 py-2 px-3 rounded-full font-medium hover:text-orange-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span class="">{{ __('Add item') }}</span>
                        </a>
                        <a href="{{ route('admin.marketplace.create') }}" class="md:hidden inline-flex bg-orange-500 hover:bg-orange-600 text-white p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('Search items…') }}"
                            class="w-full px-4 py-2 border text-gray-800 border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                    </div>
                    <div>
                        <select wire:model.live="statusFilter" class="w-full px-4 py-2 border text-gray-800 border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500">
                            <option value="all">{{ __('All') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($items as $item)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="aspect-video bg-gray-100 flex items-center justify-center overflow-hidden">
                            @if ($item->image_url)
                                <img src="{{ $item->image_url }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-400 text-sm">{{ __('No image') }}</span>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start gap-2">
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold text-gray-900 text-xl">{{ $item->name }}</h3>
                                    <p class="text-xs text-gray-500 my-1 line-clamp-2 italic capitalize">{{ $item->description }}</p>
                                </div>
                                @if ($item->is_active)
                                    <span class="shrink-0 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">{{ __('Active') }}</span>
                                @else
                                    <span class="shrink-0 text-xs bg-red-100 text-red-400 px-2 py-0.5 rounded-full">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                            <p class="flex items-center gap-1">
                                <span class="text-orange-600 font-bold">{{ number_format($item->points_cost) }} {{ __('pts') }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if ($item->category)
                                    <span class="inline-block bg-orange-100 text-orange-800 px-2 py-0.5 rounded-full text-xs capitalize">{{ $item->category?->name }}</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs capitalize">{{ __('Uncategorized') }}</span>
                                @endif
                            </p>
                            {{-- <p class="text-xs text-gray-400 mt-2">
                                @if ($item->stock === null)
                                    {{ __('Unlimited stock') }}
                                @else
                                    {{ __('Stock') }}: {{ $item->stock }}
                                @endif
                            </p> --}}
                            {{-- <p class="text-xs text-gray-400 mt-1">
                                @if ($item->locations->isEmpty())
                                    {{ __('Locations') }}: {{ __('All') }}
                                @else
                                    {{ __('Locations') }}: {{ $item->locations->pluck('name')->join(', ') }}
                                @endif
                            </p> --}}
                            <div class="mt-8 flex flex-wrap gap-2">
                                @can('marketplace.edit')
                                    <a href="{{ route('admin.marketplace.edit', $item->id) }}" class="text-xs bg-slate-600 text-white px-3 py-1.5 rounded-full hover:bg-slate-700 hover:text-slate-200 transition-all duration-300">{{ __('Edit') }}</a>
                                    <button type="button" wire:click="toggleActive({{ $item->id }})" class="text-xs bg-orange-500 text-white px-3 py-1.5 rounded-full hover:bg-orange-600 hover:text-orange-200 transition-all duration-300">
                                        @if ($item->is_active)
                                            {{ __('Set inactive') }}
                                        @else
                                            {{ __('Set active') }}
                                        @endif
                                    </button>
                                @endcan
                                @can('marketplace.delete')
                                    <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="{{ __('Archive this item?') }}" class="justify-end text-xs bg-red-500 text-white px-3 py-1.5 rounded-full hover:bg-red-600 hover:text-red-200 transition-all duration-300">{{ __('Archive') }}</button>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 col-span-full text-center py-12">{{ __('No marketplace items yet.') }}</p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
