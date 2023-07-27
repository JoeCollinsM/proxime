@extends('staff.layouts.app')

@section('page_title', 'Create Banner')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('content')
     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Banner settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item"><a href="{{ route('staff.setting.banner.index') }}">Banners</a></li>
            <li class="breadcrumb-item active">Create</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    <div id="app">
    
        <form action="{{ route('staff.setting.banner.store') }}" method="post">
            @csrf
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <img class="img-thumbnail img-fluid"
                                         src="{{ old('image')??'https://picsum.photos/500/200' }}" id="banner-preview">
                                    <input type="hidden" name="image" value="{{ old('image')??'https://picsum.photos/500/200' }}" id="banner">
                                    <button class="btn btn-outline-info btn-select">Change Banner
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Banner Title</label>
                                            <input type="text" id="title" name="title" class="form-control" placeholder="Promo Banner" value="{{ old('title') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="subtitle">Subtitle</label>
                                            <input type="text" id="subtitle" name="subtitle" class="form-control" placeholder="Winter Offer" value="{{ old('subtitle') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shop_id">Shop</label>
                                            <select name="shop_id" class="form-control" id="shop_id">
                                                <option value="">No Shop</option>
                                                @foreach($shops as $shop)
                                                    <option value="{{ $shop->id }}" {{ (old('shop_id') == $shop->id)?'selected':'' }}>{{ $shop->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tags">Tags</label>
                                            <select name="tags[]" class="form-control" id="tags" multiple>
                                                @foreach($tags as $tag)
                                                    <option
                                                            value="{{ $tag->id }}" {{ (is_array(old('tags')) && in_array($tag->id, old('tags')))?'selected':'' }}>{{ $tag->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-block btn-success" type="submit">SAVE BANNER</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                $('#shop_id').select2()
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
                openMediaManager(items => {
                    if (items[0] && items[0].hasOwnProperty('url')) {
                        $('#banner').val(items[0].url)
                        $('#banner-preview').attr('src', items[0].url)
                    }
                }, 'image', 'Select Banner Image')
            })
        })(jQuery)
    </script>
@endsection
