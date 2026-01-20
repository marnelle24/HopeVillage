<div class="card bg-base-100 shadow-lg">
    <div class="card-body">
        <h2 class="card-title text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Location Performance (Last 30 Days)
        </h2>
        <div class="relative h-[350px] mt-4">
            @if(empty($locationData['labels']))
                <div class="flex items-center justify-center h-full">
                    <p class="text-base-content/70">No location data available</p>
                </div>
            @else
                <canvas 
                    x-data="{
                        chart: null,
                        init() {
                            const ctx = this.$el.getContext('2d');
                            this.chart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: @js($locationData['labels']),
                                    datasets: [{
                                        label: 'Activities',
                                        data: @js($locationData['data']),
                                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 2,
                                        borderRadius: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
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
                                    }
                                }
                            });
                        }
                    }"
                ></canvas>
            @endif
        </div>
    </div>
</div>
