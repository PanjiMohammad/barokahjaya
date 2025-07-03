@extends('layouts.ecommerce')

@section('title')
    <title>Konfirmasi Pembayaran - Ecommerce</title>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Konfirmasi Pembayaran</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('customer.orders') }}">Konfirmasi Pembayaran</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="row">
						<div class="col-md-12">
                            @if (session('success')) 
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error')) 
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
							<div class="card">
                                <div class="card-header">
                                    <h3 class="card-title mt-3">Konfirmasi Pembayaran</h3>
                                </div>
                                @if($order->status == 0)
                                    <form action="{{ route('customer.savePayment') }}" id="paymentForm" enctype="multipart/form-data" method="post">
                                        @csrf

                                        <!-- hidden form amount -->
                                        <input type="hidden" name="amount" value="{{ $order->total }}">

                                        <div class="card-body loader-area">
                                            <div class="setting_form_inner">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="invoice">Invoice</label>
                                                            <input type="text" name="invoice" id="invoice" class="form-control" value="{{ $order->invoice }}" readonly>
                                                            <span class="text-danger" id="invoice_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name">Nama Pengirim</label>
                                                            <input type="text" name="name" id="name" class="form-control" value="{{ $customer->name }}" readonly>
                                                            <span class="text-danger" id="name_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="transfer_to">Transfer Ke</label>
                                                    <select name="transfer_to" id="transfer_to" class="form-control">
                                                        <option value="">Pilih</option>
                                                        <option value="BCA - 1234567">BCA: 1234567 a.n Putra</option>
                                                        <option value="Mandiri - 2345678">Mandiri: 2345678 a.n Putra</option>
                                                        <option value="BRI - 9876543">BRI: 9876543 a.n Putra</option>
                                                        <option value="BNI - 6789456">BNI: 6789456 a.n Putra</option>
                                                    </select>
                                                    <span class="text-danger" id="transfer_to_error"></span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="amount1">Jumlah Transfer</label>
                                                    <input type="text" name="amount1" id="amount1" class="form-control" value="{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="Masukkan Jumlah Transfer" readonly>
                                                    {{-- <small class="font-weight-bold text-dark">total: {{ 'Rp ' . number_format($order->subtotal + $order->cost, 0, ',', '.') }}</small> --}}
                                                    <span class="text-danger" id="amount_error"></span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="transfer_date">Tanggal Transfer</label>
                                                    <input type="text" name="transfer_date" id="transfer_date" placeholder="Masukkan tanggal transfer" class="form-control" required>
                                                    <span class="text-danger" id="transfer_date_error"></span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="proof">Bukti Transfer</label>
                                                    <input type="file" name="proof" id="proof" class="form-control">
                                                    <span class="text-danger" id="proof_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <button class="btn btn-primary btn-md">Konfirmasi</button>
                                        </div>
                                    </form>
                                @endif
                                
                                @if($order->status != 0)
                                    <div class="card-body">
                                        <p class="text-center font-weight-bold">Anda sudah melakukan pembayaran.</p>
                                    </div>
                                @endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            $('#transfer_date').daterangepicker({
                singleDatePicker: true,
                autoApply: true,
                showDropdowns: true,
                startDate: moment().locale('id'), // default hari ini
                locale: {
                    format: 'dddd, DD MMMM YYYY', // Format lengkap dengan nama hari dan bulan
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    firstDay: 1
                }
            });

            // submit form
            $('#paymentForm').on('submit', function(e){
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
					method: "POST",
					data: formData,
                    processData: false,
                    contentType: false,
					beforeSend: function() {
                        $('.loader-area').block({ 
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
                        $('.loader-area').unblock();
                    },
                    success: function(response) {
						console.log(response.redirect);
						$.toast({
							heading: 'Berhasil',
							text: response.success,
							showHideTransition: 'slide',
							icon: 'success',
							position: 'top-right',
							hideAfter: 3000
						});
						setTimeout(function() {
							window.location.href = response.redirect;
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
							heading: 'Gagal',
							text: errorMessage,
							showHideTransition: 'fade',
							icon: 'error',
							position: 'top-right',
							hideAfter: 3000
						});

						if(xhr.status == 422){
							$.each(errors, function(key, error) {
								$('#' + key + '_error').text(error[0]);
								$('#' + key).addClass('input-error');

								// Set timeout to clear the error text after 3 seconds
								setTimeout(function() {
									$('#' + key + '_error').text('');
									$('#' + key).removeClass('input-error');
								}, 3000);
							});

							// Retain input values
							$.each(input, function(key, value) {
								$('#' + key).val(value);
							});
						}

						setTimeout(function() {
							window.location.reload(true);
						}, 1500);
					}
                });
            });

        });

    </script>
@endsection