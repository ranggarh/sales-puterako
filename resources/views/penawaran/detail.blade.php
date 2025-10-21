@extends('layouts.app')

@section('content')
    <style>
        /* Custom styling untuk jspreadsheet */
        .jexcel_content {
            max-height: 600px;
            overflow-y: auto;
        }

        .jexcel>thead>tr>td {
            color: black !important;
            font-weight: bold;
            text-align: start;
        }

        .section-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s;
        }

        /* Style untuk disabled state */
        .spreadsheet-disabled {
            pointer-events: none;
            opacity: 0.7;
        }

        .spreadsheet-scroll-wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>

    <div class="flex items-center p-8 text-gray-600 -mb-8">
        <a href="{{ route('penawaran.list') }}" class="flex items-center hover:text-blue-600">
            <x-lucide-arrow-left class="w-5 h-5 mr-2" />
            List Penawaran
        </a>
        <span class="mx-2">/</span>
        <span class="font-semibold">Detail Penawaran</span>
    </div>

    <div class="container mx-auto p-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Detail Penawaran</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="font-semibold">No Penawaran</div>
                    <div>{{ $penawaran->no_penawaran }}</div>
                </div>
                <div>
                    <div class="font-semibold">Perihal</div>
                    <div>{{ $penawaran->perihal }}</div>
                </div>
                <div>
                    <div class="font-semibold">Nama Perusahaan</div>
                    <div>{{ $penawaran->nama_perusahaan }}</div>
                </div>
                <div>
                    <div class="font-semibold">PIC Perusahaan</div>
                    <div>{{ $penawaran->pic_perusahaan }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex border-b mb-4">
                <button
                    class="tab-btn px-4 py-2 font-semibold text-green-600 border-b-2 border-green-600 focus:outline-none"
                    data-tab="penawaran">Penawaran & Jasa</button>
                <button class="tab-btn px-4 py-2 font-semibold text-gray-600 hover:text-green-600 focus:outline-none"
                    data-tab="Jasa">Rincian Jasa</button>
                <button class="tab-btn px-4 py-2 font-semibold text-gray-600 hover:text-green-600 focus:outline-none"
                    data-tab="preview">Preview</button>
            </div>

            <div id="tabContent">
                <!-- Panel Penawaran -->
                <div class="tab-panel" data-tab="penawaran">
                    <!-- Template Selection (Global) -->
                    <div class="p-2 rounded-lg mb-6">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-4">

                                <div class="flex items-center">
                                    <label class="block text-sm font-semibold mr-2">Profit (%)</label>
                                    <input type="number" id="profitInput" class="border rounded px-3 py-2 bg-white w-24"
                                        min="0" step="0.1" placeholder="30" value="{{ $profit ?? '' }}">
                                    <span class="ml-1 text-sm text-gray-600">%</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button id="editModeBtn"
                                    class="flex items-center bg-[#FFA500] text-white px-3 py-2 rounded hover:bg-orange-600 transition text-sm font-semibold shadow-md">
                                    <x-lucide-pencil class="w-4 h-4 mr-2" />
                                    Edit Data
                                </button>
                                <div class="flex gap-2 items-center">
                                    <button id="cancelEditBtn"
                                        class="flex items-center bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600 transition text-sm font-semibold shadow-md">
                                        <x-lucide-x class="w-4 h-4 mr-2 " />
                                        Batal
                                    </button>
                                    <button id="saveAllBtn"
                                        class="flex items-center bg-[#67BC4B] text-white px-6 py-2 rounded hover:bg-green-700 transition text-sm font-semibold shadow-md">
                                        <x-lucide-save class="w-4 h-4 mr-2" />
                                        Simpan Data
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Button Tambah Section -->
                        <div class="mb-4 mt-6">
                            <button id="addSectionBtn"
                                class="bg-[#02ADB8] text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm font-semibold shadow-md">
                                Tambah Section Baru
                            </button>
                        </div>

                        <!-- Container untuk semua section -->
                        <div id="sectionsContainer"></div>

                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-gray-700">
                            💡 <strong>Tips:</strong>
                            <ul class="list-disc ml-5 mt-1">
                                <li>Setiap section bisa punya area pemasangan berbeda</li>
                                <li>Copy dari Excel → Pilih cell → Paste (Ctrl+V)</li>
                                <li>Harga Total otomatis dihitung dari QTY × Harga Satuan</li>
                                <li>Klik "Hapus Section" untuk menghapus section yang tidak dibutuhkan</li>
                                <li><strong>Mode Edit:</strong> Klik tombol "Edit Data" untuk mengubah data yang sudah
                                    tersimpan</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Panel Jasa -->
                <div class="tab-panel hidden" data-tab="Jasa">
                    <div class="flex gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Profit (%)</label>
                            <input type="number" id="jasaProfitInput" class="border rounded px-3 py-2 bg-white w-24"
                                min="0" step="0.1" value="0">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">PPH (%)</label>
                            <input type="number" id="jasaPphInput" class="border rounded px-3 py-2 bg-white w-24"
                                min="0" step="0.1" value="0">
                        </div>
                        <div class="flex-1 flex justify-end items-end gap-2 mb-4">
                            <button id="jasaAddSectionBtn"
                                class="bg-[#02ADB8] text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm font-semibold shadow-md hidden">
                                Tambah Section Jasa
                            </button>
                            <button id="jasaEditModeBtn"
                                class="flex items-center bg-[#FFA500] text-white px-3 py-2 rounded hover:bg-orange-600 transition text-sm font-semibold shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Edit Data Jasa
                            </button>
                            <button id="jasaCancelEditBtn"
                                class="flex items-center bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600 transition text-sm font-semibold shadow-md hidden">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Batal
                            </button>
                            <button id="jasaSaveAllBtn"
                                class="flex items-center bg-[#67BC4B] text-white px-6 py-2 rounded hover:bg-green-700 transition text-sm font-semibold shadow-md">
                                <x-lucide-save class="w-4 h-4 mr-2" />
                                Simpan Data Jasa
                            </button>
                        </div>
                    </div>
                    <div id="jasaSectionsContainer"></div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            // Data awal dari backend
            const initialSections = @json($sections);
            const hasExistingData = initialSections.length > 0;
        </script>
        <link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jexcel.css" type="text/css" />
        <link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />
        <script src="https://jsuites.net/v4/jsuites.js"></script>
        <script src="https://bossanova.uk/jspreadsheet/v4/jexcel.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // =====================================================
                // DEKLARASI VARIABEL
                // =====================================================

                // Variabel Penawaran
                let sections = [];
                let sectionCounter = 0;
                let isEditMode = !hasExistingData;

                // Variabel Jasa
                let jasaSections = [];
                let jasaSectionCounter = 0;
                let jasaInitialSections = [];
                let jasaProfit = 0;
                let jasaPph = 0;
                let jasaIsEditMode = true;
                let jasaHasExistingData = false;

                // =====================================================
                // TAB SWITCHING LOGIC
                // =====================================================

                const tabButtons = document.querySelectorAll('.tab-btn');
                const tabPanels = document.querySelectorAll('.tab-panel');

                tabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const targetTab = this.getAttribute('data-tab');

                        // Update button styles
                        tabButtons.forEach(btn => {
                            btn.classList.remove('text-green-600', 'border-b-2',
                                'border-green-600');
                            btn.classList.add('text-gray-600');
                        });
                        this.classList.remove('text-gray-600');
                        this.classList.add('text-green-600', 'border-b-2', 'border-green-600');

                        // Show/hide panels
                        tabPanels.forEach(panel => {
                            if (panel.getAttribute('data-tab') === targetTab) {
                                panel.classList.remove('hidden');
                            } else {
                                panel.classList.add('hidden');
                            }
                        });

                        // Load jasa data jika tab Jasa diklik (hanya sekali)
                        if (targetTab === 'Jasa' && jasaSections.length === 0) {
                            console.log('🔄 Loading Jasa tab for first time...');
                            loadJasaData();
                        }
                    });
                });

                // =====================================================
                // UTILITY FUNCTIONS
                // =====================================================

                function parseNumber(value) {
                    if (typeof value === "string") {
                        value = value.replace(/,/g, "").replace(/\./g, "");
                    }
                    const result = parseFloat(value) || 0;

                    // Log only if conversion happened or value is non-zero
                    if (value && result !== 0) {
                        console.log('🔢 parseNumber:', {
                            input: value,
                            output: result
                        });
                    }

                    return result;
                }

                // =====================================================
                // FUNGSI JASA
                // =====================================================

                // Paste kode ini ke dalam tag <script> di bagian FUNGSI JASA

                function loadJasaData() {
                    const penawaranId = {{ $penawaran->id_penawaran }};

                    fetch(`/jasa/detail?id=${penawaranId}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            console.log('🟢 Response dari /jasa/detail:', data); // Tambahkan log ini
                            jasaInitialSections = data.sections || [];
                            jasaProfit = data.profit || 0;
                            jasaPph = data.pph || 0;
                            jasaHasExistingData = jasaInitialSections.length > 0;

                            document.getElementById('jasaProfitInput').value = jasaProfit;
                            document.getElementById('jasaPphInput').value = jasaPph;

                            if (jasaHasExistingData) {
                                jasaInitialSections.forEach(section => createJasaSection(section, false));
                                jasaIsEditMode = false;
                                toggleJasaEditMode(false);
                                document.getElementById('jasaEditModeBtn').classList.remove('hidden');
                                document.getElementById('jasaCancelEditBtn').classList.add('hidden');

                                console.log('🔒 Mode: VIEW (jasa data exists)');
                            } else {
                                createJasaSection(null, true);
                                jasaIsEditMode = true;
                                toggleJasaEditMode(true);
                                document.getElementById('jasaEditModeBtn').classList.add('hidden');
                                document.getElementById('jasaCancelEditBtn').classList.remove('hidden');

                                console.log('✏️ Mode: EDIT (new jasa data)');
                            }
                        })
                        .catch(error => {
                            if (jasaSections.length === 0) {
                                createJasaSection(null, true);
                                jasaIsEditMode = true;
                                toggleJasaEditMode(true);
                                document.getElementById('jasaEditModeBtn').classList.add('hidden');
                                document.getElementById('jasaCancelEditBtn').classList.remove('hidden');
                                document.getElementById('jasaSaveAllBtn').classList.remove('hidden');
                                console.log('✏️ Mode: EDIT (first create jasa data)');
                            }
                        });
                }

                document.getElementById('jasaAddSectionBtn').addEventListener('click', () => {
                    createJasaSection(null, jasaIsEditMode);
                });

                document.getElementById('jasaEditModeBtn').addEventListener('click', () => {
                    toggleJasaEditMode(true);
                    jasaIsEditMode = true;
                    document.getElementById('jasaEditModeBtn').classList.add('hidden');
                    document.getElementById('jasaCancelEditBtn').classList.remove('hidden');
                    document.getElementById('jasaSaveAllBtn').classList.remove('hidden');
                });

                document.getElementById('jasaCancelEditBtn').addEventListener('click', () => {
                    if (confirm('Batalkan perubahan dan kembali ke mode view?')) {
                        window.location.reload();
                    }
                });

                function toggleJasaEditMode(enable) {
                    jasaIsEditMode = enable;

                    document.getElementById('jasaProfitInput').disabled = !enable;
                    document.getElementById('jasaPphInput').disabled = !enable;

                    // Tampilkan tombol tambah section jasa hanya saat edit
                    document.getElementById('jasaAddSectionBtn').classList.toggle('hidden', !enable);

                    jasaSections.forEach(section => {
                        const sectionElement = document.getElementById(section.id);
                        const spreadsheetWrapper = document.getElementById(section.spreadsheetId);
                        const namaSectionInput = sectionElement.querySelector('.nama-section-input');
                        const addRowBtn = sectionElement.querySelector('.add-row-btn');
                        const deleteSectionBtn = sectionElement.querySelector('.delete-section-btn');

                        if (enable) {
                            spreadsheetWrapper.classList.remove('spreadsheet-disabled');
                            section.spreadsheet.options.editable = true;
                        } else {
                            spreadsheetWrapper.classList.add('spreadsheet-disabled');
                            section.spreadsheet.options.editable = false;
                        }

                        namaSectionInput.disabled = !enable;
                        addRowBtn.classList.toggle('hidden', !enable);
                        deleteSectionBtn.classList.toggle('hidden', !enable);
                    });
                }

                function recalcJasaRow(spreadsheet, rowIndex) {
                    // Ambil data fresh dari spreadsheet
                    const row = spreadsheet.getRowData(rowIndex);

                    const vol = parseNumber(row[2]);
                    const hari = parseNumber(row[3]);
                    const orang = parseNumber(row[4]);
                    const unit = parseNumber(row[5]);

                    console.log('🧮 recalcJasaRow:', {
                        rowIndex,
                        vol,
                        hari,
                        orang,
                        unit,
                        rawRow: row
                    });

                    // Jika unit kosong atau 0, total = 0
                    if (!unit || unit === 0) {
                        console.log('⚠️ Unit is 0 or empty, setting total to 0');
                        spreadsheet.setValueFromCoords(6, rowIndex, 0, true);
                        const section = jasaSections.find(s => s.spreadsheet === spreadsheet);
                        if (section) updateJasaSubtotal(section);
                        return;
                    }

                    let total = unit; // Mulai dari nilai unit

                    // Kalikan dengan faktor-faktor yang ada (> 0)
                    if (vol > 0) total *= vol;
                    if (hari > 0) total *= hari;
                    if (orang > 0) total *= orang;

                    console.log('💰 Calculated total:', {
                        unit,
                        vol,
                        hari,
                        orang,
                        formula: `${unit}${vol > 0 ? ' × ' + vol : ''}${hari > 0 ? ' × ' + hari : ''}${orang > 0 ? ' × ' + orang : ''}`,
                        result: total
                    });

                    // Set value dengan force render
                    spreadsheet.setValueFromCoords(6, rowIndex, total, true);

                    // Update subtotal untuk section ini
                    const section = jasaSections.find(s => s.spreadsheet === spreadsheet);
                    if (section) {
                        updateJasaSubtotal(section);
                    }
                }

                function createJasaSection(sectionData = null, editable = true) {
                    jasaSectionCounter++;
                    const sectionId = 'jasa-section-' + jasaSectionCounter;
                    const spreadsheetId = 'jasa-spreadsheet-' + jasaSectionCounter;

                    console.log(`🏗️ Creating jasa section: ${sectionId}`, {
                        hasData: !!sectionData,
                        counter: jasaSectionCounter
                    });

                    const initialData = sectionData ? sectionData.data.map(row => [
                        row.no || '',
                        row.deskripsi || '',
                        row.vol || 0,
                        row.hari || 0,
                        row.orang || 0,
                        row.unit || 0,
                        0, // Total akan dihitung ulang
                    ]) : [
                        ['', '', 0, 0, 0, 0, 0],
                        ['', '', 0, 0, 0, 0, 0],
                    ];

                    const sectionHTML = `
        <div class="section-card p-4 mb-6 bg-white" id="${sectionId}">
            <div class="flex justify-between items-center mb-3">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-bold text-gray-700">Section Jasa ${jasaSectionCounter}</h3>
                    <input type="text" class="nama-section-input border rounded px-3 py-1" 
                        placeholder="Ex: Pekerjaan Instalasi" 
                        value="${sectionData && sectionData.nama_section ? sectionData.nama_section : ''}">
                </div>
                <div class="flex gap-2">
                    <button class="add-row-btn bg-[#02ADB8] text-white px-3 py-1 rounded hover:bg-blue-700 transition text-sm">
                        <x-lucide-plus class="w-4 h-4 inline-block mr-1" />
                         Tambah Baris
                    </button>
                    <button class="delete-section-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 transition text-sm">
                        <x-lucide-trash class="w-4 h-4 inline-block mr-1" />
                         Hapus Section
                    </button>
                </div>
            </div>
            <div class="spreadsheet-scroll-wrapper" style="overflow-x:auto;">
                <div id="${spreadsheetId}"></div>
            </div>
            <div class="text-right mt-3 font-semibold text-gray-700">
                Subtotal: Rp <span id="${sectionId}-subtotal">0</span>
            </div>
        </div>`;

                    document.getElementById('jasaSectionsContainer').insertAdjacentHTML('beforeend', sectionHTML);

                    const spreadsheet = jspreadsheet(document.getElementById(spreadsheetId), {
                        data: initialData,
                        columns: [{
                                title: 'No',
                                width: 60
                            },
                            {
                                title: 'Deskripsi',
                                width: 250
                            },
                            {
                                title: 'Vol',
                                width: 80,
                                type: 'numeric'
                            },
                            {
                                title: 'Hari',
                                width: 80,
                                type: 'numeric'
                            },
                            {
                                title: 'Orang',
                                width: 80,
                                type: 'numeric'
                            },
                            {
                                title: 'Unit',
                                width: 100,
                                type: 'numeric'
                            },
                            {
                                title: 'Total',
                                width: 120,
                                type: 'numeric',
                                readOnly: true
                            },
                        ],
                        tableOverflow: true,
                        tableWidth: '100%',
                        tableHeight: '350px',
                        editable: editable,
                        onchange: function(instance, cell, col, row, value) {
                            console.log('🔔 onchange triggered:', {
                                col,
                                row,
                                value
                            });

                            // Trigger recalc jika Vol, Hari, Orang, atau Unit berubah
                            if (col >= 2 && col <= 5) {
                                console.log('📝 Recalculating row:', row);

                                // Delay sedikit untuk memastikan value sudah tersimpan
                                setTimeout(() => {
                                    recalcJasaRow(spreadsheet, row);
                                }, 50);
                            }
                        },
                        onafterchanges: function(instance, records) {
                            console.log('🔔 onafterchanges triggered:', records);

                            // Untuk paste multiple cells
                            const rowsToRecalc = new Set();
                            records.forEach(record => {
                                const col = record.x;
                                const row = record.y;
                                if (col >= 2 && col <= 5) {
                                    rowsToRecalc.add(row);
                                }
                            });

                            // Recalculate unique rows
                            rowsToRecalc.forEach(row => {
                                setTimeout(() => {
                                    recalcJasaRow(spreadsheet, row);
                                }, 50);
                            });
                        }
                    });

                    const sectionElement = document.getElementById(sectionId);

                    // Event listener tambah baris
                    sectionElement.querySelector('.add-row-btn').addEventListener('click', () => {
                        spreadsheet.insertRow();
                        console.log('➕ Row added to jasa section:', sectionId);
                    });

                    // Event listener hapus section
                    sectionElement.querySelector('.delete-section-btn').addEventListener('click', () => {
                        if (confirm('Yakin ingin menghapus section jasa ini?')) {
                            jasaSections = jasaSections.filter(s => s.id !== sectionId);
                            sectionElement.remove();
                            console.log('🗑️ Jasa section deleted:', sectionId);
                        }
                    });

                    // Simpan ke array
                    jasaSections.push({
                        id: sectionId,
                        spreadsheetId: spreadsheetId,
                        spreadsheet: spreadsheet
                    });

                    // PENTING: Kalkulasi semua row setelah spreadsheet dibuat
                    console.log(`🔄 Running initial calculation for ${sectionId}`);

                    // Gunakan setTimeout untuk memastikan spreadsheet sudah fully rendered
                    setTimeout(() => {
                        const totalRows = spreadsheet.getData().length;
                        console.log(`   Calculating ${totalRows} rows...`);

                        for (let i = 0; i < totalRows; i++) {
                            recalcJasaRow(spreadsheet, i);
                        }

                        console.log('✅ Initial calculation completed');
                    }, 100);

                    console.log('✅ Jasa section created. Total sections:', jasaSections.length);
                }

                function updateJasaSubtotal(section) {
                    const data = section.spreadsheet.getData();
                    let subtotal = 0;

                    data.forEach(row => {
                        const total = parseNumber(row[6]);
                        subtotal += total;
                        console.log('   Row total:', total, 'Running subtotal:', subtotal);
                    });

                    const subtotalEl = document.getElementById(`${section.id}-subtotal`);
                    if (subtotalEl) {
                        subtotalEl.textContent = subtotal.toLocaleString('id-ID');
                        console.log('💰 Subtotal updated:', subtotal);
                    }
                }

                // Input profit jasa - hanya untuk informasi, tidak mempengaruhi perhitungan
                document.getElementById('jasaProfitInput').addEventListener('input', function() {
                    console.log('💰 Jasa profit changed to:', this.value, '(info only)');
                });

                // Tombol simpan jasa
                document.getElementById('jasaSaveAllBtn').addEventListener('click', () => {
                    const btn = document.getElementById('jasaSaveAllBtn');
                    btn.innerHTML = "⏳ Menyimpan...";
                    btn.disabled = true;

                    const allSectionsData = jasaSections.map(section => {
                        const sectionElement = document.getElementById(section.id);
                        const namaSectionInput = sectionElement.querySelector('.nama-section-input');
                        const rawData = section.spreadsheet.getData();

                        return {
                            nama_section: namaSectionInput.value,
                            data: rawData.map(row => ({
                                no: row[0],
                                deskripsi: row[1],
                                vol: parseNumber(row[2]),
                                hari: parseNumber(row[3]),
                                orang: parseNumber(row[4]),
                                unit: parseNumber(row[5]),
                                total: parseNumber(row[6]),
                            }))
                        };
                    });

                    console.log('💾 Saving jasa data:', {
                        penawaran_id: {{ $penawaran->id_penawaran }},
                        profit: parseNumber(document.getElementById('jasaProfitInput').value),
                        pph: parseNumber(document.getElementById('jasaPphInput').value),
                        sections: allSectionsData
                    });

                    fetch("{{ route('jasa.save') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                penawaran_id: {{ $penawaran->id_penawaran }},
                                profit: parseNumber(document.getElementById('jasaProfitInput')
                                    .value) || 0,
                                pph: parseNumber(document.getElementById('jasaPphInput').value) ||
                                    0,
                                sections: allSectionsData
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            console.log('✅ Jasa data saved successfully:', data);
                            btn.innerHTML = "✅ Tersimpan!";
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        })
                        .catch(error => {
                            console.error('❌ Failed to save jasa data:', error);
                            btn.innerHTML = "❌ Gagal";
                            setTimeout(() => {
                                btn.innerHTML = "Simpan Data Jasa";
                                btn.disabled = false;
                            }, 2000);
                        });
                });

                // =====================================================
                // FUNGSI PENAWARAN
                // =====================================================

                function recalculateRow(spreadsheet, rowIndex, changedCol = null, newValue = null) {
                    const profitPercent = parseNumber(document.getElementById('profitInput').value) || 0;
                    const profitMultiplier = 1 + (profitPercent / 100);

                    // Ambil data row saat ini
                    const row = spreadsheet.getRowData(rowIndex);

                    // Gunakan newValue jika kolom yang berubah adalah QTY atau HPP
                    let hpp = parseNumber(row[7]);
                    let qty = parseNumber(row[3]);

                    // Override dengan nilai baru jika ada
                    if (changedCol === 7) hpp = parseNumber(newValue);
                    if (changedCol === 3) qty = parseNumber(newValue);

                    const hargaSatuan = hpp * profitMultiplier;
                    const total = qty * hargaSatuan;

                    console.log('🔄 recalculateRow - Row:', rowIndex, {
                        profitPercent,
                        profitMultiplier,
                        changedCol,
                        newValue,
                        hpp,
                        qty,
                        hargaSatuan,
                        total,
                        rawRow: row
                    });

                    // Parameter ke-4 harus TRUE agar cell ter-render ulang
                    spreadsheet.setValueFromCoords(5, rowIndex, hargaSatuan, true);
                    spreadsheet.setValueFromCoords(6, rowIndex, total, true);
                    updateSubtotal(sections.find(s => s.spreadsheet === spreadsheet));

                    console.log('✅ Updated - Harga Satuan:', hargaSatuan, 'Harga Total:', total);
                }

                function recalculateAll() {
                    console.log('🔄 recalculateAll - Starting...');

                    const profitPercent = parseNumber(document.getElementById('profitInput').value) || 0;
                    const profitMultiplier = 1 + (profitPercent / 100);

                    console.log('📊 Profit Settings:', {
                        profitPercent,
                        profitMultiplier
                    });

                    sections.forEach((section, sectionIdx) => {
                        console.log(`📦 Section ${sectionIdx + 1}/${sections.length} - ID: ${section.id}`);

                        const allData = section.spreadsheet.getData();
                        console.log(`   Total rows: ${allData.length}`);

                        allData.forEach((row, i) => {
                            const hpp = parseNumber(row[7]);
                            const qty = parseNumber(row[3]);
                            const hargaSatuan = hpp * profitMultiplier;
                            const total = qty * hargaSatuan;

                            console.log(`   Row ${i + 1}:`, {
                                hpp,
                                qty,
                                hargaSatuan,
                                total
                            });

                            // Parameter ke-4 harus TRUE agar cell ter-render
                            section.spreadsheet.setValueFromCoords(5, i, hargaSatuan, true);
                            section.spreadsheet.setValueFromCoords(6, i, total, true);
                        });

                        updateSubtotal(section);
                    });

                    console.log('✅ recalculateAll - Completed');
                }

                function toggleEditMode(enable) {
                    isEditMode = enable;

                    if (hasExistingData) {
                        document.getElementById('editModeBtn').classList.toggle('hidden', enable);
                        document.getElementById('cancelEditBtn').classList.toggle('hidden', !enable);
                    } else {
                        document.getElementById('editModeBtn').classList.add('hidden');
                        document.getElementById('cancelEditBtn').classList.add('hidden');
                    }

                    document.getElementById('saveAllBtn').classList.remove('hidden');
                    document.getElementById('addSectionBtn').classList.toggle('hidden', !enable);
                    document.getElementById('profitInput').disabled = !enable;

                    sections.forEach(section => {
                        const sectionElement = document.getElementById(section.id);
                        const spreadsheetWrapper = document.getElementById(section.spreadsheetId);
                        const areaSelect = sectionElement.querySelector('.area-select');
                        const addRowBtn = sectionElement.querySelector('.add-row-btn');
                        const deleteRowBtn = sectionElement.querySelector('.delete-row-btn');
                        const deleteSectionBtn = sectionElement.querySelector('.delete-section-btn');

                        if (enable) {
                            spreadsheetWrapper.classList.remove('spreadsheet-disabled');
                            section.spreadsheet.options.editable = true;
                        } else {
                            spreadsheetWrapper.classList.add('spreadsheet-disabled');
                            section.spreadsheet.options.editable = false;
                        }

                        areaSelect.disabled = !enable;
                        addRowBtn.classList.toggle('hidden', !enable);
                        deleteRowBtn.classList.toggle('hidden', !enable);
                        deleteSectionBtn.classList.toggle('hidden', !enable);
                    });
                }

                function createSection(sectionData = null) {
                    sectionCounter++;
                    const sectionId = 'section-' + sectionCounter;
                    const spreadsheetId = 'spreadsheet-' + sectionCounter;

                    console.log(`🏗️ Creating section: ${sectionId}`, {
                        hasData: !!sectionData
                    });

                    const initialData = sectionData ? sectionData.data.map(row => [
                        row.no || '',
                        row.tipe || '',
                        row.deskripsi || '',
                        row.qty || 0,
                        row.satuan || '',
                        row.harga_satuan || 0,
                        row.harga_total || 0,
                        row.hpp || 0,
                    ]) : [
                        ['', '', '', 0, '', 0, 0, 0],
                        ['', '', '', 0, '', 0, 0, 0],
                    ];

                    const sectionHTML = `
        <div class="section-card p-4 mb-6 bg-white" id="${sectionId}">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-bold text-gray-700">Section ${sectionCounter}</h3>
                    <input type="text" class="nama-section-input border rounded px-3 py-1 ml-2" placeholder="Ex: Main Unit" value="${sectionData && sectionData.nama_section ? sectionData.nama_section : ''}">
                    <div class="flex items-center">
                        <label class="block text-sm font-semibold mr-2">Area Pemasangan:</label>
                        <input type="text" class="area-select border rounded px-3 py-1 ml-2" placeholder="Ex: Kantor" value="${sectionData && sectionData.area ? sectionData.area : ''}">
                    </div>
                </div>
                <div class="flex gap-2 items-center">
                <button class="flex items-center add-row-btn bg-[#02ADB8] text-white px-3 py-1 rounded hover:bg-blue-700 transition text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Baris
                </button>
                <button class="flex items-center delete-row-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 transition text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Baris
                </button>
                <button class="delete-section-btn bg-white text-gray-700 px-3 py-1 rounded hover:bg-gray-700 hover:text-white transition text-sm">
                    ❌
                </button>
            </div>
            </div>
            <div class="spreadsheet-scroll-wrapper" style="overflow-x:auto;">
                <div id="${spreadsheetId}"></div>
            </div>
            <div class="text-right mt-3 font-semibold text-gray-700">
                Subtotal: Rp <span id="${sectionId}-subtotal">0</span>
            </div>
        </div>`;

                    document.getElementById('sectionsContainer').insertAdjacentHTML('beforeend', sectionHTML);

                    const spreadsheet = jspreadsheet(document.getElementById(spreadsheetId), {
                        data: initialData,
                        columns: [{
                                title: 'No',
                                width: 60
                            },
                            {
                                title: 'Tipe',
                                width: 150
                            },
                            {
                                title: 'Deskripsi',
                                width: 300
                            },
                            {
                                title: 'QTY',
                                width: 100,
                                type: 'numeric'
                            },
                            {
                                title: 'Satuan',
                                width: 100
                            },
                            {
                                title: 'Harga Satuan',
                                width: 150,
                                type: 'numeric',
                                readOnly: true
                            },
                            {
                                title: 'Harga Total',
                                width: 150,
                                type: 'numeric',
                                readOnly: true
                            },
                            {
                                title: 'HPP',
                                width: 100,
                                type: 'numeric'
                            },
                        ],
                        tableOverflow: true,
                        tableWidth: '100%',
                        tableHeight: '400px',
                        editable: isEditMode,
                        onchange: function(instance, cell, colIndex, rowIndex, value) {
                            console.log('📝 Spreadsheet onChange:', {
                                spreadsheetId,
                                colIndex,
                                rowIndex,
                                value,
                                columnName: ['No', 'Tipe', 'Deskripsi', 'QTY', 'Satuan',
                                    'Harga Satuan', 'Harga Total', 'HPP'
                                ][colIndex]
                            });

                            if (colIndex == 3 || colIndex == 7) {
                                console.log('✨ Triggering recalculateRow with new value:', value);
                                recalculateRow(spreadsheet, rowIndex, colIndex, value);
                            } else {
                                console.log('⏭️ Skip calculation (column not QTY/HPP)');
                            }
                        }
                    });

                    const sectionElement = document.getElementById(sectionId);

                    if (sectionData && sectionData.area) {
                        sectionElement.querySelector('.area-select').value = sectionData.area;
                    }

                    sectionElement.querySelector('.add-row-btn').addEventListener('click', () => {
                        spreadsheet.insertRow();
                    });

                    sectionElement.querySelector('.delete-row-btn').addEventListener('click', () => {
                        const totalRows = spreadsheet.getData().length;
                        const input = prompt(
                            `Masukkan nomor baris yang ingin dihapus (1-${totalRows}):\n\nContoh:\n- Satu baris: 3\n- Beberapa baris: 2,5,7\n- Range: 3-6\n- Kombinasi: 2,5-8,10`
                        );

                        if (input) {
                            try {
                                const rowsToDelete = [];
                                const parts = input.split(',');

                                parts.forEach(part => {
                                    part = part.trim();
                                    if (part.includes('-')) {
                                        const [start, end] = part.split('-').map(n => parseInt(n
                                            .trim()));
                                        for (let i = start; i <= end; i++) {
                                            rowsToDelete.push(i);
                                        }
                                    } else {
                                        rowsToDelete.push(parseInt(part));
                                    }
                                });

                                const validRows = [...new Set(rowsToDelete)]
                                    .filter(row => row >= 1 && row <= totalRows)
                                    .sort((a, b) => b - a);

                                if (validRows.length === 0) {
                                    alert('Tidak ada baris yang valid untuk dihapus!');
                                    return;
                                }

                                if (confirm(
                                        `Hapus ${validRows.length} baris: ${validRows.sort((a,b) => a-b).join(', ')}?`
                                    )) {
                                    validRows.forEach(rowNum => {
                                        spreadsheet.deleteRow(rowNum - 1, 1);
                                    });
                                }
                            } catch (error) {
                                alert('Format input tidak valid! Gunakan format: 2,5,7 atau 3-6');
                            }
                        }
                    });

                    sectionElement.querySelector('.delete-section-btn').addEventListener('click', () => {
                        if (confirm('Yakin ingin menghapus section ini?')) {
                            sections = sections.filter(s => s.id !== sectionId);
                            sectionElement.remove();
                        }
                    });

                    sections.push({
                        id: sectionId,
                        spreadsheetId,
                        spreadsheet
                    });

                    // applyTemplateStyle(spreadsheetId);
                    updateSubtotal({
                        id: sectionId,
                        spreadsheet
                    });
                }

                function updateSubtotal(section) {
                    const data = section.spreadsheet.getData();
                    let subtotal = 0;

                    data.forEach(row => {
                        subtotal += parseNumber(row[6]); // kolom Harga Total
                    });

                    const subtotalEl = document.getElementById(`${section.id}-subtotal`);
                    if (subtotalEl) {
                        subtotalEl.textContent = subtotal.toLocaleString('id-ID');
                    }
                }

                // =====================================================
                // EVENT LISTENERS PENAWARAN
                // =====================================================

                document.getElementById('profitInput').addEventListener('input', function() {
                    console.log('💰 Profit input changed to:', this.value);
                    recalculateAll();
                });

                document.getElementById('addSectionBtn').addEventListener('click', () => createSection());

                document.getElementById('editModeBtn').addEventListener('click', () => {
                    toggleEditMode(true);
                });

                document.getElementById('cancelEditBtn').addEventListener('click', () => {
                    if (confirm('Batalkan perubahan dan kembali ke mode view?')) {
                        window.location.reload();
                    }
                });

                document.getElementById('saveAllBtn').addEventListener('click', function() {
                    const btn = this;
                    btn.innerHTML = "⏳ Menyimpan...";
                    btn.disabled = true;

                    const allSectionsData = sections.map(section => {
                        const sectionElement = document.getElementById(section.id);
                        const areaSelect = sectionElement.querySelector('.area-select');
                        const namaSectionInput = sectionElement.querySelector('.nama-section-input');
                        const rawData = section.spreadsheet.getData();

                        return {
                            area: areaSelect.value,
                            nama_section: namaSectionInput.value,
                            data: rawData.map(row => ({
                                no: row[0],
                                tipe: row[1],
                                deskripsi: row[2],
                                qty: parseNumber(row[3]),
                                satuan: row[4],
                                harga_satuan: parseNumber(row[5]),
                                harga_total: parseNumber(row[6]),
                                hpp: parseNumber(row[7])
                            }))
                        };
                    });

                    fetch("{{ route('penawaran.save') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                penawaran_id: {{ $penawaran->id_penawaran }},
                                template: document.getElementById('templateSelect').value,
                                profit: parseNumber(document.getElementById('profitInput').value) ||
                                    0,
                                sections: allSectionsData
                            })
                        })
                        .then(res => res.json())
                        .then(() => {
                            btn.innerHTML = "✅ Tersimpan!";
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        })
                        .catch(() => {
                            btn.innerHTML = "❌ Gagal";
                            setTimeout(() => {
                                btn.innerHTML = "Simpan Semua Data";
                                btn.disabled = false;
                            }, 2000);
                        });
                });

                // =====================================================
                // INISIALISASI PENAWARAN
                // =====================================================

                if (initialSections.length > 0) {
                    console.log('🗄️ Loading existing data...', {
                        totalSections: initialSections.length
                    });

                    initialSections.forEach((section, idx) => {
                        console.log(`Creating section ${idx + 1}:`, section);
                        createSection(section);
                    });

                    toggleEditMode(false);
                    console.log('🔒 Mode: VIEW (data exists)');

                    // Trigger kalkulasi awal setelah semua section dibuat
                    const profitInput = document.getElementById('profitInput');
                    if (profitInput && profitInput.value) {
                        console.log('🚀 Initial calculation with profit:', profitInput.value);
                        recalculateAll();
                    } else {
                        console.log('⚠️ No profit value found');
                    }
                } else {
                    console.log('🆕 Creating new empty section...');
                    createSection();
                    toggleEditMode(true);
                    console.log('✏️ Mode: EDIT (new data)');
                }
            });
        </script>
    @endpush
