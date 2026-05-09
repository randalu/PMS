@extends('layouts.admin')

@section('content')
<div class="toolbar"><h1>Products</h1><a class="btn primary" href="{{ route('admin.products.create') }}">Add Product</a></div>
<form method="get" class="toolbar"><input class="search" name="q" value="{{ request('q') }}" placeholder="Search SKU or name"><button>Search</button></form>
<div class="table-wrap"><table class="table">
    <thead><tr><th>Product</th><th>Collection</th><th>Active</th><th>Sort</th><th></th></tr></thead>
    <tbody>@foreach ($products as $product)<tr><td>{{ $product->sku }}<br><strong>{{ $product->name }}</strong></td><td>{{ $product->category->name }}</td><td>{{ $product->is_active ? 'Yes' : 'No' }}</td><td>{{ $product->sort_order }}</td><td class="actions"><a class="btn" href="{{ route('admin.products.edit', $product) }}">Edit</a><form method="post" action="{{ route('admin.products.destroy', $product) }}">@csrf @method('DELETE')<button class="danger">Deactivate</button></form></td></tr>@endforeach</tbody>
</table></div>{{ $products->links() }}
@endsection
