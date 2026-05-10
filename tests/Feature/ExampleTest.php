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

    public function test_seeded_products_have_only_two_active_bedsheet_sizes(): void
    {
        $this->seed();

        $variantSizes = ProductVariant::query()
            ->where('is_active', true)
            ->orderBy('product_id')
            ->orderByRaw("CASE size WHEN '90 x 90' THEN 1 WHEN '90 x 100' THEN 2 ELSE 99 END")
            ->select('product_id', 'size')
            ->get()
            ->groupBy('product_id')
            ->map(fn ($variants) => $variants->pluck('size')->values()->all());

        $this->assertNotEmpty($variantSizes);

        foreach ($variantSizes as $sizes) {
            $this->assertSame(['90 x 90', '90 x 100'], $sizes);
        }
    }

    public function test_product_size_selection_defaults_to_select_size(): void
    {
        $this->seed();
        $product = ProductVariant::query()->firstOrFail()->product;

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Select size')
            ->assertSee('Matching pillow cases (2 pcs) are free with every set.');
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

    public function test_admin_is_required_to_set_up_two_factor_authentication(): void
    {
        $this->seed();
        $admin = User::query()->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirect('/admin/multi-factor-authentication/set-up');
    }
}
