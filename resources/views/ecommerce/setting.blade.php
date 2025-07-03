@extends('layouts.ecommerce')

@section('title')
    <title>Pengaturan Akun - Ecommerce</title>
@endsection

@section('content')
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Pengaturan</h2>
					<div class="page_link">
              <a href="{{ url('/') }}">Home</a>
              <a href="{{ route('customer.settingForm') }}">Pengaturan</a>
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
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title mt-3">Informasi Pribadi</h3>
                                </div>
                                <div class="setting_form_inner">
                                    <form action="{{ route('customer.setting') }}" id="settingForm" method="post">
                                        @csrf
                                        <div class="card-body loader-area">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">Nama Lengkap</label>
                                                        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama Lengkap" value="{{ $customer->name }}">
                                                        <span class="text-danger" id="name_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan Email" value="{{ $customer->email }}" readonly>
                                                        <span class="text-danger" id="email_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="password">Password</label>
                                                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Kata Sandi">
                                                        <small>*Biarkan kosong jika tidak ingin mengganti password</small>
                                                        <span class="text-danger" id="password_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="phone_number">Nomor Telepon</label>
                                                        <input type="text" name="phone_number" id="phone_number" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" value="{{ $customer->phone_number }}">
                                                        <span class="text-danger" id="phone_number_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Alamat</label>
                                                {{-- <input type="text" name="address" class="form-control" required value="{{ $customer->address }}"> --}}
                                                <textarea class="form-control" name="address" id="address" cols="30" rows="10">{{ $customer->address }}</textarea>
                                                <span class="text-danger" id="address_error"></span>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                     <div class="form-group">
                                                        <label for="province_id">Provinsi</label>
                                                        <select class="form-control" name="province_id" id="province_id" required>
                                                            <option value="">Pilih Provinsi</option>
                                                            @foreach ($provinces as $row)
                                                                <option value="{{ $row->id }}" {{ $customer->district->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-danger" id="province_id_error">{{ $errors->first('province_id') }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group loader-area-city">
                                                        <label for="city_id">Kabupaten / Kota</label>
                                                        <select class="form-control" name="city_id" id="city_id" required>
                                                            <option value="">Pilih Kabupaten/Kota</option>
                                                        </select>
                                                        <p class="text-danger">{{ $errors->first('city_id') }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group loader-area-district">
                                                        <label for="district_id">Kecamatan</label>
                                                        <select class="form-control" name="district_id" id="district_id" required>
                                                            <option value="">Pilih Kecamatan</option>
                                                        </select>
                                                        <p class="text-danger">{{ $errors->first('district_id') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <button class="btn btn-primary btn-md">Simpan</button>
                                        </div>
                                    </form>
                                </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection


@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script>
        //JADI KETIKA HALAMAN DI-LOAD
        $(document).ready(function(){
            //MAKA KITA MEMANGGIL FUNGSI LOADCITY() DAN LOADDISTRICT()
            //AGAR SECARA OTOMATIS MENGISI SELECT BOX YANG TERSEDIA
            loadCity($('#province_id').val(), 'bySelect').then(() => {
                loadDistrict($('#city_id').val(), 'bySelect');
            })

            // submit form
            $('#settingForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var actionUrl = form.attr('action');

                bootbox.confirm({
					message: '<i class="fa-solid fa-question mr-2"></i> Kamu yakin ingin merubah profile?',
					backdrop: true,
					buttons: {
						confirm: {
							label: 'Ya <i class="fas fa-check ml-1"></i>',
							className: 'btn-primary btn-sm'
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
                                        window.location.href = "{{ route('customer.dashboard') }}";
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
                            let city_selected = {{ $customer->district->city_id }};
                           //KEMUDIAN DICEK, JIKA CITY_SELECTED SAMA DENGAN ID CITY YANG DOLOOPING MAKA 'SELECTED' AKAN DIAPPEND KE TAG OPTION
                            let selected = type == 'bySelect' && city_selected == item.id ? 'selected':'';
                            //KEMUDIAN KITA MASUKKAN VALUE SELECTED DI ATAS KE DALAM TAG OPTION
                            $('#city_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                            resolve()
                        })
                    }
                });
            })
        }

        //CARA KERJANYA SAMA SAJA DENGAN FUNGSI DI ATAS
        function loadDistrict(city_id, type) {
            $.ajax({
                url: "{{ url('/api/district') }}",
                type: "GET",
                data: { city_id: city_id },
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
                        let district_selected = {{ $customer->district->id }};
                        let selected = type == 'bySelect' && district_selected == item.id ? 'selected':'';
                        $('#district_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                    })
                }
            });
        }
    </script>
@endsection