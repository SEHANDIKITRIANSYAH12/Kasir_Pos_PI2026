<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan - PD BAGUS PUTRA POS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: normal;
        }
        .info {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-box {
            background: #f0f0f0;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            text-align: center;
        }
        .total-box strong {
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        td:nth-child(4) {
            text-align: right;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body {
                margin: 10px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PD BAGUS PUTRA POS</h1>
        <h2>LAPORAN PENJUALAN</h2>
    </div>

    <div class="info">
        <div class="info-row">
            <span><strong>Periode:</strong></span>
            <span>{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</span>
        </div>
        <div class="info-row">
            <span><strong>Tanggal Cetak:</strong></span>
            <span>{{ \Carbon\Carbon::now()->format('d F Y H:i') }}</span>
        </div>
    </div>

    <div class="total-box">
        <strong>TOTAL PENDAPATAN: Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="no-data">Tidak ada transaksi pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($transactions->count() > 0)
    <div class="summary">
        <table>
            <tr>
                <td><strong>Jumlah Transaksi:</strong></td>
                <td>{{ $transactions->count() }} transaksi</td>
            </tr>
            <tr>
                <td><strong>Rata-rata per Transaksi:</strong></td>
                <td>Rp {{ number_format($totalRevenue / $transactions->count(), 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini dicetak secara otomatis dari sistem PD BAGUS PUTRA POS</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fa fa-times"></i> Tutup
        </button>
    </div>
</body>
</html>
