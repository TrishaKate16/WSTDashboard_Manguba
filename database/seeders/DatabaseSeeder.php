<?php 
 
namespace Database\Seeders;  
 
use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
 
class DatabaseSeeder extends Seeder 
{ 
    public function run(): void 
    { 
        $categoryCatalog = [
            'Electronics' => ['Bluetooth Headphones', 'Mechanical Keyboard', 'Smartphone Charger'],
            'Books' => ['Programming Book', 'Notebook', 'Ballpoint Pen'],
            'Hardware' => ['Hammer', 'Cordless Drill', 'Adjustable Wrench'],
            'Electrical' => ['Extension Wire', 'Light Bulb', 'Circuit Breaker'],
            'Plumbing' => ['Water Pipe', 'Kitchen Faucet', 'PVC Elbow'],
            'Clothing' => ['Cotton T-Shirt', 'Casual Jacket', 'Running Shoes'],
        ];

        $categories = collect($categoryCatalog)->map(function (array $catalog, string $name) {
            return Category::firstOrCreate(
                ['name' => $name],
                ['description' => "A collection of {$name} products."]
            );
        });

        $products = collect();

        foreach ($categoryCatalog as $categoryName => $catalog) {
            $category = $categories->get($categoryName);

            for ($index = 0; $index < 400; $index++) {
                $products->push(
                    Product::factory()
                        ->for($category)
                        ->create([
                            'name' => $catalog[$index % count($catalog)],
                        ])
                );
            }
        }

        $users = User::factory(20)->create();

        $users->each(function (User $user): void {
            Address::factory(random_int(1, 2))->for($user)->create();
        });

        $orders = $users->flatMap(function (User $user): \Illuminate\Support\Collection {
            return Order::factory(random_int(0, 5))->for($user)->create();
        });

        $orders->each(function (Order $order) use ($products): void {
            $orderProducts = $products->random(random_int(1, 4));
            foreach ($orderProducts as $product) {
                OrderProduct::factory()
                    ->for($order)
                    ->for($product)
                    ->create();
            }
        });

        $products->each(function (Product $product) use ($users): void {
            $reviewers = $users->random(random_int(0, 8));
            foreach ($reviewers as $user) {
                Review::factory()
                    ->for($user)
                    ->for($product)
                    ->create();
            }
        });
    } 
} 
