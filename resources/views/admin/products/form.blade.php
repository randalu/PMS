@extends('layouts.admin')

@section('content')
<h1>{{ $product->exists ? 'Edit Product' : 'Add Product' }}</h1>
<form method="post" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf @if($product->exists) @method('PUT') @endif
    <div class="form-grid">
        <div class="field"><label>Collection</label><select name="category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id)==$category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="field"><label>SKU</label><input name="sku" value="{{ old('sku', $product->sku) }}" required></div>
        <div class="field"><label>Name</label><input name="name" value="{{ old('name', $product->name) }}" required></div>
        <div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $product->slug) }}"></div>
        <div class="field"><label>Sort order</label><input name="sort_order" type="number" min="0" value="{{ old('sort_order', $product->sort_order ?? 0) }}"></div>
        <div class="field"><label>Image</label><input name="image" type="file" accept="image/*"></div>
    </div>
    @if($product->image_path)<img src="{{ asset($product->image_path) }}" alt="" style="width:160px;border-radius:8px">@endif
    <div class="field"><label>SEO description</label><textarea name="seo_description">{{ old('seo_description', $product->seo_description) }}</textarea></div>
    <label><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->is_active ?? true))> Active</label>
    <button class="primary" type="submit">Save Product</button>
</form>
@if($product->exists)
<h2>Variants</h2>
<p class="muted">Edit stock, price, size, and color from Inventory.</p>
@endif
@endsection
