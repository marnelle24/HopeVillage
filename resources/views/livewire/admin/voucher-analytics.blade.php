<div class="card bg-white shadow border border-gray-300">
    <div class="card-body">
        <h2 class="card-title text-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
            </svg>
            Voucher Analytics
        </h2>
        <div class="stats stats-vertical lg:stats-horizontal w-full">
            <div class="stat">
                <div class="stat-title text-gray-800">Total Vouchers</div>
                <div class="stat-value text-gray-800">{{ $voucherStats['total'] }}</div>
                <div class="stat-desc text-gray-600">All time</div>
            </div>
            <div class="stat">
                <div class="stat-title text-gray-800">Active</div>
                <div class="stat-value text-gray-800">{{ $voucherStats['active'] }}</div>
                <div class="stat-desc text-gray-600">Currently available</div>
            </div>
            <div class="stat">
                <div class="stat-title text-gray-800">Redemption Rate</div>
                <div class="stat-value text-gray-800">{{ $voucherStats['redemptionRate'] }}%</div>
                <div class="stat-desc text-gray-600">{{ number_format($voucherStats['redeemed']) }} redeemed</div>
            </div>
        </div>
    </div>
</div>