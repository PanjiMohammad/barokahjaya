@extends('layouts.ecommerce')

@section('title')
    <title>Login - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Login/Register</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('customer.login') }}">Login</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_100">
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
						<h3>Login</h3>
						<form id="loginForm" class="login_form" action="{{ route('customer.post_login') }}" method="post" id="contactForm" novalidate="novalidate">
                            @csrf
							<div class="form-group">
								<input type="email" class="form-control" id="email" name="email" placeholder="Email">
								<span class="text-danger" id="email_error"></span>
							</div>
							<div class="form-group">
								<input type="password" class="form-control" id="password" name="password" placeholder="******">
								<span class="text-danger" id="password_error"></span>
							</div>
							<!-- <div class="form-group">
								<div class="creat_account">
									<input type="checkbox" id="f-option2" name="remember">
									<label for="f-option2">Keep me logged in</label>
								</div>
							</div> -->
							<div class="forgot-password float-right mt-2 mb-3">
								<a href="{{ route('customer.forgotPassword') }}">Lupa Kata Sandi?</a>
							</div>
							<div class="form-group">
								<button type="submit" value="submit" class="btn submit_btn">Log In</button>
							</div>
							<div class="member-sign-up" style="margin-top: 3rem;">
								<span>Ingin bergabung sebagai member?</span>
								<p><a href="{{ route('customer.register') }}">Daftar Disini</a></p>
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
		$(document).ready(function(){

			// session
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

			// login form
			$('#loginForm').submit(function(e) {
				e.preventDefault();

				var formData = $(this).serialize();

				$.ajax({
					url: "{{ route('customer.post_login') }}",
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
                    complete: function () {
                        $('.login_form_inner').unblock();
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

@section('css')
	<style>
		/* -- Masukkan style -- */
	</style>
@endsection