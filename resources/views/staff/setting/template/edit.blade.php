@extends('staff.layouts.app')

@section('page_title', 'Edit ' . ucfirst($notificationTemplate->channel) . ' Template')

@section('content')

    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item">{{ ucfirst($notificationTemplate->channel) }} Template</li>
            <li class="breadcrumb-item active">Edit</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->


    <div  id="app">
        <form action="{{ route('staff.setting.template.update', $notificationTemplate->id) }}" method="post">
            @csrf
            @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        @if(is_array($notificationTemplate->params) && count($notificationTemplate->params))
                            <div class="card">
                                <div
                                    class="card-header  d-flex justify-content-between align-content-center">
                                    <div class="header-left">
                                        <h5>All Shortcodes For This Template</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($notificationTemplate->params as $k => $m)
                                            <li class="list-group-item"><code>[{{ $k }}]</code> - {{ $m }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">{{ $notificationTemplate->channel == 'email'?'Subject':'Title' }}</label>
                                    <input type="text" id="title" name="title" class="form-control"
                                           placeholder="{{ config($notificationTemplate->channel . '.' . $notificationTemplate->name . '.title') }}" value="{{ $notificationTemplate->title }}">
                                </div>
                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <textarea name="content" id="content" class="form-control" placeholder="{{ config($notificationTemplate->channel . '.' . $notificationTemplate->name . '.content') }}"><?php echo $notificationTemplate->content; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-block btn-success" type="submit">
                                        UPDATE TEMPLATE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
@endsection

@section('js_libs')
    <script src="{{ asset('staff/vendors/tinymce/tinymce.min.js') }}"></script>
@endsection

@section('js')
    @if($notificationTemplate->channel == 'email')
    <script>
        var editor_config = {
            path_absolute: "{{ url('/') }}/",
            selector: "#content",
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
    @endif
@endsection
