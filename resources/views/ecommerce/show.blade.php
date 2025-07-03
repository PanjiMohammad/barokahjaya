@extends('layouts.ecommerce')

@section('title')
    <title>Jual {{ $product->name }}</title>
@endsection

@section('orderwa')
	<div class="floatwa">
		<a href="https://api.whatsapp.com/send?phone=6281382920681&amp;text=Halo%20gan,%20Saya%20mau%20order {{ $product->name }}" target="_blank"><i class="fa fa-whatsapp tombolwa"></i></a>
	</div>
@endsection

@section('content')
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				@if (session('success'))
					<div class="alert alert-success mt-2">{{ session('success') }}</div>
				@elseif(session('error'))
					<div class="alert alert-danger mt-2">{{ session('error') }}</div>
				@endif
				<div class="banner_content text-center">
                    <h2>{{ $product->name }}</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="#">{{ $product->name }}</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="product_image_area">
		<div class="container">
			<div class="row s_product_inner">
				<div class="col-lg-6">
					<div class="s_product_img">
						<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
							<div class="carousel-inner">
								<div class="carousel-item active">
									<img class="d-block w-100" src="{{ asset('/storage/products/' . $product->image) }}" alt="{{ $product->name }}">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-5 offset-lg-1">
					<div class="s_product_text">
						<h3>{{ $product->name }}</h3>
						<h2 class="harga">{{ 'Rp ' . number_format($product->price, 0, ',', '.') }}</h2>
						<hr>
						<form id="add-to-cart-form" method="POST">
							@csrf
							<div class="product_count align-items-center d-flex">
								<label for="qty" class="mt-2 mr-3">Kuantiti:</label>
								<button onclick="decreaseQty(); return false;" class="reduced items-count" type="button">
									<i class="fa fa-minus"></i>
								</button>
								<input type="text" name="qty" id="sst" maxlength="12" value="1" title="Quantity:" class="input-text qty">
								<!-- BUAT INPUTAN HIDDEN YANG BERISI ID PRODUK -->
								<input type="hidden" name="product_id" value="{{ $product->id }}" class="form-control">
								<button onclick="increaseQty(); return false;" class="increase items-count" type="button">
									<i class="fa fa-plus"></i>
								</button>
							</div>
						</form>
						<div class="d-flex">
							<button class="main-button" id="addToCart" title="Tambah Ke Keranjang">Tambah ke Keranjang <i class="lnr lnr lnr-cart" style="font-size: 14px;"></i></button>
							
							@if(auth()->guard('customer')->check())
								@if($wishlist != NULL && $product->id == $wishlist->product_id)
									<form id="delete-wishlist-form-{{ $wishlist->id }}" action="{{ route('customer.deleteWishlist', $wishlist->id) }}" method="POST">
										@csrf
										@method('DELETE')
										<button type="button" class="grey-button ml-2 delete-wishlist" data-wishlist-id="{{ $wishlist->id }}" data-product-id="{{ $product->id }}">
											Hapus dari daftar keinginan <i class="fas fa-trash" style="font-size: 14px;"></i>
										</button>
									</form>
								@else
									<form id="add-wishlist-form-{{ $product->id }}" action="{{ route('customer.save_wishlist') }}" method="POST">
										@csrf
										<input type="hidden" name="product_id" value="{{ $product->id }}" class="form-control">
										<button class="main-button ml-2 add-wishlist" data-product-id="{{ $product->id }}" title="Tambah Ke Daftar Keinginan">
											Tambah ke Daftar Keingan <i class="fa-regular fa-heart" style="font-size: 14px;"></i>
										</button>
									</form>
								@endif
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<section class="product_description_area">
		<div class="container">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Deskripsi</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Spesifikasi</a>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab" style="color: black">
					{!! $product->description !!}
				</div>
				<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
					<div class="table-responsive">
						<table class="table">
							<tbody>
								<tr>
									<td>
										<h5>Berat</h5>
									</td>
									<td>
                                        <h5>{{ $product->weight }} gr</h5>
									</td>
								</tr>
								<tr>
									<td>
										<h5>Harga</h5>
									</td>
									<td>
										<h5>Rp {{ number_format($product->price, 0, ',', '.') }}</h5>
									</td>
								</tr>
								<tr>
									<td>
										<h5>Kategori</h5>
									</td>
									<td>
										<h5>{{ $product->category->name }}</h5>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
	<script>
		$(document).ready(function(){

			function formatPrice(price) {
				return new Intl.NumberFormat('id-ID', {
					style: 'currency',
					currency: 'IDR',
					minimumFractionDigits: 0
				}).format(price);
			}

			window.increaseQty = function() {
				var result = document.getElementById('sst');
				var sst = result.value;
				console.log('Ditambahin : ', sst);
				if (!isNaN(sst)) {
					result.value++;
					updatePrice();
				}
			};

			window.decreaseQty = function() {
				var result = document.getElementById('sst');
				var sst = result.value;
				console.log('Dikurangin : ', sst);
				if (!isNaN(sst) && sst > 0) {
					result.value--;
					updatePrice();
				}
			};

			$("#sst").on('input', function() {
				var input = $(this).val();
				var validInput = input.replace(/[^0-9]/g, '');  // Allow only numbers
				$(this).val(validInput);

				if (validInput !== '') {
					updatePrice();
				} else {
					$.toast({
						heading: 'Gagal',
						text: 'Kuantiti tidak boleh kosong',
						showHideTransition: 'slide',
						icon: 'error',
						position: 'top-right',
						hideAfter: 2000
					});
					setTimeout(function() {
						window.location.reload(true);
					}, 1000)
				}
			});

			function updatePrice() {
				var qty = $("#sst").val();
				var productPrice = '{{ $product->promo_price ?? $product->price }}';
				if(qty !== 0 && qty !== ''){
					var totalPrice = productPrice * qty;
					$(".harga").text(formatPrice(totalPrice));
				} else {
					$(".harga").text('Rp ' + 0);
				}
			}

			// Add to wishlist
			$('.add-wishlist').click(function(e) {
				e.preventDefault();
				var productId = $(this).data('product-id');
				var form = $('#add-wishlist-form-' + productId);

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				$.ajax({
					url: form.attr('action'),
					method: 'POST',
					data: form.serialize(),
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
					complete: function () {
						$.unblockUI();
					},
					success: function(response) {
						if (response.success) {
							$.toast({
								heading: 'Berhasil',
								text: response.success,
								showHideTransition: 'slide',
								icon: 'success',
								position: 'top-right',
								hideAfter: 3000
							});
							setTimeout(function() {
                                window.location.reload();
                            }, 1000);
						} else {
							$.toast({
								heading: 'Gagal',
								text: response.error,
								showHideTransition: 'fade',
								icon: 'error',
								position: 'top-right',
								hideAfter: 3000
							});
						}
					},
					error: function(response) {
						var response = JSON.parse(xhr.responseText);
						if (response.error) {
							errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
						}
						$.toast({
							heading: 'Gagal',
							text: errorMessage,
							showHideTransition: 'fade',
							icon: 'error',
							position: 'top-right',
							hideAfter: 3000
						});
					}
				});
			});

			// Delete from wishlist
			$('.delete-wishlist').click(function(e) {
				e.preventDefault();
				var wishlistId = $(this).data('wishlist-id');
				var form = $('#delete-wishlist-form-' + wishlistId);

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				bootbox.confirm({
					message: '<i class="fas fa-exclamation-triangle text-warning mr-2"></i> Kamu yakin menghapus produk ini dari daftar keinginan?',
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
								url: form.attr('action'),
								method: 'DELETE',
								data: form.serialize(),
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
										window.location.reload(true);
									}, 1000);
								},
								error: function(xhr, status, error) {
									var response = JSON.parse(xhr.responseText);
									if (response.error) {
										errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
									}
									$.toast({
										heading: 'Error',
										text: errorMessage,
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

			// add to cart
			$('#addToCart').click(function(e){
				e.preventDefault();
				var formData = $('#add-to-cart-form').serialize();

				$.ajax({
					url: "{{ route('front.cart') }}",
					method: "POST",
					data: formData,
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
					complete: function () {
						$.unblockUI();
					},
					success: function(response) {
						$.toast({
							heading: 'Berhasil',
							text: response.success,
							showHideTransition: 'slide',
							icon: 'success',
							position: 'top-right',
							hideAfter: 3000,
						});

						setTimeout(function() {
							window.location.reload();
						}, 1500);
					},
					error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
						if (response.error) {
							errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
						}
						
						$.toast({
							heading: 'Error',
							text: errorMessage,
							showHideTransition: 'fade',
							icon: 'error',
							position: 'top-right',
							hideAfter: 3000
						});

						if(xhr.status === 401) {
							setTimeout(function() {
								window.location.href = response.redirect;
							}, 1500);
						}
                    }
				});
			});

		});
	</script>
@endsection

@section('css')
	<style>
		.main-button {
			display: inline-block;
			background: #1641ff;
			height: 50px;
			color: #fff;
			font-family: "Roboto", sans-serif;
			font-size: 14px;
			font-weight: 500;
			/* line-height: 48px; */
			border: 1px solid #1641ff;
			border-radius: 0px;
			outline: none !important;
			box-shadow: none !important;
			text-align: center;
			border: 1px solid #1641ff;
			cursor: pointer;
			transition: all 300ms linear 0s;
			border-radius: 5px; 
		}

		.main-button:hover {
			background: transparent;
			color: #1641ff; 
		}

		.grey-button {
			/* line-height: 50px; */
			background: #f9f9ff;
			border: 1px solid #eeeeee;
			border-radius: 5px;
			height: 50px;
			display: inline-block;
			color: #222222;
			font-weight: 500;
		}
	</style>
@endsection