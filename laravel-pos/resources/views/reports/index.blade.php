<x-app-layout>
    <x-slot name="title">Laporan Penjualan</x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Filter Laporan Penjualan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-5">
                                <label for="end_date">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Hasil Laporan</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="text-sm mb-0">
                            Menampilkan laporan dari tanggal <strong>{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }}</strong> sampai <strong>{{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</strong>
                        </p>
                        <button onclick="generatePDF()" class="btn btn-danger btn-sm">
                            <i class="fa fa-file-pdf-o me-1"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <strong>Total Pendapatan:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Kasir</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $transaction->user->name }}</td>
                                        <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada transaksi pada rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script>
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Header
    doc.setFontSize(20);
    doc.setFont('helvetica', 'bold');
    doc.text('PD BAGUS PUTRA POS', 105, 20, { align: 'center' });

    doc.setFontSize(16);
    doc.setFont('helvetica', 'normal');
    doc.text('LAPORAN PENJUALAN', 105, 30, { align: 'center' });

    // Info
    doc.setFontSize(12);
    doc.text(`Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}`, 20, 50);
    doc.text(`Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}`, 20, 58);

    // Total
    doc.setFont('helvetica', 'bold');
    doc.text(`TOTAL PENDAPATAN: Rp {{ number_format($totalRevenue, 0, ',', '.') }}`, 105, 70, { align: 'center' });

    // Table data
    const tableData = [
        ['No. Transaksi', 'Tanggal', 'Kasir', 'Total'],
        @forelse ($transactions as $transaction)
        ['{{ $transaction->id }}', '{{ $transaction->created_at->format('d/m/Y H:i') }}', '{{ $transaction->user->name }}', 'Rp {{ number_format($transaction->total, 0, ',', '.') }}'],
        @empty
        ['Tidak ada transaksi', '', '', ''],
        @endforelse
    ];

    // Add table
    doc.autoTable({
        head: [tableData[0]],
        body: tableData.slice(1),
        startY: 85,
        theme: 'grid',
        styles: { fontSize: 10, cellPadding: 3 },
        headStyles: { fillColor: [66, 139, 202], textColor: 255 },
        alternateRowStyles: { fillColor: [245, 245, 245] }
    });

    // Summary if there are transactions
    @if($transactions->count() > 0)
    const finalY = doc.lastAutoTable.finalY || 85;
    doc.setFontSize(11);
    doc.text(`Jumlah Transaksi: {{ $transactions->count() }} transaksi`, 20, finalY + 10);
    doc.text(`Rata-rata per Transaksi: Rp {{ number_format($totalRevenue / $transactions->count(), 0, ',', '.') }}`, 20, finalY + 18);
    @endif

    // Footer
    doc.setFontSize(10);
    doc.text('Laporan ini dicetak secara otomatis dari sistem PD BAGUS PUTRA POS', 105, 280, { align: 'center' });

    // Save PDF
    const fileName = `Laporan_Penjualan_{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}_{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}.pdf`;
    doc.save(fileName);
}
</script>
