@extends('layouts.ecommerce')

@section('title')
    <title>Wishlists - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Wishlists</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('customer.wishlist') }}">WishLists</a>
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
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Wishlists</h4>
                                </div>
								<div class="card-body loader-area">
                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif 
                                    
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
									<div class="table-responsive">
                                        <table id="wishlistTable" style="width: 100%;" class="table table-body">
                                            <thead>
                                                <tr>
                                                    <th style="padding: 10px 10px;">#</th>
                                                    <th style="padding: 10px 10px;">Gambar</th>
                                                    <th style="padding: 10px 10px;">Nama Produk</th>
                                                    <th style="padding: 10px 10px;">Harga</th>
                                                    <th style="padding: 10px 10px;">Aksi</th>
                                                </tr>
                                            </thead>
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

    <!-- IMPORTANT LINK -->
    <a href="{{ route('customer.wishlistDatatables') }}" id="wishlistGetData"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            
            // action
            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Barang...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada produk'
                },
                initComplete: function() {
                    var $searchInput = $('#wishlistTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Barang...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#wishlistTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#wishlistTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#wishlistGetData').attr('href');
            var table = $('#wishlistTable').DataTable({
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
                        }); // Show loader before request
                    },
                    complete: function() {
                        $('.loader-area').unblock(); // Hide loader after request complete
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
                    {data: 'name', name: 'name'},
                    {data: 'amount', name: 'amount'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // hapus produk
            $('#wishlistTable').on('click', '.delete-wishlist', function(e) {
                e.preventDefault();

                var productId = $(this).data('wishlist-id');
                var deleteUrl = '{{ route("customer.deleteWishlist", ":id") }}'.replace(':id', productId);

                bootbox.confirm({
					message: '<i class="fas fa-exclamation-triangle text-warning mr-2"></i> Kamu yakin menghapus produk ini dari daftar keinginan?',
					backdrop: true,
					buttons: {
						confirm: {
							label: 'Ya <i class="fas fa-check ml-1"></i>',
							className: 'btn-success btn-sm'
						},
						cancel: {
							label: 'Tidak <i class="fas fa-xmark ml-1"></i>',
							className: 'btn-danger btn-sm'
						}
					},
					callback: function(result) {
						if(result) {
                            $.ajax({
                                url: deleteUrl,
                                type: 'DELETE',
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
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
                                complete: function() {
                                    $('.loader-area').unblock();
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
                                        table.ajax.reload(); // Reload the table without resetting pagination
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
                                    setTimeout(function() {
                                        window.location.reload(true); // Reload the table without resetting pagination
                                    }, 1500);
                                }
                            });
                        }
                    }
                });
            });

        });
    </script>
@endsection