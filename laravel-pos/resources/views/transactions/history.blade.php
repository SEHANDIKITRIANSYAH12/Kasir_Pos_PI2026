<x-app-layout>
    <x-slot name="title">Riwayat Transaksi</x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Riwayat Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    @if(auth()->user()->role == 'admin')
                                        <th>Kasir</th>
                                    @endif
                                    <th>Total</th>
                                    <th>Bayar</th>
                                    <th>Kembali</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                        @if(auth()->user()->role == 'admin')
                                            <td>{{ $transaction->user->name }}</td>
                                        @endif
                                        <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($transaction->paid, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($transaction->change, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role == 'admin' ? '7' : '6' }}" class="text-center">Tidak ada riwayat transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
