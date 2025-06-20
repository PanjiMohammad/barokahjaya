<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="Barokah Jaya - Aplikasi Ecommerce">
    <meta name="author" content="Barokah Jaya">
    <meta name="keyword" content="aplikasi ecommerce laravel, tutorial laravel basic, belajar laravel, panduan belajar laravel">

    @yield('title')

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('admin-lte/dist/css/adminlte.min.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  @yield('css')

</head>
<body class="hold-transition login-page">
    @yield('content')

<!-- jQuery -->
<script src="{{asset('admin-lte/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('admin-lte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('admin-lte/plugins/chart.js/Chart.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('admin-lte/dist/js/demo.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('admin-lte/plugins/moment/moment.min.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('admin-lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('admin-lte/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('admin-lte/dist/js/adminlte.js')}}"></script>
<!-- Block UI -->
<script src="{{ asset('admin-lte/plugins/block-ui/jquery.blockUI.min.js') }}"></script>
<!-- Sweet Alert -->
<script src="{{ asset('admin-lte/plugins/sweetalert2/sweetalert2-new.min.js') }}"></script>
<!-- js read excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
<!-- DataTable -->
<script src="{{ asset('admin-lte/plugins/jquery-datatables/jquery.datatables.min.js') }}"></script>
<!-- Date Range Picker -->
<script src="{{ asset('admin-lte/plugins/daterangepicker/daterangepicker-new.min.js') }}"></script>
<!-- Moment -->
<script src="{{ asset('admin-lte/plugins/moment/moment.min.js') }}"></script>

  @yield('js')

</body>
</html>
