<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Total pendapatan
            $totalRevenue = Order::sum('total_amount') ?? 0;

            // Total order
            $totalOrders = Order::count() ?? 0;

            // Penjualan per bulan (hanya bulan yang ada transaksi)
            $salesByMonth = Order::select(
                    DB::raw('MONTH(order_date) as month'),
                    DB::raw('COALESCE(SUM(total_amount), 0) as total_sales')
                )
                ->whereNotNull('order_date')
                ->groupBy(DB::raw('MONTH(order_date)'))
                ->orderBy(DB::raw('MONTH(order_date)'))
                ->get();

            // Top 3 produk terlaris berdasarkan quantity
            $topProducts = Order::select('product_id', DB::raw('SUM(quantity) as total_sold'))
                ->with('product:product_id,nama')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->take(3)
                ->get();

            // 5 order terbaru
            $recentOrders = Order::with([
                    'user:id,name',
                    'product:product_id,nama'
                ])
                ->orderByDesc('order_date')
                ->take(5)
                ->get();

            // Format data agar aman untuk JSON
            $data = [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'sales_by_month' => $salesByMonth,
                'top_products' => $topProducts,
                'recent_orders' => $recentOrders,
            ];

            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
