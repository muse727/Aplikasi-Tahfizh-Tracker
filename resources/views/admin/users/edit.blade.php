<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit User: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama -->
                            <div>
                                <x-input-label for="name" :value="__('Nama')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            </div>

                            <!-- Role -->
                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <select name="role" id="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="siswa" @selected(old('role', $user->role) == 'siswa')>Siswa</option>
                                    <option value="guru" @selected(old('role', $user->role) == 'guru')>Guru</option>
                                    <option value="admin" @selected(old('role', $user->role) == 'admin')>Admin</option>
                                </select>
                            </div>

                            <!-- Kelas Aktif -->
                            <div>
                                <x-input-label for="current_course_id" :value="__('Kelas Aktif')" />
                                <select name="current_course_id" id="current_course_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Tidak ada kelas --</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" @selected(old('current_course_id', $user->current_course_id) == $course->id)>{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Guru Pembimbing -->
                            <div>
                                <x-input-label for="teacher_id" :value="__('Guru Pembimbing')" />
                                <select name="teacher_id" id="teacher_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Tidak ada guru --</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" @selected(old('teacher_id', $user->teacher_id) == $teacher->id)>{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Password Baru -->
                            <div>
                                <x-input-label for="password" :value="__('Password Baru (Opsional)')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                <small class="text-gray-500">Kosongkan jika tidak ingin mengubah password.</small>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>Update User</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
