<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{asset('img/rifkidev.ico')}}">

    @yield('title')

	<link rel="stylesheet" href="{{ asset('ecommerce/css/bootstrap.css') }}">
	<link rel="stylesheet" href="{{ asset('ecommerce/vendors/linericon/style.css') }}">
	<link rel="stylesheet" href="{{ asset('ecommerce/css/font-awesome.min.css') }}">
	<link rel="stylesheet" href="{{asset('admin-lte/plugins/fontawesome-free-2/css/all.min.css')}}">
	<link rel="stylesheet" href="{{ asset('ecommerce/vendors/owl-carousel/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ asset('ecommerce/vendors/lightbox/simpleLightbox.css') }}">
	<link rel="stylesheet" href="{{ asset('ecommerce/vendors/nice-select/css/nice-select.css') }}">
	<link rel="stylesheet" href="{{ asset('ecommerce/vendors/animate-css/animate.css') }}">
	<link rel="stylesheet" href="{{ asset('ecommerce/vendors/jquery-ui/jquery-ui.css') }}">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.lineicons.com/2.0/LineIcons.css">
	<link rel="stylesheet" href="{{ asset('ecommerce/css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('ecommerce/css/responsive.css') }}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
	<link rel="stylesheet" href="{{ asset('ecommerce/css/jquery.datatables.css') }}">
	<link rel="stylesheet" href="{{asset('ecommerce//plugins/daterangepicker/daterangepicker.css')}}">


	@yield('orderwa')

	<style>
		.floatwa {
			position:fixed;
			width:60px;
			height:60px;
			bottom:40px;
			right:40px;
			background-color:#00fbff;
			color:#FFF;
			border-radius:50px;
			text-align:center;
			font-size:30px;
			box-shadow: 2px 2px 3px #999;
			z-index:100;
		}
		.tombolwa {
			margin-top:16px;
		}
		.badge {
		padding-left: 9px;
		padding-right: 9px;
		-webkit-border-radius: 9px;
		-moz-border-radius: 9px;
		border-radius: 9px;
		}

		.label-warning[href],
		.badge-warning[href] {
		background-color: #c67605;
		}

		#lblCartCount {
			font-size: 12px;
			background: #0084ff;
			color: #fff;
			padding: 0 5px;
			vertical-align: 15px;
			margin-left: -10px;
		}

		.icons {
            display: inline-block;
            text-decoration: none;
            color: #333;
        }

        .notification-dropdown {
			display: none;
			position: absolute;
			right: 20%;
			top: 100%;
			width: 400px;
			background-color: #fff;
			box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
			border-radius: 4px;
			overflow: hidden;
			z-index: 1000;
			max-height: 500px;
			max-width: 1000px;
			overflow-y: auto; /* Enable scrolling */
		}

        .notification-dropdown::before {
			content: "";
			position: absolute;
			top: -10px;
			right: 10px;
			border-width: 0 10px 10px 10px;
			border-style: solid;
			border-color: transparent transparent #fff transparent;
		}

		.hidden-notification {
			display: none;
		}

		.notification-content {
			padding: 15px 20px 10px;
		}

		.notification-content p {
			margin: 0;
		}

        .icons:hover + .notification-dropdown, .notification-dropdown:hover {
			display: block;
		}

		.wishlist-dropdown {
            display: none;
            position: absolute;
            right: 10%;
            top: 100%;
            width: 400px;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            overflow: hidden;
            z-index: 1000;
			max-height: 500px;
			max-width: 1000px;
			overflow-y: auto;
        }

        .wishlist-dropdown::before {
            content: "";
            position: absolute;
            top: -10px;
            right: 10px;
            border-width: 0 10px 10px 10px;
            border-style: solid;
            border-color: transparent transparent #fff transparent;
        }

		.wishlist-dropdown a {
			color: #777; /* Set your default color here */
			text-decoration: none; /* Optional: Remove underline */
		}

		.wishlist-dropdown a:hover {
			color: #007bff; /* Change to your desired hover color */
		}

		.hidden-notification-wishlists {
			display: none;
		}

        .wishlist-content {
            padding: 15px 20px 10px;
        }

        .wishlist-content p {
            margin: 0;
        }

        .icons:hover + .wishlist-dropdown,
        .wishlist-dropdown:hover {
            display: block;
        }

		.setting-dropdown {
            display: none;
            position: absolute;
            right: 3%;
            top: 100%;
            width: 200px;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            overflow: hidden;
            z-index: 1000;
        }

        .setting-dropdown::before {
            content: "";
            position: absolute;
            top: -10px;
            right: 10px;
            border-width: 0 10px 10px 10px;
            border-style: solid;
            border-color: transparent transparent #fff transparent;
        }

		.setting-dropdown a {
			color: #777; /* Set your default color here */
			text-decoration: none; /* Optional: Remove underline */
		}

		.setting-dropdown a:hover {
			color: #007bff; /* Change to your desired hover color */
		}

        .setting-content {
            padding: 15px 20px 10px;
        }

        .icons:hover + .setting-dropdown, .setting-dropdown:hover {
            display: block;
        }

		.cart-dropdown {
            display: none;
            position: absolute;
            right: 5%;
            top: 100%;
            width: 400px;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            overflow: hidden;
            z-index: 1000;
        }

        .cart-dropdown::before {
            content: "";
            position: absolute;
            top: -10px;
            right: 10px;
            border-width: 0 10px 10px 10px;
            border-style: solid;
            border-color: transparent transparent #fff transparent;
        }

		.cart-dropdown a {
			color: #777; /* Set your default color here */
			text-decoration: none; /* Optional: Remove underline */
		}

		.cart-dropdown a:hover {
			color: #007bff; /* Change to your desired hover color */
		}

        .cart-content {
            padding: 15px 20px 10px;
        }

        .icons:hover + .cart-dropdown, .cart-dropdown:hover {
            display: block;
        }

		.menu-sidebar-area {
			list-style-type:none; padding-left: 0; font-size: 15pt;
		}
		.menu-sidebar-area > li {
			margin:0 0 10px 0;
			list-style-position:inside;
			border-bottom: 1px solid black;
		}
		.menu-sidebar-area > li > a {
			color: black
		}
	</style>

	@yield('css')
