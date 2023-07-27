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

    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/jquery.steps/css/jquery.steps.css') }}">
    <link href="{{ asset('staff/vendors/gijgo/css/gijgo.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}"> 
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/notifications/css/lobibox.min.css') }}">

    <script src="{{ asset('staff/admin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/notifications/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/notifications/js/notifications.min.js') }}"></script>
  
    <!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a
            href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
  
    </head>

<body>
    @include('layouts.notify')
<!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

    <div class="loader-wrapper">
        <div class="lds-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
	
		 	    
                @yield('content')
                @include('staff.layouts.partials.maps')
           
    
     <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	
	
	
	</div><!--wrapper-->


<!-- =============================================
               Jquery plugins
============================================== -->
<!-- Bootstrap core JavaScript-->
<script src="{{ asset('staff/admin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('staff/admin/js/popper.min.js') }}"></script>
        <script src="{{ asset('staff/admin/js/bootstrap.min.js') }}"></script>
	
  <!-- sidebar-menu js -->
  <script src="{{ asset('staff/admin/js/sidebar-menu.js') }}"></script>
  <script src="{{ asset('staff/admin/plugins/jquery.steps/js/jquery.steps.min.js') }}"></script>
  <script src="{{ asset('staff/admin/plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('staff/admin/plugins/jquery.steps/js/jquery.wizard-init.js') }}"></script>
  <script src="{{ asset('staff/vendors/gijgo/js/gijgo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('staff/vendors/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript"
          src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTL9IXnXdhxFXPu_n8G7XANxRu5mCeJkE"></script>
  <script src="https://unpkg.com/location-picker/dist/location-picker.min.js"></script>
  
  <!-- Custom scripts -->
  <script src="{{ asset('staff/admin/js/app-script.js') }}"></script>
@yield('js')
</body>

</html>
