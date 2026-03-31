<x-app-layout>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-700">Финансиска аналитика</h2>
                <p class="text-md text-gray-600">Преглед на перформанси</p>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">


                <form action="{{ route('dashboard') }}" method="GET"
                    class="flex gap-2 bg-white p-2 rounded-xl shadow-sm border border-gray-200">
                    <select name="year" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 text-sm focus:ring-blue-500">
                        <option value="all" {{ ($selectedYear ?? 'all') == 'all' ? 'selected' : '' }}>Сите години</option>
                        @foreach($availableYears ?? [] as $year)
                            <option value="{{ $year }}" {{ ($selectedYear ?? '') == $year ? 'selected' : '' }}>{{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <select name="quarter" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 text-sm focus:ring-blue-500">
                        <option value="">Цела година</option>
                        <option value="1" {{ ($selectedQuarter ?? '') == '1' ? 'selected' : '' }}>Q1 (Јан-Мар)</option>
                        <option value="2" {{ ($selectedQuarter ?? '') == '2' ? 'selected' : '' }}>Q2 (Апр-Јун)</option>
                        <option value="3" {{ ($selectedQuarter ?? '') == '3' ? 'selected' : '' }}>Q3 (Јул-Сеп)</option>
                        <option value="4" {{ ($selectedQuarter ?? '') == '4' ? 'selected' : '' }}>Q4 (Окт-Дек)</option>
                    </select>
                </form>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Вкупно приходи</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ number_format($totalEarnings ?? 0, 0, ',', '.') }}
                        <span class="text-sm font-normal text-gray-400">ден.</span>
                    </h3>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round">
                        {{-- Lucide: Dollar Sign --}}
                        <line x1="12" x2="12" y1="2" y2="22"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Раст / Тренд</p>
                    <h3 class="text-2xl font-black {{ ($growthTotal ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ ($growthTotal ?? 0) >= 0 ? '+' : '' }}{{ number_format($growthTotal ?? 0, 1) }}%
                    </h3>
                </div>
                <div
                    class="p-3 {{ ($growthTotal ?? 0) >= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round">
                        @if(($growthTotal ?? 0) >= 0)
                            {{-- Lucide: Trending Up --}}
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        @else
                            {{-- Lucide: Trending Down --}}
                            <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                            <polyline points="17 18 23 18 23 12"></polyline>
                        @endif
                    </svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Студенти</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $activeStudents ?? 0 }}</h3>
                </div>
                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round">
                        {{-- Lucide: Users --}}
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-400 uppercase mb-6 tracking-widest">Месечна Рентабилност</h3>
                <div style="height: 350px;"><canvas id="bizChart"></canvas></div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <h3 class="text-sm font-bold text-gray-400 uppercase mb-6 tracking-widest text-center">Спецификација по
                    услуги</h3>
                <div class="relative mb-6" style="height: 220px;"><canvas id="servicesPieChart"></canvas></div>
                <div class="flex-1 overflow-y-auto pr-2" style="max-height: 180px;">
                    @foreach($serviceStats ?? [] as $service => $stat)
                        <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full flex-shrink-0 pie-indicator-ball"
                                    data-index="{{ $loop->index }}"></span>
                                <span class="text-xs font-medium text-gray-600 truncate max-w-[120px]">{{ $service }}</span>
                            </div>
                            <span class="text-xs font-bold text-gray-800">{{ number_format($stat['sum'], 0, ',', '.') }}
                                д.</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.addEventListener('load', function () {
            const colors = ['#2563eb', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4', '#f43f5e', '#64748b'];

            // Line Chart
            const ctxL = document.getElementById('bizChart');
            if (ctxL) {
                new Chart(ctxL, {
                    type: 'line',
                    data: {
                        labels: ['Јан', 'Фев', 'Мар', 'Апр', 'Мај', 'Јун', 'Јул', 'Авг', 'Сеп', 'Окт', 'Ное', 'Дек'],
                        datasets: {!! json_encode($chartDatasets ?? []) !!}
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            // Pie Chart
            const ctxP = document.getElementById('servicesPieChart');
            if (ctxP) {
                new Chart(ctxP, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($pieLabels ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($pieData ?? []) !!},
                            backgroundColor: colors,
                            borderWidth: 4,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
                });
            }

            document.querySelectorAll('.pie-indicator-ball').forEach((el, i) => {
                el.style.backgroundColor = colors[i % colors.length];
            });
        });
    </script>
</x-app-layout>
