<div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Merchant Redemption Summary</h3>
    
    @if(count($redemptionData['merchants']) > 0)
        <!-- Bar Chart -->
        <div class="my-10">
            <div class="relative h-96">
                <canvas 
                    x-data="{
                        chart: null,
                        init() {
                            const ctx = this.$el.getContext('2d');
                            this.chart = new Chart(ctx, {
                                type: 'bar',
                                data: {
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
                                    labels: @js($redemptionData['labels']),
                                    datasets: [{
                                        label: 'Redemptions',
                                        data: @js($redemptionData['data']),
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
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                            callbacks: {
                                                label: function(context) {
                                                    return context.parsed.y + ' redemption' + (context.parsed.y !== 1 ? 's' : '');
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            min: 0,
                                            max: Math.max(5, Math.max(...@js($redemptionData['data'])) || 5),
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
                                            offset: true,
                                            min: 0,
                                            max: Math.max(5, Math.max(...@js($redemptionData['labels'])) || 5),
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
        </div>

        <!-- Summary List -->
        <div class="flex flex-wrap gap-3">
            @foreach($redemptionData['merchants'] as $merchant)
                <div class="inline-flex items-start gap-2 p-3 bg-slate-100 rounded-lg border border-gray-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-indigo-600 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $merchant['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $merchant['count'] }} redemption{{ $merchant['count'] !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <p class="text-sm">No redemptions recorded yet.</p>
        </div>
    @endif
</div>
