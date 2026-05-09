<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminInventoryController extends Controller
{
    public function index(Request $request): View
    {
        $variants = ProductVariant::query()
            ->with('product.category')
            ->when($request->query('q'), function ($query, $search): void {
                $query->whereHas('product', fn ($product) => $product->where('sku', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            })
            ->orderBy(ProductVariant::select('sort_order')->from('products')->whereColumn('products.id', 'product_variants.product_id'))
            ->paginate(30)
            ->withQueryString();

        return view('admin.inventory.index', ['variants' => $variants]);
    }

    public function update(Request $request, ProductVariant $variant): RedirectResponse
    {
        $data = $request->validate([
            'size' => ['required', 'string', 'max:80'],
            'color' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($variant, $data, $request): void {
            $oldStock = $variant->stock_quantity;
            $variant->update([
                ...$data,
                'is_active' => $request->boolean('is_active'),
            ]);

            $change = $variant->stock_quantity - $oldStock;
            if ($change !== 0) {
                InventoryMovement::query()->create([
                    'product_variant_id' => $variant->id,
                    'quantity_change' => $change,
                    'stock_after' => $variant->stock_quantity,
                    'reason' => 'manual_adjustment',
                    'note' => $data['note'] ?? null,
                    'user_id' => auth()->id(),
                ]);
            }
        });

        return back()->with('status', 'Inventory updated.');
    }
}
