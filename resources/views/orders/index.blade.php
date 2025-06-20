@extends('layouts.admin')

@section('title')
    <title>Daftar Pesanan</title>
@endsection

@section('content')

    <!-- Set Padding & Margin on DataTables -->
    <style>
        table.dataTable ul {
            padding-left: 20px;
            margin: 0; /* Reset default margin */
        }

        table.dataTable ul li {
            margin-bottom: 5px;
        }
    </style>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0 text-dark">Orders</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="#">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Pesanan</li>
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
                                <h4 class="card-title">Daftar Pesanan</h4>
                            </div>
                            <div class="card-body loader-area">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                
                                <div class="table-responsive">
                                    <table id="ordersTable" style="width: 100%;" class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th style="padding: 10px 10px;">Tanggal</th>
                                                <th class="text-capitalize" style="padding: 10px 10px; width: 18%;"><span class="float-left">Invoice</span></th>
                                                <th style="padding: 10px 10px;" style="width: 15%;">Pelanggan</th>
                                                <th style="padding: 10px 10px;" style="width: 12%;">Total</th>
                                                <th style="padding: 10px 10px;" style="width: 10%;">Opsi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY  -->
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- IMPORTANT LINK -->
    <a href="{{ route('orders.newDatatables') }}" id="ordersGetData"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
<script>
        $(document).ready(function(){

            // session
            var successMessage = $('#success-message').val();
            var errorMessage = $('#error-message').val();

            if (successMessage) {
                Swal.fire({
                    title: 'Berhasil',
                    text: successMessage,
                    icon: 'success',
                    timer: 2000,
                    showCancelButton: false,
                    showConfirmButton: false,
                    willClose: () => {
                        table.ajax.reload();
                    }
                });
            }

            if (errorMessage) {
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    timer: 2000,
                    showCancelButton: false,
                    showConfirmButton: false,
                    willClose: () => {
                        table.ajax.reload();
                    }
                });
            }

            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Pesanan...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada pesanan'
                },
                initComplete: function() {
                    // Add Bootstrap form-control class to search input
                    var $searchInput = $('#ordersTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Pesanan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    // Add Bootstrap form-control class to length menu dropdown
                    var $lengthMenu = $('#ordersTable_length select').addClass('form-control form-control-sm');

                    // Add display flex and align-items-center to the parent of the length menu
                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    // Ensure the 'Tampil' label is vertically aligned
                    $('#ordersTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#ordersGetData').attr('href');
            var table = $('#ordersTable').DataTable({
                ajax: {
                    url: url,
                    beforeSend: function() {
                        $('.loader-area').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
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
                        $('.loader-area').unblock();
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = table.page.info();
                    var value = index + 1 + info.start + '.';
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false, className: 'text-center align-items-center align-middle'}, 
                    {data: 'dates', name: 'dates', className: 'align-middle'}, 
                    {data: 'invoice', name: 'invoice', className: 'font-weight-bold text-uppercase align-middle'},
                    {data: 'customer_name', name: 'customer_name', className: 'align-middle'},
                    {data: 'total', name: 'total', className: 'align-middle'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            $('#ordersTable').on('click', '.view-order', function(e) {
                var invoice = $(this).data('invoice');
                const index = $(this).data('index');
                var url = "{{ route('orders.newView', ':invoice') }}".replace(':invoice', invoice);

                // Send Ajax request to fetch the order details and redirect to the detail page
                $.ajax({
                    url: url,
                    method: 'GET',
                    beforeSend: function() {
                        $('.view-order[data-index="' + index + '"]').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i>', 
                            overlayCSS: {
                                backgroundColor: '#fff',
                                opacity: 0.8,
                                cursor: 'wait',
                            },
                            css: {
                                border: 0,
                                padding: 0,
                                backgroundColor: 'none',
                            }
                        });
                    },
                    complete: function() {
                        $('.view-order[data-index="' + index + '"]').unblock();
                    },
                    success: function(response) {
                        // Hide the loader
                        $('#loader').hide();

                        // Redirect to the order detail page with the fetched content
                        window.location.href = url;
                    },
                    error: function(xhr) {
                        // Handle errors (optional)
                        $('#loader').hide();
                        alert('Error fetching order details.');
                    }
                });
            });

            $('#ordersTable').on('click', '.delete-order', function(e) {
                event.preventDefault(); 

                var ordersId = $(this).data('order-id');
                var deleteUrl = '{{ route("orders.newDestroy", ":id") }}'.replace(':id', ordersId);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus pesanan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            beforeSend: function() {
                                $('.loader-area').block({ 
                                    message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
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
                                    timer: 1500, // Display for 2 seconds
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        table.ajax.reload();
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
                                        table.ajax.reload();
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

@section('css')
    
@endsection