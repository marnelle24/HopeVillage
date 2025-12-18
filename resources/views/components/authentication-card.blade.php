<div class="min-h-screen flex flex-col justify-center items-center sm:pt-0 bg-transparent py-10">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 p-6 bg-white shadow-md overflow-hidden rounded-xl">
        {{ $slot }}
    </div>
    <p class="mt-10 text-center text-md text-gray-500">
        @if (Route::has('register'))
            Already have an account? <a href="{{ route('login') }}" class="text-orange-500 hover:text-orange-600">Login</a>
        @else
            Don't have an account? <a href="{{ route('register') }}" class="text-orange-500 hover:text-orange-600">Register</a>
        @endif
    </p>
</div>
