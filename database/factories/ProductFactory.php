<?php 
 
namespace Database\Factories; 
 
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory; 
use Illuminate\Support\Str; 
 
class ProductFactory extends Factory 
{ 
    public function definition(): array 
    { 
        $products = [
            'Hammer',
            'Cordless Drill',
            'Adjustable Wrench',
            'Extension Wire',
            'Light Bulb',
            'Circuit Breaker',
            'Water Pipe',
            'Kitchen Faucet',
            'PVC Elbow',
            'Bluetooth Headphones',
            'Mechanical Keyboard',
            'Smartphone Charger',
            'Programming Book',
            'Notebook',
            'Ballpoint Pen',
            'Cotton T-Shirt',
            'Casual Jacket',
            'Running Shoes',
        ];

        $name = $this->faker->randomElement($products);
 
        return [ 
            'category_id' => Category::factory(),
            'name' => $name, 
            'description' => $this->faker->sentence(),
            'slug' => Str::slug($name) . '-' . Str::random(5), 
            'price' => $this->faker->randomFloat(2, 5, 500),
            'quantity' => $this->faker->numberBetween(1, 100), 
            'status' => 'Available',  
        ]; 
    } 
} 