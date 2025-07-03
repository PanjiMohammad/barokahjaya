@extends('layouts.ecommerce')

@section('title')
    <title>Return {{ $order->invoice }} - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Return {{ $order->invoice }}</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('customer.orders') }}">Return {{ $order->invoice }}</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="login_box_area p_120">
		<div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="card">
                        <div class="setting_form_inner">
                            <form action="{{ route('customer.return') }}" id="returnForm" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body loader-area">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <div class="form-group">
                                        <label for="reason">Alasan Return</label>
                                        <textarea name="reason" placeholder="Masukkan Alasan" cols="5" rows="5" class="form-control"></textarea>
                                        <span class="text-danger" id="reason_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="refund_transfer">Refund Transfer</label>
                                        <input type="text" name="refund_transfer" placeholder="Masukkan Nominal" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="form-control">
                                        <small class="font-weight-bold text-dark">nominal: {{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</small><br>
                                        <span class="text-danger" id="refund_transfer_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="photo">Foto</label>
                                        <input type="file" name="photo" id="photo" class="form-control">
                                        <span class="text-danger" id="photo_error"></span>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button class="btn btn-primary">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
    <script>
        $(document).ready(function(){

            // submit form
            $('#returnForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(this);
                var actionUrl = form.attr('action');

                bootbox.confirm({
					message: '<i class="fa-solid fa-triangle-exclamation text-warning mr-1"></i> Kamu yakin ingin checkout?',
					backdrop: true,
					buttons: {
						confirm: {
							label: 'Ya, lanjutkan <i class="fas fa-check ml-1"></i>',
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
                                type: "POST",
                                url: actionUrl,
                                data: formData,     
                                contentType: false,
                                processData: false,
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

                                    // Clear previous errors
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

                                        setTimeout(function() {
                                            window.location.reload(true);
                                        }, 1500);
                                    }
                                }
                            });
                        }
                    }
                });
            });

        });
    </script>
@endsection