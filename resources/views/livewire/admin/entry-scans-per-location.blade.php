<div class="bg-white p-6 rounded-lg shadow border border-gray-300 mt-6">
    <h3 class="text-lg font-semibold text-orange-400 mb-4">Activity Types Over Time</h3>
    <div class="relative h-[400px] mt-4">
        @if(empty($activityTypesData['datasets']))
            <div class="flex items-center justify-center h-full">
                <p class="text-gray-500 text-center">No activity data found for the last 30 days.</p>
            </div>
        @else
            <canvas 
                x-data="{
                    chart: null,
                    init() {
                        const ctx = this.$el.getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @js($activityTypesData['labels']),
                                datasets: @js($activityTypesData['datasets'])
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
                                            stepSize: 10,
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
                                    mode: 'nearest',
                                    axis: 'x',
                                    intersect: false
                                }
                            }
                        });
                    }
                }"
            ></canvas>
        @endif
    </div>
    <p class="text-xs text-gray-500 mt-2 text-center">Activity types grouped by day (last 30 days)</p>
</div>
