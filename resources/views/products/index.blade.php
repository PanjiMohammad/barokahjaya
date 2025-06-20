@extends('layouts.admin')

@section('title')
    <title>Produk</title>
@endsection

@section('content')

    <style>
        table.dataTable ol {
            padding-left: 20px;
            margin: 0; /* Reset default margin */
        }

        table.dataTable ol li {
            margin-bottom: 5px;
        }

        table.dataTable ul {
            padding-left: 20px;
            margin: 0; /* Reset default margin */
        }

        table.dataTable ul li {
            margin-bottom: 5px;
        }
    </style>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        {{-- <h1 class="m-0 text-dark">Produk</h1> --}}
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="#">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Produk</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="row">
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST PRODUCT  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="float-right">
                                    <button class="btn btn-danger btn-sm mr-1" data-toggle="modal" data-target="#bulkUploadModal">Mass Upload <span class="fa-regular fa-file ml-1"></span></button>
                                    <a href="{{ route('product.newCreate') }}" class="btn btn-primary btn-sm">Tambah Produk <span class="fa-solid fa-plus ml-1"></span></a>
                                </div>
                            </div>
                            <div class="card-body" id="loaderArea">
                                {{-- <!-- BUAT FORM UNTUK PENCARIAN, METHODNYA ADALAH GET -->
                                <form action="{{ route('product.newIndex') }}" method="get">
                                    <div class="col-md-12">
                                        <div class="row float-right">
                                            <div class="form-group mr-1">
                                                <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                            </div>
                                            <div class="form-group mr-1">
                                                <button class="btn btn-secondary" type="submit">Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </form> --}}

                                <div class="table-responsive">
                                    <table id="productTable" style="width: 100%;" class="table table-body">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Gambar</th>
                                                <th style="padding: 10px 10px;">Nama</th>
                                                <th style="padding: 10px 10px; width: 30%;">Deskripsi</th>
                                                <th style="padding: 10px 10px;">Harga</th>
                                                {{-- <th style="padding: 10px 10px;">Stok</th> --}}
                                                <th style="padding: 10px 10px;">Status</th>
                                                <th style="padding: 10px 10px;">Opsi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <!-- FUNGSI INI AKAN SECARA OTOMATIS MEN-GENERATE TOMBOL PAGINATION  -->
                                {{-- {!! $product->links() !!} --}}
                            </div>
                        </div>
                    </div>
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY  -->
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- Modal Detail Product -->
    <div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog" aria-labelledby="productDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailModalLabel">Produk Detail</h5>
                </div>
                <div class="modal-body modal-loader-area">
                    <table class="table table-borderless table-striped">
                        <tbody id="productDetailsContent">
                            <!-- Product details will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Upload Modal -->
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" role="dialog" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 85%;" role="document">
            <form id="bulkUploadForm" action="{{ route('product.newSaveBulk') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkUploadModalLabel">Mass Upload Produk</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body loader-area-modal">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="category_id">Kategori</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($category as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger" id="category_id_error"></p>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="file">File Excel</label>
                                    <input type="file" name="file" id="file" class="form-control">
                                    <small>*NB: Format File .xlsx</small>
                                    <p class="text-danger" id="file_error"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Preview</label>
                            <div id="preview" class="table-responsive"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- IMPORTANT LINK -->
    <a href="{{ route('product.datatables') }}" id="product_get_data"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
