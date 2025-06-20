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
	<!--================End Home Banner Area =================-->

	<!--================Checkout Area =================-->
	<section class="checkout_area section_gap">
		<div class="container">
            @if (session('scccess'))
                <div class="alert alert-scccess">{{ session('scccess') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

			<div class="billing_details">
                <form class="contact_form" action="{{ route('front.store_checkout') }}" method="post" novalidate="novalidate">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
                            <h3>Informasi Pengiriman</h3>          
                            @if(auth()->guard('customer')->check())
                                <div class="row">
                                    <div class="col-md-6 form-group p_star">
                                        <label for="">Nama Penerima</label>
                                        <input type="text" class="form-control" id="first" name="customer_name" value="{{ auth()->guard('customer')->user()->name }}" required>
                                        <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                                    </div>
                                    <div class="col-md-6 form-group p_star">
                                        <label for="">No Telepon</label>
                                        <input type="text" class="form-control" id="number" name="customer_phone" value="{{ auth()->guard('customer')->user()->phone_number }}" required>
                                        <p class="text-danger">{{ $errors->first('customer_phone') }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group p_star">
                                        <label for="email">Email</label>
                                        @if (auth()->guard('customer')->check())
                                            <input type="email" class="form-control" id="email" name="email" 
                                            value="{{ auth()->guard('customer')->user()->email }}" 
                                            required {{ auth()->guard('customer')->check() ? 'readonly':'' }}>
                                        @else
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        @endif
                                        <p class="text-danger">{{ $errors->first('email') }}</p>
                                    </div>
                                    <div class="col-md-6 form-group p_star">
                                        <label for="customer_address">Alamat Lengkap</label>
                                        <input type="text" class="form-control" id="customer_address" name="customer_address" value="{{ auth()->guard('customer')->user()->address }}" required>
                                        <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group p_star">
                                        <label for="">Propinsi</label>
                                        <select class="form-control" name="province_id" id="province_id" required>
                                            <option value="">Pilih Propinsi</option>
                                            @foreach ($provinces as $row)
                                                <option value="{{ $row->id }}" {{ optional(optional($customer)->district)->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-danger">{{ $errors->first('province_id') }}</p>
                                    </div>
                            
                                    <div class="col-md-4 form-group p_star">
                                        <label for="city_id">Kabupaten / Kota</label>
                                        <select class="form-control" name="city_id" id="city_id" required>
                                            <option value="">Pilih Kabupaten/Kota</option>
                                        </select>
                                        <p class="text-danger">{{ $errors->first('city_id') }}</p>
                                    </div>
                                    <div class="col-md-4 form-group p_star">
                                        <label for="district_id">Kecamatan</label>
                                        <select class="form-control" name="district_id" id="district_id" required>
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                        <p class="text-danger">{{ $errors->first('district_id') }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group p_star">
                                    <label for="courier">Kurir</label>
                                        <input type="hidden" name="weight" id="weight" value="{{ $weight }}">
                                        <select class="form-control" name="courier" id="courier" required>
                                            <option value="">Pilih Kurir</option>
                                            <option value="jne">JNE</option>
                                            <option value="jnt">JNT</option>
                                            <option value="ninjaexpress">Ninja Express</option>
                                        </select>
                                        <p class="text-danger">{{ $errors->first('courier') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Nama Penerima</label>
                                    <input type="text" class="form-control" id="first" name="customer_name" placeholder="Masukkan Nama" required>
                                    <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                                </div>
                                <div class="col-md-6 form-group p_star">
                                    <label for="">No Telepon</label>
                                    <input type="text" class="form-control" id="number" name="customer_phone"  placeholder="Masukkan Nomor Telepon" required>
                                    <p class="text-danger">{{ $errors->first('customer_phone') }}</p>
                                </div>
                                <div class="col-md-6 form-group p_star">
                                    <label for="">Email</label>
                                    @if (auth()->guard('customer')->check())
                                        <input type="email" class="form-control" id="email"  placeholder="Masukkan Email" name="email" 
                                        value="{{ auth()->guard('customer')->user()->email }}" 
                                        required {{ auth()->guard('customer')->check() ? 'readonly':'' }}>
                                    @else
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    @endif
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Alamat Lengkap</label>
                                    <input type="text" class="form-control" id="add1" name="customer_address" required>
                                    <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Propinsi</label>
                                    <select class="form-control" name="province_id" id="province_id" required>
                                        <option value="">Pilih Propinsi</option>
                                        @foreach ($provinces as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger">{{ $errors->first('province_id') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Kabupaten / Kota</label>
                                    <select class="form-control" name="city_id" id="city_id" required>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                    <p class="text-danger">{{ $errors->first('city_id') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Kecamatan</label>
                                    <select class="form-control" name="district_id" id="district_id" required>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <p class="text-danger">{{ $errors->first('district_id') }}</p>
                                </div>
                                <div class="col-md-12 form-group p_star">
                                    <label for="">Kurir</label>
                                    <input type="hidden" name="weight" id="weight" value="{{ $weight }}">
                                    <select class="form-control" name="courier" id="courier" required>
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
                                                <span class="last">Rp {{ number_format($cart['product_price']) }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <ul class="list list_2">
                                    <li>
                                        <a href="#">Subtotal
                                        <span>Rp {{ number_format($subtotal) }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">Pengiriman
                                        <span id="ongkir">{{ 'Rp ' . number_format(25000, 0, ',', '.') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">Total
                                        <span id="total">Rp {{ number_format($subtotal + 25000) }}</span>
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
        loadCity($('#province_id').val(), 'bySelect').then(() => {
                loadDistrict($('#city_id').val(), 'bySelect');
            })

            $('#province_id').on('change', function() {
                loadCity($(this).val(), '');
            })

            $('#city_id').on('change', function() {
                loadDistrict($(this).val(), '')
            })

            function loadCity(province_id, type) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: "{{ url('/api/city') }}",
                        type: "GET",
                        data: { province_id: province_id },
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
    </script>
@endsection