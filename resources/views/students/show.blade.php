<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan Detail Santri: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Pesan Sukses/Gagal --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                 <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- KOLOM KIRI: INFO & AKSI (TIDAK DIUBAH) --}}
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center space-x-4 mb-4">
                            <img class="w-16 h-16 rounded-full" src="https://api.dicebear.com/8.x/adventurer-neutral/svg?seed={{ urlencode($user->name) }}" alt="avatar">
                            <div>
                                <h3 class="font-bold text-lg">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Kelas Aktif:</strong> {{ $user->currentCourse->name ?? 'Belum ada' }}</p>
                            <p><strong>Guru Pembimbing:</strong> {{ $user->teacher->name ?? 'Belum diatur' }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="font-semibold text-lg mb-4">Pindahkan ke Kelas Lain</h3>
                        <form action="{{ route('students.moveClass', $user->id) }}" method="POST">
                            @csrf
                            <div>
                                <label for="new_course_id" class="block text-sm font-medium text-gray-700">Pilih Kelas Baru</label>
                                <select name="new_course_id" id="new_course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-4">
                                <x-primary-button type="submit">Pindahkan</x-primary-button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="font-semibold text-lg mb-4">Cetak Laporan Bulanan</h3>
                        <form action="{{ route('students.report.pdf', $user->id) }}" method="GET" target="_blank">
                            <div class="space-y-4">
                                <div>
                                    <label for="month" class="block text-sm font-medium text-gray-700">Bulan</label>
                                    <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        @for ($m=1; $m<=12; $m++)
                                            <option value="{{ $m }}" {{ $m == date('m') ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m, 1, date('Y'))) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700">Tahun</label>
                                    <input type="number" name="year" id="year" value="{{ date('Y') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <x-primary-button type="submit">Cetak PDF</x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- KOLOM KANAN: RIWAYAT PROGRESS (TAMPILAN KARTU) --}}
                <div class="md:col-span-2 space-y-4">
                    <h3 class="text-xl font-semibold text-gray-700">Riwayat Progress Terbaru</h3>
                    @forelse ($progressRecords as $record)
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 @if(in_array($record->assessment, ['lulus', 'lancar'])) border-green-500 @else border-red-500 @endif">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">{{ $record->record_date->format('d F Y') }}</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if(in_array($record->assessment, ['lulus', 'lancar'])) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($record->assessment) }}
                                </span>
                            </div>
                            <div class="mt-2">
                                <p class="text-lg font-bold text-gray-800">{{ $record->learningModule->module_name ?? 'Materi Dihapus' }}</p>
                                <p class="text-sm text-gray-600">{{ $record->learningModule->course->name ?? 'Kelas Dihapus' }}</p>
                                @if($record->notes)
                                    <p class="mt-2 text-sm text-gray-700 bg-gray-50 p-2 rounded-md"><strong>Catatan Guru:</strong> {{ $record->notes }}</p>
                                @endif
                            </div>

                            {{-- [DIHAPUS] Bagian untuk menampilkan gambar dan anotasi sudah dihapus dari sini --}}

                        </div>
                    @empty
                        <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">
                            <p>Belum ada progress yang dicatat.</p>
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $progressRecords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- [DIHAPUS] Blok @push('scripts') untuk menggambar anotasi juga sudah dihapus --}}
</x-app-layout>