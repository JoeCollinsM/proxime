@extends('staff.layouts.app')

@section('page_title', 'New Category')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('css')
    <style>
        [v-cloak] {
            display: none;
        }

        .accordion {
            min-height: unset;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Categories</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.catalog.category.index') }}">Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">New category</li>
            </ol>
	   </div>
	   <div class="col-sm-3">
       
     </div>
     </div>
    <!-- End Breadcrumb-->


   
    <div id="app">
            
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('staff.catalog.category.store') }}" method="post">
                        @csrf
                        <div class="row  d-flex justify-content-between align-content-center">
                            <div class="card-title">General Information</div>
                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary btn-round px-5"> <i class="icon-lock"></i>Save
                                </button>
                                <button type="reset" class="btn btn-danger btn-round px-5">Reset</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <input type="text" name="name"
                                        class="form-control" v-model="name" required>
                                </div>
                            
                                <div class="form-group">
                                    <label class="control-label">Slug</label>
                                    <input type="text" name="slug"
                                        class="form-control" v-model="slug" required>
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
                                    <label for="tags" class="control-label">Tags</label>
                                    <select name="tags[]" class="form-control" id="tags" multiple>
                                        @foreach($tags as $tag)
                                            <option
                                                value="{{ $tag->id }}" {{ (is_array(old('tags')) && in_array($tag->id, old('tags')))?'selected':'' }}>{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <img :src="image"
                                        class="img-fluid">
                                    <input type="hidden" name="image" v-model="image">
                                    <button class="btn btn-success cag-btn"
                                            @click.prevent="selectImage">
                                        Select Image
                                    </button>
                                </div>
                            
                                <div class="form-group">
                                    <label class="control-label">Status</label>
                                    <select name="status" class="form-control select2">
                                        <option value="1" {{ old('status') == 1?'selected':'' }}>Enable</option>
                                        <option value="0" {{ old('status') == 0?'selected':'' }}>Disabled
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                    

                        <hr>
                        <div class="row  d-flex justify-content-between align-content-center">
                            <div class="card-title">Meta Information</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-6">
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
                                        class="form-control tags">
                                </div>
                                <div class="form-group form-og-wrap">
                                    <img src="{{ optional(old('meta'))['og_image']??'//via.placeholder.com/500x200' }}"
                                        id="og-image-preview" class="w-100">
                                    <input type="hidden" id="og-image-input" name="meta[og_image]"
                                        value="{{ optional(old('meta'))['og_image'] }}"
                                        class="form-control">
                                    <button class="btn btn-success btn-block btn-select"
                                            data-input="#og-image-input" data-preview="#og-image-preview"
                                            data-prop="url"
                                            data-title="Select Image">Select Image
                                    </button>
                                </div>
                            </div>
                        </div>

                        @foreach($languages as $language)
                        <hr>
                        <div class="row  d-flex justify-content-between align-content-center">
                            <div class="card-title">Translation ({{ $language->code }})</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <input type="text" name="lang[{{ $language->code }}][name]"
                                        value="{{ (is_array(old('lang')) && is_array(old('lang')[$language->code]))?old('lang')[$language->code]['name']:'' }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
        
    </div>
    </div>
@endsection

@section('js_libs')
    <script src="{{ asset('staff/js/vue.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            $.fn.select2.defaults.set("theme", "bootstrap");
            $(document).ready(function () {
                $('.select2').select2();
                $('#tags').select2({
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
        })(jQuery)
    </script>
    <script>
        window.makeSlug = function (str) {
            if (!str) return;
            const a = 'àáäâãåăæçèéëêǵḧìíïîḿńǹñòóöôœøṕŕßśșțùúüûǘẃẍÿź·/_,:;'
            const b = 'aaaaaaaaceeeeghiiiimnnnooooooprssstuuuuuwxyz------'
            const p = new RegExp(a.split('').join('|'), 'g')

            return str.toString().toLowerCase()
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
                .replace(/&/g, '-and-') // Replace & with ‘and’
                .replace(/[^\w\-]+/g, '') // Remove all non-word characters
                .replace(/\-\-+/g, '-') // Replace multiple - with single -
                .replace(/^-+/, '') // Trim - from start of text
                .replace(/-+$/, '') // Trim - from end of text
        }
        window.app = new Vue({
            el: '#app',
            data: {
                name: '{{ old('name')??'' }}',
                image: '{{ old('image')??'https://via.placeholder.com/200' }}',
                slug: '{{ old('slug')??'' }}',
            },
            methods: {
                makeSlug(str) {
                    return window.makeSlug(str)
                },
                selectImage() {
                    openMediaManager(items => {
                        if (items[0] && items[0].hasOwnProperty('thumb_url')) {
                            this.image = items[0].thumb_url
                        }
                    }, 'image', 'Select Image')
                }
            },
            watch: {
                name: {
                    handler: function (newValue) {
                        this.slug = window.makeSlug(newValue)
                    },
                    deep: true
                }
            }
        })
    </script>
@endsection
