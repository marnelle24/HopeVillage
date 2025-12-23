<div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
    <!-- Weekly Registration Traffic Chart -->
    <div class="bg-white p-6 rounded-lg shadow border border-gray-300">
        <h3 class="text-lg font-semibold text-orange-400 mb-4">Weekly Registration Traffic</h3>
        <div class="relative h-72">
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
                                        position: 'top',
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
                                            stepSize: 1,
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

    <!-- Entry Scanning Per Location Chart -->
    <div class="bg-white p-6 rounded-lg shadow border border-gray-300 mt-6">
        <h3 class="text-lg font-semibold text-orange-400 mb-4">Entry Scanning Per Location</h3>
        <div class="relative h-72">
            <canvas 
                x-data="{
                    chart: null,
                    init() {
                        const ctx = this.$el.getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: @js($entryScansPerLocation['labels']),
                                datasets: [{
                                    label: 'Entry Scans',
                                    data: @js($entryScansPerLocation['data']),
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.8)',
                                        'rgba(16, 185, 129, 0.8)',
                                        'rgba(245, 158, 11, 0.8)',
                                        'rgba(239, 68, 68, 0.8)',
                                        'rgba(139, 92, 246, 0.8)',
                                        'rgba(236, 72, 153, 0.8)',
                                        'rgba(20, 184, 166, 0.8)',
                                        'rgba(251, 146, 60, 0.8)',
                                        'rgba(99, 102, 241, 0.8)',
                                        'rgba(168, 85, 247, 0.8)',
                                    ],
                                    borderColor: [
                                        'rgb(59, 130, 246)',
                                        'rgb(16, 185, 129)',
                                        'rgb(245, 158, 11)',
                                        'rgb(239, 68, 68)',
                                        'rgb(139, 92, 246)',
                                        'rgb(236, 72, 153)',
                                        'rgb(20, 184, 166)',
                                        'rgb(251, 146, 60)',
                                        'rgb(99, 102, 241)',
                                        'rgb(168, 85, 247)',
                                    ],
                                    borderWidth: 2,
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
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
                                            stepSize: 1,
                                            precision: 0
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 45
                                        }
                                    }
                                },
                                interaction: {
                                    mode: 'index',
                                    intersect: false
                                }
                            }
                        });
                    }
                }"
            ></canvas>
        </div>
        <p class="text-xs text-gray-500 mt-2 text-center">Top 10 locations by entry scans (last 30 days)</p>
    </div>
</div>

