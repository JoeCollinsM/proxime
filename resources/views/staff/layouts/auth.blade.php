<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('page_title', 'Login') - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ get_option('favicon')??asset('staff/images/favicon.png') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/bootstrap.min.css') }}">
    <!-- animate CSS-->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/animate.css') }}">
    <!-- Icons CSS-->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/icons.css') }}">
    <!-- Custom Style-->
    <link rel="stylesheet" href="{{ asset('staff/admin/css/app-style.css') }}">
    <script src="{{ asset('staff/admin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('staff/admin/js/popper.min.js') }}"></script>
    <script src="{{ asset('staff/admin/js/bootstrap.min.js') }}"></script>
    <!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a
            href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
  
    </head>

<body>
    @include('layouts.notify')
<!-- start loader -->
<div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
        <div class="loader-wrapper-inner" >
            <div class="loader"></div>
            <p class="Loadertext" id="loadertext"></p>
        </div>
    </div>
</div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

 <div class="loader-wrapper"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
	<div class="card card-authentication1 mx-auto my-5">
		<div class="card-body">
		    <div class="card-content p-2">
		 	    
                @yield('content')
            </div>
	     </div>
    
     <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	
	
	
	</div><!--wrapper-->


<!-- =============================================
               Jquery plugins
============================================== -->
<!-- Bootstrap core JavaScript-->
<script src="{{ asset('js/app.js') }}"></script>	
  <!-- sidebar-menu js -->
  <script src="{{ asset('staff/admin/js/sidebar-menu.js') }}"></script>
  
  <!-- Custom scripts -->
  <script src="{{ asset('staff/admin/js/app-script.js') }}"></script>
@yield('js')
</body>

</html>
