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

        /* Style untuk template Puterako */
        .template-puterako .jexcel tbody tr td:nth-child(2) {
            color: rgb(0, 255, 72);
        }

        .template-puterako .jexcel tbody tr td:nth-child(3) {
            color: purple;
        }

        /* Style untuk template BQ */
        .template-bq .jexcel tbody tr td {
            color: black;
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
    </style>

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
                <button class="tab-btn px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600 focus:outline-none"
                    data-tab="penawaran">Penawaran</button>
                <button class="tab-btn px-4 py-2 font-semibold text-gray-600 hover:text-blue-600 focus:outline-none"
                    data-tab="jasa">Jasa</button>
            </div>

            <div id="tabContent">
                <!-- Panel Penawaran -->
                <div class="tab-panel" data-tab="penawaran">
                    <!-- Template Selection (Global) -->
                    <div class="p-2 rounded-lg mb-6">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center">
                                    <label class="block text-sm font-semibold mr-2">Pilih Template</label>
                                    <select id="templateSelect" class="border rounded px-3 py-2 bg-white">
                                        <option value="puterako">Template Puterako</option>
                                        <option value="bq">Template BQ</option>
                                    </select>
                                </div>
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

                    <!-- Panel Jasa -->
                    <div class="tab-panel hidden" data-tab="jasa">
                        <p>Konten tab Jasa...</p>
                    </div>
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
                let sections = [];
                let sectionCounter = 0;
                let isEditMode = !hasExistingData;

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

                // Fungsi untuk kalkulasi ulang harga
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

                    console.log('✅ Updated - Harga Satuan:', hargaSatuan, 'Harga Total:', total);
                }

                // Fungsi untuk kalkulasi semua baris di semua section
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
                    document.getElementById('templateSelect').disabled = !enable;
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
                    <div class="flex items-center">
                        <label class="block text-sm font-semibold mr-2">Area Pemasangan:</label>
                        <select class="area-select border rounded px-3 py-1 bg-white">
                            <option value="">-- Pilih Area --</option>
                            <option value="kantor">Kantor</option>
                            <option value="warehouse">Warehouse</option>
                            <option value="gudang">Gudang</option>
                            <option value="workshop">Workshop</option>
                            <option value="ruang_meeting">Ruang Meeting</option>
                            <option value="parkiran">Parkiran</option>
                            <option value="lobby">Lobby</option>
                            <option value="lain-lain">Lain-lain</option>
                        </select>
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
            <div id="${spreadsheetId}"></div>
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
                        // KUNCI UTAMA: Event onchange untuk trigger kalkulasi
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

                            // Kalkulasi ulang jika kolom QTY (3) atau HPP (7) berubah
                            if (colIndex == 3 || colIndex == 7) {
                                console.log('✨ Triggering recalculateRow with new value:', value);
                                // Pass colIndex dan value agar bisa langsung pakai nilai baru
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

                    applyTemplateStyle(spreadsheetId);
                }

                function applyTemplateStyle(spreadsheetId) {
                    const template = document.getElementById('templateSelect').value;
                    const wrapper = document.getElementById(spreadsheetId);
                    wrapper.classList.remove('template-puterako', 'template-bq');
                    wrapper.classList.add('template-' + template);
                }

                // Event Listeners
                document.getElementById('templateSelect').addEventListener('change', () => {
                    sections.forEach(sec => applyTemplateStyle(sec.spreadsheetId));
                });

                // Profit input: kalkulasi ulang SEMUA baris
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
                        const rawData = section.spreadsheet.getData();

                        return {
                            area: areaSelect.value,
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

                // Inisialisasi section dari database
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
