<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     */
    public function index()
    {
        // Ambil data user beserta relasi kelas dan guru pembimbingnya
        $users = User::with('currentCourse', 'teacher')->latest()->paginate(10); 
        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk menambah user baru.
     */
    public function create()
    {
        // Ambil data kelas dan guru untuk mengisi dropdown
        $courses = Course::orderBy('name')->get();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        return view('admin.users.create', compact('courses', 'teachers'));
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:admin,guru,siswa'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'current_course_id' => ['nullable', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'current_course_id' => $request->current_course_id,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        // Siapkan data kelas dan guru untuk form edit
        $courses = Course::orderBy('name')->get();
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'courses', 'teachers'));
    }

    /**
     * Mengupdate data user di database.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,guru,siswa'],
            'password' => ['nullable', 'string', 'min:8'],
            'current_course_id' => ['nullable', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->current_course_id = $request->current_course_id;
        $user->teacher_id = $request->teacher_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil di-update.');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        if ($user->id == auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak bisa menghapus diri sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
