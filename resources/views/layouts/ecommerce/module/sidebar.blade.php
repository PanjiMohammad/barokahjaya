<div class="card" style="background-color: #f9f9ff">
    <div class="card-header">
        <h3 class="card-title mt-3">Main Menu</h3>
    </div>
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="{{ route('customer.dashboard') }}" style="color: #777777;">
                    <i class="fa-solid fa-house mr-1"></i>
                    <span class="font-weight-bold">Beranda</span>
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('customer.orders') }}" style="color: #777777;">
                    <i class="fa-solid fa-cart-shopping mr-1"></i>
                    <span class="font-weight-bold">Pesanan</span>
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('customer.wishlist') }}" style="color: #777777;">
                    <i class="fa-solid fa-heart mr-1"></i>
                    <span class="font-weight-bold">Daftar Keinginan</span>
                </a>
            </li>
            {{-- <li class="list-group-item">
                <a href="{{ route('customer.settingForm') }}" style="color: #777777;">
                    <i class="fa-solid fa-house mr-1"></i>
                    <span class="font-weight-bold">Pengaturan</span>
                </a>
            </li> --}}
        </ul>
    </div>
</div>