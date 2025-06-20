@extends('layouts.ecommerce')

@section('title')
    <title>Forgot Password - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Forgot Password</h2>
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
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="offset-md-3 col-lg-6">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

					<div class="login_form_inner">
						<h3>Forgot Password</h3>
						<form class="row login_form" action="{{ route('customer.postForgotPassword') }}" method="post" id="forgotPasswordForm">
                            @csrf
							<div class="col-md-12 form-group">
								<input type="email" class="form-control" id="email" autocomplete="email" name="email" placeholder="Email Address">
                                <span class="text-danger" id="email_error"></span>
							</div>
							<div class="col-md-12 form-group">
								<button type="submit" value="submit" class="btn submit_btn">Reset</button>
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

            // submit
            $('#forgotPasswordForm').on('submit', function(event) {
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