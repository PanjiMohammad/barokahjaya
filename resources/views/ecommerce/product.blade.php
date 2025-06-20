@extends('layouts.ecommerce')

@section('title')
    <title>Jual Produk Fashion - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
        <div class="banner_inner d-flex align-items-center">
            <div class="container">
                <div class="banner_content text-center">
                    <h2>Jual Produk</h2>
                    <div class="page_link">
                        <a href="{{ route('front.index') }}">Home</a>
                        <a href="{{ route('front.product') }}">Produk</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Home Banner Area =================-->

    <!--================Category Product Area =================-->
    <section class="cat_product_area section_gap">
        <div class="container-fluid">
            <div class="row flex-row-reverse">
                <div class="col-lg-9">
                    <div class="product_top_bar">
                        <div class="left_dorp">
                            <form action="{{ route('front.product') }}" method="get">
                                <div class="input-group mb-12 col-md-12 float-right">
                                    <select name="price" class="form-control">
                                        <option value="">Sorting</option>
                                        <option value="ASC">Murah ke Mahal</option>
                                        <option value="DESC">Mahal Ke Murah</option>
                                    </select>
                                    <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">Cari</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="right_page ml-auto">
                            {{ $products->links() }}
                        </div>
                    </div>
                    <div class="latest_product_inner row">
                      
                      	<!-- PROSES LOOPING DATA PRODUK, SAMA DENGAN CODE YANG ADDA DIHALAMAN HOME -->
                        @forelse ($products as $row)
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="f_p_item">
                                <div class="f_p_img">
                                    <img class="img-fluid" src="{{ asset('/imageProducts/' . $row->image) }}" alt="{{ $row->name }}">
                                    <div class="p_icon">
                                        <a href="{{ url('/product/' . $row->slug) }}">
                                            <i class="lnr lnr-cart"></i>
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ url('/product/' . $row->slug) }}">
                                    <h4>{{ $row->name }}</h4>
                                </a>
                                <h5>Rp {{ number_format($row->price) }}</h5>
                            </div>
                        </div>
                        @empty
                        <div class="col-md-12">
                            <h3 class="text-center">Tidak ada produk</h3>
                        </div>
                        @endforelse
                      <!-- PROSES LOOPING DATA PRODUK, SAMA DENGAN CODE YANG ADDA DIHALAMAN HOME -->
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="left_sidebar_area">
                        <aside class="left_widgets cat_widgets">
                            <div class="l_w_title">
                                <h3>Kategori Produk</h3>
                            </div>
                            <div class="widgets_inner">
                                <ul class="list">
                                  
                                  	<!-- PROSES LOOPING DATA KATEGORI -->
                                    @foreach ($categories as $category)
                                    <li>
                                        <!-- JIKA CHILDNYA ADA, MAKA KATEGORI INI AKAN MENG-EXPAND DATA DIBAWAHNYA -->
                                        <strong><a href="{{ url('/category/' . $category->slug) }}">{{ $category->name }}</a></strong>
                                        
                                      	<!-- PROSES LOOPING DATA CHILD KATEGORI -->
                                        @foreach ($category->child as $child)
                                        <ul class="list" style="display: block">
                                            <li>
                                                <a href="{{ url('/category/' . $child->slug) }}">{{ $child->name }}</a>
                                            </li>
                                        </ul>
                                        @endforeach
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>

          	<!-- GENERATE PAGINATION PRODUK -->
            <div class="row">
                {{ $products->links() }}
            </div>
        </div>
    </section>
    <!--================End Category Product Area =================-->
@endsection

