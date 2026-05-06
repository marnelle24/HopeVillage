<div class="card bg-white shadow border border-gray-300">
    <div class="card-body">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="card-title text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 00-2 2z"></path>
                </svg>
                Membership Signups vs Event Days
            </h2>
            <label class="form-control w-44">
                <span class="label-text text-xs text-gray-600 mb-1">Analysis Range</span>
                <select class="select select-bordered select-sm" wire:model.live="range">
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last 365 days</option>
                </select>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                <p class="text-xs uppercase tracking-wide text-emerald-700">Event-day signup rate</p>
                <p class="text-2xl font-semibold text-emerald-800">{{ number_format($signupTrendData['eventRate'], 2) }}/day</p>
                <p class="text-xs text-emerald-700 mt-1">
                    {{ $signupTrendData['eventDaySignups'] }} signups across {{ $signupTrendData['eventDayCount'] }} event days
                </p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <p class="text-xs uppercase tracking-wide text-slate-600">Regular-day signup rate</p>
                <p class="text-2xl font-semibold text-slate-800">{{ number_format($signupTrendData['regularRate'], 2) }}/day</p>
                <p class="text-xs text-slate-600 mt-1">
                    {{ $signupTrendData['regularDaySignups'] }} signups across {{ $signupTrendData['regularDayCount'] }} regular days
                </p>
            </div>
        </div>

        <div class="mt-3 text-sm">
            @if(!is_null($signupTrendData['upliftPercent']))
                <span class="font-medium {{ $signupTrendData['upliftPercent'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ number_format($signupTrendData['upliftPercent'], 2) }}% {{ $signupTrendData['upliftPercent'] >= 0 ? 'higher' : 'lower' }}
                </span>
                <span class="text-gray-600">signups/day on event days vs regular days.</span>
            @else
                <span class="text-gray-600">Uplift unavailable because regular-day rate is 0.</span>
            @endif
        </div>

        <div class="relative h-[350px] mt-4">
        <canvas 
            wire:key="signup-rate-chart-{{ $range }}"
            x-data="{
                chart: null,
                init() {
                    const ctx = this.$el.getContext('2d');
                    const labels = @js($signupTrendData['labels']);
                    const data = @js($signupTrendData['data']);
                    const eventDayFlags = @js($signupTrendData['eventDayFlags']);

                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                label: 'New Member Signups',
                                data,
                                borderColor: 'rgb(234, 88, 12)',
                                backgroundColor: 'rgba(234, 88, 12, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: eventDayFlags.map((isEventDay) => isEventDay ? 'rgb(16, 185, 129)' : 'rgb(234, 88, 12)'),
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            afterLabel: function(context) {
                                                return eventDayFlags[context.dataIndex] ? 'Event day' : 'Regular day';
                                            }
                                        }
                                    }
                                },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            interaction: {
                                mode: 'nearest',
                                axis: 'x',
                                intersect: false
                            }
                        }
                    });
                }
            }"
        ></canvas>
        </div>
        <p class="text-xs text-gray-600 mt-2 text-center">
            Event days are exact calendar days between event start and end (inclusive), from {{ $signupTrendData['startDate'] }} to {{ $signupTrendData['endDate'] }}.
        </p>
    </div>
</div>
