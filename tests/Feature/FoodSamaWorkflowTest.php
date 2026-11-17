<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FoodSamaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_menu_is_tenant_scoped_and_repeated_submission_creates_one_order(): void
    {
        $business = Business::factory()->create(['slug' => 'sabor-del-valle']);
        $otherBusiness = Business::factory()->create();
        $menuItem = MenuItem::factory()->for($business)->create([
            'name' => 'Hamburguesa de la casa',
            'price' => '18000.00',
            'cost' => '10500.00',
        ]);
        MenuItem::factory()->for($business)->create([
            'name' => 'Producto agotado',
            'is_available' => false,
        ]);
        $otherBusinessItem = MenuItem::factory()->for($otherBusiness)->create([
            'name' => 'Producto de otro negocio',
        ]);

        $this->get(route('public.menu', $business->slug))
            ->assertOk()
            ->assertSee('Hamburguesa de la casa')
            ->assertDontSee('Producto agotado')
            ->assertDontSee('Producto de otro negocio');

        $key = (string) Str::uuid();
        $payload = [
            'customer_name' => 'Camila Torres',
            'customer_phone' => '+57 300 555 6677',
            'channel' => 'delivery',
            'delivery_address' => 'Calle 10 # 5-20',
            'idempotency_key' => $key,
            'items' => [
                $menuItem->id => ['quantity' => 2],
            ],
        ];

        $url = route('public.orders.store', $business->slug);
        $this->post($url, $payload)->assertRedirect();
        $order = Order::query()->where('idempotency_key', $key)->sole();

        $this->assertSame('36000.00', $order->total);
        $this->assertSame('web', $order->origin->value);
        $this->assertNull($order->paid_at);
        $this->assertSame('10500.00', $order->items->first()->unit_cost);
        $this->assertSame($business->id, $order->items->first()->business_id);

        $this->post($url, $payload)->assertRedirect();
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', [
            'business_id' => $business->id,
            'order_number' => $order->order_number,
            'subtotal' => '36000.00',
            'tax' => '0.00',
        ]);

        $this->post($url, [
            ...$payload,
            'idempotency_key' => (string) Str::uuid(),
            'items' => [$otherBusinessItem->id => ['quantity' => 1]],
        ])->assertSessionHasErrors("items.{$otherBusinessItem->id}.quantity");

        $this->assertDatabaseCount('orders', 1);
        $this->get(route('public.orders.show', [$business->slug, $order->public_token]))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Enviar resumen por WhatsApp');
    }

    public function test_payment_and_delivery_are_separate_and_issue_only_one_receipt(): void
    {
        $business = Business::factory()->create();
        $employee = User::factory()->for($business)->create(['role' => 'employee']);
        $menuItem = MenuItem::factory()->for($business)->create([
            'price' => '18000.00',
            'cost' => '10500.00',
        ]);
        $order = $this->createOrder($employee, $menuItem);
        $menuItem->update(['price' => '25000.00', 'cost' => '14000.00']);

        $this->actingAs($employee)
            ->patch(route('orders.payment', $order))
            ->assertRedirect();
        $order->refresh();
        $this->assertNotNull($order->paid_at);
        $this->assertNull($order->completed_at);
        $this->assertDatabaseCount('receipts', 0);

        $this->patch(route('orders.payment', $order))->assertRedirect();
        $this->assertDatabaseCount('order_events', 2);

        $this->patch(route('orders.status', $order), ['status' => 'preparing'])->assertRedirect();
        $this->patch(route('orders.status', $order), ['status' => 'ready'])->assertRedirect();
        $this->patch(route('orders.status', $order), ['status' => 'delivered'])->assertRedirect();

        $order->refresh();
        $this->assertNotNull($order->completed_at);
        $this->assertSame('delivered', $order->status->value);
        $this->assertSame('18000.00', $order->items->first()->unit_price);
        $this->assertSame('10500.00', $order->items->first()->unit_cost);
        $this->assertDatabaseCount('receipts', 1);
        $this->assertDatabaseCount('order_events', 5);
        $this->assertDatabaseMissing('order_events', [
            'order_id' => $order->id,
            'user_id' => null,
        ]);

        $this->post(route('receipts.store', $order))->assertRedirect();
        $this->assertDatabaseCount('receipts', 1);
        $this->get(route('receipts.show', $order->receipt))
            ->assertOk()
            ->assertDontSee('10500.00');
    }

    public function test_profit_report_uses_completed_sales_historical_costs_and_period_expenses(): void
    {
        $this->travelTo('2026-11-17 10:00:00');
        $business = Business::factory()->create();
        $owner = User::factory()->for($business)->create(['role' => 'owner']);
        $menuItem = MenuItem::factory()->for($business)->create([
            'price' => '18000.00',
            'cost' => '10500.00',
        ]);
        $order = $this->createOrder($owner, $menuItem, 10);
        $menuItem->update(['price' => '25000.00', 'cost' => '14000.00']);

        $this->actingAs($owner)
            ->patch(route('orders.status', $order), ['status' => 'preparing'])
            ->assertRedirect();
        $this->patch(route('orders.status', $order), ['status' => 'ready'])->assertRedirect();
        $this->patch(route('orders.status', $order), ['status' => 'delivered'])->assertRedirect();
        $this->patch(route('orders.payment', $order))->assertRedirect();
        $this->post(route('expenses.store'), [
            'concept' => 'Gastos operativos',
            'amount' => '25000.00',
            'expense_date' => '2026-11-17',
        ])->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'business_id' => $business->id,
            'concept' => 'Gastos operativos',
        ]);

        $response = $this->get(route('profits.index', [
            'start_date' => '2026-11-17',
            'end_date' => '2026-11-17',
        ]));

        $response->assertOk()->assertViewHas('report', function (array $report): bool {
            $this->assertSame('180000.00', $report['sales']);
            $this->assertSame('105000.00', $report['costs']);
            $this->assertSame('75000.00', $report['gross_profit']);
            $this->assertSame('25000.00', $report['expenses']);
            $this->assertSame('50000.00', $report['operating_result']);
            $this->assertSame(27.78, $report['margin']);

            return true;
        });

        $this->get(route('profits.export', [
            'start_date' => '2026-11-17',
            'end_date' => '2026-11-17',
        ]))->assertOk()->assertDownload('food-sama-reporte-2026-11-17-2026-11-17.csv');
    }

    public function test_report_marks_missing_costs_and_does_not_calculate_margin_without_sales(): void
    {
        $this->travelTo('2026-11-17 10:00:00');
        $business = Business::factory()->create();
        $owner = User::factory()->for($business)->create(['role' => 'owner']);
        $menuItem = MenuItem::factory()->for($business)->create([
            'price' => '18000.00',
            'cost' => null,
        ]);
        $order = $this->createOrder($owner, $menuItem);

        $this->actingAs($owner)
            ->patch(route('orders.status', $order), ['status' => 'preparing'])
            ->assertRedirect();
        $this->patch(route('orders.status', $order), ['status' => 'ready'])->assertRedirect();
        $this->patch(route('orders.status', $order), ['status' => 'delivered'])->assertRedirect();
        $this->patch(route('orders.payment', $order))->assertRedirect();

        $this->get(route('profits.index', [
            'start_date' => '2026-11-17',
            'end_date' => '2026-11-17',
        ]))->assertViewHas('report', function (array $report): bool {
            $this->assertTrue($report['is_incomplete']);
            $this->assertSame(1, $report['missing_cost_lines']);

            return true;
        })->assertSee('resultado está incompleto');

        $this->get(route('profits.index', [
            'start_date' => '2026-11-16',
            'end_date' => '2026-11-16',
        ]))->assertViewHas('report', function (array $report): bool {
            $this->assertNull($report['margin']);

            return true;
        })->assertSee('No calculable');
    }

    public function test_employees_cannot_view_financial_pages_and_other_business_orders_are_hidden(): void
    {
        $business = Business::factory()->create();
        $otherBusiness = Business::factory()->create();
        $employee = User::factory()->for($business)->create(['role' => 'employee']);
        $otherOrder = Order::factory()->for($otherBusiness)->create();

        $this->actingAs($employee)
            ->get(route('profits.index'))
            ->assertForbidden();
        $this->get(route('expenses.index'))->assertForbidden();
        $this->get(route('menu.index'))->assertForbidden();
        $this->get(route('orders.show', $otherOrder))->assertNotFound();
    }

    public function test_registration_requires_business_phone_and_employee_accounts_belong_to_the_owner_business(): void
    {
        $this->post(route('register'), [
            'name' => 'Ana',
            'business_name' => 'Comidas Ana',
            'email' => 'ana@example.test',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ])->assertSessionHasErrors('business_phone');
        $this->assertDatabaseCount('businesses', 0);

        $business = Business::factory()->create();
        $owner = User::factory()->for($business)->create(['role' => 'owner']);

        $this->actingAs($owner)
            ->post(route('settings.employees.store'), [
                'name' => 'Luis Empleado',
                'email' => 'luis@example.test',
                'password' => 'password-123',
                'password_confirmation' => 'password-123',
            ])
            ->assertRedirect(route('settings.edit'));

        $this->assertDatabaseHas('users', [
            'business_id' => $business->id,
            'email' => 'luis@example.test',
            'role' => 'employee',
        ]);
    }

    private function createOrder(User $user, MenuItem $menuItem, int $quantity = 2): Order
    {
        $this->actingAs($user)
            ->post(route('orders.store'), [
                'idempotency_key' => (string) Str::uuid(),
                'customer_name' => 'Camila Torres',
                'customer_phone' => '+57 300 555 6677',
                'channel' => 'takeaway',
                'origin' => 'phone',
                'items' => [
                    $menuItem->id => ['quantity' => $quantity],
                ],
            ])
            ->assertRedirect();

        return Order::query()->latest('id')->firstOrFail();
    }
}
