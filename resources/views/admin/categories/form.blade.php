@extends('layouts.admin')

@section('content')
<h1>{{ $category->exists ? 'Edit Collection' : 'Add Collection' }}</h1>
<form method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf @if($category->exists) @method('PUT') @endif
    <div class="form-grid">
        <div class="field"><label>Name</label><input name="name" value="{{ old('name', $category->name) }}" required></div>
        <div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $category->slug) }}"></div>
        <div class="field"><label>Sort order</label><input name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}"></div>
    </div>
    <div class="field"><label>Description</label><textarea name="description">{{ old('description', $category->description) }}</textarea></div>
    <label><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $category->is_active ?? true))> Active</label>
    <button class="primary" type="submit">Save Collection</button>
</form>
@endsection
