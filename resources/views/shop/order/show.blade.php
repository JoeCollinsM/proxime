@extends('shop.layouts.app')

@section('page_title', 'Order #' . $order->id)

@section('css')
    <style>
        [v-cloak] {
            display: none;
        }

        .bubble {
            margin-top: 20px;
            position: relative;
            background: #ddd;
            color: #48484b;
            width: 100%;
            border-radius: 10px;
            padding: 10px;
        }

        .bubble:after {
            content: '';
            position: absolute;
            display: block;
            width: 0;
            z-index: 1;
            border-style: solid;
            border-color: #ddd transparent;
            border-width: 0 14px 20px;
            top: -20px;
            left: 12%;
            margin-left: -14px;
        }

        .notes {
            height: 30vh;
            overflow-y: scroll;
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
            <li class="breadcrumb-item"><a href="{{ route('staff.catalog.order.index') }}">Orders</a></li>
            <li class="breadcrumb-item active">{{ 'Order #' . $order->id }}</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    <div  id="app">
            
            <div class="row">
                <div class="col-md-9">
                    <div class=" card">
                        <div
                                class=" card-header d-flex justify-content-between align-content-center">
                            <div class=" pull-left">
                                <h3>Order #{{ $order->id }} Details</h3>
                                <h6 class="text-black-50">Placed
                                    on {{ optional($order->created_at)->format('M d, Y @ h:i a') }}</h6>
                            </div>
                        </div>
                        <div class=" card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>General</h5>
                                    <p class="text-black-50">Status: @if($order->status == 0) <span
                                                class="badge badge-warning">Pending</span> @elseif($order->status == 1)
                                            <span
                                                    class="badge badge-info">Processing</span> @elseif($order->status == 2)
                                            <span
                                                    class="badge badge-info">On The Way</span> @elseif($order->status == 3)
                                            <span
                                                    class="badge badge-success">Completed</span> @elseif($order->status == 4)
                                            <span class="badge badge-warning">Hold</span> @elseif($order->status == 5)
                                            <span class="badge badge-danger">Canceled</span> @endif</p>
                                    @if($review)<p class="text-black-50">Rating: @php echo $review->rating_html; @endphp @endif
                                </div>
                                <div class="col-md-4">
                                    <h5>Charge & Commission</h5>
                                    <p class="text-black-50">Total: {{ $order->gross_total-$order->discount }} {{ $currency->code }}</p>
                                    <p class="text-black-50">Your Profit: {{ $order->shop_commission }} {{ $currency->code }}</p>
                                    <p class="text-black-50">System Commission: {{ $order->system_commission }} {{ $currency->code }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" card mt-5">
                        <div class=" card-body table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                <tr>
                                    <td>Item</td>
                                    <td>Cost</td>
                                    <td>Qty</td>
                                    <td>Total</td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="image mr-2">
                                                    <img class="img-thumbnail" style="width: 50px;height: 50px;"
                                                         src="{{ $item->variant?$item->variant->image:$item->product->image }}"
                                                         alt="{{ $order->product_title }}">
                                                </div>
                                                <div class="meta">
                                                    <p>
                                                        <a href="{{ route('staff.catalog.product.edit', $item->product_id) }}">{{ $item->product_title }}</a>
                                                    </p>
                                                    @if($item->attrs && is_array($item->attrs))
                                                        <p class="text-black-50">
                                                            @foreach($item->attrs as $key => $value)
                                                                {{ $key }}: {{ $value }}<br>
                                                            @endforeach
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $currency->symbol }}{{ $item->price+$item->tax }}</td>
                                        <td>x{{ $item->quantity }}</td>
                                        <td>{{ $currency->symbol }}{{ ($item->price+$item->tax)*$item->quantity }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right">Items Subtotal</td>
                                    <td>{{ $currency->symbol }}{{ $order->gross_total }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Shipping Method</td>
                                    <td>{{ $order->shipping_method_name }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Shipping Charge</td>
                                    <td>{{ $currency->symbol }}{{ $order->shipping_charge }}</td>
                                </tr>
                                @if($order->coupon_code)
                                    <tr>
                                        <td colspan="3" class="text-right">Coupon Code</td>
                                        <td>{{ $order->coupon_code }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right">Coupon Discount</td>
                                        <td>{{ $currency->symbol }}{{ $order->discount }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-right">Total</td>
                                    <td>{{ $currency->symbol }}{{ $order->gross_total+$order->shipping_charge-$order->discount }}</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    @if($order->status == 0 || $order->status == 1)
                    <div class=" card mb-3">
                        <div
                                class=" card-header d-flex justify-content-between align-content-center">
                            <div class=" pull-left">
                                <h5>Update Status</h5>
                            </div>
                        </div>
                        <div class=" card-body">
                            <form action="{{ route('shop.catalog.order.status', $order->id) }}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label class="text-black-50">Status</label>
                                    <select name="status" class="form-control select2">
                                        @if($order->status == 0)
                                        <option value="1" {{ $order->status == 1?'selected':'' }}>Processing
                                        </option>
                                        @endif
                                        <option value="5" {{ $order->status == 5?'selected':'' }}>Canceled
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
    </div>
@endsection
