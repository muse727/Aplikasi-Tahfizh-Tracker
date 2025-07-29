<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    {{-- CSS Khusus untuk Anotasi --}}
    <style>
        .annotation-container { position: relative; display: inline-block; line-height: 0; }
        .annotation-box { position: absolute; border: 2px solid rgba(239, 68, 68, 0.8); background-color: rgba(239, 68, 68, 0.2); box-sizing: border-box; }
    </style>

    {{-- x-data untuk mengontrol modal (popup) dengan Alpine.js --}}
    <div class="py-12" x-data="{ openModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Tampilan untuk Guru atau Admin (TIDAK DIUBAH) --}}
            @if(in_array(Auth::user()->role, ['admin', 'guru']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        Selamat datang, {{ Auth::user()->name }}! Silakan gunakan menu navigasi di atas.
                    </div>
                </div>
            @else

            {{-- Tampilan untuk Siswa --}}
            @if($activeCourse)
                {{-- Banner Sambutan & Kelas Aktif (TIDAK DIUBAH) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 items-center">
                    <div class="md:col-span-2 relative overflow-visible">
                        <div class="relative bg-gradient-to-r from-emerald-500 to-green-600 p-8 rounded-2xl shadow-lg text-white">
                            <div class="relative z-10">
                                <h3 class="text-3xl font-bold mb-1">Assalamu'alaikum, {{ $user->name }}!</h3>
                                <p class="text-green-100">Ini adalah ringkasan progress mengajimu.</p>
                            </div>
                        </div>
                        <img src="/assets/images/anak islami.png" alt="Anak menyambut" class="absolute -top-16 right-6 w-10 md:w-40 lg:w-48 z-30 drop-shadow-xl">
                    </div>
                    <div class="bg-white p-6 text-center rounded-2xl shadow-sm border border-emerald-100">
                        <p class="text-sm font-medium text-gray-500">Kelas Aktif</p>
                        <p class="mt-1 text-xl font-bold text-emerald-600 truncate">{{ $activeCourse->name }}</p>
                        <div class="mt-4">
                            <img src="/assets/images/quran.svg" alt="Quran Icon" class="mx-auto w-10 opacity-80">
                        </div>
                    </div>
                </div>

                {{-- Papan Peringkat dan Riwayat --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    {{-- Kolom Kiri: Papan Peringkat (TIDAK DIUBAH) --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Papan Peringkat Kelas</h3>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-4 space-y-4">
                            @forelse ($leaderboard as $rank => $student)
                                <div class="flex items-center space-x-4 p-2 rounded-lg @if(Auth::id() == $student->id) bg-emerald-50 @endif">
                                    <span class="text-lg font-bold w-8 text-center {{ $rank < 3 ? 'text-emerald-500' : 'text-gray-400' }}">{{ $rank + 1 }}</span>
                                    <img class="w-10 h-10 rounded-full" src="https://avatar.iran.liara.run/public/boy" alt="avatar">
                                    <div class="flex-1 font-medium text-sm">{{ $student->name }}</div>
                                    <div class="inline-flex items-center text-sm font-semibold text-gray-900">
                                        {{ $student->total_score ?? 0 }} {{ Str::contains($activeCourse->name, 'Tahfizh') ? 'Ayat' : 'Poin' }}
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-4">Belum ada data peringkat.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Kolom Kanan: Riwayat Progress & Statistik --}}
                    <div class="space-y-8">
                        <div class="mt-12 lg:mt-0">
                            <h3 class="px-3 sm:px-0 text-lg font-semibold mb-4 text-gray-800">Riwayat Progress Terakhir</h3>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl">
                                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                                    <table class="min-w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materi</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Penilaian</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse ($latestProgress as $record)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->record_date->format('d M Y') }}</td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm font-medium text-gray-800">
                                                            {{-- [DIPERBAIKI] Logika Ziyadah hanya untuk kelas Tahfizh --}}
                                                            @if(Str::contains($record->learningModule->course->name, 'Tahfizh'))
                                                                @if($record->submission_type == 'ziyadah') Ziyadah
                                                                @elseif($record->submission_type == 'murojaah') Murojaah
                                                                @endif
                                                            @endif
                                                            {{ $record->learningModule->module_name ?? 'N/A' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 italic">"{{ $record->notes ?? 'Tidak ada catatan' }}"</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                        @if($record->progress_count > 0)
                                                            {{ $record->progress_count }} {{ Str::contains($record->learningModule->course->name, 'Tahfizh') ? 'Ayat' : 'Poin' }}
                                                        @else - @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                                        @if($record->annotations && $record->page_number)
                                                            <button 
                                                                type="button" 
                                                                class="view-annotation-btn font-medium text-blue-600 hover:text-blue-800"
                                                                @click="openModal = true"
                                                                data-page="{{ $record->page_number }}"
                                                                data-annotations="{{ json_encode($record->annotations) }}">
                                                                Lihat Tanda
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full @if(in_array($record->assessment, ['lulus', 'lancar'])) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                            {{ ucfirst($record->assessment) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">Statistik Peringkat</h3>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6">
                                <canvas id="leaderboardChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Tampilan jika siswa belum punya kelas --}}
            @endif
            @endif
        </div>

        {{-- Modal (Popup) untuk Anotasi --}}
        <div x-show="openModal" @keydown.escape.window="openModal = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" x-cloak>
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full p-4" @click.outside="openModal = false">
                <div class="flex justify-between items-center border-b pb-2 mb-4">
                    <h3 class="text-lg font-semibold" id="modal-title">Tanda dari Guru</h3>
                    <button @click="openModal = false" class="text-2xl leading-none text-gray-500 hover:text-gray-800">&times;</button>
                </div>
                <div class="overflow-auto max-h-[70vh]">
                    <div id="modal-annotation-container" class="annotation-container">
                        <img id="modal-quran-image" src="" alt="Halaman Al-Quran" style="max-width: 100%;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Chart --}}
    @if(Auth::user()->role == 'siswa' && isset($chartData))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const chartData = @json($chartData);
            const ctx = document.getElementById('leaderboardChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Total Poin / Ayat',
                            data: chartData.data,
                            backgroundColor: ['rgba(22, 163, 74, 0.6)', 'rgba(251, 191, 36, 0.6)', 'rgba(249, 115, 22, 0.6)', 'rgba(59, 130, 246, 0.6)', 'rgba(139, 92, 246, 0.6)'],
                            borderColor: ['rgba(22, 163, 74, 1)','rgba(251, 191, 36, 1)','rgba(249, 115, 22, 1)','rgba(59, 130, 246, 1)','rgba(139, 92, 246, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
                });
            }
        </script>
    @endif
    
    {{-- Script untuk Modal Anotasi --}}
   @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalImage = document.getElementById('modal-quran-image');
    const modalContainer = document.getElementById('modal-annotation-container');
    const modalTitle = document.getElementById('modal-title');

    document.querySelectorAll('.view-annotation-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.clear(); // Bersihkan console setiap kali tombol diklik
            console.log("--- MEMULAI PROSES ANOTASI DI DASHBOARD ---");

            const pageNum = this.dataset.page;
            const annotationsJSON = this.dataset.annotations;
            const imageSrc = `/quran/${pageNum}.png`;
            
            modalTitle.innerText = `Memuat Halaman ${pageNum}...`;
            modalImage.style.display = 'none';
            modalContainer.querySelectorAll('.annotation-box').forEach(box => box.remove());

            const tempImg = new Image();
            tempImg.src = imageSrc;
            tempImg.onload = () => {
                modalImage.src = tempImg.src;
                modalImage.style.display = 'block';
                modalTitle.innerText = `Tanda dari Guru - Halaman ${pageNum}`;
                
                // Beri sedikit waktu agar gambar render sempurna sebelum menggambar
                setTimeout(() => {
                    try {
                        const saveData = JSON.parse(annotationsJSON || '{}');
                        console.log("1. DATA DITERIMA:", saveData);

                        const parsedAnnotations = saveData.annotations || [];
                        const originalWidth = saveData.originalWidth;
                        const displayedWidth = modalImage.offsetWidth;
                        
                        console.log("2. Lebar Asli (dari form input):", originalWidth);
                        console.log("3. Lebar Tampil (di modal):", displayedWidth);

                        if (!originalWidth) {
                            console.error("KESALAHAN: Lebar asli tidak tersimpan!");
                            return;
                        }

                        const scale = displayedWidth / originalWidth;
                        console.log("4. Faktor Skala:", scale);

                        if (parsedAnnotations.length === 0) {
                            console.log("5. Info: Tidak ada data anotasi untuk digambar.");
                        }

                        parsedAnnotations.forEach((anno, index) => {
                            const box = document.createElement('div');
                            box.className = 'annotation-box';
                            
                            const left = (anno.x * scale);
                            const top = (anno.y * scale);
                            const width = (anno.width * scale);
                            const height = (anno.height * scale);

                            box.style.left = left + 'px';
                            box.style.top = top + 'px';
                            box.style.width = width + 'px';
                            box.style.height = height + 'px';
                            
                            modalContainer.appendChild(box);
                        });
                        console.log("6. Selesai menggambar.");

                    } catch (e) {
                        console.error("ERROR FATAL SAAT MENGGAMBAR:", e);
                    }
                }, 100);
            };
            tempImg.onerror = () => { console.error(`Gagal Memuat Gambar: ${imageSrc}`); };
        });
    });
});
</script>
@endpush
</x-app-layout>