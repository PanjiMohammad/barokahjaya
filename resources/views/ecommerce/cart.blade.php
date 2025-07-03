@extends('layouts.ecommerce')

@section('title')
    <title>Keranjang Belanja - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Keranjang Belanja</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('front.list_cart') }}">Cart</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Cart Area =================-->
	<section class="cart_area">
		<div class="container">
			<div class="cart_inner">
                @if(auth()->guard('customer')->check() && $carts)
					<form action="{{ route('front.update_cart') }}" method="post">
						@csrf
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th scope="col">Product</th>
										<th scope="col">Price</th>
										<th scope="col" class="text-center">Quantity</th>
										<th scope="col" class="text-center">Total</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($carts as $row)
										<tr>
											<td>
												<div class="media">
													<div class="d-flex">
														<img src="{{ asset('/storage/products/' . $row['product_image']) }}" width="100px" height="100px" alt="{{ $row['product_name'] }}">
													</div>
													<div class="media-body">
														<p>{{ $row['product_name'] }}</p>
													</div>
												</div>
											</td>
											<td>
												<h5>Rp {{ number_format($row['product_price'], 0, ',', '.') }}</h5>
											</td>
											<td class="text-center">
												<div class="product_count">
													<button class="quantity-btn reduced" type="button" data-id="{{ $row['product_id'] }}" data-price="{{ $row['product_price'] }}">
														<i class="fa fa-minus"></i>
													</button>
													<input type="text" name="qty[]" id="sst{{ $row['product_id'] }}" value="{{ $row['qty'] }}" class="input-text qty" data-id="{{ $row['product_id'] }}" data-price="{{ $row['product_price'] }}">
													<input type="hidden" name="product_id[]" value="{{ $row['product_id'] }}" class="form-control">
													<button class="quantity-btn increase" type="button" data-id="{{ $row['product_id'] }}" data-price="{{ $row['product_price'] }}">
														<i class="fa fa-plus"></i>
													</button>
												</div>
											</td>
											<td class="text-center">
												<h5 class="product-total" 
													data-id="{{ $row['product_id'] }}" 
													data-price-total="{{ $row['product_price'] * $row['qty'] }}">
													Rp {{ number_format($row['product_price'] * $row['qty'], 0, ',', '.') }}
												</h5>
											</td>
										</tr>
									@endforeach
									<tr>
										<td></td>
										<td></td>
										<td class="text-center">
											<h5>Subtotal :</h5>
										</td>
										<td class="text-center">
											<h5 id="subtotal-amount">Rp {{ number_format($subtotal, 0, ',', '.') }}</h5>
										</td>
									</tr>
								</tbody>
							</table>
							<hr>
							<div class="checkout_btn_inner text-right">
								<a class="gray_btn_cart" href="{{ route('front.product') }}" title="Continue Shopping">Lanjut Berbelanja</a>
								<a class="main_btn" href="{{ route('front.checkout') }}" title="Next To Payment">Checkout</a>
							</div>
						</div>              
					</form>
				@else
					<div class="d-flex flex-column">
						<div style="height: 200px; width: 200px; display: block; margin: 0 auto;">
							<img src="{{ asset('ecommerce/img/test-no-products.webp') }}" alt="" style="width: 100%; height: 100%; display: block; margin: 0 auto;">
						</div>
						<p class="text-center font-weight-bold" style="color: black;">Wah, keranjang belanjamu kosong</p>
						<p class="text-center font-weight-bold" style="color: black;">Yuk, isi dengan barang-barang impianmu!</p>
						<div style="padding: 0 30px;" class="text-center">
							<a href="{{ route('front.product') }}" class="main_btn_cart"><span>Belanja Sekarang</span></a>
						</div>
					</div>
				@endif
			</div>
		</div>
	</section>
	<!--================End Cart Area =================-->
@endsection

@section('js')
	<script>
		$(document).ready(function() {
		
			// Update subtotal
			function formatRupiah(angka) {
				return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
			}

			function updateProductTotal(productId, price, qty) {
				let total = price * qty;
				$('.product-total[data-id="' + productId + '"]')
					.text(formatRupiah(total))
					.attr('data-price-total', total);
			}

			function updateSubtotal() {
				let subtotal = 0;
				$('.product-total').each(function () {
					let value = parseInt($(this).attr('data-price-total')) || 0;
					subtotal += value;
				});
				$('#subtotal-amount').text(formatRupiah(subtotal));
			}

			function sendUpdateAjax(productId, qty) {
				$.ajax({
					url: '{{ route('front.update_cart') }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						product_id: productId,
						qty: qty
					},
					success: function(response) {
						console.log(response.redirect);
						$('#subtotal-amount').text(response.formatted_subtotal);
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
						}, 1500);
					},
					error: function(xhr, status, error) {
						let errors = xhr.responseJSON.errors;
						let input = xhr.responseJSON.input;

						$('.text-danger').text('');

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

			$(document).on('click', '.quantity-btn', function () {
				let button = $(this);
				let productId = button.data('id');
				let price = parseInt(button.data('price'));
				let qtyInput = $('#sst' + productId);
				let qty = parseInt(qtyInput.val());

				if (isNaN(qty)) qty = 1;

				if (button.hasClass('increase')) {
					qty++;
				} else if (button.hasClass('reduced') && qty > 1) {
					qty--;
				}

				qtyInput.val(qty);
				updateProductTotal(productId, price, qty);
				updateSubtotal();
				sendUpdateAjax(productId, qty);
			});

			// Update total when user types manually
			$(document).on('input', '.qty', function () {
				let input = $(this);
				let productId = input.data('id');
				let price = parseInt(input.data('price'));
				let qty = parseInt(input.val());

				if (isNaN(qty) || qty < 1) qty = 1;
				input.val(qty);

				updateProductTotal(productId, price, qty);
				updateSubtotal();
				sendUpdateAjax(productId, qty);
			});
			
		});
	</script>
@endsection

@section('css')
	<style>
		/* Container around the checkbox */
		.custom-checkbox-container {
			position: relative;
			padding-left: 25px;
			cursor: pointer;
			font-size: 16px;
			user-select: none;
		}

		/* Hide the default checkbox */
		.custom-checkbox-container input[type="checkbox"] {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		/* Create a custom checkbox */
		.custom-checkbox {
			position: absolute;
			top: 0;
			left: 0;
			height: 20px;
			width: 20px;
			background-color: #eee;
			border-radius: 4px;
			border: 1px solid #ccc;
		}

		/* On mouse-over, add a grey background color */
		.custom-checkbox-container:hover input ~ .custom-checkbox {
			background-color: #ccc;
		}

		/* When the checkbox is checked, add a blue background */
		.custom-checkbox-container input:checked ~ .custom-checkbox {
			background-color: #007bff;
			border-color: #007bff;
		}

		/* Create the checkmark/indicator (hidden when not checked) */
		.custom-checkbox:after {
			content: "";
			position: absolute;
			display: none;
		}

		/* Show the checkmark when checked */
		.custom-checkbox-container input:checked ~ .custom-checkbox:after {
			display: block;
		}

		/* Style the checkmark/indicator */
		.custom-checkbox-container .custom-checkbox:after {
			left: 6px;
			top: 3px;
			width: 5px;
			height: 10px;
			border: solid white;
			border-width: 0 2px 2px 0;
			transform: rotate(45deg);
		}
	</style>
@endsection