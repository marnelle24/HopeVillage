<x-app-layout>
    <x-slot name="header">
        @livewire('member.points-header')
    </x-slot>

    <div class="max-w-md mx-auto">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            @livewire(\App\Livewire\Member\VouchersV3\Index::class, key('member-vouchers-v3-index-page'))
        </div>
    </div>
</x-app-layout>
