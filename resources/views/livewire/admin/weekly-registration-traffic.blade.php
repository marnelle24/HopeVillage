<div class="bg-white p-6 rounded-lg shadow border border-gray-300">
    <h3 class="text-lg font-semibold text-orange-400 mb-4">Weekly Registration Traffic</h3>
    <div class="relative h-[400px] mt-4">
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
                                    position: 'bottom',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
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
    <p class="text-xs text-gray-500 mt-2 text-center">Last 7 days of member registrations</p>
</div>
