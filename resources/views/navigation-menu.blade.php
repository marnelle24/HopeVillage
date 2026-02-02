@php
    $navBgClass = 'bg-white';

    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            $navBgClass = 'bg-gray-100';
        } elseif (auth()->user()->isMerchantUser()) {
            $navBgClass = 'bg-green-100';
        } else {
            // Member (default)
            $navBgClass = 'bg-yellow-100';
        }
    }
@endphp

@if(auth()->check() && auth()->user()->isAdmin())
    @include('admin-navigation-menu')
@elseif(auth()->check() && auth()->user()->isMerchantUser())
    @include('merchant-navigation-menu')
@else
    @include('member-navigation-menu')
@endif

