<div class="card bg-base-100 shadow-lg">
    <div class="card-body">
        <h2 class="card-title text-warning">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Points Distribution (Last 30 Days)
        </h2>
        <div class="relative h-[280px] mt-4">
            @if(empty($pointsData['labels']))
                <div class="flex items-center justify-center h-full">
                    <p class="text-base-content/70">No points data available</p>
                </div>
            @else
                <canvas 
                    x-data="{
                        chart: null,
                        init() {
                            const ctx = this.$el.getContext('2d');
                            this.chart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: @js($pointsData['labels']),
                                    datasets: [{
                                        data: @js($pointsData['data']),
                                        backgroundColor: [
                                            'rgba(59, 130, 246, 0.8)',
                                            'rgba(16, 185, 129, 0.8)',
                                            'rgba(245, 158, 11, 0.8)',
                                            'rgba(239, 68, 68, 0.8)',
                                            'rgba(139, 92, 246, 0.8)',
                                            'rgba(236, 72, 153, 0.8)',
                                            'rgba(20, 184, 166, 0.8)',
                                            'rgba(251, 146, 60, 0.8)',
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '60%',
                                    plugins: {
                                        legend: {
                                            display: false
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
