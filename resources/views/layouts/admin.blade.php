<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PMS Admin')</title>
    <link rel="icon" href="{{ asset('images/logo.webp') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="admin-header">
    <div class="container nav">
        <a class="brand" href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/logo.webp') }}" alt=""> PMS Admin</a>
        <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit">Logout</button></form>
    </div>
</header>
<div class="admin-shell">
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.orders.index') }}">Orders</a>
        <a href="{{ route('admin.products.index') }}">Products</a>
        <a href="{{ route('admin.categories.index') }}">Collections</a>
        <a href="{{ route('admin.inventory.index') }}">Inventory</a>
        <a href="{{ route('admin.settings.edit') }}">Settings</a>
        <a href="{{ route('home') }}">View Store</a>
    </aside>
    <main class="admin-main">
        @if (session('status')) <div class="notice">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="errors">{{ $errors->first() }}</div> @endif
        @yield('content')
    </main>
</div>
</body>
</html>
