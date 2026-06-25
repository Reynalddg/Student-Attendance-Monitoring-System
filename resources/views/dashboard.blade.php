@include('partials.header')
@include('partials.modal')

<nav class="navbar sticky-top d-md-none" style="background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);">
    <div class="container-fluid">
        <button class="navbar-toggler border-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenuMobile" style="background-color: #FFAE42;
background-image: linear-gradient(315deg, #FFAE42 0%, #FFFF31 74%);">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span class="navbar-brand ms-2">Dashboard</span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Mobile Offcanvas Sidebar -->
        <nav id="sidebarMenuMobile" class="offcanvas offcanvas-start d-md-none fw-bold" tabindex="-1" style="background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                @include('partials.sidebar-content')
            </div>
        </nav>

        <!-- Desktop Sidebar -->
        <div class="col-md-3 col-lg-2 d-none d-md-block  text-dark fw-bold sidebar p-3 min-vh-100" style="background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);">
            @include('partials.sidebar-content')
        </div>

        <!-- Main Content -->
       <main class="col-md-9 col-lg-10 px-md-4 mt-4" id="main">

          
          @yield('content') 
        </main>
    </div>
</div>

@include('partials.footer')
