@extends('layouts.admin')

@section('title')
    <title>Detail Pesanan</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1 class="m-0 text-dark">Detail Pesanan</h1> --}}
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('orders.newIndex') }}">Pesanan</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('orders.newView', $order->invoice) }}">Detail Pesanan</a>
                            </li>
                            <li class="breadcrumb-item active">Detail Pengembalian Pesanan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    Detail Pesanan
                                </h4>
                            </div>
                            <div class="card-body loader-area">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <div class="row">
                                    <!-- BLOCK UNTUK MENAMPILKAN DATA PELANGGAN -->
                                    <div class="col-md-6">
                                        <h4>Detail Pelanggan</h4>
                                        <table class="table table-striped">
                                            <tr>
                                                <th width="30%">Nama Pelanggan</th>
                                                <td>{{ $order->customer_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nomor Telepon</th>
                                                <td>{{ $order->customer_phone }}</td>
                                            </tr>
                                            <tr>
                                                <th>Telp</th>
                                                <td>{{ $order->customer->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alasan Return</th>
                                                <td>{{ optional($order->return->first())->reason }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rekening Pengembalian Dana</th>
                                                <td>{{ 'Rp. ' . number_format(optional($order->return->first())->refund_transfer, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>{!! optional($order->return->first())->status_label !!}</td>
                                            </tr>
                                        </table>

                                        @if (optional($order->return->first())->status == 0)
                                            <form action="{{ route('orders.new_approve_return') }}" id="approveForm" method="post">
                                                @csrf
                                                <div class="input-group mb-3" id="status">
                                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                    <select name="status" class="form-control">
                                                        <option value="">Pilih</option>
                                                        <option value="1">Terima</option>
                                                        <option value="2">Tolak</option>
                                                    </select>
                                                    <div class="input-group-prepend">
                                                        <button class="btn btn-primary btn-sm">Proses Return</button>
                                                    </div>
                                                </div>
                                                <span class="text-danger" id="status_error"></span>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Foto Barang Return</h4>
                                        <img src="{{ asset('/images/proof/' . optional($order->return->first())->photo) }}" class="img-responsive" height="200" alt="">
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

            $('#approveForm').on('submit', function(e){
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
                                window.location.href = response.redirect;
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

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
        }
    </style>
@endsection
