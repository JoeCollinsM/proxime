@extends('staff.layouts.app')


@section('css_libs')
    <!-- Apex chart -->
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/apexcharts/apexcharts.css') }}">
@endsection

@section('content')

    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Dashboard</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item">Dashboard</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
   

    <div class="row mt-3">
       <div class="col-12 col-lg-6 col-xl-3">
         <div class="card gradient-deepblue">
           <div class="card-body">
              <h5 class="text-white mb-0">{{ $currency->symbol }}{{ $total_sale }} <span class="float-right"><i class="fa fa-shopping-cart"></i></span></h5>
                <!-- <div class="progress my-3" style="height:3px;">
                   <div class="progress-bar" style="width:55%"></div>
                </div> -->
              <p class="mb-0 text-white small-font">Total Sales <span class="float-right">   <i class="zmdi zmdi-long-arrow-up"></i></span></p>
            </div>
         </div> 
       </div>
       <div class="col-12 col-lg-6 col-xl-3">
         <div class="card gradient-orange">
           <div class="card-body">
              <h5 class="text-white mb-0">{{ $currency->symbol }}{{ $today_sale }} <span class="float-right"><i class="fa fa-usd"></i></span></h5>
                <!-- <div class="progress my-3" style="height:3px;">
                   <div class="progress-bar" style="width:55%"></div>
                </div> -->
              <p class="mb-0 text-white small-font">Today's Sale <span class="float-right">  <i class="zmdi zmdi-long-arrow-up"></i></span></p>
            </div>
         </div>
       </div>
       <div class="col-12 col-lg-6 col-xl-3">
         <div class="card gradient-ohhappiness">
            <div class="card-body">
              <h5 class="text-white mb-0">{{ $new_orders }}<span class="float-right"><i class="fa fa-eye"></i></span></h5>
                <!-- <div class="progress my-3" style="height:3px;">
                   <div class="progress-bar" style="width:55%"></div>
                </div> -->
              <p class="mb-0 text-white small-font">New Orders <span class="float-right"> <i class="zmdi zmdi-long-arrow-up"></i></span></p>
            </div>
         </div>
       </div>
       <div class="col-12 col-lg-6 col-xl-3">
         <div class="card gradient-ibiza">
            <div class="card-body">
              <h5 class="text-white mb-0">{{ $total_customers }} <span class="float-right"><i class="fa fa-envira"></i></span></h5>
                <!-- <div class="progress my-3" style="height:3px;">
                   <div class="progress-bar" style="width:55%"></div>
                </div> -->
              <p class="mb-0 text-white small-font">Customers <span class="float-right"> <i class="zmdi zmdi-long-arrow-up"></i></span></p>
            </div>
         </div>
       </div>
    </div><!--End Row-->




    <div class="row">
     <div class="col-12 col-lg-8 col-xl-8">
	    <div class="card">
		 <div class="card-header">Sales Statistics of this month (In {{ $currency->code }})
		   <div class="card-action">
			 <div class="dropdown">
			 <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
			  <i class="icon-options"></i>
			 </a>
				<div class="dropdown-menu dropdown-menu-right">
				<a class="dropdown-item" href="javascript:void();">Action</a>
				<a class="dropdown-item" href="javascript:void();">Another action</a>
				<a class="dropdown-item" href="javascript:void();">Something else here</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="javascript:void();">Separated link</a>
			   </div>
			  </div>
		   </div>
		 </div>
		 <div class="card-body">
			<div class="chart-container-1">
		      <!-- <canvas id="chart1"></canvas> -->

              <div id="comparison"></div>
			</div>
		 </div>
		 
		 
		 
		</div>
	 </div>

     <div class="col-12 col-lg-4 col-xl-4">
        <div class="card">
           <div class="card-header">Order Status
             
           </div>
           <div class="card-body">
              <div class="chart-container-2">
              <div id="order-status"></div>
                    <!-- <canvas id="chart2"></canvas> -->
			  </div>
           </div>
           <ul class="chart-coin-list email-sent-list">
                <li><span
                        class="color-box color-box-1"></span> {{ round($t_orders == 0?0:($new_orders*100)/$t_orders) }}
                    % New
                </li>
                <li><span
                        class="color-box color-box-2"></span> {{ round($t_orders == 0?0:($processing_orders*100)/$t_orders) }}
                    % Processing
                </li>
                <li><span
                        class="color-box color-box-3"></span> {{ round($t_orders == 0?0:($way_orders*100)/$t_orders) }}
                    % On The Way
                </li>
                <li><span
                        class="color-box color-box-1"></span> {{ round($t_orders == 0?0:($completed_orders*100)/$t_orders) }}
                    % Completed
                </li>
            </ul>
         </div>
     </div>

	</div><!--End Row-->

    


    <div class="row">
	 <div class="col-12 col-lg-12">
	   <div class="card">
	     <div class="card-header border-0">Recent Order Tables</div>
         <div class="card-body table-responsive">
                <table class="table table-striped table-bordered"
                        id="data-table" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                </table>
	   </div>
	 </div>
	</div><!--End Row-->
    
@endsection

@section('js_libs')

    <!-- Apex chart -->
    <script src="{{ asset('staff/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('staff/js/jquery.circlechart.js') }}"></script>


@endsection

@section('js')
    <script>
        $(document).ready(function () {

            $('#data-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('staff.catalog.order.index', ['status' => 0]) }}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'order', name: 'order', orderable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'status', name: 'status'},
                    {data: 'total', name: 'total', searchable: false}
                ],
                order: [[ 0, 'desc' ]]
            });

            var options = {
                series: [{{ round($t_orders == 0?0:($new_orders*100)/$t_orders) }}, {{ round($t_orders == 0?0:($processing_orders*100)/$t_orders) }}, {{ round($t_orders == 0?0:($way_orders*100)/$t_orders) }}, {{ round($t_orders == 0?0:($completed_orders*100)/$t_orders) }}],
                chart: {
                    height: 295,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: {
                                fontSize: '22px',
                            },
                            value: {
                                fontSize: '16px',
                            },
                            total: {
                                show: false,
                                label: 'Total',
                                formatter: function (w) {
                                    return 175
                                }
                            }
                        }
                    }
                },
                labels: ['New', 'Processing', 'On The Way', 'Completed'],
                fill: {
                    colors: ['#F1682C', '#0EB7FE', '#F1682C', '#2E42B4']
                }
            };

            var chart = new ApexCharts(document.querySelector("#order-status"), options);
            chart.render();


            var options = {
                series: [
                    {
                        name: 'Order Placed',
                        data: @json($order_placed_in_month_by_date)
                    },
                    {
                        name: 'Net Sale',
                        data: @json($net_sale_in_month_by_date)
                    },
                    {
                        name: 'Discount',
                        data: @json($discount_in_month_by_date)
                    },
                    {
                        name: 'Gross Sale',
                        data: @json($gross_sale_in_month_by_date)
                    },
                ],
                chart: {
                    type: 'line',
                    height: 310,
                    stacked: true,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: true
                    }
                },
                legend: {
                    show: false
                },
                responsive: [{
                    breakpoint: 480,
                    options: {}
                }],
                plotOptions: {
                    bar: {
                        horizontal: false,
                    },
                },
                stroke: {
                    curve: 'smooth',
                },
                xaxis: {
                    type: 'datetime',
                    categories: @json($dates_in_month_by_order),
                },
            };

            var chart = new ApexCharts(document.querySelector("#comparison"), options);
            chart.render();


           
        })
    </script>
@endsection
