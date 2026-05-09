<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->query('q'), function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $orders, 'statuses' => Order::STATUSES]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('items.variant.product'), 'statuses' => Order::STATUSES]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::STATUSES)],
            'payment_status' => ['required', 'string', 'max:60'],
            'delivery_fee' => ['required', 'numeric', 'min:0'],
            'courier_name' => ['nullable', 'string', 'max:120'],
            'tracking_number' => ['nullable', 'string', 'max:120'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($order, $data): void {
                $confirming = $data['status'] === 'confirmed' && $order->confirmed_at === null;

                if ($confirming) {
                    foreach ($order->items()->with('variant')->get() as $item) {
                        $variant = $item->variant;
                        if (! $variant || $variant->stock_quantity < $item->quantity) {
                            throw new \RuntimeException("Not enough stock for {$item->sku} {$item->size} {$item->color}.");
                        }
                    }

                    foreach ($order->items()->with('variant')->get() as $item) {
                        $variant = $item->variant;
                        $variant->decrement('stock_quantity', $item->quantity);
                        $variant->refresh();
                        InventoryMovement::query()->create([
                            'product_variant_id' => $variant->id,
                            'order_id' => $order->id,
                            'quantity_change' => -$item->quantity,
                            'stock_after' => $variant->stock_quantity,
                            'reason' => 'order_confirmed',
                            'note' => "Order {$order->order_number}",
                            'user_id' => auth()->id(),
                        ]);
                    }

                    $data['confirmed_at'] = now();
                }

                $data['total'] = $order->subtotal + (float) $data['delivery_fee'];
                $order->update($data);
            });
        } catch (\RuntimeException $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }

        return back()->with('status', 'Order updated.');
    }
}
