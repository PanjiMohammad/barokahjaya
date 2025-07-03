@extends('layouts.admin')

@section('title')
    <title>Pengaturan Akun</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        {{-- <h1 class="m-0 text-dark">Produk</h1> --}}
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">Dasboard</a>
                            </li>
                            <li class="breadcrumb-item active">Pengaturan Akun</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <section class="content">
            <div class="container">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pengaturan Akun</h4>
                    </div>
                    <form action="{{ route('setting.updateAcount') }}" id="settingForm" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <div class="card-body loader-area">
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" id="name" placeholder="Masukkan Nama" class="form-control" value="{{ $user->name }}">
                                <span class="text-danger" id="name_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" name="email" id="email" placeholder="Masukkan Email" class="form-control" value="{{ $user->email }}">
                                <span class="text-danger" id="email_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" placeholder="*******" class="form-control" value="">
                                <span class="text-danger" id="name_error"></span>
                            </div>
                        </div> 
                        <div class="card-footer text-right">
                            <button class="btn btn-primary float-right">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

			$('#settingForm').submit(function(e) {
				e.preventDefault();

				var formData = $(this).serialize();
                console.log(formData);

				$.ajax({
					url: $(this).attr('action'),
					method: "POST",
					data: formData,
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
                                backgroundColor: 'none',
                                '-webkit-border-radius': '10px', 
                                '-moz-border-radius': '10px', 
                            }
                        });
                    },
                    complete: function() {
                        $('.loader-area').unblock();
                    },
					success: function(response) {
						Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 1500, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = response.redirect;
                            }
                        });
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
                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error',
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                if(xhr.status == 422) {
                                    let errorMessage = '';
                                    $.each(errors, function(key, error) {
                                        errorMessage += error[0] + '<br>';
                                        $('#' + key + '_error').text(error[0]);

                                        // id
                                        $('#' + key).addClass('input-error');
                                        // class
                                        $('.' + key).addClass('input-error');

                                        setTimeout(function() {
                                            $('#' + key + '_error').text('');
                                            $('#' + key).removeClass('input-error');
                                            $('.' + key).removeClass('input-error');
                                        }, 3000);
                                    });

                                    $.each(input, function(key, value) {
                                        $('#' + key).val(value);
                                    });
                                } else {
                                    window.location.reload(true);
                                }
                            }
                        });
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
            border-radius: 4px;
        }
    </style>
@endsection