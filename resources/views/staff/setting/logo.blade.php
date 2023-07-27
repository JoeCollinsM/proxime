@extends('staff.layouts.app')

@section('page_title', 'Logo Settings')

@section('content')
     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Logo settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Logo Settings</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    <div id="app">
    
        <form action="{{ route('staff.setting.logo.update') }}" method="post">
            @csrf
            @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group text-center">
                                    <div>
                                        <img class="img-thumbnail" src="{{ get_option('large_logo', config('ui.logo.large')) }}" id="large-logo" alt="Large Logo">
                                    </div>
                                    <input type="hidden" name="large_logo" id="large_logo" value="{{ get_option('large_logo', config('ui.logo.large')) }}">
                                    <button class="btn btn-outline-info btn-lg btn-select" data-input="#large_logo" data-preview="#large-logo" data-prop="url" data-title="Select Large Logo">Change Large Logo</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group text-center mt-auto">
                                    <div>
                                        <img class="img-thumbnail" src="{{ get_option('small_logo', config('ui.logo.small')) }}" id="small-logo" alt="Small Logo">
                                    </div>
                                    <input type="hidden" name="small_logo" id="small_logo" value="{{ get_option('small_logo', config('ui.logo.small')) }}">
                                    <button class="btn btn-outline-info btn-lg btn-select" data-input="#small_logo" data-preview="#small-logo" data-prop="url" data-title="Select Small Logo">Change Small Logo</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group text-center mt-auto">
                                    <div>
                                        <img class="img-thumbnail" src="{{ get_option('favicon') }}" id="favicon" alt="favicon">
                                    </div>
                                    <input type="hidden" name="favicon" id="favicon-input" value="{{ get_option('favicon') }}">
                                    <button class="btn btn-outline-info btn-lg btn-select" data-input="#favicon-input" data-preview="#favicon" data-prop="url" data-title="Select Favicon">Change Favicon</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block btn-lg"
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
