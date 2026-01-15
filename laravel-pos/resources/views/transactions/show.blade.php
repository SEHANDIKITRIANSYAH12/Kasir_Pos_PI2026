<x-app-layout>
    <x-slot name="title">Detail Transaksi</x-slot>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card" id="receipt">
                <div class="card-header text-center">
                    <h5 class="card-title mb-0">Struk Belanja</h5>
                    <p class="mb-0">Kasir POS PD BAGUS PUTRA</p>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6"><strong>No. Transaksi:</strong></div>
                        <div class="col-6 text-end">{{ $transaction->id }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Tanggal:</strong></div>
                        <div class="col-6 text-end">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Kasir:</strong></div>
                        <div class="col-6 text-end">{{ $transaction->user->name }}</div>
                    </div>

                    <hr>

                    <table class="table table-sm table-borderless">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr>

                    <div class="row">
                        <div class="col-6"><strong>Total:</strong></div>
                        <div class="col-6 text-end"><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></div>
                    </div>
                    <div class="row">
                        <div class="col-6">Bayar:</div>
                        <div class="col-6 text-end">Rp {{ number_format($transaction->paid, 0, ',', '.') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6">Kembali:</div>
                        <div class="col-6 text-end">Rp {{ number_format($transaction->change, 0, ',', '.') }}</div>
                    </div>

                    <div class="row">
                        <div class="col-6">Metode Pembayaran:</div>
                        <div class="col-6 text-end">{{ ucfirst($transaction->payment_method) }}</div>
                    </div>

                    <hr>

                    <p class="text-center mt-3">Terima kasih telah berbelanja!</p>
                </div>
            </div>

            <div class="text-center mt-4 d-print-none">
                <button class="btn btn-primary" onclick="printReceipt()">Cetak Struk</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Transaksi Baru</a>
            </div>
        </div>
    </div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #receipt, #receipt * {
            visibility: visible;
        }
        #receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .d-print-none {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function printReceipt() {
        window.print();
    }
</script>
@endpush

</x-app-layout>
