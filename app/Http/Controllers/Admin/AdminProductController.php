<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->query('q'), fn ($query, $search) => $query->where('sku', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', ['products' => $products]);
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product, 'categories' => $this->categories()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::query()->create($this->data($request));

        foreach (['Single', 'Double', 'Queen', 'King'] as $size) {
            $product->variants()->create(['size' => $size, 'color' => 'As pictured', 'stock_quantity' => 0]);
        }

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', ['product' => $product->load('variants'), 'categories' => $this->categories()]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->data($request, $product));

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return back()->with('status', 'Product deactivated.');
    }

    private function data(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:80', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:220', 'unique:products,slug,'.($product?->id ?? 'NULL')],
            'image' => ['nullable', 'image', 'max:4096'],
            'seo_description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['sku'].' '.$data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
            $data['image_path'] = 'storage/'.$data['image_path'];
        } elseif ($product) {
            $data['image_path'] = $product->image_path;
        }

        unset($data['image']);

        return $data;
    }

    private function categories()
    {
        return Category::query()->orderBy('sort_order')->get();
    }
}
