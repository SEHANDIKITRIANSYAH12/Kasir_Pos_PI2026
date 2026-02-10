# Lampiran Kode Penting – Sistem Kasir POS

## 1) Middleware CheckRole (Pembatasan Akses Role)
```php
// app/Http/Middleware/CheckRole.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }
        return $next($request);
    }
}
```

---

## 2) Route dengan Role Middleware (web.php)
```php
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('users', UserController::class);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');
    });

    // Admin & Kasir Routes
    Route::middleware(['role:admin,kasir'])->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    });
});
```

---

## 3) Model User (Role & Relasi)
```php
// app/Models/User.php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

---

## 4) TransactionController (Validasi & Stok)
```php
// app/Http/Controllers/TransactionController.php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|json',
            'total' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:' . $request->total,
            'payment_method' => 'required|in:tunai,qris',
        ]);

        $items = json_decode($request->items, true);

        try {
            DB::beginTransaction();

            // 1. Check stock availability first
            foreach ($items as $item) {
                $product = Product::find($item['id']);
                if (!$product || $product->stock < $item['qty']) {
                    throw new \Exception('Stok produk ' . ($product->name ?? 'yang dipilih') . ' tidak mencukupi.');
                }
            }

            // 2. Create Transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'total' => $request->total,
                'paid' => $request->paid,
                'change' => $request->paid - $request->total,
                'payment_method' => $request->payment_method,
            ]);

            // 3. Create Transaction Items and Update Stock
            foreach ($items as $item) {
                $product = Product::find($item['id']);
                $transaction->items()->create([
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                ]);
                $product->decrement('stock', $item['qty']);
            }

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Transaksi berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Transaksi gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        if (auth()->user()->role == 'kasir' && $transaction->user_id !== auth()->id()) {
            abort(403);
        }
        $transaction->load('items.product');
        return view('transactions.show', compact('transaction'));
    }

    public function history()
    {
        if (auth()->user()->role == 'admin') {
            $transactions = Transaction::with('user')->latest()->paginate(10);
        } else {
            $transactions = Transaction::where('user_id', auth()->id())->latest()->paginate(10);
        }
        return view('transactions.history', compact('transactions'));
    }
}
```

---

## 5) DashboardController (Filter Data per Role)
```php
// app/Http/Controllers/DashboardController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = \App\Models\Product::count();
        $today = now()->startOfDay();
        $user = auth()->user();
        $isAdmin = $user->role == 'admin';

        // Query dasar untuk transaksi
        $baseTransactionQuery = $isAdmin
            ? \App\Models\Transaction::query()
            : \App\Models\Transaction::where('user_id', $user->id);

        // Data untuk card
        $todayTransactions = (clone $baseTransactionQuery)
            ->where('created_at', '>=', $today)
            ->count();

        $todayRevenue = (clone $baseTransactionQuery)
            ->where('created_at', '>=', $today)
            ->sum('total');

        // Data stok produk yang hampir habis (stok <= 10)
        $lowStockProducts = \App\Models\Product::select('name', 'stock', 'price')
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->limit(5)
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'price' => $product->price
                ];
            });

        return view('dashboard', [
            'totalProducts' => $totalProducts,
            'todayTransactions' => $todayTransactions,
            'todayRevenue' => $todayRevenue,
            'lowStockProducts' => $lowStockProducts
        ]);
    }
}
```

---

## 6) Alert/Flash Messages (layouts/partials/alerts.blade.php)
```blade
{{-- resources/views/layouts/partials/alerts.blade.php --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="ni ni-like-2"></i></span>
        <span class="alert-text"><strong>Sukses!</strong> {{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="ni ni-notification-70"></i></span>
        <span class="alert-text"><strong>Error!</strong> {{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="ni ni-notification-70"></i></span>
        <span class="alert-text">
            <strong>Validasi Gagal!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
```

---

## 7) JavaScript Validasi Transaksi (transactions/index.blade.php)
```javascript
// resources/views/transactions/index.blade.php (bagian script)
function addToCart(id, name, price, stock) {
    const item = cart.find(i => i.id === id);
    if (item) {
        if (item.qty >= stock) {
            alert(`Stok produk "${name}" hanya tersisa ${stock} pcs. Tidak bisa menambah lagi.`);
            return;
        }
        item.qty++;
    } else {
        if (stock <= 5) {
            const confirmAdd = confirm(`Peringatan: Stok produk "${name}" hanya tersisa ${stock} pcs. Tetap tambahkan ke keranjang?`);
            if (!confirmAdd) return;
        }
        cart.push({ id, name, price, qty: 1 });
    }
    updateCart();
}

document.getElementById('transaction-form').addEventListener('submit', function(e) {
    const total = Number(document.getElementById('total-input').value || 0);
    const paid = Number(document.getElementById('paid').value || 0);
    if (cart.length === 0) {
        e.preventDefault();
        alert('Keranjang masih kosong! Silakan tambahkan produk terlebih dahulu.');
        return;
    }
    if (paid < total) {
        e.preventDefault();
        alert('Maaf, uang anda kurang dari total belanja');
        return;
    }
    const confirmPay = confirm(`Apakah Anda yakin ingin membayar Rp ${total.toLocaleString('id-ID')}?`);
    if (!confirmPay) {
        e.preventDefault();
    }
});
```

---

## 8) Seeder User (UserSeeder.php)
```php
// database/seeders/UserSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@example.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);
    }
}
```

---

## 9) Migration Users (Role Field)
```php
// database/migrations/xxxx_xx_xx_xxxxxx_create_users_table.php
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('role')->default('kasir'); // admin/kasir
        $table->rememberToken();
        $table->timestamps();
    });
}
```

---

## 10) Migration Transactions
```php
// database/migrations/xxxx_xx_xx_xxxxxx_create_transactions_table.php
public function up(): void
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users');
        $table->decimal('total', 12, 2);
        $table->decimal('paid', 12, 2);
        $table->decimal('change', 12, 2);
        $table->enum('payment_method', ['tunai', 'qris']);
        $table->timestamps();
    });
}
```

---

## 11) Migration Transaction Items
```php
// database/migrations/xxxx_xx_xx_xxxxxx_create_transaction_items_table.php
public function up(): void
{
    Schema::create('transaction_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
        $table->foreignId('product_id')->constrained('products');
        $table->integer('qty');
        $table->decimal('price', 12, 2);
        $table->timestamps();
    });
}
```

---

### Catatan
- Semua kode di atas sudah **disederhanakan** agar mudah dipahami di lampiran penulisan ilmiah.
- Fokus pada **logika role, validasi, transaksi, dan notifikasi/alert**.
- Tidak termasuk styling Blade yang panjang agar lampiran tetap ringkas.
