<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('admin-lte/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link {{Request::path() == 'administrator/home' ? 'active' : ''}}">
                    <i class="nav-icon fas fa-home"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-header">MANAJEMEN PRODUK</li>
            <li class="nav-item">
                <a href="{{ route('category.index') }}" class="nav-link {{Request::path() == 'administrator/category' ? 'active' : ''}}">
                    <i class="nav-icon fas fa-folder"></i>
                    <p>Kategori</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('product.newIndex') }}" class="nav-link {{Request::path() == 'administrator/product' ? 'active' : ''}}">
                    <i class="nav-icon fas fa-carrot"></i>
                    <p>Produk</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('orders.newIndex') }}" class="nav-link {{Request::path() == 'administrator/orders' ? 'active' : ''}}">
                    <i class="nav-icon fa-solid fa-cart-shopping"></i>
                    <p>Pesanan</p>
                </a>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Laporan<i class="right fas fa-angle-right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('report.order') }}" class="nav-link {{Request::path() == 'administrator/reports/order' ? 'active' : ''}}">
                            <i class="nav-icon fa-regular fa-file-lines"></i>
                            <p>Pesanan</p>
                        </a>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('report.return') }}" class="nav-link {{Request::path() == 'administrator/reports/return' ? 'active' : ''}}">
                            <i class="nav-icon fa-solid fa-file-lines"></i>
                            <p>Pengembalian Pesanan</p>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>Pengaturan<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-warehouse"></i>
                    <p>Toko</p>
                </a>
                </li>
            </ul> --}}
            </li>
        </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<!-- Main Sidebar Container -->