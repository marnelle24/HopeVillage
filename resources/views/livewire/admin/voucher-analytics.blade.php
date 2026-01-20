<div class="card bg-base-100 shadow-lg">
    <div class="card-body">
        <h2 class="card-title text-success">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
            </svg>
            Voucher Analytics
        </h2>
        <div class="stats stats-vertical lg:stats-horizontal shadow w-full">
            <div class="stat">
                <div class="stat-title">Total Vouchers</div>
                <div class="stat-value text-primary">{{ $voucherStats['total'] }}</div>
                <div class="stat-desc">All time</div>
            </div>
            <div class="stat">
                <div class="stat-title">Active</div>
                <div class="stat-value text-success">{{ $voucherStats['active'] }}</div>
                <div class="stat-desc">Currently available</div>
            </div>
            <div class="stat">
                <div class="stat-title">Redemption Rate</div>
                <div class="stat-value text-warning">{{ $voucherStats['redemptionRate'] }}%</div>
                <div class="stat-desc">{{ number_format($voucherStats['redeemed']) }} redeemed</div>
            </div>
        </div>
    </div>
</div>