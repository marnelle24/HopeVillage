@if(auth()->check() && auth()->user()->isAdmin())
    @include('admin-navigation-menu')
    <br />
@elseif(auth()->check() && auth()->user()->isMerchantUser())
    @include('merchant-navigation-menu')
@else
    @include('member-navigation-menu')
@endif

