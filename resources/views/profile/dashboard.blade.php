@extends('layouts.site')

@section('content')
    @include('layouts.header')
    <div class="site__body">
        <div class="block-space block-space--layout--after-header"></div>
        <div class="block">
            <div class="container container--max--xl">
                <div class="row">
                    <div class="col-12 col-lg-3 d-flex">
                        @include('profile.sidebar',['selected'=>'dashboard'])
                    </div>
                    <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                        <div class="dashboard">
                            <div class="dashboard__profile card profile-card">
                                <div class="card-body profile-card__body">
                                    <div class="profile-card__avatar">
                                        @if($user->avatar)
                                            <img src="{{ asset('uploads/avatars/'.$user->avatar) }}" alt="">
                                        @else
                                            <div class="profile-card__avatar-upload">
                                                <i class="fas fa-user-circle" style="font-size: 80px; color: #ccc;"></i>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="profile-card__name">{{ $user->name }}</div>
                                    <div class="profile-card__email">{{ $user->login }}</div>
                                    <div class="profile-card__edit"><a href="/profile/edit"
                                                                       class="btn btn-secondary btn-sm">Profilni o'zgartirish</a>
                                    </div>
                                </div>
                            </div>
                            <a href="/listings/create" class="addresses-list__item addresses-list__item--new">
                                <div class="addresses-list__plus"></div>
                                <div class="btn btn-secondary btn-sm">E'lon berish</div>
                            </a>
                            <div class="dashboard__orders card">
                                <div class="card-header"><h5>So‘nggi Xabarlar</h5></div>
                                <div class="card-divider"></div>
                                <div class="card-table">
                                    <div class="table-responsive-sm">
                                        <table>
                                            <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><a href="account-order-details.html">#8132</a></td>
                                                <td>02 April, 2019</td>
                                                <td>Pending</td>
                                                <td>$2,719.00 for 5 item(s)</td>
                                            </tr>
                                            <tr>
                                                <td><a href="account-order-details.html">#7592</a></td>
                                                <td>28 March, 2019</td>
                                                <td>Pending</td>
                                                <td>$374.00 for 3 item(s)</td>
                                            </tr>
                                            <tr>
                                                <td><a href="account-order-details.html">#7192</a></td>
                                                <td>15 March, 2019</td>
                                                <td>Shipped</td>
                                                <td>$791.00 for 4 item(s)</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

