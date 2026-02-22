<header class="app-header">
    <nav class="navbar navbar-expand-xl navbar-light container-fluid px-0">

        {{--<ul class="navbar-nav quick-links d-none d-xl-flex">
            <li class="nav-item dropdown-hover d-none d-xl-block">
                <a class="nav-link btn"><i class="fa fa-location-arrow"></i> &nbsp; Ташкент</a>
            </li>
        </ul>--}}

        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a href="javascript:void(0)" class="nav-link round-40 ps-0 d-flex d-xl-none align-items-center justify-content-center" type="button"
                   data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                    <i class="ti ti-align-justified fs-7"></i>
                </a>
            </li>

            {{-- Desktop Logo --}}
            <li class="nav-item d-none d-xl-block">
                <a href="/" class="text-nowrap nav-link">
                    <img src="/dist/images/logos/dark-logo.svg" class="dark-logo" width="180" alt="" />
                    <img src="/dist/images/logos/light-logo.svg" class="light-logo"  width="180" alt="" />
                </a>
            </li>
        </ul>

        <div class="d-block d-xl-none">
            <a href="/" class="text-nowrap nav-link">
                <img src="/dist/images/logos/dark-logo.svg" height="30" alt="" />
            </a>
        </div>
        <div>

            @auth
                <?php
                $user = \Auth::user();
                ?>
                <button class="navbar-toggler p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                    <span class="p-2"><img src="{{ $user->photo ?? '/dist/images/profile/user-1.jpg'}}" class="rounded-circle" width="35" height="35" alt="" /></span>
                </button>

                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" style="width: 100%;" aria-labelledby="drop1">
                    <div class="profile-dropdown position-relative" data-simplebar>
                        <div class="py-3 px-7 pb-0">
                            <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                        </div>
                        <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                            <img src="{{ $user->photo ?? '/dist/images/profile/user-1.jpg'}}" class="rounded-circle" width="80" height="80" alt="" />
                            <div class="ms-3">
                                <h5 class="mb-1 fs-3">{{ $user->name }}</h5>
                                <span class="mb-1 d-block text-dark">+998 {{ $user->phone }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between py-4 px-7 pt-8">
                            <a @if($user->user_type == 1) href="/dashboard" @endif class="btn btn-outline-primary"><i class="fa fa-user"></i> Kabinet</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" class="btn btn-outline-danger"
                                   onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Chiqish') }}
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="/login" class=" btn waves-effect waves-light btn-light-primary text-primary navbar-toggler p-0 border-0">
                    <span class="p-2">Kirish</span>
                </a>
            @endauth


        </div>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <div class="d-flex align-items-center justify-content-between px-0 px-xl-8">
                <ul class="navbar-nav">
                    <li class="nav-item d-none d-xl-block">
                        <a class="nav-link nav-icon-hover" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="ti ti-search"></i>
                        </a>
                    </li>
                </ul>
                <div class="mr-5 d-flex">
                    <ul class="navbar-nav quick-links d-none d-xl-flex">
                        <li class="nav-item dropdown-hover d-none d-xl-block">
                            <a class="nav-link" href="tel:+998950883334">+998 95 088 3334</a>
                        </li>
                        <li class="nav-item dropdown-hover d-none d-xl-block">
                            <a class="nav-link" href="/bids/create">Ta'mirlash uchun ariza</a>
                        </li>
                    </ul>

                    <a href="javascript:void(0)" class="nav-link round-40 ps-0 d-flex d-xl-none align-items-center justify-content-center" type="button">
                        <i class="fa fa-phone"></i>
                    </a>
                    <a href="javascript:void(0)" class="nav-link round-40 ps-0 d-flex d-xl-none align-items-center justify-content-center" type="button"
                       data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                        <i class="ti ti-align-justified fs-7"></i>
                    </a>
                </div>

                <ul class="navbar-nav flex-row ms-auto">
                    <li class="nav-item dropdown">
                        @auth
                            <?php
                                $user = \Auth::user();
                            ?>
                        <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="user-profile-img">
                                    <img src="{{ $user->photo ?? '/dist/images/profile/user-1.jpg'}}" class="rounded-circle" width="35" height="35" alt="" />
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                            <div class="profile-dropdown position-relative" data-simplebar>
                                <div class="py-3 px-7 pb-0">
                                    <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                                </div>
                                <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                    <img src="{{ $user->photo ?? '/dist/images/profile/user-1.jpg'}}" class="rounded-circle" width="80" height="80" alt="" />
                                    <div class="ms-3">
                                        <h5 class="mb-1 fs-3">{{ $user->name }}</h5>
                                        <span class="mb-1 d-block text-dark">+998 {{ $user->phone }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between py-4 px-7 pt-8">
                                    <a @if($user->user_type == 1) href="/dashboard" @endif class="btn btn-outline-primary"><i class="fa fa-user"></i> Kabinet</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}" class="btn btn-outline-danger"
                                           onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                            {{ __('Выход') }}
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                            <a href="/login" class="btn waves-effect waves-light btn-light-primary text-primary">Kirish</a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
