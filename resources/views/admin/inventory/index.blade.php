@extends('layouts.admin')

@section('content')
<div class="toolbar"><h1>Inventory</h1><form method="get" class="actions"><input class="search" name="q" value="{{ request('q') }}" placeholder="Search SKU or product"><button>Search</button></form></div>
<div class="table-wrap"><table class="table">
    <thead><tr><th>Product</th><th>Variant</th><th>Price</th><th>Stock</th><th>Low</th><th>Active</th><th></th></tr></thead>
    <tbody>
    @foreach ($variants as $variant)
        <tr>
            <form method="post" action="{{ route('admin.inventory.update', $variant) }}">
                @csrf @method('PATCH')
                <td>{{ $variant->product->sku }}<br><strong>{{ $variant->product->name }}</strong></td>
                <td><input name="size" value="{{ $variant->size }}" style="width:100px"> <input name="color" value="{{ $variant->color }}" style="width:140px"></td>
                <td><input name="price" type="number" step="0.01" min="0" value="{{ $variant->price }}" style="width:100px"></td>
                <td><input name="stock_quantity" type="number" min="0" value="{{ $variant->stock_quantity }}" style="width:85px"> @if($variant->isLowStock()) <span class="badge warn">Low</span> @endif</td>
                <td><input name="low_stock_threshold" type="number" min="0" value="{{ $variant->low_stock_threshold }}" style="width:70px"></td>
                <td><input name="is_active" type="checkbox" value="1" @checked($variant->is_active)></td>
                <td><input name="note" placeholder="Adjustment note" style="width:150px"><button>Save</button></td>
            </form>
        </tr>
    @endforeach
    </tbody>
</table></div>{{ $variants->links() }}
@endsection
