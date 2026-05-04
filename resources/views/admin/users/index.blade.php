@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Karyawan & Akun</h2>
    <p class="text-sm text-slate-500">Pusat manajemen hak akses pengguna sistem.</p>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
    <!-- Form Tambah User -->
    <div class="lg:col-span-4">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h5 class="font-bold text-slate-800">Tambah Akun Baru</h5>
            </div>
            <div class="p-6">
                @if(session('error'))
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="/users" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required placeholder="Nama lengkap karyawan">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Email Login</label>
                        <input type="email" name="email" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required placeholder="nama@gmail.com">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Password Login</label>
                        <input type="password" name="password" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" minlength="8" required placeholder="Min. 8 karakter">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Tipe Akun (Role)</label>
                        <select name="role" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                            <option value="administrator">Kasir / Administrator</option>
                            <option value="admin">Manajer / Admin Owner</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
                        <i class="bi bi-person-plus text-base"></i> 
                        Simpan Pengguna Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Tabel Daftar User -->
    <div class="lg:col-span-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-white px-6 py-4">
                <h5 class="font-bold text-slate-800">Daftar Pengguna Sistem</h5>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role Akses</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900">{{ $user->name }}</span>
                                    @if(auth()->id() === $user->id)
                                        <span class="rounded-full bg-secondary/10 px-2 py-0.5 text-[10px] font-bold text-secondary">ANDA</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if($user->role === 'admin')
                                    <span class="inline-flex rounded-lg bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">Admin (Manajer)</span>
                                @else
                                    <span class="inline-flex rounded-lg bg-cyan-50 px-2.5 py-1 text-xs font-bold text-cyan-700">Administrator (Kasir)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/users/{{ $user->id }}/edit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-blue-50 hover:text-blue-600">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    @if(auth()->id() !== $user->id)
                                    <form action="/users/{{ $user->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
