@include('layouts.nav')

<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">

  <header class="main-header">

    <!-- Logo -->
    <a href="{{ route('admin.manage_student.index') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>UNC</b>B</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>UNC</b>B</span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Notifications: style can be found in dropdown.less -->
          {{-- <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 10 notifications</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <li>
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                      page and may cause design problems
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-users text-red"></i> 5 new members joined
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-user text-red"></i> You changed your username
                    </a>
                  </li>
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li> --}}
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              {{--  <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">  --}}
              <span class="hidden-xs">
                {{ Auth::user()->first_name . ' ' . Auth::user()->last_name }} |
                @if (Auth::user()->role == 1)
                  System Administrator
                @elseif (Auth::user()->role == 2)
                  Cashier
                @elseif (Auth::user()->role == 3)
                  Accounting
                @endif
              </span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              {{--  <li class="user-header">
                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                <p>
                  Administrator
                  <small></small>
                </p>
              </li>  --}}
              <!-- Menu Body -->
              {{-- <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
                <!-- /.row -->
              </li> --}}
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                </div>
                <div class="pull-right">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                  <a href="{{ route('logout') }}"
                              onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();"
                              class="btn btn-default btn-flat" class="btn btn-default btn-flat">Sign out</a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>

    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        {{-- <li class="treeview">
          <a href="#">
            <i class="fa fa-pie-chart"></i>
            <span>Charts</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="pages/charts/chartjs.html"><i class="fa fa-circle-o"></i> ChartJS</a></li>
            <li><a href="pages/charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a></li>
            <li><a href="pages/charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a></li>
            <li><a href="pages/charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a></li>
          </ul>
        </li> --}}
        
        {{--  <li class="dashboard">
          <a href="#">
            <i class="fa fa-circle-o text-red"></i> <span>Dashboard</span>
          </a>
        </li>  --}}
        @if (Auth::user()->role == 1)
          <li class="film"><a href="{{ route('admin.manage_student.index') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Manage Students</span></a></li>
          <li class="film"><a href="{{ route('admin.manage_fees.index') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Manage Fees</span></a></li>
          {{--  <li class="film"><a href="{{ route('admin.manage_discounts.index') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Manage Discounts</span></a></li>  --}}
        @elseif (Auth::user()->role == 2)
            <li class="film"><a href="{{ route('cashier.student_payment.index') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Student Payment</span></a></li>
            <li class="film"><a href="{{ route('reports.receivedpayments.index') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Received Payments</span></a></li> 
            <li class="film"><a href="{{ route('reports.monthly_payment_monitor.index') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Monthly Payment Monitor</span></a></li> 
        @elseif (Auth::user()->role == 3)
          <li class="film"><a href="{{ route('reports.receivedpayments.index') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Received Payments</span></a></li> 
          <li class="film"><a href="{{ route('reports.monthly_payment_monitor.index') }}"><i class="fa fa-circle-o text-yellow"></i> <span>Monthly Payment Monitor</span></a></li> 
        @endif
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        @yield('content_title')
      </h1>
      <!--<ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>-->
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
          <div class="col-sm-12">
          <div class="row">
              <div class="col-sm-12">
                  <div class="js-messages_holder" style="display:none"></div>
              </div>
          </div>
            @yield('content')
          </div>
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



@include('layouts.footer')