<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Tambahkan relasi seekerProfile dan company agar lebih efisien
        $query = User::withCount('applications')->with(['seekerProfile', 'company']);

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Fitur Filter berdasarkan Role (Opsional, sangat berguna di halaman Admin)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10)->appends($request->query());
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,company,seeker',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(), // Bypass verifikasi email jika dibuat oleh Admin
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    // METHOD BARU: Untuk melihat detail profil (Khusus Pelamar / Seeker)
    public function show(User $user)
    {
        if ($user->role === 'seeker') {
            // Load relasi lengkap profil pelamar
            $user->load(['seekerProfile.experiences', 'seekerProfile.educations', 'seekerProfile.skills']);
            return view('admin.users.show', compact('user'));
        } 
        elseif ($user->role === 'company') {
            // Jika yang diklik adalah perusahaan, arahkan ke detail perusahaan
            if ($user->company) {
                return redirect()->route('admin.companies.show', $user->company->id);
            }
            return back()->with('error', 'Perusahaan ini belum mengisi profil perusahaannya.');
        }

        return back()->with('error', 'Tidak ada halaman detail untuk role Admin.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,company,seeker',
            'is_active' => 'nullable',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // 1. Hapus Avatar jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // 2. Hapus CV/Resume jika dia seorang Seeker
        if ($user->seekerProfile && $user->seekerProfile->resume_path) {
            if (Storage::disk('public')->exists($user->seekerProfile->resume_path)) {
                Storage::disk('public')->delete($user->seekerProfile->resume_path);
            }
        }

        // 3. Hapus Logo Perusahaan jika dia seorang Company
        if ($user->company && $user->company->company_logo) {
            if (Storage::disk('public')->exists($user->company->company_logo)) {
                Storage::disk('public')->delete($user->company->company_logo);
            }
        }

        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User beserta seluruh file datanya berhasil dihapus secara permanen.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Status user berhasil $status.");
    }
}