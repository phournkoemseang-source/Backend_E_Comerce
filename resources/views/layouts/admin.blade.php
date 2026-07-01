<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            transition: transform 0.3s ease;
        }
        .sidebar.hidden {
            transform: translateX(-100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-blue-900 to-blue-800 text-white shadow-xl flex flex-col">
            <div class="p-6 border-b border-blue-700">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-crown mr-3"></i>Admin Panel
                </h1>
            </div>

            <nav class="mt-6 flex-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="block px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-chart-line mr-3"></i>Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" 
                   class="block px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('admin.products.*') ? 'bg-blue-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-box mr-3"></i>Products
                </a>
                <a href="{{ route('admin.categories.index') }}" 
                   class="block px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('admin.categories.*') ? 'bg-blue-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-list mr-3"></i>Categories
                </a>
            </nav>
            <div class="p-6 border-t border-blue-700">
                <a href="{{ route('admin.logout') }}" 
                   class="block px-6 py-3 hover:bg-blue-700 transition text-red-300 hover:text-red-200"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-3"></i>Logout
                </a>
                <form id="logout-form" method="POST" action="{{ route('admin.logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow">
                <div class="flex justify-between items-center px-6 py-4">
                    <h2 class="text-2xl font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">{{ auth()->user()->name }}</span>
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

@yield('content')
             </main>
         </div>
     </div>
     @stack('scripts')
 </body>
</html>
