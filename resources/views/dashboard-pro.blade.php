<x-app-layout>
    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Banner Sambutan --}}
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 rounded-2xl shadow-lg p-8 mb-8 flex items-center justify-between overflow-hidden">
            <div>
                <h3 class="text-2xl md:text-3xl font-bold text-white">Assalamu'alaikum, {{ Auth::user()->name }}!</h3>
                <p class="text-green-100 mt-1">Semoga hari ini penuh berkah.</p>
            </div>
            <div class="relative z-10 hidden sm:block">
                <img src="https://i.imgur.com/KpdD35f.png" alt="Ilustrasi Anak Mengaji" class="h-36 w-auto -mb-8 -mr-4">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM UTAMA (KIRI) --}}
            <div class="lg:col-span-2 space-y-8">
                @if(Auth::user()->role == 'admin')
                    <div class="bg-white p-6 rounded-2xl shadow-sm border">
                         <h3 class="text-lg font-semibold mb-4 text-gray-800">Daftar Guru & Jumlah Bimbingan</h3>
                         <div class="space-y-4">
                            @forelse($teachers as $teacher)
                                <div class="flex items-center space-x-4 p-2">
                                    <img class="w-10 h-10 rounded-full" src="https://api.dicebear.com/8.x/adventurer-neutral/svg?seed={{ urlencode($teacher->name) }}" alt="avatar">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $teacher->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $teacher->email }}</p>
                                    </div>
                                    <span class="text-sm text-gray-600 font-medium">{{ $teacher->students_as_teacher_count }} Santri</span>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-4">Belum ada data guru.</p>
                            @endforelse
                         </div>
                    </div>
                @endif

                @if(Auth::user()->role == 'guru')
                     <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Aktivitas Terakhir Santri Bimbingan</h3>
                        <div class="bg-white p-4 rounded-2xl shadow-sm space-y-4 border">
                            @forelse($recentActivities as $activity)
                                <div class="flex items-center space-x-3">
                                    <img class="w-10 h-10 rounded-full" src="https://api.dicebear.com/8.x/initials/svg?seed={{ urlencode($activity->student->name) }}" alt="avatar">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            <span class="font-bold">{{ $activity->student->name }}</span> menyelesaikan materi
                                            <span class="text-emerald-600">{{ $activity->learningModule->module_name }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas.</p>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>

            {{-- KOLOM KANAN (SIDEBAR KONTEN) --}}
            <div class="lg:col-span-1 space-y-8">
                @if(Auth::user()->role == 'guru')
                    <div>
                         <h3 class="text-lg font-semibold mb-4 text-gray-800">Santri Bimbingan Anda</h3>
                         <div class="bg-white p-4 rounded-2xl shadow-sm space-y-2 border">
                            @forelse($myStudents as $student)
                                <a href="{{ route('students.show', $student->id) }}" class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <img class="w-10 h-10 rounded-full" src="https://api.dicebear.com/8.x/adventurer-neutral/svg?seed={{ urlencode($student->name) }}" alt="avatar">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-emerald-600">&rarr;</span>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">Belum ada santri bimbingan.</p>
                            @endforelse
                         </div>
                    </div>
                @endif

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Aktivitas 7 Hari Terakhir</h3>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border">
                        <canvas id="activityChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('activityChart');
            if (ctx) { 
                const chartLabels = @json($chartLabels ?? []);
                const chartData = @json($chartData ?? []);
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Jumlah Progress per Hari',
                            data: chartData,
                            fill: true,
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: 'rgba(5, 150, 105, 1)',
                            tension: 0.3
                        }]
                    },
                    options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, responsive: true, plugins: { legend: { display: false } } }
                });
            }
        });
    </script>
</x-app-layout>