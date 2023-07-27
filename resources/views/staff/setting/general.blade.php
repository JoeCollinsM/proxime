@extends('staff.layouts.app')

@section('page_title', 'General Settings')

@section('css_libs')
    <link href="{{ asset('staff/css/bootstrap4-toggle.min.css') }}"
          rel="stylesheet">
    <link href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection

@section('css')
    <style>
        .toggle.btn {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">General settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">General Settings</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    <div  id="app">
        <form action="{{ route('staff.setting.general.update') }}" method="post">
            @csrf
            @method('PUT')
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="project_name">Project Name</label>
                                    <input id="project_name" type="text" class="form-control" name="project_name"
                                           value="{{ get_option('project_name', config('app.name')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="timezone">Timezone</label>
                                    <select name="timezone" id="timezone" class="form-control select2">
                                        @foreach(config('timezones') as $k => $v)
                                            <option
                                                    value="{{ $k }}" {{ get_option('timezone', config('app.timezone')) == $k?'selected':'' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="debug_mode">Debug Mode</label>
                                    <input id="debug_mode" name="debug_mode" type="checkbox"
                                           {{ get_option('debug_mode', (config('app.debug')?1:0))?'checked':'' }} data-toggle="toggle"
                                           data-on="Enabled" data-off="Disabled" data-onstyle="danger"
                                           data-offstyle="success">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email_verification">Email Verification</label>
                                    <input id="email_verification" name="email_verification" type="checkbox"
                                           {{ get_option('email_verification', (config('proxime.email_verification')?1:0))?'checked':'' }} data-toggle="toggle"
                                           data-on="Enabled" data-off="Disabled" data-onstyle="success"
                                           data-offstyle="danger">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sms_verification">SMS Verification</label>
                                    <input id="sms_verification" name="sms_verification" type="checkbox"
                                           {{ get_option('sms_verification', (config('proxime.sms_verification')?1:0))?'checked':'' }} data-toggle="toggle"
                                           data-on="Enabled" data-off="Disabled" data-onstyle="success"
                                           data-offstyle="danger">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="forgot_password_via">Forgot Password Via</label>
                                    <select name="forgot_password_via" id="forgot_password_via"
                                            class="form-control select2">
                                        <option
                                                value="email" {{ get_option('forgot_password_via', config('proxime.forgot_password_via')) == 'email'?'selected':'' }}>
                                            Email
                                        </option>
                                        <option
                                                value="sms" {{ get_option('forgot_password_via', config('proxime.forgot_password_via')) == 'sms'?'selected':'' }}>
                                            SMS
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email_notification">Email Notification</label>
                                    <input id="email_notification" name="email_notification" type="checkbox"
                                           {{ get_option('email_notification', (config('proxime.email_notification')?1:0))?'checked':'' }} data-toggle="toggle"
                                           data-on="Enabled" data-off="Disabled" data-onstyle="success"
                                           data-offstyle="danger">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sms_notification">SMS Notification</label>
                                    <input id="sms_notification" name="sms_notification" type="checkbox"
                                           {{ get_option('sms_notification', (config('proxime.sms_notification')?1:0))?'checked':'' }} data-toggle="toggle"
                                           data-on="Enabled" data-off="Disabled" data-onstyle="success"
                                           data-offstyle="danger">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fcm_notification">FCM Notification (Firebase Cloud Messaging)</label>
                                    <input id="fcm_notification" name="fcm_notification" type="checkbox"
                                           {{ get_option('fcm_notification', (config('proxime.fcm_notification')?1:0))?'checked':'' }} data-toggle="toggle"
                                           data-on="Enabled" data-off="Disabled" data-onstyle="success"
                                           data-offstyle="danger">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="delivery_type">Delivery Commission Type</label>
                                    <select name="delivery_type" id="delivery_type"
                                            class="form-control select2">
                                        <option
                                                value="fixed" {{ get_option('delivery_type', config('proxime.delivery.type')) == 'fixed'?'selected':'' }}>
                                            Fixed
                                        </option>
                                        <option
                                                value="custom" {{ get_option('delivery_type', config('proxime.delivery.type')) == 'custom'?'selected':'' }}>
                                            Custom
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="delivery_custom_percentage">Delivery Commission (x% of total order amount)</label>
                                    <div class="input-group">
                                        <input id="delivery_custom_percentage" type="number" step="0.01" class="form-control" name="delivery_custom_percentage"
                                               value="{{ get_option('delivery_custom_percentage', config('proxime.delivery.custom_percentage')) }}" min="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sms_via">SMS Via</label>
                                    <select name="sms_via" id="sms_via"
                                            class="form-control select2">
                                        <option
                                                value="twilio" {{ get_option('sms_via', config('proxime.sms_via')) == 'twilio'?'selected':'' }}>
                                            Twilio
                                        </option>
                                        <option
                                                value="other" {{ get_option('sms_via', config('proxime.sms_via')) == 'other'?'selected':'' }}>
                                            Other API
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="default_user_status">Default Customer Status</label>
                                    <input id="default_user_status" name="default_user_status" type="checkbox"
                                           {{ get_option('default_user_status', (config('proxime.default_user_status')?1:0))?'checked':'' }} data-toggle="toggle"
                                           data-on="Enabled" data-off="Disabled" data-onstyle="success"
                                           data-offstyle="danger">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="default_vendor_status">Default Vendor Status</label>
                                    <input id="default_vendor_status" name="default_vendor_status" type="checkbox"
                                           {{ get_option('default_vendor_status', (config('proxime.default_vendor_status')?1:0))?'checked':'' }} data-toggle="toggle"
                                           data-on="Enabled" data-off="Disabled" data-onstyle="success"
                                           data-offstyle="danger">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="featured_tags">Featured Tags</label>
                                    <select name="featured_tags[]" class="form-control" id="featured_tags" multiple>
                                        @foreach($tags as $tag)
                                            <option
                                                    value="{{ $tag->id }}" {{ (in_array($tag->id, $featured_tag_ids))?'selected':'' }}>{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="faq_text">FAQ Content</label>
                                    <textarea id="faq_text" class="form-control editor" name="faq_text">{!! clean(get_option('faq_text', config('proxime.faq_text'))) !!}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="toc_text">Terms & Conditions Content</label>
                                    <textarea id="toc_text" class="form-control editor" name="toc_text">{!! clean(get_option('toc_text', config('proxime.toc_text'))) !!}</textarea>
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

@section('js_libs')
    <script src="{{ asset('staff/js/bootstrap4-toggle.min.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('staff/vendors/tinymce/tinymce.min.js') }}"></script>
@endsection

@section('js')
    <script>
        var editor_config = {
            path_absolute: "{{ url('/') }}/",
            selector: ".editor",
            convert_urls: false,
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern",
                "fullpage"
            ],
            toolbar: "fullpage | insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
            relative_urls: false,
            file_browser_callback: function (field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + 'staff/media?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                    var title = 'Select Images'
                } else {
                    cmsURL = cmsURL + "&type=Files";
                    var title = 'Select FIles'
                }

                tinyMCE.activeEditor.windowManager.open({
                    file: cmsURL,
                    title: title,
                    width: x * 0.8,
                    height: y * 0.8,
                    resizable: "yes",
                    close_previous: "no"
                });
            }
        };

        tinymce.init(editor_config);
    </script>
    <script>
        (function ($) {
            $.fn.select2.defaults.set("theme", "bootstrap");
            $(document).ready(function () {
                $('.select2').select2();
                $('#featured_tags').select2({
                    tags: true,
                    tokenSeparators: [','],
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: term,
                            text: term
                        }
                    }
                }).on('change', function (e) {
                    var isNew = $(this).find('[data-select2-tag="true"]');
                    if (isNew.length && $.inArray(isNew.val(), $(this).val()) !== -1) {
                        $.post('{{ route('staff.catalog.tag.store') }}', {
                            _token: '{{ csrf_token() }}',
                            name: isNew.val()
                        }).done(tag => {
                            isNew.replaceWith('<option selected value="' + tag.id + '">' + tag.name + '</option>');
                        })
                    }
                })
            })
        })(jQuery)
    </script>
@endsection