<script>
    $(document).ready(function(){

        $.extend($.fn.dataTable.defaults, {
            autoWidth: false,
            autoLength: false,
            dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
            language: {
                search: '<span>Pencarian:</span> _INPUT_',
                searchPlaceholder: 'Cari Produk...',
                lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                emptyTable: 'Tidak ada produk'
            },
            initComplete: function() {
                var $searchInput = $('#productTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Produk...');
                $searchInput.parent().addClass('d-flex align-items-center');

                var $lengthMenu = $('#productTable_length select').addClass('form-control form-control-sm');

                $lengthMenu.parent().addClass('d-flex align-items-center');
                
                $('#productTable_length').addClass('d-flex align-items-center');
            }
        });

        var url = $('#product_get_data').attr('href');
        var table = $('#productTable').DataTable({
            ajax: {
                url: url,
                beforeSend: function() {
                    $('#loaderArea').block({ 
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
                    }); // Show loader before request
                },
                complete: function() {
                    $('#loaderArea').unblock(); // Hide loader after request complete
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
                {data: null, sortable: false, orderable: false, searchable: false},
                {data: 'image', name: 'image', orderable: false, searchable: false},
                {data: 'productName', name: 'productName'},
                {data: 'description', name: 'description', width: '30%'},
                {data: 'price', name: 'price', render: function(data, type, row) {
                        // format angka
                        var formattedPrice = 'Rp ' + numeral(data).format('0,0');
                        return formattedPrice.replace(',', '.');
                    }
                },
                // {data: 'stock', name: 'stock', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            error: function(xhr, errorType, exception) {
                console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
            }
        });

        $('#productTable').on('click', '.detail-product', function() {
            var productId = $(this).data('product-id');
            const index = $(this).data('index');
            var showUrl = '{{ route("product.newShow", ":id") }}'.replace(':id', productId);

            $.ajax({
                url: showUrl,
                type: 'GET',
                beforeSend: function() {
                    $('.detail-product[data-index="' + index + '"]').block({ 
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
                    $('.detail-product[data-index="' + index + '"]').unblock();
                },
                success: function(response) {
                    // Create table rows from the response
                    var productDetailsHtml = `
                        <tr>
                            <th style="width: 15%;">Nama Produk</th>
                            <td style="width: 5%;">:</td>
                            <td style="width: 45%;">${response.name}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Kategori Produk</th>
                            <td style="width: 5%;">:</td>
                            <td style="width: 45%;">${response.category}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Deskripsi Produk</th>
                            <td style="width: 5%;">:</td>
                            <td style="width: 45%;">${response.description}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Harga Produk</th>
                            <td style="width: 5%;">:</td>
                            <td style="width: 45%;">${response.price}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Berat Produk</th>
                            <td style="width: 5%;">:</td>
                            <td style="width: 45%;">${response.weight}</td>
                        </tr>
                        ${response.status !== '-' && response.status !== null ? `
                            <tr>
                                <th style="width: 15%;">Status Produk</th>
                                <td style="width: 5%;">:</td>
                                <td style="width: 45%;">${response.status}</td>
                            </tr>
                        ` : ''}
                        <tr>
                            <th style="width: 10%;">Gambar Produk</th>
                            <td style="width: 5%;">:</td>
                            <td style="width: 45%;">
                                <div style="width: 100px; height: 100px; display: flex;">
                                    <img src="${response.image}" alt="${response.name}" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                            </td>
                        </tr>
                    `;

                    // Insert the HTML into the table body
                    $('#productDetailsContent').html(productDetailsHtml);
                    // Show the modal
                    $('#productDetailModal').modal('show');
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                    }
                    
                    if(xhr.status === 404){
                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error',
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Tidak dapat memuat detail produk. Coba lagi nanti.',
                            icon: 'error',
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.reload(true);
                            }
                        });
                    }
                }
            });
        });

        // hapus produk
        $('#productTable').on('click', '.delete-product', function(e) {
            e.preventDefault();

            var productId = $(this).data('product-id');
            const index = $(this).data('index');
            var deleteUrl = '{{ route("product.newDestroy", ":id") }}'.replace(':id', productId);

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus produk ini?',
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
                            $('#loaderArea').block({ 
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
                            $('#loaderArea').unblock();
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
                                title: 'Gagal',
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

        // reset preview
        $('#bulkUploadModal').on('hidden.bs.modal', function () {
            $('#preview').empty();  // Clear the preview content
            $('#file').val(''); // Reset the file input
        });

        $('#bulkUploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('.loader-area-modal').block({ 
                        message: '<i class="fa fa-spinner fa-spin"></i> Sedang mengupload data, harap menunggu...', 
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
                    $('.loader-area-modal').unblock();
                },
                success: function(response) {
                    $('#bulkUploadModal').modal('hide');
                    $('#submitBtn').html('Upload');
                    Swal.fire({
                        title: 'Berhasil',
                        text: response.success,
                        icon: 'success',
                        timer: 2000, // Display for 2 seconds
                        showCancelButton: false,
                        showConfirmButton: false,
                        willClose: () => {
                            table.ajax.reload();
                        }
                    });

                    // Clear the preview content
                    $('#preview').empty();  
                    $('#file').val('');
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
                        timer: 2000, // Display for 2 seconds
                        showCancelButton: false,
                        showConfirmButton: false,
                        willClose: () => {
                            if(xhr.status === 422){
                                $('#bulkUploadModal').modal('show');
                                let errorMessage = '';
                                $.each(errors, function(key, error) {
                                    errorMessage += error[0] + '<br>';
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
                                table.ajax.reload();
                            }
                        }
                    });
                    $('#submitBtn').attr('disabled', false).html('Upload');

                    $('#preview').empty(); 
                    $('#file').val('');
                }
            });
        });

        document.getElementById('file').addEventListener('change', function(e) {
            var file = e.target.files[0];
            var fileType = file.type;

            // Check if the file is an xlsx file
            if (fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var data = new Uint8Array(e.target.result);
                    var workbook = XLSX.read(data, {type: 'array'});
                    var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    var json = XLSX.utils.sheet_to_json(firstSheet, {header: 1});

                    // Check if the file has no data
                    if (json.length === 0 || (json.length === 1 && json[0].length === 0)) {
                        var previewElement = document.getElementById('preview');
                        previewElement.innerHTML = '<p class="alert alert-warning">Tidak ada data pada file excel ini.</p>';

                        setTimeout(function() {
                            previewElement.innerHTML = '';
                            window.location.reload(true);
                        }, 2000);
                        return;
                    }

                    var html = '<table class="table table-bordered table-hover">';
                    for (var i = 0; i < json.length; i++) {
                        html += '<tr>';
                        for (var j = 0; j < json[i].length; j++) {
                            if (i == 0) {
                                html += '<th>' + json[i][j] + '</th>';
                            } else {
                                html += '<td>' + json[i][j] + '</td>';
                            }
                        }
                        html += '</tr>';
                    }
                    html += '</table>';
                    document.getElementById('preview').innerHTML = html;
                };
                reader.readAsArrayBuffer(file);
            } else {
                var previewElement = document.getElementById('preview');
                previewElement.innerHTML = '<div class="alert alert-danger">Format file tidak mendukung.</div>';

                setTimeout(function() {
                    previewElement.innerHTML = '';
                    window.location.reload(true);
                }, 2000);
            }
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