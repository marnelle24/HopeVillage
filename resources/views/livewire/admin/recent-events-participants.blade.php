<div class="bg-white p-6 rounded-lg shadow border border-gray-300 mt-6">
    <div class="flex justify-between items-center mb-4 flex-col md:flex-row gap-4">
        <h3 class="text-lg font-semibold text-orange-400">Recent Events Participants</h3>
        <div class="w-full md:w-1/2">
            <select 
                wire:model.live="selectedLocationId"
                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
            >
                <option value="">Select Location</option>
                @foreach($activeLocations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="relative h-[400px] mt-4">
        @if(empty($recentEvents['labels']))
            <div class="flex items-center justify-center h-full">
                <p class="text-gray-500 text-center">No finished events found for this location.</p>
            </div>
        @else
            <canvas 
                wire:key="recent-events-chart-{{ $selectedLocationId }}"
                x-data="{
                    chart: null,
                    init() {
                        const ctx = this.$el.getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: @js($recentEvents['labels']),
                                datasets: [
                                    {
                                        label: 'Registered',
                                        data: @js($recentEvents['registered']),
                                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 2,
                                        borderRadius: 4
                                    },
                                    {
                                        label: 'Attended',
                                        data: @js($recentEvents['attended']),
                                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                        borderColor: 'rgb(16, 185, 129)',
                                        borderWidth: 2,
                                        borderRadius: 4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
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
                                        },
                                        {{-- ticks: {
                                            maxRotation: 90,
                                            minRotation: 90
                                        } --}}
                                    }
                                },
                                interaction: {
                                    mode: 'index',
                                    intersect: false
                                }
                            }
                        });
                    },
                    destroy() {
                        if (this.chart) {
                            this.chart.destroy();
                        }
                    }
                }"
                @destroy.window="destroy()"
            ></canvas>
        @endif
    </div>
    {{-- <p class="text-xs text-gray-500 mt-2 text-center">Last 5 finished events for selected location</p> --}}
</div>
