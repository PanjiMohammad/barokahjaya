<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pengembalian Pesanan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 70px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ccc;
            padding: 10px;
            vertical-align: top;
            text-align: left;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .invoice-id {
            font-size: 12px;
            color: #000;
        }

        .customer-info p {
            margin: 2px 0;
            line-height: 1.3;
            text-align: justify;
            text-justify: inter-word;
        }
    </style>
</head>
<body>
    @php
        // transform format tanggal
        $startDate = Carbon\Carbon::parse($date[0])->locale('id')->translatedFormat('l, d F Y');
        $endDate = Carbon\Carbon::parse($date[1])->locale('id')->translatedFormat('l, d F Y');
        $dates = '(' . $startDate . ' - ' . $endDate . ')';
    @endphp

    <header>
        <div class="report-title">Laporan Pengembalian Pesanan</div>
        <div>Periode: {{ $dates }}</div>
    </header>

    <footer>
        {{-- @php
            if (isset($pdf) && method_exists($pdf, 'page_script')) {
                $pdf->page_script('
                    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                    $size = 10;
                    $pageText = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
                    $pdf->text(500, 820, $pageText, $font, $size);
                ');
            }
        @endphp --}}
        Dicetak pada {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y H:i') }}
    </footer>

    {{-- <table width="100%" class="table-hover table-bordered">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse ($orders as $row)
                <tr>
                    <td><strong>{{ $row->invoice }}</strong></td>
                    <td>
                        <strong>{{ $row->customer_name }}</strong><br>
                        <label><strong>Telp:</strong> {{ $row->customer_phone }}</label><br>
                        <label><strong>Alamat:</strong> {{ $row->customer_address }} {{ $row->customer->district->name }} - {{  $row->customer->district->city->name}}, {{ $row->customer->district->city->province->name }}</label>
                    </td>
                    <td>Rp {{ number_format($row->total) }}</td>
                    <td>{{ $row->created_at->format('d-m-Y') }}</td>
                </tr>

                @php $total += $row->total @endphp
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td>Rp {{ number_format($total) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table> --}}

    <main>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 22%;">Tanggal</th>
                    <th style="width: 25%;">Invoice</th>
                    <th style="width: 40%;">Pelanggan</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @forelse ($orders as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->created_at)->locale('id')->translatedFormat('l, d M Y') }}</td>
                        <td class="invoice-id">{{ $row->invoice }}</td>
                        <td class="customer-info">
                            <p><strong>{{ $row->customer_name . ' (' . $row->customer_phone . ')' }}</strong></p>
                            <p>Email: {{ $row->customer->email }}</p>
                            <p>
                                Alamat: {{ $row->customer_address }},
                                Kecamatan {{ $row->customer->district->name }},
                                Kota {{ $row->customer->district->city->name }},
                                {{ $row->customer->district->city->province->name }}
                            </p>
                        </td>
                        <td>Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @php $grandTotal += $row->subtotal; @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total</td>
                    <td colspan="1">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>