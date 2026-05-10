<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderStatusService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Order $order, array $data, ?int $userId = null): Order
    {
        $result = DB::transaction(function () use ($order, $data, $userId): array {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            $previousStatus = $order->status;
            $confirming = ($data['status'] ?? $order->status) === 'confirmed' && $order->confirmed_at === null;

            if ($confirming) {
                $items = $order->items()->with('variant')->get();

                foreach ($items as $item) {
                    $variant = $item->variant()->lockForUpdate()->first();

                    if (! $variant || $variant->stock_quantity < $item->quantity) {
                        throw new RuntimeException("Not enough stock for {$item->sku} {$item->size} {$item->color}.");
                    }
                }

                foreach ($items as $item) {
                    $variant = $item->variant()->lockForUpdate()->firstOrFail();
                    $variant->decrement('stock_quantity', $item->quantity);
                    $variant->refresh();

                    InventoryMovement::query()->create([
                        'product_variant_id' => $variant->id,
                        'order_id' => $order->id,
                        'quantity_change' => -$item->quantity,
                        'stock_after' => $variant->stock_quantity,
                        'reason' => 'order_confirmed',
                        'note' => "Order {$order->order_number}",
                        'user_id' => $userId,
                    ]);
                }

                $data['confirmed_at'] = now();
            }

            if (array_key_exists('delivery_fee', $data)) {
                $data['total'] = (float) $order->subtotal + (float) $data['delivery_fee'];
            }

            $order->update($data);

            return [
                'order' => $order->refresh(),
                'previous_status' => $previousStatus,
            ];
        });

        /** @var Order $updatedOrder */
        $updatedOrder = $result['order'];

        if (($result['previous_status'] ?? null) !== $updatedOrder->status) {
            app(OrderSmsNotifier::class)->sendStatusUpdate($updatedOrder);
        }

        return $updatedOrder;
    }
}