</head>

<body>
	<!--================Header Menu Area =================-->
	<header class="header_area">
		{{-- <div class="top_menu row m0">
			<div class="container-fluid">
				<div class="float-left">
					<p>Call Us: 0896 5447 3005</p>
				</div>
				<div class="float-right">
					<ul class="right_side">
						@if (auth()->guard('customer')->check())
							<li><a href="{{ route('customer.dashboard') }}">My Account</a></li>
							<li><a href="{{ route('customer.logout') }}">Logout</a></li>
						@else
							<li><a href="{{ route('customer.login') }}">Login</a></li>
							<li><a href="{{ route('customer.register') }}">Register</a></li>
						@endif
						<li><a href="https://api.whatsapp.com/send?phone=6289654473005&amp;text=Halo%20gan,%20Saya%20butuh%20bantuan%20terkait%20">Contact Us</a></li>
					</ul>
				</div>
			</div>
		</div> --}}
		<div class="main_menu">
			<nav class="navbar navbar-expand-lg navbar-light" style="background-color: white;">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
                    <a class="navbar-brand logo_h" href="{{ url('/') }}">
						<img src="{{ asset('ecommerce/img/logo-ud.png') }}" alt="">
					</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
					 aria-expanded="false" aria-label="Toggle navigation">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse offset" id="navbarSupportedContent">
						<div class="row w-100">
							<div class="col-lg-7 pr-0">
								@include('layouts.ecommerce.module.menu')
							</div>

							<div class="col-lg-5">
								<ul class="nav navbar-nav navbar-right right_nav pull-right">
									<!--<hr>
									<li class="nav-item">
										<a href="#" class="icons">
											<i class="fa fa-search" aria-hidden="true"></i>
										</a>
									</li> -->
									@if (auth()->guard('customer')->check())
										{{-- <hr>
										<li class="nav-item">
											<a href="{{ route('customer.dashboard') }}" class="icons">
												<i class="fa fa-user" aria-hidden="true"></i>
											</a>
										</li>
										<hr>
										<li class="nav-item">
											<a href="{{ route('customer.wishlist') }}" class="icons">
												<i class="fa fa-heart-o" aria-hidden="true"></i>
											</a>
										</li> --}}
										<li class="nav-item">
											<div class="icons">
												<i class="fa-solid fa-bell" aria-hidden="true"></i>
												<span class='badge badge-warning' id='lblCartCount'>{{ $ordersWithSpecificStatusesCount != 0 ? $ordersWithSpecificStatusesCount : '' }}</span>
											</div>
											<div class="notification-dropdown">
												<div class="notification-content">
													<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 5px;">
														<div class="d-flex justify-content-between align-items-center">
															<span class="font-weight-bold" style="font-size: 16px;">Notifikasi</span>
															<span><a href="{{ route('customer.orders') }}"><i class="fas fa-gear"></i></a></span>
														</div>
													</div>

													<div class="mt-2 notification-list">
														@if(count($productStatusAnnouncements) === 0)
															<p>Tidak ada pembaruan status produk</p>
														@else
															@php
																$maxNotifications = 5;
																$count = 0;
															@endphp
															@foreach($productStatusAnnouncements as $announcement)
																<a href="{{ route('customer.view_order', $announcement['invoice']) }}" style="color: #777;"
																class="{{ $count >= $maxNotifications ? 'hidden-notification' : '' }}">
																	<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 5px;">
																		<div class="d-flex align-items-center">
																			<div style="border: 1px solid transparent; height: 80px; width: 80px; display: block;">
																				<img style="height: 100%; width: 100%; object-fit: contain; border-radius: 4px;" src="{{ asset('/storage/products/' . $announcement['image']) }}" alt="{{ $announcement['product_name'] }}">
																			</div>
																			<div class="d-flex flex-column ml-3">
																				<span>{{ '#' . $announcement['invoice'] }}</span>
																				<span>{{ $announcement['product_name'] }}</span>
																				<span>{{ $announcement['qty'] . ' item x Rp ' . number_format($announcement['price'], 0, ',', '.') }}</span>
																			</div>
																		</div>
																		<div class="mt-1">
																			@if($announcement['return_status'])
																				<p class="text-danger">{{ 'Status (Return) : ' . $announcement['return_status'] }}</p>
																			@else
																				{{-- Check if the status is 'Sampai' --}}
																				@if($announcement['status_count'] == 0 && $announcement['status'] == 'Pesanan baru, harap segera melakukan pembayaran.')
																					<p>{{ 'Status: ' . $announcement['status'] . ' sebesar ' . $announcement['total'] }}</p>
																				@else
																					<p>{{ 'Status: ' . $announcement['status'] }}</p>
																				@endif
																			@endif
																		</div>
																	</div>
																</a>
																@php $count++; @endphp
															@endforeach

															@if(count($productStatusAnnouncements) > $maxNotifications)
																<div class="text-center mt-2">
																	<a href="javascript:void(0);" id="showAllNotifications" class="main_btn_cart">Lihat Semua</a>
																</div>
															@endif
														@endif
													</div>
												</div>
											</div>
										</li>
										<li class="nav-item">
											<div class="icons">
												@if($totalWishlistCount > 0)
													<i class="fa fa-heart-o" aria-hidden="true"></i>
													<span class='badge badge-warning' id='lblCartCount'> {{ $totalWishlistCount }}</span>
												@else
													<i class="fa fa-heart-o" aria-hidden="true"></i>
													<span class='badge badge-warning' id='lblCartCount'></span>
												@endif
											</div>
											<div class="wishlist-dropdown">
												<div class="wishlist-content">
													<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 5px;">
														<div class="d-flex justify-content-between align-items-center">
															<span class="font-weight-bold" style="font-size: 16px;">Daftar Keinginan</span>
															<span><a href="{{ route('customer.wishlist') }}">Lihat Semua</a></span>
														</div>
													</div>
													@if($getWishlist->isEmpty())
														<p>Tidak ada produk.</p>
													@else
														@foreach($getWishlist as $item)
															@php
																$maxWishlists = 5;
																$count = 0;
															@endphp
															<a href="{{ url('/product/' . $item->product->slug) }}" class="{{ $count >= $maxWishlists ? 'hidden-notification-wishlists' : '' }}">
																<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding: 0 5px 10px 0;">
																<div class="d-flex justify-content-between align-items-center">
																	<div class="d-flex align-items-center">
																		<div style="border: 1px solid transparent; height: 80px; width: 80px; display: block;">
																			<img style="height: 100%; width: 100%; object-fit: contain; border-radius: 4px;" src="{{ asset('/storage/products/' . $item->product->image) }}" alt="{{ $item->product->name }}">
																		</div>
																		<div class="d-flex flex-column ml-3">
																			<p class="font-weight-bold">{{ $item->product->name }}</p>
																			<p>{{ 'Rp ' . number_format($item->product->price, 0, ',', '.') }}</p>
																		</div>
																	</div>
																	<i class="fas fa-heart" style="font-size: 16px;"></i>
																</div>
															</div>
															</a>
															@php $count++; @endphp
														@endforeach

														@if($getWishlist->count() > $maxWishlists)
															<div class="text-center mt-2">
																<a href="javascript:void(0);" id="showAllWishlists" class="main_btn_cart">Lihat Semua</a>
															</div>
														@endif
													@endif
												</div>
											</div>
										</li>
									@endif

									<li class="nav-item">
										<div class="icons">
											@if(auth()->guard('customer')->check())
												<i class="fa-solid fa-cart-shopping"></i>
												<span class='badge badge-warning' id='lblCartCount'> {{ $cart_total > 0 ? $cart_total : '' }}</span>
											@else
												<i class="fa-solid fa-cart-shopping"></i>
												<span class='badge badge-warning' id='lblCartCount'></span>
											@endif
										</div>
										<div class="cart-dropdown">
											<div class="cart-content">
												<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 5px;">
													<div class="d-flex justify-content-between align-items-center">
														<span class="font-weight-bold" style="font-size: 16px;">Keranjang</span>
														<span>
															<a href="{{ route('front.list_cart') }}">Lihat Keranjang</a>
															@if(auth()->guard('customer')->check() && $cart_total > 0)
																| <a href="{{ route('front.delete_cart') }}" id="delete-cart-btn" class="delete-all-cart">Hapus Pesanan</a>
															@endif
														</span>
													</div>
												</div>
												@if(auth()->guard('customer')->check())
													@forelse($cart as $row)
														<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; margin-top: 10px;">
															<div class="d-flex align-items-center">
																<div style="border: 1px solid transparent; height: 80px; width: 80px; display: block;" class="mb-2">
																	<img style="height: 100%; width: 100%; object-fit: contain; border-radius: 4px;" src="{{ asset('/storage/products/' . $row['product_image']) }}" alt="{{ $row['product_name'] }}">
																</div>
																<div class="d-flex flex-column ml-3">
																	<span class="font-weight-bold">{{ $row['qty'] . ' item x ' . $row['product_name'] }}</span>
																	<span>{{ 'Rp ' . number_format($row['qty'] * $row['product_price'], 0, ',', '.') }}</span>
																</div>
															</div>
														</div>
													@empty
														<div class="mb-1 d-flex flex-column">
															<img src="{{ asset('ecommerce/img/test-no-products.webp') }}" alt="" style="width: 150px; height: 150px; display: block; margin: 0 auto;">
															<p class="text-center font-weight-bold" style="color: black;">Wah, keranjang belanjamu kosong</p>
															<p class="text-center font-weight-bold" style="color: black;">Yuk, isi dengan barang-barang impianmu!</p>
															<div style="padding: 0 30px;" class="text-center">
																<a href="{{ route('front.product') }}" class="main_btn_cart">Belanja Sekarang</a>
															</div>
														</div>
													@endforelse
												@else
													<div class="mb-1 d-flex flex-column">
														<img src="{{ asset('ecommerce/img/test-no-products.webp') }}" alt="" style="width: 150px; height: 150px; display: block; margin: 0 auto;">
														<p class="text-center font-weight-bold" style="color: black;">Wah, keranjang belanjamu kosong</p>
														<p class="text-center font-weight-bold" style="color: black;">Yuk, isi dengan barang-barang impianmu!</p>
														<div style="padding: 0 30px;" class="text-center">
															<a href="{{ route('front.product') }}" class="main_btn_cart">Belanja Sekarang</a>
														</div>
													</div>
												@endif
											</div>
										</div>
									</li>
									{{-- <hr> --}}
									<li class="nav-item">
										<div class="icons">
											<i class="fas fa-gear"></i>
										</div>
										<div class="setting-dropdown">
											<div class="setting-content">
												@if(Auth::guard('customer')->check())
													<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 10px;">
														<a href="{{ route('customer.dashboard') }}" class="text-capitalize" title="Profile {{ Auth::guard('customer')->user()->name }}">
															<div class="d-flex justify-content-between align-items-center">
																<span>Dashboard</span>
																<span><i class="fas fa-user" style="font-size: 18px;"></i></span>
															</div>
														</a>
													</div>
													<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 10px;">
														<a href="{{ route('customer.settingForm') }}" class="text-capitalize" title="Profile {{ Auth::guard('customer')->user()->name }}">
															<div class="d-flex justify-content-between align-items-center">
																<span>Edit Profile</span>
																@if(Auth::guard('customer')->user()->image)
																	<img src="{{ asset('profiles/' . Auth::guard('customer')->user()->image) }}" alt="{{ Auth::guard('customer')->user()->image }}" style="display: block; width: 18px; height: 18px; border-radius: 100%;">
																@else
																	<span><i class="fas fa-gear"></i></span>
																@endif
															</div>
														</a>
													</div>
													<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 10px;">
														<a href="https://api.whatsapp.com/send?phone=087889165715&text=Halo%20Admin" class="text-capitalize" title="Hubungi Kami">
															<div class="d-flex justify-content-between align-items-center">
																<span>Hubungi Kami</span>
																<i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
															</div>
														</a>
													</div>
													<a href="{{ route('customer.logout') }}" id="customer-logout" class="text-capitalize" title="Keluar">
														<div class="d-flex justify-content-between align-items-center">
															<span>Keluar</span>
															<i class="fa-solid fa-right-from-bracket" style="font-size: 18px;"></i>
														</div>
													</a>
												@else
													<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 10px;">
														<a href="https://api.whatsapp.com/send?phone=087889165715&text=Halo%20Admin" class="text-capitalize" title="Hubungi Kami">
															<div class="d-flex justify-content-between align-items-center">
																<span>Hubungi Kami</span>
																<i class="fa-brands fa-whatsapp"></i>
															</div>
														</a>
													</div>
													<div style="border-bottom: 2px solid #ededed; margin-bottom: 10px; padding-bottom: 10px;">
														<a href="{{ route('customer.register') }}" class="text-capitalize" title="Daftar Akun">
															<div class="d-flex justify-content-between align-items-center">
																<span>Registrasi Member</span>
																<i class="fa-regular fa-address-card"></i>
															</div>
														</a>
													</div>
													<a href="{{ route('customer.login') }}" class="text-capitalize" title="Masuk">
														<div class="d-flex justify-content-between align-items-center">
															<span>Masuk</span>
															<i class="fa-solid fa-right-from-bracket"></i>
														</div>
													</a>
												@endif
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</nav>
		</div>
	</header>
	<!--================Header Menu Area =================-->

    @yield('content')

    <!--================ Subscription Area ================-->
	{{-- <section class="subscription-area section_gap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-title text-center">
                        <h2>Subscribe for Our Newsletter</h2>
                        <span>We won’t send any kind of spam</span>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div id="mc_embed_signup">
                        <form target="_blank" novalidate action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&id=92a4423d01"
                            method="get" class="subscription relative">
                            <input type="email" name="EMAIL" placeholder="Email address" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email address'"
                                required="">
                            <!-- <div style="position: absolute; left: -5000px;">
                                <input type="text" name="b_36c4fd991d266f23781ded980_aefe40901a" tabindex="-1" value="">
                            </div> -->
                            <button type="submit" class="newsl-btn">Get Started</button>
                            <div class="info"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!--================ End Subscription Area ================-->

	<!--================ start footer Area  =================-->
	<footer class="footer-area section_gap">
		<div class="container">
			<div class="row">
				<div class="col-lg-3  col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h6 class="footer_title">Tentang Kami</h6>
						<p class="text-justify">U.D. Barokah Jaya melakukan usaha penjualan berbagai inventaris kantor. Mereka menyediakan berbagai peralatan dan perlengkapan yang dibutuhkan untuk mendukung kegiatan perkantoran</p>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h6 class="footer_title">Newsletter</h6>
						<p>Stay updated with our latest trends</p>
						<div id="mc_embed_signup">
							{{-- <form target="_blank" action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01"
							 method="get" class="subscribe_form relative">
								<div class="input-group d-flex flex-row">
									<input name="EMAIL" placeholder="Email Address" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email Address '"
									 required="" type="email">
									<button class="btn sub-btn">
										<span class="lnr lnr-arrow-right"></span>
									</button>
								</div>
								<div class="mt-10 info"></div>
							</form> --}}
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="single-footer-widget instafeed">
						<h6 class="footer_title">Instagram Feed</h6>
						<ul class="list instafeed d-flex flex-wrap">
							<li>
								<img src="{{ asset('ecommerce/img/instagram/Image-01.jpg') }}" alt="">
							</li>
							<li>
								<img src="{{ asset('ecommerce/img/instagram/Image-02.jpg') }}" alt="">
							</li>
							<li>
								<img src="{{ asset('ecommerce/img/instagram/Image-03.jpg') }}" alt="">
							</li>
							<li>
								<img src="{{ asset('ecommerce/img/instagram/Image-04.jpg') }}" alt="">
							</li>
							<li>
								<img src="{{ asset('ecommerce/img/instagram/Image-05.jpg') }}" alt="">
							</li>
							<li>
								<img src="{{ asset('ecommerce/img/instagram/Image-06.jpg') }}" alt="">
							</li>
							<li>
								<img src="{{ asset('ecommerce/img/instagram/Image-07.jpg') }}" alt="">
							</li>
							<li>
								<img src="{{ asset('ecommerce/img/instagram/Image-08.jpg') }}" alt="">
							</li>
						</ul>
					</div>
				</div>
				<div class="col-lg-2 col-md-6 col-sm-6">
					<div class="single-footer-widget f_social_wd">
						<h6 class="footer_title">Follow Us</h6>
						<p>Let us be social</p>
						<div class="f_social">
							<a href="#">
								<i class="fa fa-facebook"></i>
							</a>
							<a href="#">
								<i class="fa fa-twitter"></i>
							</a>
							<a href="#">
								<i class="fa fa-dribbble"></i>
							</a>
							<a href="#">
								<i class="fa fa-behance"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="row footer-bottom d-flex justify-content-between align-items-center">
				<p class="col-lg-12 footer-text text-center">
                    Copyright &copy;<script>document.write(new Date().getFullYear());</script>
                    All rights reserved

				</p>
			</div>
		</div>
	</footer>

	<script src="{{ asset('ecommerce/js/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/jquery-3.6.0.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/popper.js') }}"></script>
	<script src="{{ asset('ecommerce/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/stellar.js') }}"></script>
	<script src="{{ asset('ecommerce/js/simple.money.format.js') }}"></script>
	<script src="{{ asset('ecommerce/vendors/lightbox/simpleLightbox.min.js') }}"></script>
	<script src="{{ asset('ecommerce/vendors/nice-select/js/jquery.nice-select.min.js') }}"></script>
	<script src="{{ asset('ecommerce/vendors/isotope/imagesloaded.pkgd.min.js') }}"></script>
	<script src="{{ asset('ecommerce/vendors/isotope/isotope-min.js') }}"></script>
	<script src="{{ asset('ecommerce/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/jquery.ajaxchimp.min.js') }}"></script>
	<script src="{{ asset('ecommerce/vendors/counter-up/jquery.waypoints.min.js') }}"></script>
	<!-- <script src="{{ asset('ecommerce/vendors/flipclock/timer.js') }}"></script> -->
	<script src="{{ asset('ecommerce/vendors/counter-up/jquery.counterup.js') }}"></script>
	<script src="{{ asset('ecommerce/js/mail-script.js') }}"></script>
	<script src="{{ asset('ecommerce/plugins/daterangepicker/moment.min.js')}}"></script>
	<script src="{{ asset('ecommerce/plugins/moment/moment.min.js') }}"></script>
	<script src="{{ asset('ecommerce/plugins/moment/moment-with-locales.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/jquery.mask.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/jquery.blockUI.js') }}"></script>
	<script src="{{ asset('ecommerce/js/jquery.blockUI.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/jquery.toast.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/jquery.sweetalert-2.min.js') }}"></script>
	<script src="{{ asset('ecommerce/js/jquery.bootbox.min.js') }}"></script>
	<!-- <script src="{{ asset('ecommerce/js/theme.js') }}"></script> -->
	<script src="{{ asset('ecommerce/js/jquery.datatables.min.js') }}"></script>
	<script src="{{ asset('ecommerce/plugins/daterangepicker/daterangepicker-new.min.js') }}"></script>

	<!-- jquery for action -->
	<script>
		$(document).ready(function() {

			// delete cart button
			$('#delete-cart-btn').on('click', function(e) {
				e.preventDefault();

				let url = $(this).attr('href');

				bootbox.confirm({
					message: '<i class="fa-solid fa-triangle-exclamation text-warning mr-1"></i> Yakin mau menghapus semua pesanan kamu dalam keranjang ?',
					backdrop: true,
					buttons: {
						confirm: {
							label: 'Ya <i class="fas fa-check ml-1"></i>',
							className: 'btn-success btn-sm'
						},
						cancel: {
							label: 'Tidak <i class="fas fa-xmark ml-1"></i>',
							className: 'btn-danger btn-sm'
						}
					},
					callback: function(result) {
						if(result) {
							$.ajax({
								url: url,
								type: 'GET',
								data: {
									_token: '{{ csrf_token() }}'
								},
								beforeSend: function() {
									$.blockUI({
										message: '<i class="fa fa-spinner"></i>',
										overlayCSS: {
											backgroundColor: '#fff',
											opacity: 0.8,
											cursor: 'wait'
										},
										css: {
											border: 0,
											padding: 0,
											backgroundColor: 'none'
										}
									});
								},
								complete: function() {
									$.unblockUI();
								},
								success: function(response) {
									if (response.success) {
										$.toast({
											heading: 'Berhasil',
											text: response.message,
											showHideTransition: 'slide',
											icon: 'success',
											position: 'top-right',
											hideAfter: 3000
										});
										setTimeout(function() {
											location.reload(true);
										}, 1500);
									} else {
										$.toast({
											heading: 'Gagal',
											text: 'Terjadi Kesalahan, Silahkann Coba Lagi.',
											showHideTransition: 'fade',
											icon: 'error',
											position: 'top-right',
											hideAfter: 3000
										});
									}
								},
								error: function(xhr, status, error) {
									var response = JSON.parse(xhr.responseText);
									if (response.error) {
										errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
									}
									$.toast({
										heading: 'Gagal',
										text: 'Terjadi Kesalahan ' + errorMessage,
										showHideTransition: 'fade',
										icon: 'error',
										position: 'top-right',
										hideAfter: 3000
									});
									setTimeout(function() {
										window.location.reload(true);
									}, 1500);
								}
							});
						}
					}
				});
			});

			// logout
			$('#customer-logout').on('click', function(event) {
				console.log('berhasil');
				event.preventDefault();
				bootbox.confirm({
					message: '<i class="fa-solid fa-triangle-exclamation text-warning mr-1"></i> Anda yakin ingin logout ?',
					backdrop: true,
					buttons: {
						confirm: {
							label: 'Ya, keluar <i class="fas fa-check ml-1"></i>',
							className: 'btn-success btn-sm'
						},
						cancel: {
							label: 'Tidak <i class="fas fa-xmark ml-1"></i>',
							className: 'btn-danger btn-sm'
						}
					},
					callback: function(result) {
						console.log(result);
						if(result) {
							$.ajax({
								url: "{{ route('customer.logout') }}",
                				method: 'GET',
								beforeSend: function() {
									$.blockUI({
										message: '<i class="fa fa-spinner"></i>',
										overlayCSS: {
											backgroundColor: '#fff',
											opacity: 0.8,
											cursor: 'wait'
										},
										css: {
											border: 0,
											padding: 0,
											backgroundColor: 'none'
										}
									});
								},
								complete: function() {
									$.unblockUI();
								},
								success: function(response) {
									$.toast({
										heading: 'Berhasil',
										text: response.success,
										showHideTransition: 'slide',
										icon: 'success',
										position: 'top-right',
										hideAfter: 3000
									});
									setTimeout(function() {
										window.location.href = "{{ route('customer.login') }}";
									}, 1500);
								},
								error: function(xhr, status, error) {
									$.toast({
										heading: 'Gagal',
										text: 'Terjadi Kesalahan, Silahkan Coba Lagi.',
										showHideTransition: 'fade',
										icon: 'error',
										position: 'top-right',
										hideAfter: 3000
									});
									setTimeout(function() {
										window.location.reload(true);
									}, 1500);
								}
							});
						}
					}
				});
			});
		});
	</script>

	@yield('js')
</body>
</html>
