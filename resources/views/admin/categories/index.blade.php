@extends('layouts.admin')

@section('content')
<div class="toolbar"><h1>Collections</h1><a class="btn primary" href="{{ route('admin.categories.create') }}">Add Collection</a></div>
<div class="table-wrap"><table class="table">
    <thead><tr><th>Name</th><th>Slug</th><th>Active</th><th>Sort</th><th></th></tr></thead>
    <tbody>@foreach ($categories as $category)<tr><td>{{ $category->name }}</td><td>{{ $category->slug }}</td><td>{{ $category->is_active ? 'Yes' : 'No' }}</td><td>{{ $category->sort_order }}</td><td class="actions"><a class="btn" href="{{ route('admin.categories.edit', $category) }}">Edit</a><form method="post" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('DELETE')<button class="danger">Deactivate</button></form></td></tr>@endforeach</tbody>
</table></div>{{ $categories->links() }}
@endsection
