@extends('staff.layouts.app')

@section('page_title', 'App Settings')

@section('content')
    
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">App settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">App Settings</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    <div id="app">
        <form action="{{ route('staff.setting.app.update') }}" method="post">
            @csrf
            @method('PUT')
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="android_app_version">App Version</label>
                                    <input type="text"
                                           value="{{ get_option('android_app_version', config('proxime.app.app_version.android')) }}"
                                           name="android_app_version" id="android_app_version"
                                           class="form-control form-control-lg" placeholder="3.8.16">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="google_map_api_key">Google Map API Key</label>
                                    <input type="text"
                                           value="{{ get_option('google_map_api_key', config('proxime.app.api_key.google_map_api_key')) }}"
                                           name="google_map_api_key" id="google_map_api_key"
                                           class="form-control form-control-lg">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="direction_api_key">Google Direction API Key</label>
                                    <input type="text"
                                           value="{{ get_option('direction_api_key', config('proxime.app.api_key.direction_api_key')) }}"
                                           name="direction_api_key" id="direction_api_key"
                                           class="form-control form-control-lg">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="app_primary_color">Primary Color</label>
                                    <input type="color"
                                           value="{{ get_option('app_primary_color', config('proxime.app.color.color_primary')) }}"
                                           name="app_primary_color" id="app_primary_color"
                                           class="form-control form-control-lg">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="app_primary_dark_color">Primary Dark Color</label>
                                    <input type="color"
                                           value="{{ get_option('app_primary_dark_color', config('proxime.app.color.color_primary_dark')) }}"
                                           name="app_primary_dark_color" id="app_primary_dark_color"
                                           class="form-control form-control-lg">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="app_accent_color">Accent Color</label>
                                    <input type="color"
                                           value="{{ get_option('app_accent_color', config('proxime.app.color.color_accent')) }}"
                                           name="app_accent_color" id="app_accent_color"
                                           class="form-control form-control-lg">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_button_color_1">Primary Button Color</label>
                                    <input type="color"
                                           value="{{ get_option('app_button_color_1', config('proxime.app.color.button_color_1')) }}"
                                           name="app_button_color_1" id="app_button_color_1"
                                           class="form-control form-control-lg">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_button_color_2">Secondary Button Color</label>
                                    <input type="color"
                                           value="{{ get_option('app_button_color_2', config('proxime.app.color.button_color_2')) }}"
                                           name="app_button_color_2" id="app_button_color_2"
                                           class="form-control form-control-lg">
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <div>
                                <img class="img-thumbnail"
                                     src="{{ get_option('app_splash_logo', config('proxime.app.splash.logo')) }}"
                                     id="splash-logo" alt="Splash Logo">
                            </div>
                            <input type="hidden" name="app_splash_logo" id="app_splash_logo"
                                   value="{{ get_option('app_splash_logo', config('proxime.app.splash.logo')) }}">
                            <button class="btn btn-outline-info btn-lg btn-select"
                                    data-input="#app_splash_logo" data-preview="#splash-logo" data-prop="url"
                                    data-title="Select Splash Logo">Change Splash Logo
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block btn-lg "
                                    type="submit">Update Settings
                            </button>
                        </div>
                    </div>
                </div>
           
        </form>
    </div>
@endsection

@section('js')
    <script>
        (function ($) {
            $(document).on('click', '.btn-select', function (e) {
                e.preventDefault()
                const preview = $($(this).data('preview'))
                const input = $($(this).data('input'))
                const title = $(this).data('title')
                const prop = $(this).data('prop')
                openMediaManager(items => {
                    if (items[0] && items[0].hasOwnProperty(prop)) {
                        input.val(items[0][prop])
                        preview.attr('src', items[0][prop])
                    }
                }, 'image', title)
            })
        })(jQuery)
    </script>
@endsection
