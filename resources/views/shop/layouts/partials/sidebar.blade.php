<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
     <div class="brand-logo">
      <a href="{{ route('shop.dashboard') }}">
       <img src="{{ config('ui.logo.large') }}" class="logo-icon" alt="{{ config('app.name') }}">
       <h5 class="logo-text">@if(Auth::check())
                                {{ auth()->guard('shop')->user()->name }} 
                            @else() {{ config('app.name') }} @endif</h5>
     </a>
   </div>

   <div class="user-details">
        <div class="media align-items-center user-pointer collapsed" data-toggle="collapse" data-target="#user-dropdown">
            <div class="avatar"><img class="mr-3 side-user-img" src="{{ auth()->guard('shop')->user()->logo }}" alt=""></div>
            <div class="media-body">
                <h6 class="side-user-name">{{ auth()->guard('shop')->user()->vendor_name }}</h6>
            </div>
        </div>
        <div id="user-dropdown" class="collapse">
            <ul class="user-setting-menu">
                <li><a href="{{ route('shop.profile') }}"><i class="icon-user"></i>  My Profile</a></li>
                <form action="{{ route('shop.logout') }}" method="post" id="logout-form"
                                        class="d-none">
                                        @csrf
                                    </form>
                <li><a href="{{ route('shop.logout') }}"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit()"><i class="icon-power"></i> Logout</a></li>
            </ul>
        </div>
    </div>

   <ul class="sidebar-menu in">
    <li class="sidebar-header">MAIN NAVIGATION</li>
    <li class="{{ request()->route()->getName() == 'shop.dashboard'?'active':'' }}">
        <a href="{{ route('shop.dashboard') }} " class="waves-effect">
          <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
        </a>
    </li>
    <li @if(request()->is('shop/catalog/order*')) class="mm-active" @endif>
        <a href="{{ route('shop.catalog.order.index') }}">
            <i class="fa fa-cart-arrow-down"></i> <span>Orders</span></a>
    </li>

    <li @if(request()->is('shop/catalog/product*')) class="mm-active" @endif >
        <a aria-expanded="false" href="#"> <i class="fa fa-product-hunt"></i> <span>Products</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="sidebar-submenu">
            <li class="{{ in_array(request()->route()->getName(), ['shop.catalog.product.index', 'shop.catalog.product.edit'])?'active':'' }}">
                <a href="{{ route('shop.catalog.product.index') }}">All Products</a>
            </li>
            <li class="{{ in_array(request()->route()->getName(), ['shop.catalog.product.create'])?'active':'' }}">
                <a href="{{ route('shop.catalog.product.create') }}">Add New</a>
            </li>
        </ul>
    </li>

      <!-- manage withdrawals -->
    <li @if(request()->is('shop/withdraw*')) class="mm-active" @endif>
      <a class="waves-effecthas-arrow" href="#" aria-expanded="false">
      <i class="zmdi zmdi-money-box"></i> <span>Withdraw</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sidebar-submenu">
            <li class="{{ in_array(request()->route()->getName(), ['shop.withdraw.index'])?'active':'' }}">
                <a  href="{{ route('shop.withdraw.index') }}">All Withdraws</a>
            </li>
            <li class="{{ in_array(request()->route()->getName(), ['shop.withdraw.create'])?'active':'' }}">
                <a href="{{ route('shop.withdraw.create') }}">Add New</a>
            </li>
        </ul>
    </li>

     <!-- Transaction -->
    <li class="{{ request()->route()->getName() == 'shop.transaction.index'?'active':'' }}">
        <a href="{{ route('shop.transaction.index') }} " class="waves-effect">
        <i class="fa fa-bitcoin"></i> <span>Transactions</span>
        </a>
    </li>

    <!-- reports -->
    <li @if(in_array(request()->route()->getName(), ['shop.report.sales-by-date', 'shop.report.sales-by-product'])) class="mm-active" @endif>
      <a class="waves-effecthas-arrow" href="#" aria-expanded="false">
          <i class="zmdi zmdi-case-download"></i> <span>Reports</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sidebar-submenu @if(in_array(request()->route()->getName(), ['shop.report.sales-by-date', 'shop.report.sales-by-product'])) collapse active @endif ">
            <li class="{{ request()->route()->getName() == 'shop.report.sales-by-date'?'active':'' }}">
                <a href="{{ route('shop.report.sales-by-date') }}">Sales By Date</a>
            </li>
            <li class="{{ request()->route()->getName() == 'shop.report.sales-by-product'?'active':'' }}">
                <a href="{{ route('shop.report.sales-by-product') }}">Sales By Product</a>
            </li>
        </ul>
    </li>

    <!-- shop info -->
    <li class="{{ request()->route()->getName() == 'shop.profile'?'active':'' }}">
        <a href="{{ route('shop.profile') }} " class="waves-effect">
          <i class="zmdi zmdi-edit"></i> <span>Edit Shop Profile</span>
        </a>
    </li>


    </ul>
   
   </div>