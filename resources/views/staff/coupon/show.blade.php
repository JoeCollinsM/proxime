@extends('staff.layouts.app')

@section('page_title', 'Coupon: ' . $coupon->code)

@section('content')
    

     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Coupon {{ $coupon->code }}</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"> <a href="{{ route('staff.coupon.index') }}">Coupons</a></li>
            <li class="breadcrumb-item active">Coupon: {{ $coupon->code }}</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-8">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Coupon: {{ $coupon->code }} Details</h3>
                    </div>
                    <div class="box-body">
                        <ul class="list-group">
                            <li class="list-group-item">Start At: {{ optional($coupon->start_at)->format('d M, Y') }}</li>
                            <li class="list-group-item">Expire At: {{ optional($coupon->expire_at)->format('d M, Y') }}</li>
                            <li class="list-group-item">Min: {{ $coupon->min == -1?'No Limit':$coupon->min . get_option('currency', 'BDT') }}</li>
                            <li class="list-group-item">Discount: {{ $coupon->amount }} {{ $coupon->discount_type == 1?'%':get_option('currency', 'BDT') }} {{ $coupon->discount_type == 1 && $coupon->upto != -1?'(' . $coupon->upto . ' ' . get_option('currency', 'BDT') . ')':'' }}</li>
                            <li class="list-group-item">Status: @if($coupon->status == 1) <span class="label label-primary">Active</span> @elseif($coupon->status == 0) <span class="label label-danger">Expired</span> @else <span class="label label-success">Upcoming</span> @endif</li>
                            <li class="list-group-item">Used: {{ $coupon->used }} times</li>
                            <li class="list-group-item">Services: {{ $coupon->services()->pluck('title')->implode(', ') }}</li>
                            <li class="list-group-item">Users: {{ $coupon->users()->pluck('name')->implode(', ') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
@endsection
