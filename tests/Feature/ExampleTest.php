<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_lists_seeded_products(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Order bedsheet sets')
            ->assertSee('EC-NEM-01');
    }

    public function test_customer_can_place_online_order(): void
    {
        $this->seed();
        $variant = ProductVariant::query()->firstOrFail();

        $this->post('/cart', ['variant_id' => $variant->id, 'quantity' => 2])->assertRedirect('/cart');

        $this->post('/checkout', [
            'customer_name' => 'Test Customer',
            'customer_phone' => '0771234567',
            'customer_email' => 'customer@example.com',
            'delivery_address' => 'Katunayake',
            'customer_notes' => 'Call before delivery',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['customer_phone' => '0771234567', 'status' => 'new']);
        $this->assertDatabaseHas('order_items', ['product_variant_id' => $variant->id, 'quantity' => 2]);
    }

    public function test_confirming_order_deducts_stock_once_and_logs_inventory(): void
    {
        $this->seed();
        $admin = User::query()->firstOrFail();
        $variant = ProductVariant::query()->firstOrFail();

        $this->post('/cart', ['variant_id' => $variant->id, 'quantity' => 2]);
        $this->post('/checkout', [
            'customer_name' => 'Stock Customer',
            'customer_phone' => '0777654321',
            'delivery_address' => 'Negombo',
        ]);

        $order = Order::query()->where('customer_phone', '0777654321')->firstOrFail();

        app(OrderStatusService::class)->update($order, [
            'status' => 'confirmed',
            'payment_status' => 'cod_pending',
            'delivery_fee' => 350,
        ], $admin->id);

        app(OrderStatusService::class)->update($order->refresh(), [
            'status' => 'processing',
            'payment_status' => 'cod_pending',
            'delivery_fee' => 350,
        ], $admin->id);

        $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock_quantity' => 8]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_variant_id' => $variant->id,
            'order_id' => $order->id,
            'quantity_change' => -2,
            'stock_after' => 8,
            'reason' => 'order_confirmed',
        ]);
        $this->assertSame(1, $order->movements()->count());
    }

    public function test_admin_dashboard_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $this->seed();
        $admin = User::query()->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Dashboard');
    }
}
