<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\User;
use App\OrderChannel;
use App\OrderStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $demo = $this->createBusiness(
            'Sabor del Valle',
            'sabor-co',
            '+57 300 123 4567',
            'demo@foodsama.test',
            'Santander de Quilichao',
        );

        $other = $this->createBusiness(
            'La Esquina Saludable',
            'la-esquina-saludable',
            '+57 310 444 2211',
            'otro@foodsama.test',
            'Norte del Cauca',
        );

        $menu = collect([
            ['category' => 'Platos principales', 'name' => 'Lomo saltado', 'description' => 'Salteado de lomo con papas, cebolla y ají.', 'price' => 32, 'cost' => 14],
            ['category' => 'Platos principales', 'name' => 'Ajiaco de pollo', 'description' => 'Ajiaco cremoso con pollo y papas nativas.', 'price' => 28, 'cost' => 11],
            ['category' => 'Platos principales', 'name' => 'Pizza margarita', 'description' => 'Salsa de tomate, mozzarella y albahaca.', 'price' => 36, 'cost' => 13],
            ['category' => 'Entradas', 'name' => 'Ceviche clásico', 'description' => 'Pescado fresco con limón, ají y cebolla.', 'price' => 30, 'cost' => 12],
            ['category' => 'Entradas', 'name' => 'Papas rellenas', 'description' => 'Papas rellenas de carne sazonada.', 'price' => 18, 'cost' => 7],
            ['category' => 'Bebidas', 'name' => 'Limonada natural', 'description' => 'Limonada recién exprimida, 500 ml.', 'price' => 9, 'cost' => 2],
            ['category' => 'Bebidas', 'name' => 'Chicha morada', 'description' => 'Bebida tradicional de maíz morado.', 'price' => 8, 'cost' => 2],
            ['category' => 'Postres', 'name' => 'Tres leches', 'description' => 'Bizcocho bañado en tres leches.', 'price' => 14, 'cost' => 5],
        ])->map(fn (array $item, int $index): MenuItem => $demo->menuItems()->create([
            'category' => $item['category'],
            'name' => $item['name'],
            'description' => $item['description'],
            'price' => $item['price'],
            'cost' => $item['cost'],
            'is_available' => true,
            'sort_order' => $index,
        ]));

        $other->menuItems()->createMany([
            ['category' => 'Bowls', 'name' => 'Bowl de quinoa', 'price' => 29, 'cost' => 10, 'is_available' => true],
            ['category' => 'Bebidas', 'name' => 'Smoothie verde', 'price' => 15, 'cost' => 5, 'is_available' => true],
        ]);

        $this->createOrder($demo, 'Camila Torres', now()->subDays(1), [
            ['item' => $menu[0], 'quantity' => 2],
            ['item' => $menu[5], 'quantity' => 2],
        ], OrderStatus::Delivered, true);

        $this->createOrder($demo, 'Miguel Ramírez', now()->subDays(2), [
            ['item' => $menu[1], 'quantity' => 1],
            ['item' => $menu[3], 'quantity' => 1],
        ], OrderStatus::Delivered, true);

        $this->createOrder($demo, 'Lucía Pérez', now()->subHours(5), [
            ['item' => $menu[2], 'quantity' => 1],
            ['item' => $menu[7], 'quantity' => 2],
        ], OrderStatus::Preparing, false);

        $this->createOrder($demo, 'Andrés López', now()->subHours(2), [
            ['item' => $menu[4], 'quantity' => 2],
            ['item' => $menu[6], 'quantity' => 3],
        ], OrderStatus::Pending, false);

        $this->createOrder($other, 'María Cubas', now()->subDays(1), [
            ['name' => 'Bowl de quinoa', 'price' => 29, 'cost' => 10, 'quantity' => 1],
        ], OrderStatus::Delivered, true);
    }

    /**
     * Create a business and its owner account.
     */
    private function createBusiness(
        string $name,
        string $slug,
        string $phone,
        string $email,
        string $address,
    ): Business {
        $business = Business::create([
            'name' => $name,
            'slug' => $slug,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'tax_rate' => 0,
            'currency' => 'COP',
        ]);

        User::create([
            'name' => Str::before($email, '@') === 'demo' ? 'Ana Demostración' : 'Bruno Saludable',
            'email' => $email,
            'password' => Hash::make('password'),
            'business_id' => $business->id,
            'role' => 'owner',
        ]);

        return $business;
    }

    /**
     * Create a seeded order with snapshot lines and an optional receipt.
     *
     * @param  array<int, array{item: MenuItem, quantity: int}|array{name: string, price: float|int, cost: float|int, quantity: int}>  $lines
     */
    private function createOrder(
        Business $business,
        string $customerName,
        CarbonInterface $orderedAt,
        array $lines,
        OrderStatus $status,
        bool $withReceipt,
    ): void {
        $business->increment('order_counter');
        $business->refresh();

        $subtotal = 0.0;
        $costTotal = 0.0;
        $snapshotLines = [];

        foreach ($lines as $line) {
            $name = $line['name'] ?? $line['item']->name;
            $price = (float) ($line['price'] ?? $line['item']->price);
            $cost = (float) ($line['cost'] ?? $line['item']->cost);
            $quantity = (int) $line['quantity'];

            $subtotal += $price * $quantity;
            $costTotal += $cost * $quantity;
            $snapshotLines[] = [
                'menu_item_id' => isset($line['item']) ? $line['item']->id : null,
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $price,
                'unit_cost' => $cost,
            ];
        }

        $order = Order::create([
            'business_id' => $business->id,
            'order_number' => sprintf('PED-%06d', $business->order_counter),
            'customer_name' => $customerName,
            'customer_phone' => '+57 310 555 6677',
            'channel' => fake()->randomElement(OrderChannel::cases()),
            'origin' => 'phone',
            'status' => $status,
            'subtotal' => round($subtotal, 2),
            'tax' => 0,
            'total' => round($subtotal, 2),
            'cost_total' => round($costTotal, 2),
            'profit' => round($subtotal - $costTotal, 2),
            'delivered_at' => $status === OrderStatus::Delivered ? $orderedAt : null,
            'paid_at' => $withReceipt ? $orderedAt : null,
            'completed_at' => $status === OrderStatus::Delivered && $withReceipt ? $orderedAt : null,
            'created_at' => $orderedAt,
            'updated_at' => $orderedAt,
        ]);

        foreach ($snapshotLines as $snapshotLine) {
            $order->items()->create($snapshotLine);
        }

        if ($withReceipt) {
            $business->increment('receipt_counter');
            $business->refresh();

            Receipt::create([
                'business_id' => $business->id,
                'order_id' => $order->id,
                'number' => sprintf('C-%06d', $business->receipt_counter),
                'issued_at' => $orderedAt,
                'customer_name' => $customerName,
                'customer_phone' => $order->customer_phone,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'total' => $order->total,
            ]);
        }
    }
}
