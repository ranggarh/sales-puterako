@extends('layouts.app')

@section('content')
    <style>
        /* Custom styling untuk jspreadsheet */
        .jexcel_content {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .jexcel > thead > tr > td {
            background-color: #4299e1 !important;
            color: white !important;
            font-weight: bold;
        }

        /* Style untuk template Puterako */
        .template-puterako .jexcel tbody tr td:nth-child(2) {
            color: blue;
        }
        .template-puterako .jexcel tbody tr td:nth-child(3) {
            color: purple;
        }

        /* Style untuk template BQ */
        .template-bq .jexcel tbody tr td {
            color: black;
        }

        .section-card {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .section-card:hover {
            border-color: #4299e1;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
                            </div>
                            <button id="saveAllBtn"
                                class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-700 transition text-sm font-semibold shadow-md">
                                Simpan Semua Data
                            </button>
                        </div>
                    </div>

                    <!-- Button Tambah Section -->
                    <div class="mb-4">
                        <button id="addSectionBtn"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm font-semibold shadow-md">
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
    <!-- jSpreadsheet CSS + JS -->
    <link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jexcel.css" type="text/css" />
    <link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />
    <script src="https://jsuites.net/v4/jsuites.js"></script>
    <script src="https://bossanova.uk/jspreadsheet/v4/jexcel.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let sections = [];
            let sectionCounter = 0;

            // Fungsi untuk membuat section baru
            function createSection() {
                sectionCounter++;
                const sectionId = 'section-' + sectionCounter;
                const spreadsheetId = 'spreadsheet-' + sectionCounter;

                // Buat HTML untuk section
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
                            <div class="flex gap-2">
                                <button class="add-row-btn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700 transition text-sm">
                                    + Tambah Baris
                                </button>
                                <button class="delete-row-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 transition text-sm">
                                    🗑️ Hapus Baris
                                </button>
                                <button class="delete-section-btn bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-700 transition text-sm">
                                    ❌ Hapus Section
                                </button>
                            </div>
                        </div>
                        <div id="${spreadsheetId}"></div>
                    </div>
                `;

                // Tambahkan ke container
                document.getElementById('sectionsContainer').insertAdjacentHTML('beforeend', sectionHTML);

                // Inisialisasi jSpreadsheet untuk section ini
                const spreadsheet = jspreadsheet(document.getElementById(spreadsheetId), {
                    data: [
                        ['', '', '', 0, '', 0, 0, 0],
                        ['', '', '', 0, '', 0, 0, 0],
                        ['', '', '', 0, '', 0, 0, 0],
                    ],
                    columns: [
                        { 
                            title: 'No', 
                            width: 60,
                            type: 'text'
                        },
                        { 
                            title: 'Tipe', 
                            width: 150,
                            type: 'text'
                        },
                        { 
                            title: 'Deskripsi', 
                            width: 300,
                            type: 'text'
                        },
                        { 
                            title: 'QTY', 
                            width: 100,
                            type: 'numeric',
                            mask: '#,##0',
                            decimal: '.'
                        },
                        { 
                            title: 'Satuan', 
                            width: 100,
                            type: 'text'
                        },
                        { 
                            title: 'Harga Satuan', 
                            width: 150,
                            type: 'numeric',
                            mask: '#,##0',
                            decimal: '.'
                        },
                        { 
                            title: 'Harga Total', 
                            width: 150,
                            type: 'numeric',
                            mask: '#,##0',
                            decimal: '.',
                            readOnly: true
                        },
                        { 
                            title: 'HPP', 
                            width: 100,
                            type: 'numeric',
                            mask: '#,##0',
                            decimal: '.'
                        }
                    ],
                    minDimensions: [8, 3],
                    allowInsertRow: true,
                    allowInsertColumn: false,
                    allowDeleteRow: true,
                    allowDeleteColumn: false,
                    allowRenameColumn: false,
                    tableOverflow: true,
                    tableWidth: '100%',
                    tableHeight: '400px',
                    
                    // Event saat cell berubah
                    onchange: function(instance, cell, x, y, value) {
                        // Hitung Harga Total jika QTY atau Harga Satuan berubah
                        if (x == 3 || x == 5) { // kolom QTY (3) atau Harga Satuan (5)
                            const data = spreadsheet.getData();
                            const qty = parseFloat(data[y][3]) || 0;
                            const harga = parseFloat(data[y][5]) || 0;
                            const total = qty * harga;
                            spreadsheet.setValueFromCoords(6, y, total, false);
                        }
                        
                        applyTemplateStyle(spreadsheetId);
                    },
                    
                    // Event setelah paste
                    onpaste: function(instance, data) {
                        setTimeout(function() {
                            const allData = spreadsheet.getData();
                            allData.forEach((row, index) => {
                                const qty = parseFloat(row[3]) || 0;
                                const harga = parseFloat(row[5]) || 0;
                                const total = qty * harga;
                                spreadsheet.setValueFromCoords(6, index, total, false);
                            });
                            applyTemplateStyle(spreadsheetId);
                        }, 100);
                    }
                });

                // Simpan referensi section
                sections.push({
                    id: sectionId,
                    spreadsheetId: spreadsheetId,
                    spreadsheet: spreadsheet
                });

                // Event listeners untuk button dalam section
                const sectionElement = document.getElementById(sectionId);
                
                // Tambah baris
                sectionElement.querySelector('.add-row-btn').addEventListener('click', function() {
                    spreadsheet.insertRow(1, false, true);
                });

                // Hapus baris
                sectionElement.querySelector('.delete-row-btn').addEventListener('click', function() {
                    const selected = spreadsheet.getSelectedRows();
                    if (selected && selected.length > 0) {
                        selected.reverse().forEach(function(rowIndex) {
                            spreadsheet.deleteRow(rowIndex);
                        });
                    } else {
                        alert('Pilih baris yang ingin dihapus dengan klik nomor barisnya');
                    }
                });

                // Hapus section
                sectionElement.querySelector('.delete-section-btn').addEventListener('click', function() {
                    if (confirm('Yakin ingin menghapus section ini?')) {
                        // Hapus dari array
                        sections = sections.filter(s => s.id !== sectionId);
                        // Hapus dari DOM
                        sectionElement.remove();
                    }
                });

                // Apply template style
                applyTemplateStyle(spreadsheetId);
            }

            // Fungsi untuk apply styling berdasarkan template
            function applyTemplateStyle(spreadsheetId) {
                const template = document.getElementById('templateSelect').value;
                const wrapper = document.getElementById(spreadsheetId);
                
                // Remove semua class template
                wrapper.classList.remove('template-puterako', 'template-bq');
                
                // Add class sesuai template
                wrapper.classList.add('template-' + template);
            }

            // Event ketika template berubah (apply ke semua section)
            document.getElementById('templateSelect').addEventListener('change', function() {
                sections.forEach(section => {
                    applyTemplateStyle(section.spreadsheetId);
                });
            });

            // Button tambah section
            document.getElementById('addSectionBtn').addEventListener('click', function() {
                createSection();
            });

            // Tombol Simpan Semua
            document.getElementById('saveAllBtn').addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = "⏳ Menyimpan...";
                btn.disabled = true;

                // Kumpulkan data dari semua section
                const allSectionsData = sections.map(section => {
                    const sectionElement = document.getElementById(section.id);
                    const areaSelect = sectionElement.querySelector('.area-select');
                    const rawData = section.spreadsheet.getData();
                    
                    // Format data
                    const formattedData = rawData.map(row => {
                        return {
                            no: row[0] || '',
                            tipe: row[1] || '',
                            deskripsi: row[2] || '',
                            qty: parseFloat(row[3]) || 0,
                            satuan: row[4] || '',
                            harga_satuan: parseFloat(row[5]) || 0,
                            harga_total: parseFloat(row[6]) || 0,
                            hpp: parseFloat(row[7]) || 0
                        };
                    });

                    return {
                        area: areaSelect.value,
                        data: formattedData
                    };
                });

                fetch("{{ route('penawaran.save') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        penawaran_id: {{ $penawaran->id ?? 'null' }},
                        template: document.getElementById('templateSelect').value,
                        sections: allSectionsData
                    })
                })
                .then(response => response.json())
                .then(result => {
                    btn.innerHTML = "✅ Tersimpan!";
                    console.log('Data saved:', result);
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 2000);
                })
                .catch(error => {
                    console.error("Error:", error);
                    btn.innerHTML = "❌ Gagal";
                    alert('Gagal menyimpan data. Silakan coba lagi.');
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 2000);
                });
            });

            // Handle tab switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Update button styles
                    document.querySelectorAll('.tab-btn').forEach(b => {
                        b.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                        b.classList.add('text-gray-600');
                    });
                    this.classList.remove('text-gray-600');
                    this.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                    
                    // Show/hide panels
                    document.querySelectorAll('.tab-panel').forEach(panel => {
                        if (panel.getAttribute('data-tab') === tabName) {
                            panel.classList.remove('hidden');
                        } else {
                            panel.classList.add('hidden');
                        }
                    });
                });
            });

            // Buat section pertama otomatis
            createSection();
        });
    </script>
@endpush