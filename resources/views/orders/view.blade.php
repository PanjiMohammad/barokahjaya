@extends('layouts.admin')

@section('title')
    <title>Detail Pesanan</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        {{-- <h1 class="m-0 text-dark">Detail Pesanan</h1> --}}
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('orders.newIndex') }}">Pesanan</a>
                        </li>
                        <li class="breadcrumb-item active">Detail Pesanan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <section class="content">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    Detail Pesanan
                                </h4>
                                <div class="float-right">
                                    <!-- TOMBOL INI HANYA TAMPIL JIKA STATUSNYA 1 DARI ORDER DAN 0 DARI PEMBAYARAN -->
                                    @if ($order->status == 1 && $order->payment->status == 0)
                                        <a href="javascript:void(0);" data-invoice="{{ $order->invoice }}" class="btn btn-primary btn-sm approve-payment">Terima Pembayaran</a>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body loader-area">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Detail Pelanggan</h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="30%">Nama Pelanggan</th>
                                                <td>{{ $order->customer_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Telp</th>
                                                <td>{{ $order->customer_phone }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $order->customer->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alamat</th>
                                                <td>{{ $order->customer_address }} {{ $order->customer->district->name }} - {{  $order->customer->district->city->name}}, {{ $order->customer->district->city->province->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status Pesanan</th>
                                                <td>{!! $order->status_label !!}</td>
                                            </tr>
                                            <!-- FORM INPUT RESI HANYA AKAN TAMPIL JIKA STATUS LEBIH BESAR 1 -->
                                            @if ($order->status > 1)
                                            <tr>
                                                <th>Nomor Resi</th>
                                                <td>
                                                    @if ($order->status == 2)
                                                        <form action="{{ route('orders.newShipping') }}" id="shippingForm" method="post">
                                                            @csrf
                                                            <div class="input-group">
                                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                <input type="text" name="tracking_number" placeholder="Masukkan Nomor Resi" id="tracking_number" value="{{ $order->tracking_number }}" class="form-control">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-secondary" type="submit">Kirim</button>
                                                                </div>
                                                            </div>
                                                            <span class="text-danger" id="tracking_number_error"></span>
                                                        </form>
                                                    @else
                                                        {{ $order->tracking_number }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Detail Pembayaran</h4>
                                        @if ($order->status != 0)
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="30%">Nama Pengirim</th>
                                                    <td>{{ $order->payment->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Bank Tujuan</th>
                                                    <td>{{ $order->payment->transfer_to }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tanggal Transfer</th>
                                                    <td>{{ \Carbon\Carbon::parse($order->payment->transfer_date)->locale('id')->translatedFormat('l, d F Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Bukti Pembayaran</th>
                                                    <td>
                                                        <a target="_blank" href="{{ asset('/storage/proof/' . $order->payment->proof) }}" class="btn btn-sm btn-primary" title="Bukti Pembayaran">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Status Pembayaran</th>
                                                    <td>
                                                        {!! $order->payment->status_label !!} <br>
                                                    </td>
                                                </tr>
                                                @if($order->return_count == 1)
                                                    <tr>
                                                        <th>Return Status</th>
                                                        <td> 
                                                            {!! optional($order->return->first())->status_label !!} 
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-right">
                                                            <a href="{{ route('orders.newReturn', $order->invoice) }}" class="btn btn-sm btn-danger">Return</a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        @else
                                            <h5 class="text-center">Belum Konfirmasi Pembayaran</h5>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <h4>Detail Produk</h4>
                                        <table class="table table-borderd table-hover">
                                            <tr>
                                                <th>Produk</th>
                                                <th>Harga</th>
                                                <th>Quantity</th>
                                                <th>Berat</th>
                                            </tr>
                                            @foreach ($order->details as $row)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="mr-2">
                                                                <img src="{{ asset('storage/products/' . $row->product->image) }}" alt="{{ $row->product->name }}" style="width: 65px; height: 65px;">
                                                            </div>
                                                            <div>
                                                                {{ $row->product->name }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                                                    <td>{{ $row->qty }} item</td>
                                                    <td>{{ $row->weight }} gr</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="text-center">Subtotal</td>
                                                <td colspan="4" class="text-center font-weight-bold">{{'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">Kurir : <span class="text-uppercase font-weight-bold">{{ $order->shipping }}</span></td>
                                                <td colspan="4" class="text-center font-weight-bold">{{ 'Rp ' . number_format($order->cost, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center"><b>TOTAL</b></td>
                                                <td colspan="4" class="text-center font-weight-bold">{{'Rp ' . number_format($order->total, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){

            // approve payment form
            $('.approve-payment').on('click', function() {
                var invoice = $(this).data('invoice');

                $.ajax({
                url: '{{ route("orders.new_approve_payment", ["invoice" => ":invoice"]) }}'
                    .replace(':invoice', invoice),
                type: 'GET',
                beforeSend: function() {
                    $('.loader-area').block({ 
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
                complete: function(){
                    $('.loader-area').unblock();
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: response.success,
                        icon: 'success',
                        timer: 2000,
                        showCancelButton: false,
                        showConfirmButton: false,
                        willClose: () => {
                            window.location.reload(true);
                        }
                    });
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                    }
                    // Handle specific errors if needed
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        timer: 2000, // Display for 2 seconds
                        showCancelButton: false,
                        showConfirmButton: false,
                        willClose: () => {
                            location.reload(true);
                        }
                    });
                }
            });
            });

            // shipping form
            $('#shippingForm').on('submit', function(e){
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    beforeSend: function() {
                        $('.loader-area').block({ 
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
                    complete: function () {
                        $('.loader-area').unblock();
                    },
                    success: function(response){
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.reload(true);
                            }
                        });
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
                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error',
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                if(xhr.status == 500){
                                    window.location.reload(true);
                                } else {
                                    let errorMessage = '';
                                    $.each(errors, function(key, error) {
                                        errorMessage += error[0] + '<br>';
                                        $('#' + key + '_error').text(error[0]);

                                        $('#' + key).addClass('input-error');

                                        setTimeout(function() {
                                            $('#' + key + '_error').text('');
                                            $('#' + key).removeClass('input-error');
                                        }, 3000);
                                    });

                                    $.each(input, function(key, value) {
                                        $('#' + key).val(value);
                                    });
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection