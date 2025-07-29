<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Progress Santri
        </h2>
    </x-slot>

    {{-- CSS Khusus --}}
    <style>
        #quran-image-container { position: relative; display: inline-block; touch-action: none; }
        .draggable-box { position: absolute; border: 2px solid red; background-color: rgba(255, 0, 0, 0.2); cursor: move; box-sizing: border-box; }
        .resizer { position: absolute; width: 10px; height: 10px; background-color: blue; border: 1px solid white; border-radius: 50%; }
        .resizer.br { bottom: -5px; right: -5px; cursor: se-resize; }
        .select2-container .select2-selection--single { height: 2.625rem !important; border-color: #d1d5db !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 2.5rem !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 2.5rem !important; }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('progress.store') }}">
                        @csrf
                        <div class="space-y-6">
                            {{-- Dropdown Kelas --}}
                            <div>
                                <x-input-label for="course_id" value="1. Pilih Kelas" />
                                <select name="course_id" id="course_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Salah Satu Kelas --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" data-type="{{ $course->type }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Dropdown Santri --}}
                            <div>
                                <x-input-label for="student_id" value="2. Pilih Santri (Bisa Dicari)" />
                                <select name="student_id" id="student_id" class="block mt-1 w-full" required disabled>
                                    <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                                </select>
                            </div>
                            {{-- Dropdown Materi --}}
                            <div>
                                <x-input-label for="learning_module_id" value="3. Pilih Materi"/>
                                <select name="learning_module_id" id="learning_module_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                                </select>
                            </div>
                            <div id="page_input_wrapper" style="display:none;">
                                <x-input-label for="page_number" value="Setoran di Halaman Nomor"/>
                                <x-text-input id="page_number" name="page_number" type="number" class="mt-1 block w-full"/>
                            </div>
                            <div id="quran_page_wrapper" style="display:none;">
                                <div class="flex justify-between items-center mb-2 flex-wrap gap-2">
                                    <p class="font-semibold">Tampilan Halaman (Bisa ditandai):</p>
                                    <div class="space-x-2">
                                        <button type="button" id="draw-rect-btn" class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300">Tandai Kotak</button>
                                        <button type="button" id="clear-btn" class="px-3 py-1 text-sm bg-red-200 text-red-700 rounded hover:bg-red-300">Hapus Tanda</button>
                                    </div>
                                </div>
                                <div id="quran-image-container" class="border rounded-lg p-2 bg-gray-50">
                                    <img id="quran-image" src="" alt="Halaman Al-Quran" style="max-width: 100%; display: none;">
                                </div>
                            </div>
                            <div>
                                <x-input-label for="assessment" value="Penilaian" />
                                <select name="assessment" id="assessment" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="lulus">Lulus</option><option value="lancar">Lancar</option><option value="mengulang">Mengulang</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="notes" value="Catatan Guru" />
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </div>
                            <input type="hidden" name="annotations" id="annotations_data">
                            <div class="flex items-center justify-end pt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Simpan Progress
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
{{-- Muat jQuery & Select2 dari CDN --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // --- Inisialisasi Select2 ---
    $('#student_id').select2({ placeholder: "-- Pilih Santri --", width: '100%' });

    // --- Definisi Variabel ---
    const courseSelect = $('#course_id');
    const studentSelect = $('#student_id');
    const moduleSelect = $('#learning_module_id');
    const pageInputWrapper = $('#page_input_wrapper');
    const quranPageWrapper = $('#quran_page_wrapper');
    const pageNumberInput = $('#page_number');
    const quranImage = $('#quran-image');
    const imageContainer = $('#quran-image-container');
    const drawBtn = $('#draw-rect-btn');
    const clearBtn = $('#clear-btn');
    const form = $('form');
    const annotationsInput = $('#annotations_data');

    // --- Logika Dropdown Dinamis ---
    courseSelect.on('change', function () {
        const courseId = $(this).val();
        const courseType = $(this).find('option:selected').data('type');
        
        studentSelect.val(null).trigger('change').empty().append('<option value="">Memuat...</option>').prop('disabled', true);
        moduleSelect.empty().append('<option value="">Memuat...</option>').prop('disabled', true);

        if (courseId) {
            const fetchStudents = fetch(`/courses/${courseId}/students`).then(res => res.json());
            const fetchModules = fetch(`/modules/${courseId}`).then(res => res.json());

            Promise.all([fetchStudents, fetchModules]).then(([students, modules]) => {
                studentSelect.empty().append('<option value=""></option>').prop('disabled', students.length === 0);
                if (students.length > 0) students.forEach(s => studentSelect.append(new Option(s.name, s.id, false, false)));
                else studentSelect.append('<option value="">-- Tidak ada santri --</option>');
                studentSelect.trigger('change');

                moduleSelect.empty().append('<option value="">-- Pilih Materi --</option>').prop('disabled', modules.length === 0);
                if (modules.length > 0) modules.forEach(m => moduleSelect.append(new Option(m.module_name, m.id, false, false)));
                else moduleSelect.append('<option value="">-- Tidak ada materi --</option>');
            });
        }
        
        if (courseType === 'bacaan_quran') pageInputWrapper.show();
        else { pageInputWrapper.hide(); quranPageWrapper.hide(); }
    });

    // --- Logika Anotasi Gambar ---
    pageNumberInput.on('input', function() {
        const pageNum = $(this).val();
        if (pageNum && pageNum > 0) {
            quranPageWrapper.show();
            quranImage.show().attr('src', `/quran/${pageNum}.png`);
        } else {
            quranPageWrapper.hide();
            quranImage.hide();
        }
    });

    drawBtn.on('click', function() {
        const box = $('<div class="draggable-box"><div class="resizer br"></div></div>');
        box.css({ left: '50px', top: '50px' });
        imageContainer.append(box);
        makeInteractive(box[0]);
    });

    clearBtn.on('click', function() {
        imageContainer.find('.draggable-box').remove();
    });

    form.on('submit', function() {
        const annotations = [];
        imageContainer.find('.draggable-box').each(function() {
            annotations.push({
                x: this.offsetLeft, y: this.offsetTop,
                width: this.offsetWidth, height: this.offsetHeight,
            });
        });

        // [DEBUGGING] Tambahkan console.log di sini
        const saveData = {
            originalWidth: quranImage.width(),
            originalHeight: quranImage.height(),
            annotations: annotations
        };
        
        console.log("DATA YANG AKAN DISIMPAN:", saveData); 

        annotationsInput.val(JSON.stringify(saveData));
    });

    function makeInteractive(box) {
        const resizer = box.querySelector('.resizer');
        let isDragging = false, isResizing = false;
        let initialX, initialY, xOffset, yOffset, initialWidth, initialHeight;
        
        function getCoords(e) {
            return e.touches ? { x: e.touches[0].clientX, y: e.touches[0].clientY } : { x: e.clientX, y: e.clientY };
        }
        
        function dragStart(e) {
            e.preventDefault();
            e.stopPropagation();
            initialX = getCoords(e).x;
            initialY = getCoords(e).y;
            if (e.target === resizer) {
                isResizing = true;
                initialWidth = box.offsetWidth;
                initialHeight = box.offsetHeight;
            } else {
                isDragging = true;
                xOffset = initialX - box.offsetLeft;
                yOffset = initialY - box.offsetTop;
            }
        }
        
        function drag(e) {
            if (!isDragging && !isResizing) return;
            e.preventDefault();
            if (isDragging) {
                box.style.left = (getCoords(e).x - xOffset) + 'px';
                box.style.top = (getCoords(e).y - yOffset) + 'px';
            }
            if (isResizing) {
                const newWidth = initialWidth + (getCoords(e).x - initialX);
                const newHeight = initialHeight + (getCoords(e).y - initialY);
                if (newWidth > 20) box.style.width = newWidth + 'px';
                if (newHeight > 20) box.style.height = newHeight + 'px';
            }
        }
        
        function dragEnd(e) {
            isDragging = false;
            isResizing = false;
        }

        box.addEventListener('mousedown', dragStart);
        box.addEventListener('touchstart', dragStart, { passive: false });
        resizer.addEventListener('mousedown', dragStart);
        resizer.addEventListener('touchstart', dragStart, { passive: false });
        document.addEventListener('mousemove', drag);
        document.addEventListener('touchmove', drag, { passive: false });
        document.addEventListener('mouseup', dragEnd);
        document.addEventListener('touchend', dragEnd);
    }
});
</script>
@endpush
</x-app-layout>