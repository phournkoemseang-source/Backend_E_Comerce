<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-3xl bg-white rounded-3xl shadow-xl p-10">
        <h1 class="text-4xl font-bold mb-4">Dashboard</h1>
        <p class="text-slate-600">You are logged in. You can now access protected pages.</p>

        <div class="mt-8 flex gap-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-red-600 px-5 py-3 text-white font-semibold hover:bg-red-700">
                    Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
