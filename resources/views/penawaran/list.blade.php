@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold">List Penawaran</h1>
            <button id="btnTambah"
                class="bg-blue-600 text-white px-4 py-2 rounded flex items-center gap-2 text-sm hover:bg-blue-700 transition">

                Tambah
            </button>
        </div>
        <div class="bg-white shadow rounded-lg">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-blue-100 text-gray-700">
                        <th class="px-2 py-2 font-semibold text-center">ID</th>
                        <th class="px-2 py-2 font-semibold text-center">No Penawaran</th>
                        <th class="px-2 py-2 font-semibold text-center">Perihal</th>
                        <th class="px-2 py-2 font-semibold text-center">Nama Perusahaan</th>
                        <th class="px-2 py-2 font-semibold text-center">PIC Perusahaan</th>
                        <th class="px-2 py-2 font-semibold text-center">PIC Admin</th>
                        <th class="px-2 py-2 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penawarans as $p)
                        <tr class="border-b hover:bg-blue-50 transition">
                            <td class="px-2 py-2 text-center">{{ $p->id_penawaran }}</td>
                            <td class="px-2 py-2 text-center">{{ $p->no_penawaran }}</td>
                            <td class="px-2 py-2">{{ $p->perihal }}</td>
                            <td class="px-2 py-2">{{ $p->nama_perusahaan }}</td>
                            <td class="px-2 py-2">{{ $p->pic_perusahaan }}</td>
                            <td class="px-2 py-2">{{ $p->pic_admin }}</td>
                            <td class="px-2 py-2 text-center">
                                <div class="flex gap-1 justify-center">
                                    @if ($p->tiket)
                                        <button
                                            class="bg-gray-300 text-gray-700 px-2 py-1 rounded flex items-center gap-1 text-xs"
                                            disabled>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-ticket"
                                                width="14" height="14" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M3 7V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2" />
                                                <path d="M21 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7" />
                                                <path d="M7 7v10" />
                                                <path d="M17 7v10" />
                                                <path d="M9 11h6" />
                                            </svg>
                                            Tiket
                                        </button>
                                    @else
                                        <button
                                            class="bg-blue-500 text-white px-2 py-1 rounded flex items-center gap-1 text-xs hover:bg-blue-600 transition">
                                            <x-lucide-square-pen class="w-5 h-5 inline" />
                                            Buat
                                        </button>
                                    @endif
                                    <button class="bg-blue-200 text-blue-700 px-2 py-1 rounded hover:bg-blue-300 transition"
                                        title="Lihat">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-eye" width="14"
                                            height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="3" />
                                            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
                                        </svg>
                                    </button>
                                    <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-x" width="14"
                                            height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8">
                                <div class="flex flex-col items-center justify-center text-gray-500 gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-ticket" width="32"
                                        height="32" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 7V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2" />
                                        <path d="M21 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7" />
                                        <path d="M7 7v10" />
                                        <path d="M17 7v10" />
                                        <path d="M9 11h6" />
                                    </svg>
                                    Belum ada tiket penawaran
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Slide-over Form -->
    <div id="formSlide" class="fixed inset-0 bg-black bg-opacity-30 z-50 hidden">
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-lg p-8 transition-transform transform translate-x-full"
            id="formPanel">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Tambah Penawaran</h2>
                <button id="closeForm" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-x" width="24" height="24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('penawaran.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Perihal</label>
                    <input type="text" name="perihal" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Nama Perusahaan</label>
                    <input type="text" name="nama_perusahaan" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">PIC Perusahaan</label>
                    <input type="text" name="pic_perusahaan" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">PIC Admin</label>
                    <input type="text" name="pic_admin" class="w-full border rounded px-3 py-2 text-sm"
                        value="Admin Dummy" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">No Penawaran</label>
                    <input type="text" name="no_penawaran" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
            </form>
            <div class="absolute bottom-0 left-0 w-full p-4 bg-white border-t">
                <button type="submit" form="formPanel"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition w-full text-sm">
                    Simpan
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Slide-over logic
        const btnTambah = document.getElementById('btnTambah');
        const formSlide = document.getElementById('formSlide');
        const formPanel = document.getElementById('formPanel');
        const closeForm = document.getElementById('closeForm');

        btnTambah.addEventListener('click', function() {
            formSlide.classList.remove('hidden');
            setTimeout(() => {
                formPanel.classList.remove('translate-x-full');
            }, 10);
        });

        closeForm.addEventListener('click', function() {
            formPanel.classList.add('translate-x-full');
            setTimeout(() => {
                formSlide.classList.add('hidden');
            }, 300);
        });

        formSlide.addEventListener('click', function(e) {
            if (e.target === formSlide) {
                formPanel.classList.add('translate-x-full');
                setTimeout(() => {
                    formSlide.classList.add('hidden');
                }, 300);
            }
        });
    </script>
@endpush
