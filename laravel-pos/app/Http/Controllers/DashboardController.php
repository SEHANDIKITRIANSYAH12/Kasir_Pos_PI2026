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

        // Data untuk grafik pendapatan 6 bulan terakhir
        $revenueData = (clone $baseTransactionQuery)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Format data untuk chart.js
        $months = [];
        $revenues = [];
        
        // Generate 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $months[] = $monthYear;
            
            // Cari data untuk bulan ini
            $monthData = $revenueData->first(function ($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            });
            
            $revenues[] = $monthData ? (int)$monthData->total : 0;
        }

        // Data produk terlaris
        $topProducts = \App\Models\TransactionItem::with('product')
            ->select('product_id', \DB::raw('SUM(qty) as total_quantity'))
            ->when(!$isAdmin, function($query) use ($user) {
                return $query->whereHas('transaction', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->product->name,
                    'total' => $item->total_quantity
                ];
            });
            
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
            'revenueMonths' => $months,
            'revenueData' => $revenues,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts
        ]);
    }
}
