@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black tracking-tight text-primary">Edit Data Supplier</h2>
        <p class="mt-1 text-sm font-medium text-slate-500">Perbarui informasi detail untuk pemasok/vendor.</p>
    </div>
    <a href="/suppliers" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-primary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
</div>

<!-- Premium Split Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 pb-12">
    
    <!-- Left Column: Context / Info -->
    <div class="lg:col-span-4 space-y-6">
        <div class="rounded-3xl bg-primary p-8 text-white">
            
            <div class="relative z-10">
                <h3 class="text-xl font-black mb-2">{{ $supplier->name }}</h3>
                <p class="text-sm text-white/80 leading-relaxed mb-6">
                    Pastikan informasi kontak dan alamat selalu terbarui untuk mempermudah proses pemesanan barang dan logistik gudang.
                </p>

                <div class="space-y-4 pt-6 border-t border-white/20">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-info-circle text-white/60 mt-0.5"></i>
                        <p class="text-xs text-white/70">
                            Data supplier ini tertaut dengan <span class="font-bold text-white">{{ $supplier->products_count ?? 0 }} barang</span> <br> 
                            di gudang.
                        </p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="bi bi-clock-history text-white/60 mt-0.5"></i>
                        <p class="text-xs text-white/70">
                            Terakhir diperbarui:<br>
                            <span class="font-bold text-white">{{ $supplier->updated_at->format('d M Y, H:i') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: The Form -->
    <div class="lg:col-span-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 shadow-sm relative">
            
            @if($errors->any())
                <div class="mb-8 rounded-2xl border border-alert/20 bg-alert/5 p-5 animate-shake">
                    <div class="flex items-start gap-4">
                        <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-alert/10 text-alert">
                            <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-alert">Pembaruan Gagal Disimpan</h4>
                            <ul class="mt-2 space-y-1 text-sm text-alert/80">
                                @foreach($errors->all() as $error)
                                    <li class="flex items-center gap-2">
                                        <i class="bi bi-dot"></i> {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="/suppliers/{{ $supplier->id }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Perusahaan -->
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-bold tracking-widest text-slate-500">
                            Nama Perusahaan <span class="text-alert">*</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <i class="bi bi-shop"></i>
                            </div>
                            <input type="text" name="name" value="{{ old('name', $supplier->name) }}" 
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-semibold text-slate-900 transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" 
                                required placeholder="Contoh: PT. Sumber Makmur Abadi">
                        </div>
                    </div>
                    
                    <!-- Nama Kontak / PIC -->
                    <div>
                        <label class="mb-2 block text-xs font-bold tracking-widest text-slate-500">
                            Nama PIC / Kontak
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" 
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-semibold text-slate-900 transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" 
                                placeholder="Nama perwakilan">
                        </div>
                    </div>
                    
                    <!-- Nomor Telepon -->
                    <div>
                        <label class="mb-2 block text-xs font-bold tracking-widest text-slate-500">
                            Telepon / WhatsApp
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" 
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-semibold text-slate-900 transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" 
                                placeholder="0812-xxxx-xxxx">
                        </div>
                    </div>
                    
                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-bold tracking-widest text-slate-500">
                            Alamat Lengkap
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute top-4 left-0 flex items-center pl-4 text-slate-400">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <textarea name="address" rows="4" 
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-semibold text-slate-900 transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" 
                                placeholder="Tuliskan alamat lengkap kantor atau gudang supplier...">{{ old('address', $supplier->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="/suppliers" class="inline-flex items-center justify-center px-6 py-3.5 rounded-2xl bg-slate-100 text-sm font-bold text-slate-600 transition hover:bg-slate-200">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-3 rounded-2xl bg-secondary px-8 py-3.5 text-sm font-black tracking-widest text-white shadow-xl shadow-secondary/30 transition-all hover:bg-primary active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake {
        animation: shake 0.4s ease-in-out;
    }
</style>
@endsection
