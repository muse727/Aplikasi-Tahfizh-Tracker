<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Setoran Hafalan (Tahfizh)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                            <p class="font-bold">Sukses</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tahfizh.store') }}">
                        @csrf
                        <div class="space-y-6">
                            <!-- Pilih Siswa -->
                            <div>
                                <x-input-label for="student_id" value="Pilih Santri" />
                                <select name="student_id" id="student_id" class="block mt-1 w-full ..." required>
                                    <option value="">-- Pilih salah satu santri --</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jenis Setoran -->
                            <div>
                                <x-input-label value="Jenis Setoran" />
                                <div class="mt-2 flex items-center space-x-6">
                                    <label class="flex items-center">
                                        <input type="radio" name="submission_type" value="ziyadah" class="text-emerald-600 focus:ring-emerald-500" checked>
                                        <span class="ml-2">Ziyadah (Hafalan Baru)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="submission_type" value="murojaah" class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2">Murojaah (Ulang Hafalan)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Pilih Surah -->
                            <div>
                                <x-input-label for="learning_module_id" value="Pilih Surah" />
                                <select name="learning_module_id" id="learning_module_id" class="block mt-1 w-full ..." required>
                                    @foreach ($tahfizhModules as $module)
                                        <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jumlah Ayat yang Disetor -->
                            <div>
                                <x-input-label for="progress_count" value="Jumlah Ayat yang Disetor" />
                                <x-text-input id="progress_count" name="progress_count" type="number" class="mt-1 block w-full" placeholder="Hanya diisi untuk Ziyadah" />
                            </div>
                            
                            <!-- Penilaian -->
                            <div>
                                <x-input-label for="assessment" value="Penilaian" />
                                <select name="assessment" id="assessment" class="block mt-1 w-full ..." required>
                                    <option value="lancar">Lancar</option>
                                    <option value="tidak lancar">Tidak Lancar</option>
                                </select>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <x-input-label for="notes" value="Catatan (Contoh: Setoran dari ayat 1-10)" />
                                <textarea name="notes" id="notes" class="block mt-1 w-full ..." rows="3"></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>Simpan Setoran</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
