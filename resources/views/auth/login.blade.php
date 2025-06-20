@extends('layouts.auth')

@section('title')
    <title>Login</title>
@endsection

@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href=#><b>E-</b>COMMERCE</a>
    </div>
    @if (session('success'))
        <input type="hidden" id="success-message" value="{{ session('success') }}">
    @endif

    @if (session('error'))
        <input type="hidden" id="error-message" value="{{ session('error') }}">
    @endif
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <form id="loginForm" action="{{ route('post.newLogin') }}" method="post">
                @csrf
                <div class="mb-3">
                    <div class="input-group email">
                        <input class="form-control" id="email" type="email" name="email" placeholder="Email" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <span class="text-danger" id="email_error"></span>
                </div>
                <div class="mb-3">
                    <div class="input-group password">
                        <input class="form-control" id="password" type="password" name="password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                     <span class="text-danger" id="password_error"></span>
                </div>
                <div class="row">
                    <div class="col-8">
                        <p class="mt-2">
                            <a href="{{ route('forgotPassword') }}">Lupa Kata Sandi</a>
                        </p>
                    </div>

                    <div class="col-4">
                        <div class="mt-1">
                            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                        </div>
                    </div>
                </div>

                {{-- <div class="mt-2 text-center">
                    <span>Tidak punya akun? <a href="{{ route('register') }}">Daftar Disini</a></span><br>
                </div> --}}
            </form>
        </div>
    <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->
@endsection

@section('js')

    <!-- set session with toast -->

    <script>
        $(document).ready(function() {

            // login form
			$('#loginForm').submit(function(e) {
				e.preventDefault();

				var formData = $(this).serialize();
                console.log(formData);

				$.ajax({
					url: $(this).attr('action'),
					method: "POST",
					data: formData,
					beforeSend: function() {
                        $('.login-card-body').block({ 
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
                        $('.login-card-body').unblock();
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
                            title: 'Gagal',
                            text: errorMessage,
                            icon: 'error',
                            timer: 2000,
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