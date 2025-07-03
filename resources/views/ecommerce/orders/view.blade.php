@extends('layouts.ecommerce')

@section('title')
    <title>Order {{ $order->invoice }} - Ecommerce</title>
@endsection

@section('content')
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Order {{ $order->invoice }}</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('customer.orders') }}">Order {{ $order->invoice }}</a>
					</div>
				</div>
			</div>
		</div>
	</section>
    
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
                </div>
				<div class="col-md-9">
                    @if (session('success')) 
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="row">
						<div class="col-md-6">
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mt-3">Detail Pelanggan</h4>
                                </div>
								<div class="card-body customer-details">
                                    <div class="table-responsive">
                                        <table>
                                            <tr>
                                                <td width="30%"><p class="custom-margin">Invoice</p></td>
                                                <td width="5%"><p class="custom-margin">:</p></td>
                                                <td>
                                                    <p class="custom-margin">
                                                        <a title="Download Invoice" href="{{ route('customer.order_pdf', $order->invoice) }}" title="{{ $order->invoice  }}" target="_blank" class="font-weight-bold text-uppercase order-pdf-link">
                                                            {{ $order->invoice }}
                                                        </a>
                                                        {{-- <a href="{{ route('customer.order_pdf', $order->invoice) }}" target="_blank">
                                                            <strong>{{ $order->invoice }}</strong>
                                                        </a> --}}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><p class="custom-margin">Nama Penerima</p></td>
                                                <td width="5%"><p class="custom-margin">:</p></td>
                                                <td><p class="custom-margin">{{ $order->customer_name . ' (' . $order->customer_phone . ')' }}</p></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><p class="custom-margin">Email Penerima</p></td>
                                                <td width="5%"><p class="custom-margin">:</p></td>
                                                <td><p class="custom-margin">{{ $order->customer->email }}</p></td>
                                            </tr>
                                            <tr>
                                                <td><p class="custom-margin">Alamat Penerima</p></td>
                                                <td><p class="custom-margin">:</p></td>
                                                <td>
                                                    <p class="custom-margin text-justify">{{ $order->customer_address . ', Kecamatan ' . $order->customer->district->name . ', Kota ' . $order->customer->district->city->name }}, {{ $order->customer->district->city->province->name . ', Kode Pos ' . $order->customer->district->city->postal_code . ', Indonesia' }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mt-3">
                                        Detail Pembayaran

                                        @if ($order->status == 0)
                                            <a href="{{ route('customer.paymentForm', $order->invoice) }}" class="btn btn-primary btn-sm float-right">Konfirmasi</a>
                                        @endif
                                    </h4>
                                </div>
								<div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        @if ($order->payment)
                                            <div class="d-flex flex-column">
                                                <p class="custom-margin">Nama Pengirim</p>
                                                <p class="custom-margin">Tanggal Pembayaran</p>
                                                <p class="custom-margin">Jumlah Pembayaran</p>
                                                <p class="custom-margin">Metode Pembayaran</p>
                                                <p class="custom-margin">Bukti Pembayaran</p>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <p class="custom-margin">:</p>
                                                <p class="custom-margin">:</p>
                                                <p class="custom-margin">:</p>
                                                <p class="custom-margin">:</p>
                                                <p class="custom-margin">:</p>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <p class="custom-margin">{{ $order->payment->name }}</p>
                                                <p class="custom-margin">{{ \Carbon\Carbon::parse($order->payment->transfer_date)->locale('id')->translatedFormat('l, d F Y') }}</p>
                                                <p class="custom-margin">{{ 'Rp ' . number_format($order->payment->amount, 0, ',', '.') }}</p>
                                                <p class="custom-margin">{{ $order->payment->transfer_to }}</p>
                                                <p class="custom-margin">
                                                    {{-- <img src="{{ asset('/proof/' . $order->payment->proof) }}" width="50px" height="50px" alt=""> --}}
                                                    <a href="{{ asset('/storage/proof/' . $order->payment->proof) }}" class="btn btn-sm btn-primary" title="Lihat pembayaran {{ $order->payment->proof }}" target="_blank" alt="{{ $order->payment->proof }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </p>
                                            </div>
                                        @else
                                            <h4 class="text-center">Belum ada data pembayaran</h4>
                                        @endif
                                    </div>
								</div>
							</div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mt-3">Detail Produk</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover borderless">
                                            {{-- <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th>Harga</th>
                                                    <th>Quantity</th>
                                                    <th>Berat</th>
                                                </tr>
                                            </thead> --}}
                                            <tbody>
                                                @forelse ($order->details as $row)
                                                    <tr>
                                                        <td>
                                                            <img src="{{ asset('/storage/products/' . $row->product->image) }}" width="50px" height="50px" alt="">
                                                            {{ $row->product->name }}
                                                        </td>
                                                        <td>{{ 'Rp. ' . number_format($row->price, 0, ',', '.') }}</td>
                                                        <td>{{ $row->qty }} Item</td>
                                                        <td>{{ $row->weight }} gr</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">Tidak ada data</td>
                                                    </tr>
                                                @endforelse
                                                <tr>
                                                    <td class="text-center">Subtotal</td>
                                                    <td colspan="3" class="text-center font-weight-bold">Rp. {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">Kurir <span class="font-weight-bold">{{ strtoupper($order->shipping) }}</span></td>
                                                    <td colspan="3" class="text-center font-weight-bold">Rp. {{ number_format($order->cost, 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center font-weight-bold">Total</td>
                                                    <td colspan="3" class="text-center font-weight-bold">Rp. {{ number_format($order->total, 0, ',', '.') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
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
    <script>
        $(document).ready(function (){

            // download pdf
            $('.order-pdf-link').on('click', function(event){
                event.preventDefault();

                var url = $(this).attr('href');

                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        $('.customer-details').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i>',
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
                    complete: function() {
                        $('.customer-details').unblock();
                    },
                    success: function(response){
                        $.toast({
                            heading: 'Berhasil',
                            text: response.success,
                            showHideTransition: 'slide',
                            icon: 'success',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                        setTimeout(function() {
                            window.open(url, '_blank');
                        }, 1500);
                    },
                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let input = xhr.responseJSON.input;

                        $('.text-danger').text('');

                        // response error
                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                        }
                        $.toast({
                            heading: 'Error',
                            text: errorMessage,
                            showHideTransition: 'fade',
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                    }
                });
            });

        });
    </script>    
@endsection

@section('css')
    <style>
        .custom-margin {
            margin-bottom: 5px;
        }
    </style>
@endsection