@extends('layouts.app')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="/users" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition hover:bg-slate-50 hover:text-slate-600">
        <i class="bi bi-arrow-left text-lg"></i>
    </a>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Edit Akun Pengguna</h2>
        <p class="text-sm text-slate-500">Ubah informasi profil atau role akses pengguna.</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
    <div class="lg:col-span-6">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/users/{{ $user->id }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Password Baru (Kosongkan jika tidak ganti)</label>
                        <input type="password" name="password" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Role / Hak Akses</label>
                        <select name="role" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Manajer)</option>
                            <option value="administrator" {{ old('role', $user->role) === 'administrator' ? 'selected' : '' }}>Administrator (Kasir)</option>
                        </select>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-6">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-secondary px-8 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
                            <i class="bi bi-save"></i> 
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
