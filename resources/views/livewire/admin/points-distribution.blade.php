<div class="card bg-white shadow border border-gray-300">
    <div class="card-body">
        <div class="card-title flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="size-6 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h2 class="text-gray-800 flex flex-col">
                <span class="text-gray-800 text-lg font-bold">Points Distribution</span>
                <span class="text-gray-500 text-sm italic">Last 30 Days</span>
            </h2>
        </div>
        <div class="relative h-[280px] mt-4">
            @if(empty($pointsData['labels']))
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-600">No points data available</p>
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
