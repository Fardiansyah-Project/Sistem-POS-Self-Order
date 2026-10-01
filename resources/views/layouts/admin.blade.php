<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Koriro POS</title>

    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <!-- Custom Admin CSS -->
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --bs-body-bg: #f8f9fa;
            --brand-color: #c97d20;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 12px 20px;
            margin-bottom: 5px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--brand-color);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .brand-logo {
            font-weight: 800;
            color: var(--brand-color);
            letter-spacing: -0.5px;
        }

        .content-wrapper {
            padding: 24px;
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="container-fluid p-0">
        <div class="d-flex">
            <!-- Sidebar -->
            <div class="sidebar flex-shrink-0 p-3" style="width: 280px;">
                <a href="#"
                    class="d-flex align-items-center mb-3 mb-md-4 me-md-auto text-white text-decoration-none px-2">
                    <span class="fs-4 brand-logo"><i class="bi bi-cup-hot-fill me-2"></i>Koriru POS</span>
                </a>
                <hr class="text-white-50">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->is('cms/admin/dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    @if (auth()->check() && auth()->user()->role === 'admin')
                    <li class="mt-3 mb-2 px-3 text-uppercase text-white-50"
                        style="font-size: 0.75rem; letter-spacing: 1px;">Katalog</li>
                    <li>
                        <a href="{{ route('admin.products.index') }}"
                            class="nav-link {{ request()->is('cms/admin/products*') ? 'active' : '' }}">
                            <i class="bi bi-box-seam"></i> Produk Menu
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}"
                            class="nav-link {{ request()->is('cms/admin/categories*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i> Kategori
                        </a>
                    </li>

                    <li class="mt-3 mb-2 px-3 text-uppercase text-white-50"
                        style="font-size: 0.75rem; letter-spacing: 1px;">Inventory</li>
                    <li>
                        <a href="{{ route('admin.ingredients.index') }}"
                            class="nav-link {{ request()->is('cms/admin/ingredients*') ? 'active' : '' }}">
                            <i class="bi bi-basket2"></i> Bahan Baku
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.forecast.index') }}"
                            class="nav-link {{ request()->is('cms/admin/forecast*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow"></i> Prediksi Bahan
                        </a>
                    </li>
                    @endif

                    <li class="mt-3 mb-2 px-3 text-uppercase text-white-50"
                        style="font-size: 0.75rem; letter-spacing: 1px;">Operasional</li>
                    <li>
                        <a href="{{ route('admin.pos.index') }}"
                            class="nav-link {{ request()->is('cms/admin/pos*') ? 'active' : '' }}">
                            <i class="bi bi-calculator"></i> Kasir POS
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.transactions.index') }}"
                            class="nav-link {{ request()->is('cms/admin/transactions*') ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i> Data Transaksi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kitchen.index') }}"
                            class="nav-link {{ request()->is('cms/admin/kitchen*') ? 'active' : '' }}">
                            <i class="bi bi-display"></i> Monitor Dapur (Kasir)
                        </a>
                    </li>

                    @if (auth()->check() && auth()->user()->role === 'admin')
                    <li class="mt-3 mb-2 px-3 text-uppercase text-white-50"
                        style="font-size: 0.75rem; letter-spacing: 1px;">Laporan</li>
                    <li>
                        <a href="{{ route('admin.reports.index') }}"
                            class="nav-link {{ request()->is('cms/admin/reports*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph"></i> Ekspor Laporan
                        </a>
                    </li>
                    @endif
                </ul>
                <hr class="text-white-50 mt-auto">
                <div class="dropdown px-2 pb-2">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                        id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'User' }}&background=c97d20&color=fff"
                            alt="" width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="/logout" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="w-100 bg-body-tertiary">
                <!-- Topbar -->
                <header
                    class="p-3 mb-4 border-bottom bg-white shadow-sm d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">@yield('title', 'Dashboard')</h5>
                    <div>
                        <span class="badge bg-warning text-dark me-2"><i
                                class="bi bi-clock me-1"></i>{{ now('Asia/Makassar')->format('d M Y, H:i') }}</span>
                        <a href="/" target="_blank" class="btn btn-sm btn-outline-secondary"><i
                                class="bi bi-phone me-1"></i> Buka PWA</a>
                    </div>
                </header>

                <!-- Content -->
                <div class="content-wrapper">
                    {{-- Alert container untuk AJAX responses --}}
                    <div id="alert-container"></div>

                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
    @vite(['resources/js/app.js'])
    <!-- Chart.js untuk dashboard & grafik peramalan WMA -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <template id="admin-page-scripts">@stack('scripts')</template>
</body>

</html>