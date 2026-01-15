<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('transactions.index', compact('products'));
    }

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
                $product = Product::find($item['id']); // Re-fetch product to lock for update
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
        // Pastikan kasir hanya bisa melihat transaksinya sendiri, kecuali admin
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
