{{-- filepath: resources/views/penawaran/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Penawaran - {{ $penawaran->no_penawaran }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            padding: 15px;
        }

        .container {
            max-width: 100%;
        }

        /* Header */
        .header {
            margin-bottom: 15px;
            text-align: center;
        }

        .header img {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            display: block;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 12px;
        }

        .info-section p {
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .info-section .greeting {
            margin-top: 8px;
            margin-bottom: 8px;
        }

        /* Section */
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 6px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 0;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 5px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        table thead th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }

        /* Column Widths */
        table th:nth-child(1),
        table td:nth-child(1) {
            width: 4%;
            text-align: center;
        }

        table th:nth-child(2),
        table td:nth-child(2) {
            width: 12%;
        }

        table th:nth-child(3),
        table td:nth-child(3) {
            width: 38%;
        }

        table th:nth-child(4),
        table td:nth-child(4) {
            width: 6%;
            text-align: center;
        }

        table th:nth-child(5),
        table td:nth-child(5) {
            width: 8%;
            text-align: center;
        }

        table th:nth-child(6),
        table td:nth-child(6) {
            width: 16%;
            text-align: right;
        }

        table th:nth-child(7),
        table td:nth-child(7) {
            width: 16%;
            text-align: right;
        }

        table tbody tr.subtotal td {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .pre-wrap {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Summary */
        .summary {
            margin-top: 15px;
            width: 100%;
        }

        .summary-inner {
            float: right;
            width: 280px;
        }

        .summary-table {
            width: 100%;
            border: none;
            font-size: 11px;
        }

        .summary-table td {
            border: none;
            padding: 4px 0;
        }

        .summary-table td:first-child {
            font-weight: bold;
            text-align: left;
        }

        .summary-table td:last-child {
            text-align: right;
        }

        .summary-table tr.grand-total {
            border-top: 2px solid #333;
        }

        .summary-table tr.grand-total td {
            padding-top: 6px;
            font-size: 12px;
        }

        /* Notes */
        .notes {
            clear: both;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #333;
        }

        .notes h4 {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .notes ol {
            margin-left: 18px;
            padding-left: 0;
        }

        .notes ol li {
            margin-bottom: 3px;
            line-height: 1.4;
        }

        .notes ol li .indent {
            display: block;
            margin-left: 0;
            margin-top: 2px;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
        }

        .footer p {
            margin-bottom: 3px;
        }

        .signature {
            margin-top: 50px;
        }

        .signature-line {
            display: inline-block;
            width: 180px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .signature-name {
            font-size: 10px;
            margin-top: 3px;
        }

        /* Clear floats */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Page Break */
        @page {
            margin: 1cm;
        }

        @media print {
            body {
                padding: 0;
            }

            .section {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('assets/banner.png') }}" alt="Kop Perusahaan">
        </div>
        <!-- Header -->

        <!-- Info Penawaran -->
        <div class="info-section">
            <p><strong>Surabaya,
                    {{ \Carbon\Carbon::parse($penawaran->created_at ?? now())->locale('id')->translatedFormat('F Y') }}</strong>
            </p>
            <p style="margin-top: 20px;">Kepada Yth:</p>
            <p><strong>{{ $penawaran->nama_perusahaan }}</strong></p>
            <p>{{ $penawaran->lokasi }}</p>
            @if ($penawaran->pic_perusahaan)
                <p><strong>Up. {{ $penawaran->pic_perusahaan }}</strong></p>
            @endif

            <p style="margin-top: 20px;"><strong>Perihal:</strong> {{ $penawaran->perihal }}</p>
            <p><strong>No:</strong> {{ $penawaran->no_penawaran }}</p>

            <p class="greeting" style="margin-top: 20px;"><strong>Dengan Hormat,</strong></p>
            <p>Bersama ini kami PT. Puterako Inti Buana memberitahukan Penawaran Harga {{ $penawaran->perihal }} dengan
                perincian sebagai berikut:</p>
        </div>

        <!-- Sections -->
        @php
            $groupedSections = collect($sections)->groupBy('nama_section');
            $sectionNumber = 0;

            function convertToRoman($num)
            {
                $map = [
                    'M' => 1000,
                    'CM' => 900,
                    'D' => 500,
                    'CD' => 400,
                    'C' => 100,
                    'XC' => 90,
                    'L' => 50,
                    'XL' => 40,
                    'X' => 10,
                    'IX' => 9,
                    'V' => 5,
                    'IV' => 4,
                    'I' => 1,
                ];
                $result = '';
                foreach ($map as $roman => $value) {
                    while ($num >= $value) {
                        $result .= $roman;
                        $num -= $value;
                    }
                }
                return $result;
            }
        @endphp

        @foreach ($groupedSections as $namaSection => $sectionGroup)
            @php $sectionNumber++; @endphp

            <div class="section">
                <h3 class="section-title">
                    {{ convertToRoman($sectionNumber) }}. {{ $namaSection ?: 'Section ' . $sectionNumber }}
                </h3>

                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga Satuan</th>
                            <th>Harga Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $subtotal = 0; @endphp
                        @foreach ($sectionGroup as $section)
                            @foreach ($section['data'] as $row)
                                @php $subtotal += $row['harga_total']; @endphp
                                <tr>
                                    <td>{{ $row['no'] }}</td>
                                    <td>{{ $row['tipe'] }}</td>
                                    <td>
                                        <div class="pre-wrap">{{ $row['deskripsi'] }}</div>
                                    </td>
                                    <td>{{ number_format($row['qty'], 0) }}</td>
                                    <td>{{ $row['satuan'] }}</td>
                                    <td>{{ number_format($row['harga_satuan'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($row['harga_total'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr class="subtotal">
                            <td colspan="6" style="text-align: right;">Sub Total</td>
                            <td style="text-align: right;">{{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <!-- Summary -->
        <div class="summary clearfix">
            <div class="summary-inner">
                <table class="summary-table">
                    <tr>
                        <td>Total</td>
                        <td>Rp {{ number_format($penawaran->total ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    @if ($penawaran->is_best_price)
                        <tr>
                            <td>Best Price</td>
                            <td>Rp {{ number_format($penawaran->best_price ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td>PPN {{ number_format((float) ($penawaran->ppn_persen ?? 11), 0, ',', '.') }}%</td>
                        <td>Rp {{ number_format($penawaran->ppn_nominal ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>Grand Total</td>
                        <td>Rp {{ number_format($penawaran->grand_total ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Notes -->
        <div class="notes">
            <h4>NOTE:</h4>
            @if (!empty($penawaran->note))
                <ol>
                    @foreach (explode("\n", $penawaran->note) as $note)
                        @if (trim($note) !== '')
                            <li>{{ $note }}</li>
                        @endif
                    @endforeach
                </ol>
            @else
                <p class="text-gray-500 text-sm">Belum ada catatan penawaran.</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Demikian penawaran ini kami sampaikan</p>
            <p style="margin-top: 8px;"><strong>Hormat kami,</strong></p>
            <div class="signature">
                <p class="signature-line"></p>
                <p class="signature-name">Junly Kodradjaya</p>
            </div>
        </div>
    </div>
</body>

</html>
