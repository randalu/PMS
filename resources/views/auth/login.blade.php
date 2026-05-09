<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>PMS Admin Login</title><link rel="stylesheet" href="{{ asset('css/app.css') }}"></head>
<body>
<main class="section">
    <div class="container" style="max-width:460px">
        <div class="card"><div class="card-body">
            <h1>Admin Login</h1>
            @if ($errors->any()) <div class="errors">{{ $errors->first() }}</div> @endif
            <form method="post" action="{{ route('admin.login') }}">
                @csrf
                <div class="field"><label>Email</label><input name="email" type="email" value="{{ old('email') }}" required autofocus></div>
                <div class="field"><label>Password</label><input name="password" type="password" required></div>
                <label><input name="remember" type="checkbox" value="1"> Remember me</label>
                <button class="primary" type="submit">Login</button>
            </form>
        </div></div>
    </div>
</main>
</body>
</html>
