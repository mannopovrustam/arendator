<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('img/logo.png') }}" alt=""  style="height: 50px; width: 50px">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('img/logo.png') }}" alt="" style="height: 50px; margin-top: 10px; width: 150px">
            </span>
        </a>

        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('img/logo.png') }}" alt="" style="height: 50px; width: 50px">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('img/logo.png') }}" alt="" style="height: 50px; margin-top: 10px; width: 150px">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{route('dashboard')}}">
                        <i class="uil-home-alt"></i>
                        <span>Asosiy</span>
                    </a>
                </li>
                <li>
                    <a href="/backend/listings">
                        <i class="uil-list-ul"></i>
                        <span>Barcha e'lonlar</span>
                    </a>
                </li>
                <li>
                    <a href="/backend/listings/create">
                        <i class="uil-focus-add"></i>
                        <span>E'lon qo'shing</span>
                    </a>
                </li>
                <li>
                    <a href="/bookings">
                        <i class="fas fa-user-plus"></i>
                        <span>Booking</span>
                    </a>
                </li>
                <div style="margin: 5px;padding: 5px 2px;background-color: #5b73e830; color:#fff; border-radius: 10px">
                <li>
                    <a href="/backend/listok/create">
                        <i class="fa fa-plus"></i>
                        <span>Ro'yhatga qo'yish</span>
                    </a>
                </li>
                <li>
                    <a href="/backend/listok">
                        <i class="fas fa-address-card"></i>
                        <span>Ro'yhatga qo'yilgan</span>
                    </a>
                </li>
                <li>
                    <a href="/backend/kadastr">
                        <i class="uil-home-alt"></i>
                        <span>Kadastrlarim</span>
                    </a>
                </li>
                </div>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
