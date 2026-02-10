<x-app-layout>
    <x-slot name="title">Daftar Produk</x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daftar Produk</h5>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Produk Baru</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama</th>
                                    <th>SKU</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration + $products->firstItem() - 1 }}</td>
                                        <td>
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="50" class="img-fluid rounded">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->sku }}</td>
                                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $product->stock <= 10 ? 'bg-warning' : 'bg-success' }}">
                                                {{ $product->stock }}
                                            </span>
                                            @if($product->stock <= 10)
                                                <i class="fas fa-exclamation-triangle text-warning ms-1" title="Stok rendah"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .pagination .page-link {
            color: #2dce89; /* Warna teks hijau */
            background-color: #fff;
            border-color: #dee2e6;
        }
        .pagination .page-item.active .page-link {
            background-color: #2dce89; /* Warna background hijau */
            border-color: #2dce89;
            color: white;
        }
        .pagination .page-link:hover {
            color: #24a46d; /* Warna teks hijau lebih gelap saat hover */
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }
    </style>
    @endpush
</x-app-layout>
