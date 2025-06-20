@extends('layouts.ecommerce')

@section('title')
    <title>Register Member - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Registrasi</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('customer.login') }}">Registrasi</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="offset-md-3 col-lg-6">
                    @if (session('success'))
                        <input type="hidden" id="success-message" value="{{ session('success') }}">
                    @endif
        
                    @if (session('error'))
                        <input type="hidden" id="error-message" value="{{ session('error') }}">
                    @endif

					<div class="login_form_inner">
                        <h3>Registrasi Member</h3>
                        <form class="row login_form" id="registerForm" action="{{ route('customer.post_register') }}" method="post" novalidate="novalidate">
                            @csrf
                            <div class="col-md-12 form-group">
                                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Masukkan Nama Lengkap" required>
                                <span class="text-danger" id="customer_name_error"></span>
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required>
                                <span class="text-danger" id="password_error"></span>
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="Masukkan Nomor Telepon" required>
                                <span class="text-danger" id="customer_phone_error"></span>
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" required>
                                <span class="text-danger" id="email_error"></span>
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="text" class="form-control" id="customer_address" name="customer_address" placeholder="Masukkan Alamat" required>
                                <span class="text-danger" id="customer_address_error"></span>
                            </div>
                            <div class="col-md-12 form-group">
                                <select class="form-control" name="province_id" id="province_id">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="province_id_error"></span>
                            </div>
                    
                            <div class="col-md-12 form-group loader-area-city">
                                <select class="form-control" name="city_id" id="city_id">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                </select>
                                <span class="text-danger" id="city_id_error"></span>
                            </div>
                            <div class="col-md-12 form-group loader-area-district">
                                <select class="form-control" name="district_id" id="district_id">
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <span class="text-danger" id="district_id_error"></span>
                            </div>
                            <div class="col-md-12 form-group">
                                <button type="submit" class="btn submit_btn">Register</button>
                            </div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // custom session
            var successMessage = $('#success-message').val();
            var errorMessage = $('#error-message').val();

            if (successMessage) {
                $.toast({
                    heading: 'Berhasil',
                    text: successMessage,
                    showHideTransition: 'slide',
                    icon: 'success',
                    position: 'top-right',
                    hideAfter: 3000
                });
            }

            if (errorMessage) {
                $.toast({
                    heading: 'Gagal',
                    text: errorMessage,
                    showHideTransition: 'fade',
                    icon: 'error',
                    position: 'top-right',
                    hideAfter: 3000
                });
            }

            //KETIKA SELECT BOX DENGAN ID province_id DIPILIH
            $('#province_id').on('change', function() {
                //MAKA AKAN MELAKUKAN REQUEST KE URL /API/CITY DENGAN MENGIRIM PROVINCE_ID
                $.ajax({
                    url: "{{ url('/api/city') }}",
                    type: "GET",
                    data: { province_id: $(this).val() },
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
                    complete: function(){
                        $('.loader-area-city').unblock();
                    },
                    success: function(html){
                        //SETELAH DATA DITERIMA, SELECTBOX DENGAN ID CITY_ID DI KOSONGKAN
                        $('#city_id').empty()
                        //KEMUDIAN APPEND DATA BARU YANG DIDAPATKAN DARI HASIL REQUEST VIA AJAX
                        //UNTUK MENAMPILKAN DATA KABUPATEN / KOTA
                        $('#city_id').append('<option value="">Pilih Kabupaten/Kota</option>')
                        $.each(html.data, function(key, item) {
                            $('#city_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                        })
                    },
                    error: function(xhr){
                        $.toast({
                            heading: 'Error',
                            text: 'Terjadi kesalahan saat mengambil data kecamatan',
                            showHideTransition: 'slide',
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                    }
                });
            })

            //LOGICNYA SAMA DENGAN CODE DIATAS HANYA BERBEDA OBJEKNYA SAJA
            $('#city_id').on('change', function() {
                $.ajax({
                    url: "{{ url('/api/district') }}",
                    type: "GET",
                    data: { city_id: $(this).val() },
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
                    complete: function(){
                        $('.loader-area-district').unblock();
                    },
                    success: function(html){
                        $('#district_id').empty()
                        $('#district_id').append('<option value="">Pilih Kecamatan</option>')
                        $.each(html.data, function(key, item) {
                            $('#district_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                        })
                    },
                    error: function(xhr){
                        $.toast({
                            heading: 'Error',
                            text: 'Terjadi kesalahan saat mengambil data kecamatan',
                            showHideTransition: 'slide',
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                    }
                });
            })

            $('#registerForm').on('submit', function(event) {
                event.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: "POST",
                    data: formData,
                    beforeSend: function() {
                        $('.login_form_inner').block({ 
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
                    complete: function(){
                        $('.login_form_inner').unblock();
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
                        
                        if(xhr.status === 422){
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
                        } else {
                            window.location.reload(true);
                        }
                    }
                });
            });

        });
    </script>
@endsection