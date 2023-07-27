@extends('staff.layouts.app')

@section('page_title', 'New Shop Category')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('content')

    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Shop Categories</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.shop.index') }}">Shop</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.shop-category.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">New Category</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    
    <form action="{{ route('staff.shop-category.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            <h5>General Info</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label">Category Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Category Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                    class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Parent Category</label>
                            <select name="parent_id" class="form-control select2">
                                <option value="">No Parent</option>
                                @foreach($categories as $category)
                                    <option
                                        value="{{ $category->id }}" {{ old('parent_id') == $category->id?'selected':'' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select name="status" class="form-control select2">
                                <option value="1" {{ old('status') == 1?'selected':'' }}>Enable</option>
                                <option value="0" {{ old('status') == 0?'selected':'' }}>Disable</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="add-img-p">
                                <img src="{{ old('image')??'//via.placeholder.com/200x200' }}"
                                        id="add-image-preview">
                            </div>
                            <input type="hidden" name="image" id="add-image-input" value="{{ old('image') }}">
                            <button type="button" class="btn btn-success btn-select"
                                    data-preview="#add-image-preview"
                                    data-input="#add-image-input" data-prop="thumb_url">Select Icon
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="pull-left">
                            <h5>Meta Info (Optional)</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label">Meta Title</label>
                            <input type="text" name="meta[title]" value="{{ optional(old('meta'))['title'] }}"
                                    class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Meta Description</label>
                            <textarea name="meta[description]"
                                        class="form-control">{{ optional(old('meta'))['description'] }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Meta Keywords (separate by comma)</label>
                            <input type="text" name="meta[keywords]"
                                    value="{{ optional(old('meta'))['keywords'] }}"
                                    class="form-control">
                        </div>
                        <div class="form-group form-og-wrap">
                            <img src="{{ optional(old('meta'))['og_image']??'//via.placeholder.com/500x200' }}"
                                    id="og-image-preview" class="img-fluid">
                            <input type="hidden" id="og-image-input" name="meta[og_image]"
                                    value="{{ optional(old('meta'))['og_image'] }}"
                                    class="form-control">
                            <button class="btn btn-success btn-block"
                                    data-input="#og-image-input" data-preview="#og-image-preview" data-prop="url"
                                    data-title="Select Image">Select Image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <button type="submit"
                    class="btn btn-block btn-primary btn-lg">
                SAVE CATEGORY
            </button>
        </div>
    </form>
        
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
                slugify()
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
