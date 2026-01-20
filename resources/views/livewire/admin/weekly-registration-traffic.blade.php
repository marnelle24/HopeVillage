<div class="card bg-base-100 shadow-lg">
    <div class="card-body">
        <h2 class="card-title text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 00-2 2z"></path>
            </svg>
            Weekly Registration Traffic
        </h2>
        <div class="relative h-[350px] mt-4">
        <canvas 
            x-data="{
                chart: null,
                init() {
                    const ctx = this.$el.getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @js($weeklyRegistrations['labels']),
                            datasets: [{
                                label: 'New Registrations',
                                data: @js($weeklyRegistrations['data']),
                                borderColor: 'rgb(234, 88, 12)',
                                backgroundColor: 'rgba(234, 88, 12, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'rgb(234, 88, 12)',
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
                                    }
                                },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 50,
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
        <p class="text-xs text-base-content/60 mt-2 text-center">Last 7 days of member registrations</p>
    </div>
</div>
