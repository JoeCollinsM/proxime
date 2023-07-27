@extends('staff.layouts.app')

@section('page_title', 'Edit Shop')

@section('css_libs')
    <link href="{{ asset('staff/vendors/gijgo/css/gijgo.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('content')
     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Shop</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.shop.index') }}">Shop</a></li>
            <li class="breadcrumb-item active">Create</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <form action="{{ route('staff.shop.update', $shop->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div
                                class="card-header">
                                <div class="pull-left">
                                    <h5>General Info</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Shop Name</label>
                                            <input type="text" name="name" value="{{ $shop->name }}"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Shop Slug</label>
                                            <input type="text" name="slug" value="{{ $shop->slug }}"
                                                   class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label time-picker">Opening At</label>
                                            <input type="text" name="opening_at" value="{{ $shop->opening_at->format('h:i a') }}"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label time-picker">Closing At</label>
                                            <input type="text" name="closing_at" value="{{ $shop->closing_at->format('h:i a') }}"
                                                   class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Shop Category</label>
                                            <select name="shop_category_id" class="form-control select2">
                                                @foreach($categories as $category)
                                                    <option
                                                        value="{{ $category->id }}" {{ $shop->shop_category_id == $category->id?'selected':'' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Shop Status</label>
                                            <select name="status" class="form-control select2">
                                                <option value="1" {{ $shop->status == 1?'selected':'' }}>Active</option>
                                                <option value="2" {{ $shop->status == 2?'selected':'' }}>Deactivate
                                                </option>
                                                <option value="0" {{ $shop->status == 0?'selected':'' }}>Pending
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="minimum_order">Minimum Order Amount (-1 for no limit)</label>
                                            <div class="input-group">
                                                <input type="number" id="minimum_order" step="any" name="minimum_order" value="{{ $shop->minimum_order??-1 }}"
                                                       class="form-control" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $currency->code }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="system_commission">System Commission/Order (before shipping cost)</label>
                                            <div class="input-group">
                                                <input type="number" id="system_commission" min="0" max="100" step="any" name="system_commission" value="{{ $shop->system_commission??10 }}"
                                                       class="form-control" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="pull-left">
                                    <h5>Location Info</h5>
                                </div>
                                <button class="btn btn-outline-success btn-sm pull-right" type="button" data-toggle="modal" data-target="#maps-modal">Pick from map</button>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label">Short Address</label>
                                    <input type="text" name="address" value="{{ $shop->address }}"
                                           class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Latitude</label>
                                            <input type="text" name="latitude" value="{{ $shop->latitude }}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Longitude</label>
                                            <input type="text" name="longitude" value="{{ $shop->longitude }}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="pull-left">
                                    <h5>Cover and Logo</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <img src="{{ $shop->cover??'//via.placeholder.com/500x200' }}"
                                         id="add-cover-preview" class="img-fluid">
                                    <input type="hidden" name="cover" id="add-cover-input" value="{{ $shop->cover }}">
                                    <button type="button"
                                            class="btn btn-block btn-success btn-select "
                                            data-preview="#add-cover-preview"
                                            data-input="#add-cover-input" data-prop="url" data-title="Select Cover">
                                        Select
                                        Cover
                                    </button>
                                </div>
                                <div class="form-group">
                                    <div class="text-center">
                                        <img src="{{ $shop->logo??'//via.placeholder.com/200x200' }}"
                                             id="add-logo-preview" class="img-fluid">
                                    </div>
                                    <input type="hidden" name="logo" id="add-logo-input" value="{{ $shop->logo }}">
                                    <button type="button"
                                            class="btn btn-block btn-success btn-select "
                                            data-preview="#add-logo-preview"
                                            data-input="#add-logo-input" data-prop="url" data-title="Select Logo">Select
                                        Logo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div
                                class="card-header">
                                <div class="pull-left">
                                    <h5>Access Info</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label">Account Name</label>
                                    <input type="text" name="vendor_name" value="{{ $shop->vendor_name }}"
                                           class="form-control"
                                           required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            <input type="email" name="email" value="{{ $shop->email }}"
                                                   class="form-control"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Phone (optional)</label>
                                            <input type="text" name="phone" value="{{ $shop->phone }}"
                                                   class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Password (optional)</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Confirm Password (optional)</label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div
                                class="card-header">
                                <div class="pull-left">
                                    <h5>Meta Info (Optional)</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label">Meta Title</label>
                                    <input type="text" name="meta[title]" value="{{ isset($meta['title'])?$meta['title']:null }}"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Meta Description</label>
                                    <textarea name="meta[description]"
                                              class="form-control">{{ isset($meta['description'])?$meta['description']:null }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Meta Keywords (separate by comma)</label>
                                    <input type="text" name="meta[keywords]"
                                           value="{{ isset($meta['keywords'])?$meta['keywords']:null }}"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <img src="{{ isset($meta['og_image'])?$meta['og_image']:'//via.placeholder.com/500x200' }}"
                                         id="og-image-preview" class="img-fluid">
                                    <input type="hidden" id="og-image-input" name="meta[og_image]"
                                           value="{{ isset($meta['og_image'])?$meta['og_image']:null }}"
                                           class="form-control">
                                    <button class="btn btn-success btn-block  btn-select"
                                            data-input="#og-image-input" data-preview="#og-image-preview"
                                            data-prop="url"
                                            data-title="Select Image">Select Image
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label">Shop Details</label>
                            <textarea name="details" class="form-control"
                                      id="editor">{!! clean($shop->details) !!}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-primary btn-lg">
                        UPDATE SHOP
                    </button>
                </div>
            </form>
        
@endsection

@section('js_libs')
    <script src="{{ asset('staff/vendors/gijgo/js/gijgo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('staff/vendors/tinymce/tinymce.min.js') }}"></script>
@endsection

@section('js')
    <script>
        var editor_config = {
            path_absolute: "{{ url('/') }}/",
            selector: "#editor",
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
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
                slugify()
                $('[name="opening_at"]').timepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'hh:MM tt',
                    value: $('[name="opening_at"]').val()
                });
                $('[name="closing_at"]').timepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'hh:MM tt',
                    value: $('[name="closing_at"]').val()
                });
            })
            $(document).on('change keypress keyup', '[name="name"]', function (e) {
                slugify()
            })
            $(document).on('click', '.btn-select', function (e) {
                e.preventDefault()
                var input = $($(this).data('input'))
                var preview = $($(this).data('preview'))
                var title = $(this).data('title')
                var prop = $(this).data('prop')
                openMediaManager(items => {
                    if (items[0] && items[0].hasOwnProperty(prop)) {
                        preview.attr('src', items[0][prop])
                        input.val(items[0][prop])
                    }
                }, 'image', title || 'Select Icon')
            })

            function slugify() {

                var string = $('[name="name"]').val();

                const a = 'àáäâãåăæçèéëêǵḧìíïîḿńǹñòóöôœøṕŕßśșțùúüûǘẃẍÿź·/_,:;'
                const b = 'aaaaaaaaceeeeghiiiimnnnooooooprssstuuuuuwxyz------'
                const p = new RegExp(a.split('').join('|'), 'g')

                var slug = string.toString().toLowerCase()
                    .replace(/\s+/g, '-') // Replace spaces with -
                    .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
                    .replace(/&/g, '-and-') // Replace & with ‘and’
                    .replace(/[^\w\-]+/g, '') // Remove all non-word characters
                    .replace(/\-\-+/g, '-') // Replace multiple - with single -
                    .replace(/^-+/, '') // Trim - from start of text
                    .replace(/-+$/, '') // Trim - from end of text

                $('[name="slug"]').val(slug)
            }
        })(jQuery)
    </script>
@endsection
