@extends('layouts.ecommerce')

@section('title')
    <title>Checkout - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="overlay"></div>
			<div class="container">
				<div class="banner_content text-center">
					<h2>Informasi Pengiriman</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
						<a href="#">Checkout</a>
					</div>
				</div>
			</div>
		</div>
	</section>
    
	<section class="checkout_area section_gap">
		<div class="container">
            @if (session('scccess'))
                <div class="alert alert-scccess">{{ session('scccess') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

			<div class="billing_details">
                <form class="contact_form" id="checkoutForm" action="{{ route('front.store_checkout') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
                            <h3>Informasi Pengiriman</h3>          
                            @if(auth()->guard('customer')->check())
                                <div class="row">
                                    <div class="col-md-6 form-group p_star">
                                        <label for="customer_name">Nama Penerima</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ auth()->guard('customer')->user()->name }}">
                                        <span class="text-danger" id="customer_name_error"></span>
                                    </div>
                                    <div class="col-md-6 form-group p_star">
                                        <label for="customer_phone">Nomor Telepon</label>
                                        <input type="text" class="form-control" id="customer_phone" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" name="customer_phone" value="{{ auth()->guard('customer')->user()->phone_number }}"  >
                                        <span class="text-danger" id="customer_phone_error"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group p_star">
                                        <label for="email">Email</label>
                                        @if (auth()->guard('customer')->check())
                                            <input type="email" class="form-control" id="email" name="email" 
                                            value="{{ auth()->guard('customer')->user()->email }}" {{ auth()->guard('customer')->check() ? 'readonly':'' }}>
                                        @else
                                            <input type="email" class="form-control" id="email" name="email">
                                        @endif
                                        <span class="text-danger" id="email_error"></span>
                                    </div>
                                    <div class="col-md-6 form-group p_star">
                                        <label for="customer_address">Alamat Lengkap</label>
                                        <input type="text" class="form-control" id="customer_address" name="customer_address" value="{{ auth()->guard('customer')->user()->address }}"  >
                                        <span class="text-danger" id="customer_address_error"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group p_star">
                                        <label for="province_id">Provinsi</label>
                                        <select class="form-control" name="province_id" id="province_id"  >
                                            <option value="">Pilih Provinsi</option>
                                            @foreach ($provinces as $row)
                                                <option value="{{ $row->id }}" {{ optional(optional($customer)->district)->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" id="province_id_error"></span>
                                    </div>
                            
                                    <div class="col-md-4 form-group p_star loader-area-city">
                                        <label for="city_id">Kabupaten / Kota</label>
                                        <select class="form-control" name="city_id" id="city_id"  >
                                            <option value="">Pilih Kabupaten/Kota</option>
                                        </select>
                                        <span class="text-danger" id="city_id_error"></span>
                                    </div>
                                    <div class="col-md-4 form-group p_star loader-area-district">
                                        <label for="district_id">Kecamatan</label>
                                        <select class="form-control" name="district_id" id="district_id"  >
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                        <span class="text-danger" id="district_id_error"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group p_star">
                                    <label for="courier">Kurir</label>
                                        <input type="hidden" name="weight" id="weight" value="{{ $weight }}">
                                        <select class="form-control" name="courier" id="courier"  >
                                            <option value="">Pilih Kurir</option>
                                            <option value="jne">JNE</option>
                                            <option value="jnt">JNT</option>
                                            <option value="ninjaexpress">Ninja Express</option>
                                        </select>
                                        <span class="text-danger" id="courier_error"></span>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Nama Penerima</label>
                                    <input type="text" class="form-control" id="first" name="customer_name" placeholder="Masukkan Nama"  >
                                    <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                                </div>
                                <div class="col-md-6 form-group p_star">
                                    <label for="">No Telepon</label>
                                    <input type="text" class="form-control" id="number" name="customer_phone"  placeholder="Masukkan Nomor Telepon"  >
                                    <p class="text-danger">{{ $errors->first('customer_phone') }}</p>
                                </div>
                                <div class="col-md-6 form-group p_star">
                                    <label for="">Email</label>
                                    @if (auth()->guard('customer')->check())
                                        <input type="email" class="form-control" id="email"  placeholder="Masukkan Email" name="email" 
                                        value="{{ auth()->guard('customer')->user()->email }}" 
                                          {{ auth()->guard('customer')->check() ? 'readonly':'' }}>
                                    @else
                                        <input type="email" class="form-control" id="email" name="email"  >
                                    @endif
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Alamat Lengkap</label>
                                    <input type="text" class="form-control" id="add1" name="customer_address"  >
                                    <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Propinsi</label>
                                    <select class="form-control" name="province_id" id="province_id"  >
                                        <option value="">Pilih Propinsi</option>
                                        @foreach ($provinces as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger">{{ $errors->first('province_id') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Kabupaten / Kota</label>
                                    <select class="form-control" name="city_id" id="city_id"  >
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                    <p class="text-danger">{{ $errors->first('city_id') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Kecamatan</label>
                                    <select class="form-control" name="district_id" id="district_id"  >
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <p class="text-danger">{{ $errors->first('district_id') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Kurir</label>
                                    <input type="hidden" name="weight" id="weight" value="{{ $weight }}">
                                    <select class="form-control" name="courier" id="courier"  >
                                        <option value="">Pilih Kurir</option>
                                        <option value="jne">JNE</option>
                                        <option value="jnt">JNT</option>
                                        <option value="ninjaexpress">Ninja Express</option>
                                    </select>
                                    <p class="text-danger">{{ $errors->first('courier') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-4">
                            <div class="order_box">
                                <h2>Ringkasan Pesanan</h2>
                                <ul class="list">
                                    <li>
                                        <a href="#">Product
                                            <span>Total</span>
                                        </a>
                                    </li>
                                    @foreach ($carts as $cart)
                                        <li>
                                            <a href="#">{{ \Str::limit($cart['product_name'], 10) }}
                                                <span class="middle">x {{ $cart['qty'] }}</span>
                                                <span class="last">Rp {{ number_format($cart['product_price'], 0, ',', '.') }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <ul class="list list_2">
                                    <li>
                                        <a href="#">Subtotal
                                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">Pengiriman
                                        <span id="ongkir">{{ 'Rp ' . number_format(25000, 0, ',', '.') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">Total
                                        <span id="total">Rp {{ number_format($subtotal + 25000, 0, ',', '.') }}</span>
                                        </a>
                                    </li>
                                </ul>
                                <button class="main_btn">Bayar Pesanan</button>
						    </div>
					    </div>
				    </div>
                </form>    
			</div>
		</div>
	</section>
	<!--================End Checkout Area =================-->
@endsection

@section('js')
    <script>
        function loadCity(province_id, type) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "{{ url('/api/city') }}",
                    type: "GET",
                    data: { province_id: province_id },
                    beforeSend: function() {
                        $('.loader-area-city').block({ 
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
                        $('.loader-area-city').unblock();
                    },
                    success: function(html){
                        $('#city_id').empty()
                        $('#city_id').append('<option value="">Pilih Kabupaten/Kota</option>')
                        $.each(html.data, function(key, item) {
                            
                            // KITA TAMPUNG VALUE CITY_ID SAAT INI
                            let city_selected = {{ optional(optional($customer)->district)->city_id }};
                            //KEMUDIAN DICEK, JIKA CITY_SELECTED SAMA DENGAN ID CITY YANG DOLOOPING MAKA 'SELECTED' AKAN DIAPPEND KE TAG OPTION
                            let selected = type == 'bySelect' && city_selected == item.id ? 'selected':'';

                            var el = $('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>');
                            //KEMUDIAN KITA MASUKKAN VALUE SELECTED DI ATAS KE DALAM TAG OPTION
                            $('#city_id').append(el)
                            resolve()
                        })
                    }
                });
            })
        }

        //CARA KERJANYA SAMA SAJA DENGAN FUNGSI DI ATAS
        function loadDistrict(destination, type) {
            $.ajax({
                url: "{{ url('/api/district') }}",
                type: "GET",
                data: { city_id: destination },
                beforeSend: function() {
                    $('.loader-area-district').block({ 
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
                    $('.loader-area-district').unblock();
                },
                success: function(html){
                    $('#district_id').empty()
                    $('#district_id').append('<option value="">Pilih Kecamatan</option>')
                    $.each(html.data, function(key, item) {
                        let district_selected = {{ optional(optional($customer)->district)->id }};
                        let selected = type == 'bySelect' && district_selected == item.id ? 'selected':'';
                        $('#district_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                    })
                }
            });
        }
        $(document).ready(function() {

            loadCity($('#province_id').val(), 'bySelect').then(() => {
                loadDistrict($('#city_id').val(), 'bySelect');
            })

            $('#province_id').on('change', function() {
                loadCity($(this).val(), '');
            })

            $('#city_id').on('change', function() {
                loadDistrict($(this).val(), '')
            })

            // submit form
            $('#checkoutForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var actionUrl = form.attr('action');

                bootbox.confirm({
					message: '<i class="fa-solid fa-question mr-2"></i> Kamu yakin ingin checkout?',
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
                                data: form.serialize(), 
                                beforeSend: function() {
                                    $('.billing_details').block({ 
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
                                    $('.billing_details').unblock();
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

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
        }
    </style>
@endsection