@extends('staff.layouts.app')

@section('page_title', 'Edit Delivery Man')

@section('css_libs')
    <link href="{{ asset('staff/css/bootstrap4-toggle.min.css') }}"
          rel="stylesheet">
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
		    <h4 class="page-title">Delivery Man</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.catalog.delivery-man.index') }}">Delivery Man</a></li>
            <li class="breadcrumb-item active">Edit</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    <div id="app">
        <form action="{{ route('staff.catalog.delivery-man.update', $user->id) }}" method="post">
            @csrf
            @method('PUT')
                
                <div class="row">
                    <div class="col-xl-3 col-lg-12">
                        <div class="card  avatar-pro">
                            <div class="card-body">
                                <div class="form-group text-center">
                                    <img class="img-thumbnail" style="width: 200px;height: 200px;"
                                         src="{{ $user->avatar??'https://picsum.photos/200' }}" id="avatar-preview">
                                    <input type="hidden" name="avatar" value="{{ $user->avatar??'https://picsum.photos/200' }}" id="avatar">
                                    <button class="btn btn-outline-info btn-select"
                                            style="width: 200px;">Change Avatar
                                    </button>
                                </div>
                                <p class="text-black-50 text-center">
                                    Total Pending Consignments: {{ $user->consignments()->where('status', 0)->count() }}
                                </p>
                                <p class="text-black-50 text-center">
                                    Total Accepted Consignments: {{ $user->consignments()->where('status', 1)->count() }}
                                </p>
                                <p class="text-black-50 text-center">
                                    Total Rejected Consignments: {{ $user->consignments()->where('status', 2)->count() }}
                                </p>
                                <p class="text-black-50 text-center">
                                    Total Running Consignments: {{ $user->consignments()->where('status', 3)->count() }}
                                </p>
                                <p class="text-black-50 text-center">
                                    Total Shipped Consignments: {{ $user->consignments()->where('status', 4)->count() }}
                                </p>
                                <p class="text-black-50 text-center">
                                    Total Canceled Consignments: {{ $user->consignments()->where('status', 6)->count() }}
                                </p>
                                <p class="text-black-50 text-center">
                                    Total Consignments: {{ $user->consignments()->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-12">
                        <div class="card">
                            <div
                                class="card-header d-flex justify-content-between align-content-center">
                                <div class="pull-left">
                                    <h3>Profile Details</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Full Name</label>
                                            <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username <span class="badge badge-info">unique</span></label>
                                            <input id="username" type="text" class="form-control" name="username" value="{{ $user->username }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email <span class="badge badge-info">unique</span></label>
                                            <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone (without +) <span class="badge badge-info">unique</span></label>
                                            <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email_verification">Email Verification</label>
                                            <input id="email_verification" name="email_verification" type="checkbox" {{ $user->email_verified_at?'checked':'' }} data-toggle="toggle" data-on="Verified" data-off="Unverified" data-onstyle="success" data-offstyle="danger">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone_verification">Phone Verification</label>
                                            <input id="phone_verification" name="phone_verification" type="checkbox" {{ $user->phone_verified_at?'checked':'' }} data-toggle="toggle" data-on="Verified" data-off="Unverified" data-onstyle="success" data-offstyle="danger">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <input id="status" name="status" type="checkbox" {{ $user->status?'checked':'' }} data-toggle="toggle" data-on="Activated" data-off="Deactivated" data-onstyle="success" data-offstyle="danger">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password (Optional)</label>
                                            <input id="password" type="password" class="form-control" name="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Retype Password (Optional)</label>
                                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block btn-lg" type="submit">Update Profile</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
@endsection

@section('js_libs')
    <script src="{{ asset('staff/js/bootstrap4-toggle.min.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            $(document).on('click', '.btn-select', function (e) {
                e.preventDefault()
                openMediaManager(items => {
                    if (items[0] && items[0].hasOwnProperty('url')) {
                        $('#avatar').val(items[0].url)
                    }
                    if (items[0] && items[0].hasOwnProperty('thumb_url')) {
                        $('#avatar-preview').attr('src', items[0].thumb_url)
                    }
                }, 'image', 'Select Avatar')
            })
        })(jQuery)
    </script>
@endsection
