@extends('staff.layouts.app')

@section('page_title', 'Email Settings')

@section('css_libs')
    <link href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection

@section('content')
     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Email settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Email Settings</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    <div  id="app">
        
    
        <form action="{{ route('staff.setting.email.update') }}" method="post">
            @csrf
            @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="smtp_host">SMTP Host</label>
                                    <input id="smtp_host" type="text" class="form-control" name="smtp_host"
                                           value="{{ get_option('smtp_host', config('mail.mailers.smtp.host')) }}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="smtp_port">SMTP Port</label>
                                    <input id="smtp_port" type="number" class="form-control" name="smtp_port"
                                           value="{{ get_option('smtp_port', config('mail.mailers.smtp.port')) }}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="smtp_encryption">SMTP Encryption</label>
                                    <select name="smtp_encryption" id="smtp_encryption" class="form-control select2">
                                        <option value="" {{ get_option('smtp_encryption', config('mail.mailers.smtp.encryption')) == ""?'selected':'' }}>No Encryption</option>
                                        <option value="ssl" {{ get_option('smtp_encryption', config('mail.mailers.smtp.encryption')) == "ssl"?'selected':'' }}>SSL</option>
                                        <option value="tls" {{ get_option('smtp_encryption', config('mail.mailers.smtp.encryption')) == "tls"?'selected':'' }}>TLS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="smtp_username">SMTP Username</label>
                                    <input id="smtp_username" type="text" class="form-control" name="smtp_username"
                                           value="{{ get_option('smtp_username', config('mail.mailers.smtp.username')) }}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="smtp_password">SMTP Password</label>
                                    <input id="smtp_password" type="text" class="form-control" name="smtp_password"
                                           value="{{ get_option('smtp_password', config('mail.mailers.smtp.password')) }}"
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_from_name">Mail From Name</label>
                                    <input id="mail_from_name" type="text" class="form-control" name="mail_from_name"
                                           value="{{ get_option('mail_from_name', config('mail.from.name')) }}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_from_address">Mail From Address</label>
                                    <input id="mail_from_address" type="email" class="form-control" name="mail_from_address"
                                           value="{{ get_option('mail_from_address', config('mail.from.address')) }}"
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block btn-lg"
                                    type="submit">Update Settings
                            </button>
                        </div>
                    </div>
                </div>>
        </form>
    </div>
@endsection

@section('js_libs')
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            $.fn.select2.defaults.set("theme", "bootstrap");
            $(document).ready(function () {
                $('.select2').select2();
            })
        })(jQuery)
    </script>
@endsection
