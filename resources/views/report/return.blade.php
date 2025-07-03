@extends('layouts.admin')

@section('title')
    <title>Laporan Pengembalian Pesanan</title>
@endsection

@section('content')
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            {{-- <h1 class="m-0 text-dark">Orders</h1> --}}
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Laporan Pengembalian Pesanan</li>
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
                                    <h4 class="card-title">Laporan Pengembalian Pesanan</h4>
                                </div>
                                <div class="card-body loader-area">
                                    <div class="d-flex mb-3 justify-content-end">
                                        {{-- <div class="input-group flex-nowrap" style="width: 36%;">
                                            <span class="input-group-text" id="basic-addon1"><span class="fa-regular fa-calendar-days"></span></span>
                                            <input type="text" id="created_at" name="date" class="form-control" aria-describedby="basic-addon1">
                                        </div> --}}
                                        <div class="d-flex align-items-center">
                                            <span style="margin-top: 1px;" class="font-weight-bold mr-2">Filter Tanggal: </span>
                                            <div id="created_at" class="pull-right" style="background: #fff; cursor: pointer; padding: 3px 10px; border: 1px solid #ccc; width: auto; border-radius: 4px;">
                                                <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                                <span></span> <b class="caret"></b>
                                            </div>
                                        </div>
                                        
                                        <div style="display: none;" id="downloadButton">
                                            <a target="_blank" class="btn btn-primary btn-sm ml-1 text-white" id="exportPDF" title="Export File">Export PDF<i class="fa-regular fa-file-pdf ml-1"></i></a>
                                            {{-- <a target="_blank" class="btn btn-info btn-sm ml-1 text-white" id="exportExcel" title="Download File Excel">Export Excel<i class="fa-solid fa-file-excel ml-1"></i></a> --}}
                                        </div>
                                    
                                    </div>
                                    <div class="table-responsive">
                                        <table id="returnReportOrderTable" style="width: 100%;" class="table">
                                            <thead>
                                                <tr>
                                                    <th style="padding: 10px 10px;">#</th>
                                                    <th style="padding: 10px 10px; width: 18%;">Tanggal</th>
                                                    <th style="padding: 10px 10px; width: 20%;" class="text-capitalize">Invoice</th>
                                                    <th style="padding: 10px 10px; width: 22%;">Pelanggan</th>
                                                    <th style="padding: 10px 10px; width: 25%;">Alasan</th>
                                                    <th style="padding: 10px 10px; width: 15%;">Total</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- important link -->
        <a href="{{ route('return.newDatatables') }}" id="returnGetData"></a>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let start = moment().startOf('month')
            let end = moment().endOf('month')

            function updateExportLink(start, end) {
                $('#exportPDF').data('href', '/administrator/reports/reportreturn/' + start.format('YYYY-MM-DD') + '+' + end.format('YYYY-MM-DD'));
            }

            updateExportLink(start, end);

            function cb(start, end) {
                $('#created_at span').html(start.locale('id').format('dddd, DD MMMM YYYY') + ' - ' + end.locale('id').format('dddd, DD MMMM YYYY'));
            }

            $('#created_at').daterangepicker({
                startDate: start,
                endDate: end,
                locale: {
                    format: 'dddd, DD MMMM YYYY',
                    applyLabel: "Terapkan",
                    cancelLabel: "Batal",
                    customRangeLabel: "Custom Tanggal",
                    daysOfWeek: [
                        "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"
                    ],
                    monthNames: [
                        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                    ],
                    firstDay: 1
                },
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
            }, updateExportLink, cb);

            cb(start, end);

            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Laporan...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada pesanan'
                },
                initComplete: function() {
                    var $searchInput = $('#returnReportOrderTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Laporan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#returnReportOrderTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#returnReportOrderTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#returnGetData').attr('href');
            var table = $('#returnReportOrderTable').DataTable({
                ajax: {
                    url: url,
                    data: function(d) {
                        d.start_date = $('#created_at').data('daterangepicker').startDate.format('dddd, D MMMM YYYY');
                        d.end_date = $('#created_at').data('daterangepicker').endDate.format('dddd, D MMMM YYYY');
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
                    {data: 'details', name: 'details', className: 'align-middle'},
                    {data: 'reason', name: 'reason', className: 'align-middle'},
                    {data: 'refundTransfer', name: 'refundTransfer', className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            $('#created_at').on('apply.daterangepicker', function(ev, picker) {
                moment.locale('id');
                // Format the start and end dates using moment.locale
                let startDate = moment(picker.startDate).locale('id').format('dddd, D MMMM YYYY');
                let endDate = moment(picker.endDate).locale('id').format('dddd, D MMMM YYYY');
                
                table.ajax.url('/administrator/reports/return/orderReportReturnDatatables?date=' + startDate + ' - ' + endDate).load();
                
                // change style none
                $('#downloadButton').css({ "display" : 'block' });

                // Update the span text inside the date range picker
                $('#created_at span').html(startDate + ' - ' + endDate);
            });

            // generate pdf
            $('#exportPDF').on('click', function(e) {
                console.log('it works');
                e.preventDefault();
                const href = $(this).data('href');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mendownload laporan pengembalian pesan?',
                    icon: 'warning',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Download PDF',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: href,
                            type: 'GET',
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
                                        window.open(response.file_url, '_blank');
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
                                    timer: 2000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    willClose: () => {
                                       window.location.reload(true);
                                    }
                                });
                            }
                        });
                    }
                });
            });
        })
    </script>
@endsection

@section('css')
    <style>
        .custom-margin {
            margin-bottom: 5px;
        }
    </style>
@endsection