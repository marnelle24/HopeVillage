<div>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-3">
        <!-- Stat Card 1: Locations -->
        <div class="stat bg-white shadow border border-gray-300">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-7 h-7 md:w-8 md:h-8 lg:w-10 lg:h-10 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="stat-title">Locations</div>
            <div class="stat-value text-primary font-semibold">{{ $totalLocations }}</div>
            <div class="stat-desc">Active facilities</div>
        </div>

        <!-- Stat Card 2: Members -->
        <div class="stat bg-white shadow border border-gray-300">
            <div class="stat-figure text-secondary lg:pr-0 pr-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-7 h-7 md:w-8 md:h-8 lg:w-10 lg:h-10  stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div class="stat-title">Members</div>
            <div class="stat-value text-secondary font-semibold">{{ number_format($totalMembers) }}</div>
            <div class="stat-desc">{{ $engagementRate }}% active (30d)</div>
        </div>

        <!-- Stat Card 3: Total Merchants -->
        <div class="stat bg-white shadow border border-gray-300">
            <div class="stat-figure text-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="stat-title">Total Merchants</div>
            <div class="stat-value text-info">{{ number_format($totalMerchants) }}</div>
            <div class="stat-desc line-clamp-2">Registered merchants</div>
        </div>
        <!-- Points -->
        <div class="stat bg-white shadow border border-gray-300">
            <div class="stat-figure text-warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="stat-title">Total Points</div>
            <div class="stat-value text-warning">{{ number_format($totalPoints) }}</div>
            <div class="stat-desc">Awarded</div>
        </div>
    </div>
</div>
