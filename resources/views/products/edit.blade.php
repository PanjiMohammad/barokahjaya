@extends('layouts.admin')

@section('title')
    <title>Edit Produk</title>
@endsection

@section('content')
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
                            <a href="#">Produk</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Produk</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Produk</h4>
                    </div>
                    <form action="{{ route('product.newUpdate') }}" id="edit-product-form" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="card-body loader-area">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name">Nama Produk</label>
                                        <input type="text" name="name" class="form-control" value="{{ $product->name }}" placeholder="Masukkan Nama">
                                        <span class="text-danger" id="name_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Deskripsi</label>
                                    
                                        <!-- TAMBAHKAN ID YANG NNTINYA DIGUNAKAN UTK MENGHUBUNGKAN DENGAN CKEDITOR -->
                                        <textarea name="description" id="description" class="form-control" placeholder="Masukkan Deskripsi">{{ $product->description }}</textarea>
                                        <span class="text-danger" id="description_error"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ $product->status == '1' ? 'selected':'' }}>Publish</option>
                                            <option value="0" {{ $product->status == '0' ? 'selected':'' }}>Draft</option>
                                        </select>
                                        <span class="text-danger" id="status_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_id">Kategori</label>
                                        <select name="category_id" id="category_id" class="form-control">
                                            <option value="">Pilih</option>
                                            @foreach ($category as $row)
                                                <option value="{{ $row->id }}" {{ $product->category_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" id="category_id_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Harga</label>
                                        <input type="text" name="price" id="price" placeholder="Masukkan Harga" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="form-control" value="{{ $product->price }}">
                                        <span class="text-danger" id="price_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight">Berat</label>
                                        <input type="text" name="weight" id="weight" placeholder="Masukkan Berat" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="form-control" value="{{ $product->weight }}">
                                        <span class="text-danger" id="weight_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Foto Produk</label>
                                        <!-- show image -->    
                                        <div class="mt-2 mb-2">
                                            <img src="{{ asset('/imageProducts/' . $product->image) }}" width="100px" height="100px" alt="{{ $product->name }}">
                                        </div>
                                        <!-- end of show image -->

                                        <input type="file" name="image" id="image" class="form-control">
                                        <small class="font-weight-bold">*note: biarkan kosong jika tidak ingin mengganti gambar</small>
                                        <span class="text-danger" id="image_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="card-footer">
                            <div class="form-group">
                                <button class="btn btn-primary float-right">Tambah</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('js')
    <!-- LOAD CKEDITOR -->
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {

            // ckeditor 5
            let aboutEditor;

            ClassicEditor
                .create(document.querySelector('#description'))
                .then(editor => {
                    aboutEditor = editor;
                })
                .catch(error => {
                    console.error('There was a problem initializing CKEditor:', error);
                });
            
            $('#edit-product-form').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                formData.set('description', aboutEditor.getData());
                var actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
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
                            timer: 2000, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = "{{ route('product.newIndex') }}";
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
                                if (xhr.status == 500) {
                                    window.location.reload(true);
                                } else {
                                    $.each(errors, function(key, errorArr) {
                                        let message = errorArr[0];
                                        $('#' + key + '_error').text(message);

                                        let $input = $('#' + key);
                                        $input.addClass('input-error');

                                        if (key === 'description') {
                                            $('.ck-editor__main').addClass('input-error');
                                        }

                                        setTimeout(function () {
                                            $('#' + key + '_error').text('');
                                            $input.removeClass('input-error');

                                            if (key === 'description') {
                                                $('.ck-editor__main').removeClass('input-error');
                                            }
                                        }, 3000);
                                    });

                                    // Kembalikan nilai input sebelumnya
                                    $.each(input, function(key, value) {
                                        // Jika CKEditor, set isi editor
                                        if (key === 'description' && typeof editorInstance !== 'undefined') {
                                            editorInstance.setData(value);
                                        } else {
                                            $('#' + key).val(value);
                                        }
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