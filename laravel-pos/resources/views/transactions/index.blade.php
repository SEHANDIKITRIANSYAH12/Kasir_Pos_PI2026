<x-app-layout>
    <x-slot name="title">Transaksi Baru</x-slot>

    <div class="row">
        {{-- Product List --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
    <h5 class="card-title">Daftar Produk</h5>
    <div class="input-group mt-2">
        <input type="text" id="search-product" class="form-control form-control-sm" placeholder="Cari nama produk...">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
    </div>
</div>
                <div class="card-body">
                    <div class="row">
                        @forelse ($products as $product)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
                                    @else
                                        <img src="https://via.placeholder.com/150?text=No+Image" class="card-img-top" alt="No Image" style="height: 150px; object-fit: cover;">
                                    @endif
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ $product->name }}</h6>
                                        <p class="card-text">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">Tambah</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center">Tidak ada produk yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Cart --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Keranjang</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('transactions.store') }}" method="POST" id="transaction-form">
                        @csrf
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <!-- Cart items will be injected here by JavaScript -->
                            </tbody>
                        </table>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="cart-total">Rp 0</strong>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <div>
                                <input type="radio" id="tunai" name="payment_method" value="tunai" checked onchange="toggleQris(false)">
                                <label for="tunai">Tunai</label>                            </div>
                        </div>
                        <div class="mt-3" id="qris-section" style="display:none;">
                            <label class="form-label">Scan QRIS untuk membayar:</label>
                            <div>
                                <img src="{{ asset('images/qris-demo.png') }}" alt="QRIS" style="width:200px;">
                            </div>
                            <small class="text-muted">Setelah pembayaran, klik tombol Bayar untuk konfirmasi.</small>
                        </div>

                        <div class="mt-3">
                            <label for="paid" class="form-label">Bayar</label>
                            <input type="number" id="paid" name="paid" class="form-control" required oninput="updateChange()">
                        </div>

                        <div class="mt-2">
                            <strong>Kembalian: </strong>
                            <span id="change-amount" class="fw-bold text-success">Rp 0</span>
                        </div>

                        <input type="hidden" name="items" id="items-input">
                        <input type="hidden" name="total" id="total-input">

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success w-100">Bayar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Fitur filter produk by nama
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-product');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll('.card.h-100').forEach(function(card) {
                    const title = card.querySelector('.card-title').innerText.toLowerCase();
                    card.parentElement.style.display = title.includes(keyword) ? '' : 'none';
                });
            });
        }
    });
    function toggleQris(show) {
        document.getElementById('qris-section').style.display = show ? 'block' : 'none';
    }
    document.addEventListener('DOMContentLoaded', function() {
        toggleQris(document.getElementById('qris') && document.getElementById('qris').checked);
    });
    let cart = [];

    function addToCart(id, name, price) {
        const item = cart.find(i => i.id === id);
        if (item) {
            item.qty++;
        } else {
            cart.push({ id, name, price, qty: 1 });
        }
        updateCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        updateCart();
    }

    function updateQuantity(id, qty) {
        const item = cart.find(i => i.id === id);
        if (item) {
            item.qty = parseInt(qty);
            if (item.qty <= 0) {
                removeFromCart(id);
            }
        }
        updateCart();
    }

    function updateChange() {
        const total = Number(document.getElementById('total-input').value || 0);
        const paid = Number(document.getElementById('paid').value || 0);
        const change = paid - total;
        const changeElement = document.getElementById('change-amount');

        if (change >= 0) {
            changeElement.textContent = `Rp ${change.toLocaleString('id-ID')}`;
            changeElement.className = 'fw-bold text-success';
        } else {
            changeElement.textContent = `-Rp ${Math.abs(change).toLocaleString('id-ID')}`;
            changeElement.className = 'fw-bold text-danger';
        }
    }

    function updateCart() {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');
        const itemsInput = document.getElementById('items-input');
        const totalInput = document.getElementById('total-input');

        cartItemsContainer.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            total += item.price * item.qty;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td><input type="number" value="${item.qty}" class="form-control form-control-sm" style="width: 60px;" onchange="updateQuantity(${item.id}, this.value)"></td>
                <td>${(item.price * item.qty).toLocaleString('id-ID')}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">X</button></td>
            `;
            cartItemsContainer.appendChild(row);
        });

        cartTotalElement.innerText = `Rp ${total.toLocaleString('id-ID')}`;
        itemsInput.value = JSON.stringify(cart);
        totalInput.value = total;
        updateChange(); // Update change when cart changes
    }

    document.getElementById('transaction-form').addEventListener('submit', function(e) {
        const total = Number(document.getElementById('total-input').value || 0);
        const paid = Number(document.getElementById('paid').value || 0);
        if (cart.length === 0) {
            e.preventDefault();
            alert('Keranjang masih kosong!');
            return;
        }
        if (paid < total) {
            e.preventDefault();
            alert('Maaf, uang anda kurang dari total belanja');
            return;
        }
    });
</script>
@endpush

</x-app-layout>
