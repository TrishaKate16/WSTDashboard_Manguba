<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard');
    }

    public function chartData(): JsonResponse
    {
        $categoryProducts = DB::table('categories')
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as label')
            ->selectRaw('COUNT(products.id) as value')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        $averagePriceByCategory = DB::table('categories')
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as label')
            ->selectRaw('COALESCE(AVG(products.price), 0) as value')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        $inventoryByCategory = DB::table('categories')
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as label')
            ->selectRaw('COALESCE(SUM(products.quantity), 0) as value')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        $stockStatus = DB::table('products')
            ->select('status as label')
            ->selectRaw('COUNT(*) as value')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $priceBands = DB::table('products')
            ->selectRaw("
                CASE
                    WHEN price < 50 THEN 'Under $50'
                    WHEN price < 150 THEN '$50–$149.99'
                    WHEN price < 300 THEN '$150–$299.99'
                    ELSE '$300 and above'
                END as label
            ")
            ->selectRaw('COUNT(*) as value')
            ->groupBy('label')
            ->get()
            ->keyBy('label');

        $priceBandLabels = ['Under $50', '$50–$149.99', '$150–$299.99', '$300 and above'];

        $topPricedProducts = Product::query()
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->orderByDesc('products.price')
            ->orderBy('products.name')
            ->limit(10)
            ->get(['products.name', 'categories.name as category', 'products.price']);

        $lowestPricedProducts = Product::query()
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->orderBy('products.price')
            ->orderBy('products.name')
            ->limit(10)
            ->get(['products.name', 'categories.name as category', 'products.price']);

        $monthlyLabels = collect(range(11, 0))
            ->map(fn (int $offset) => CarbonImmutable::now()->startOfMonth()->subMonths($offset))
            ->values();
        $monthKeys = $monthlyLabels->map(fn (CarbonImmutable $month) => $month->format('Y-m'));
        $monthNames = $monthlyLabels->map(fn (CarbonImmutable $month) => $month->format('M Y'));

        $monthlyOrders = Order::query()
            ->whereDate('date', '>=', $monthlyLabels->first()->toDateString())
            ->get(['date'])
            ->groupBy(fn (Order $order) => $order->date->format('Y-m'));

        $orderItems = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->get(['orders.date', 'order_products.quantity']);
        $monthlyUnits = $orderItems->groupBy(
            fn (object $item) => CarbonImmutable::parse($item->date)->format('Y-m')
        );

        $bestSelling = DB::table('order_products')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.name', 'categories.name as category')
            ->selectRaw('SUM(order_products.quantity) as value')
            ->groupBy('products.name', 'categories.name')
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        $highestRevenue = DB::table('order_products')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.name', 'categories.name as category')
            ->selectRaw('SUM(order_products.quantity * products.price) as value')
            ->groupBy('products.name', 'categories.name')
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        $lineItems = DB::table('orders')
            ->leftJoin('order_products', 'order_products.order_id', '=', 'orders.id')
            ->select('orders.id')
            ->selectRaw('COUNT(order_products.product_id) as item_count')
            ->groupBy('orders.id')
            ->get()
            ->groupBy(fn (object $order) => min((int) $order->item_count, 4));

        $ratingDistribution = DB::table('reviews')
            ->select('rating as label')
            ->selectRaw('COUNT(*) as value')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        $averageRatingByCategory = DB::table('categories')
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->leftJoin('reviews', 'reviews.product_id', '=', 'products.id')
            ->select('categories.name as label')
            ->selectRaw('COALESCE(AVG(reviews.rating), 0) as value')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        $monthlyReviews = Review::query()
            ->whereDate('created_at', '>=', $monthlyLabels->first()->toDateString())
            ->get(['created_at'])
            ->groupBy(fn (Review $review) => $review->created_at->format('Y-m'));

        $chart = fn (
            string $title,
            string $type,
            array $labels,
            array $values,
            string $datasetLabel,
            string $color = '#db2777',
            string $format = 'number',
            ?string $indexAxis = null
        ): array => [
            'title' => $title,
            'type' => $type,
            'labels' => $labels,
            'values' => $values,
            'datasetLabel' => $datasetLabel,
            'color' => $color,
            'format' => $format,
            'indexAxis' => $indexAxis,
        ];

        $labelsAndValues = fn ($rows): array => [
            $rows->pluck('label')->map(fn ($label) => (string) $label)->all(),
            $rows->pluck('value')->map(fn ($value) => (float) $value)->all(),
        ];

        [$categoryLabels, $productCounts] = $labelsAndValues($categoryProducts);
        [$priceCategoryLabels, $averagePrices] = $labelsAndValues($averagePriceByCategory);
        [$inventoryLabels, $inventoryCounts] = $labelsAndValues($inventoryByCategory);
        [$stockLabels, $stockCounts] = $labelsAndValues($stockStatus);
        [$topPriceLabels, $topPriceValues] = [
            $topPricedProducts->map(fn ($product) => "{$product->name} ({$product->category})")->all(),
            $topPricedProducts->pluck('price')->map(fn ($value) => (float) $value)->all(),
        ];
        [$lowestPriceLabels, $lowestPriceValues] = [
            $lowestPricedProducts->map(fn ($product) => "{$product->name} ({$product->category})")->all(),
            $lowestPricedProducts->pluck('price')->map(fn ($value) => (float) $value)->all(),
        ];
        [$sellingLabels, $sellingValues] = [
            $bestSelling->map(fn ($product) => "{$product->name} ({$product->category})")->all(),
            $bestSelling->pluck('value')->map(fn ($value) => (float) $value)->all(),
        ];
        [$revenueLabels, $revenueValues] = [
            $highestRevenue->map(fn ($product) => "{$product->name} ({$product->category})")->all(),
            $highestRevenue->pluck('value')->map(fn ($value) => (float) $value)->all(),
        ];
        [$ratingCategoryLabels, $averageRatings] = $labelsAndValues($averageRatingByCategory);

        return response()->json([
            'summary' => [
                'products' => Product::count(),
                'categories' => Category::count(),
                'orders' => Order::count(),
                'reviews' => Review::count(),
            ],
            'charts' => [
                $chart('Products by Category', 'doughnut', $categoryLabels, $productCounts, 'Products', '#be185d'),
                $chart('Average Product Price by Category', 'bar', $priceCategoryLabels, $averagePrices, 'Average price', '#db2777', 'currency'),
                $chart('Inventory Units by Category', 'bar', $inventoryLabels, $inventoryCounts, 'Units in stock', '#ec4899'),
                $chart('Products by Availability Status', 'doughnut', $stockLabels, $stockCounts, 'Products', '#f472b6'),
                $chart(
                    'Products by Price Range',
                    'pie',
                    $priceBandLabels,
                    collect($priceBandLabels)->map(fn (string $label) => (float) ($priceBands->get($label)->value ?? 0))->all(),
                    'Products',
                    '#f9a8d4'
                ),
                $chart('10 Highest-Priced Products', 'bar', $topPriceLabels, $topPriceValues, 'Price', '#e11d48', 'currency', 'y'),
                $chart('10 Lowest-Priced Products', 'bar', $lowestPriceLabels, $lowestPriceValues, 'Price', '#fda4af', 'currency', 'y'),
                $chart(
                    'Orders by Month',
                    'line',
                    $monthNames->all(),
                    $monthKeys->map(fn (string $key) => (float) ($monthlyOrders->get($key)?->count() ?? 0))->all(),
                    'Orders',
                    '#db2777'
                ),
                $chart(
                    'Units Sold by Month',
                    'bar',
                    $monthNames->all(),
                    $monthKeys->map(fn (string $key) => (float) ($monthlyUnits->get($key)?->sum('quantity') ?? 0))->all(),
                    'Units sold',
                    '#fb7185'
                ),
                $chart('Top 10 Best-Selling Products', 'bar', $sellingLabels, $sellingValues, 'Units sold', '#c026d3', 'number', 'y'),
                $chart('Top 10 Products by Sales Value', 'bar', $revenueLabels, $revenueValues, 'Sales value', '#9d174d', 'currency', 'y'),
                $chart(
                    'Orders by Number of Different Products',
                    'bar',
                    ['0 products', '1 product', '2 products', '3 products', '4+ products'],
                    collect(range(0, 4))->map(fn (int $count) => (float) ($lineItems->get($count)?->count() ?? 0))->all(),
                    'Orders',
                    '#f472b6'
                ),
                $chart('Reviews by Rating', 'doughnut', ...[
                    ...$labelsAndValues($ratingDistribution),
                    'Reviews',
                    '#ec4899',
                ]),
                $chart('Average Review Rating by Category', 'bar', $ratingCategoryLabels, $averageRatings, 'Average rating (1–5)', '#be185d'),
                $chart(
                    'Reviews by Month',
                    'line',
                    $monthNames->all(),
                    $monthKeys->map(fn (string $key) => (float) ($monthlyReviews->get($key)?->count() ?? 0))->all(),
                    'Reviews',
                    '#e11d48'
                ),
            ],
        ]);
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Category $category)
    {
        //
    }

    public function update(Request $request, Category $category)
    {
        //
    }

    public function destroy(Category $category)
    {
        //
    }
}
