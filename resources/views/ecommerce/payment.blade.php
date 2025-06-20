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
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
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
                                    <h4 class="card-title">Konfirmasi Pembayaran</h4>
                                </div>
                                <div class="card-body">
                                    @if($order->status == 0)
                                    <form action="{{ route('customer.savePayment') }}" enctype="multipart/form-data" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label for="">Invoice ID</label>
                                            <input type="text" name="invoice" class="form-control" value="{{ $order->invoice }}"  required readonly>
                                            <p class="text-danger">{{ $errors->first('invoice') }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Nama Pengirim</label>
                                            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                                            <p class="text-danger">{{ $errors->first('name') }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label for="transfer_to">Transfer Ke</label>
                                            <select name="transfer_to" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <option value="BCA - 1234567">BCA: 1234567 a.n Putra</option>
                                                <option value="Mandiri - 2345678">Mandiri: 2345678 a.n Putra</option>
                                                <option value="BRI - 9876543">BRI: 9876543 a.n Putra</option>
                                                <option value="BNI - 6789456">BNI: 6789456 a.n Putra</option>
                                            </select>
                                            <p class="text-danger">{{ $errors->first('transfer_to') }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label for="amount">Jumlah Transfer</label>
                                            <input type="text" name="amount" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="Masukkan Jumlah Transfer" required>
                                            <small class="font-weight-bold text-dark">total: {{ 'Rp ' . number_format($order->subtotal + $order->cost, 0, ',', '.') }}</small>
                                            <p class="text-danger">{{ $errors->first('amount') }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Tanggal Transfer</label>
                                            <input type="text" name="transfer_date" id="transfer_date" placeholder="Masukkan tanggal transfer" class="form-control" required>
                                            <p class="text-danger">{{ $errors->first('transfer_date') }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label for="proof">Bukti Transfer</label>
                                            <input type="file" name="proof" class="form-control" required>
                                            <p class="text-danger">{{ $errors->first('proof') }}</p>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-sm">Konfirmasi</button>
                                        </div>
                                    </form>
                                    @endif

                                    @if($order->status != 0)
                                        <p class="text-center">Anda Sudah Melakukan Pembayaran</p>
                                    @endif
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
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $('#transfer_date').datepicker({
            "todayHighlight": true,
            "setDate": new Date(),
            "autoclose": true
        })
    </script>
@endsection