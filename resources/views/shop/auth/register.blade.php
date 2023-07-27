@extends('shop.layouts.auth')

@section('page_title', 'Register')

@section('content')

<div class="card card-authentication2 mx-auto my-5">
    <div class="card-body">
        <div class="card-content p-2">
        <div class="text-center">
            <img src="{{ asset('staff/admin/images/logo-icon.png') }}" alt="logo icon">
            <div class="card-title text-uppercase text-center py-3">Vendor Registeration</div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <form class="vendor-register" id="wizard-validation-form" action="{{ route('shop.register') }}" method="post">
                        @csrf
                    <div>
                        <h3>Vendor Profile</h3>
                        <section>
                            <div class="form-group">
                                <label class="control-label">Account Name *</label>
                                <input type="text" name="vendor_name" value="{{ old('vendor_name') }}"
                                       class="form-control"
                                       required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Email *</label>
                                        <input type="email" name="email" value="{{ old('email') }}"
                                               class="form-control"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Phone *</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Password*</label>
                                        <div class="position-relative has-icon-right">
                                            <input type="password" name="password" class="form-control" required>
                                            <div class="form-control-position">
                                                <i class="icon-lock"></i>
                                            </div>
                                            <span class="eye"><i class="fa fa-eye-slash"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Confirm Password*</label>
                                        <div class="position-relative has-icon-right">
                                            <input type="password" name="password_confirmation" class="form-control"
                                                required>
                                            <div class="form-control-position">
                                                <i class="icon-lock"></i>
                                            </div>
                                            <span class="eye"><i class="fa fa-eye-slash"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-12 control-label">(*) Mandatory</label>
                            </div>
                        </section>
                        <h3>Account Info</h3>
                        <section>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Shop Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Shop Slug</label>
                                        <input type="text" name="slug" value="{{ old('slug') }}"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label time-picker">Opening At</label>
                                        <input type="text" name="opening_at" value="{{ old('opening_at') }}"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label time-picker">Closing At</label>
                                        <input type="text" name="closing_at" value="{{ old('closing_at') }}"
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
                                                    value="{{ $category->id }}" {{ old('shop_category_id') == $category->id?'selected':'' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="minimum_order">Minimum Order Amount (-1 for no limit)</label>
                                        <div class="input-group">
                                            <input type="number" id="minimum_order" step="any" name="minimum_order" value="{{ old('minimum_order')??-1 }}"
                                                   class="form-control" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ $currency->code }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Short Address</label>
                                <input type="text" name="address" value="{{ old('address') }}"
                                       class="form-control">
                            </div>
                            <div class="row">
                                <button class="btn btn-outline-success btn-sm pull-right" type="button" data-toggle="modal" data-target="#maps-modal">Pick from map</button>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Latitude</label>
                                        <input type="text" name="latitude" value="{{ old('latitude') }}"
                                               class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Longitude</label>
                                        <input type="text" name="longitude" value="{{ old('longitude') }}"
                                               class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <h3>Logo $ Cover</h3>
                        <section>
                            <div class="form-group form-og-wrap">
                                <img src="{{ old('cover')??'//via.placeholder.com/500x200' }}"
                                     id="add-cover-preview" class="img-fluid">
                                <input type="hidden" name="cover" id="add-cover-input" value="{{ old('cover') }}">
                                <button type="button"
                                        class="btn btn-block btn-success btn-select"
                                        data-preview="#add-cover-preview"
                                        data-input="#add-cover-input" data-prop="url" data-title="Select Cover">
                                    Select
                                    Cover
                                </button>
                            </div>
                            <div class="form-group">
                                <div class="text-center">
                                    <img src="{{ old('logo')??'//via.placeholder.com/200x200' }}"
                                         id="add-logo-preview" class="img-fluid">
                                </div>
                                <input type="hidden" name="logo" id="add-logo-input" value="{{ old('logo') }}">
                                <button type="button"
                                        class="btn btn-block btn-success btn-select"
                                        data-preview="#add-logo-preview"
                                        data-input="#add-logo-input" data-prop="url" data-title="Select Logo">Select
                                    Logo
                                </button>
                            </div>
                        </section>
                        <h3>Finish</h3>
                        <section>
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <div class="icheck-material-white">
                                        <input id="checkbox-h" type="checkbox">
                                        <label for="checkbox-h">
                                            I agree with the Terms and Conditions.
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </form> 
            </div>
        </div><!-- End Row-->
    </div>
</div>
<div>

@endsection

@section('js')
    <script>
        $(document).on('click', '.eye', function (e) {
            if (document.getElementById('password').type === 'password') {
                document.getElementById('password').type = 'text'
                $(this).children('i').removeClass('fa-eye-slash')
                $(this).children('i').addClass('fa-eye')
            } else {
                document.getElementById('password').type = 'password'
                $(this).children('i').removeClass('fa-eye')
                $(this).children('i').addClass('fa-eye-slash')
            }
        })
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
                value: '10:00 am'
            });
            $('[name="closing_at"]').timepicker({
                uiLibrary: 'bootstrap4',
                format: 'hh:MM tt',
                value: '06:00 pm'
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
