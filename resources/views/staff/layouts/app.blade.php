<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('page_title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ get_option('favicon')??asset('staff/images/favicon.png') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/notifications/css/lobibox.min.css') }}">
    <!-- simplebar CSS-->
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/simplebar/css/simplebar.css') }}">
    <!-- animate CSS-->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/animate.css') }}">
    <!-- Icons CSS-->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/icons.css') }}">
    <!-- Sidebar CSS-->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/sidebar-menu.css') }}">
    <!-- Custom Style-->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/app-style.css') }}">
    <!-- skins CSS-->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/skins.css') }}">

    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/bootstrap-datatable/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/bootstrap-datatable/css/buttons.bootstrap4.min.css') }}">
    
    <script src="{{ asset('staff/admin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/notifications/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/notifications/js/notifications.min.js') }}"></script>
  
 

@yield('css_libs')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">

@yield('css')
<!-- Modernizr Js -->
    <!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a
            href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
</head>

<body>
  @include('layouts.notify')
   <!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner"><div class="loader"></div></div></div></div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">
 
  <!--Start sidebar-wrapper-->
@include('staff.layouts.partials.sidebar')

    @include('staff.layouts.partials.header')

    <div class="content-wrapper">
        <div class="container-fluid">
            @yield('content')

            @include('staff.layouts.partials.maps')

              <!--start overlay-->
            <div class="overlay toggle-menu"></div>
              <!--end overlay-->
        </div>
          <!-- End container-fluid-->
    
    </div><!--End content-wrapper-->
   <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
  

	<!--Start footer-->
    @include('staff.layouts.partials.footer')
	<!--End footer-->
	
	<!--start color switcher-->
   <!-- <div class="right-sidebar">
    <div class="switcher-icon">
      <i class="zmdi zmdi-settings zmdi-hc-spin"></i>
    </div>
    <div class="right-sidebar-content">
	
	
	 <p class="mb-0">Header Colors</p>
      <hr>
	  
	  <div class="mb-3">
	    <button type="button" id="default-header" class="btn btn-outline-primary">Default Header</button>
	  </div>
      
      <ul class="switcher">
        <li id="header1"></li>
        <li id="header2"></li>
        <li id="header3"></li>
        <li id="header4"></li>
        <li id="header5"></li>
        <li id="header6"></li>
      </ul>

      <p class="mb-0">Sidebar Colors</p>
      <hr>
	  
      <div class="mb-3">
	    <button type="button" id="default-sidebar" class="btn btn-outline-primary">Default Header</button>
	  </div>
	  
      <ul class="switcher">
        <li id="theme1"></li>
        <li id="theme2"></li>
        <li id="theme3"></li>
        <li id="theme4"></li>
        <li id="theme5"></li>
        <li id="theme6"></li>
      </ul>
      
     </div>
   </div> -->
  <!--end color switcher-->
  
  </div><!--End wrapper-->

  
  <script src="{{ asset('js/app.js') }}"></script>

 <!-- Bootstrap core JavaScript-->
 {{-- <script src="{{ asset('staff/admin/js/jquery.min.js') }}"></script> --}}
  <script src="{{ asset('staff/admin/js/popper.min.js') }}"></script>
  <script src="{{ asset('staff/admin/js/bootstrap.min.js') }}"></script>
	
 <!-- simplebar js -->
  <script src="{{ asset('staff/admin/plugins/simplebar/js/simplebar.js') }}"></script>
  <!-- sidebar-menu js -->
  <script src="{{ asset('staff/admin/js/sidebar-menu.js') }}"></script>
  <!-- loader scripts -->
  <!-- <script src="{{ asset('staff/admin/js/jquery.loading-indicator.js') }}"></script> -->
  <!-- Custom scripts -->
  <script src="{{ asset('staff/admin/js/app-script.js') }}"></script>

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9aSb6xWsX5p_RV9v-2r-x4vhTE2F0t6Q&libraries=places&callback=initMaps" async defer></script>
  <script src="{{ asset('staff/admin/plugins/gmaps/map-custom-script.js') }}"></script>


 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/dataTables.buttons.min.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/buttons.bootstrap4.min.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/jszip.min.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/pdfmake.min.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/vfs_fonts.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/buttons.html5.min.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/buttons.print.min.js') }}"></script>
 <script src="{{ asset('staff/admin/plugins/bootstrap-datatable/js/buttons.colVis.min.js') }}"></script>

  <script src="{{ asset('staff/admin/plugins/jquery-knob/excanvas.js') }}"></script>
  <script src="{{ asset('staff/admin/plugins/jquery-knob/jquery.knob.js') }}"></script>

<script src="{{ asset('staff/js/jquery.scrollbar.min.js') }}"></script>

@yield('js_libs')
<!-- main js -->
<script src="{{ asset('staff/js/scripts.js') }}"></script>
<script>
     $(function() {
            $(".knob").knob();
        });
    function openMediaManager(callback, type, title) {
        type = type || 'image'
        title = title || 'FileManager'
        window.open('{{ route('staff.unisharp.lfm.show') }}?type=' + type, title, 'width=900,height=600');
        window.SetUrl = callback
    }
</script>


@yield('js')
</body>

</html>
