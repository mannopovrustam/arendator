@extends('layouts.site')

@section('content')
    @include('layouts.header')
    <div class="site__body">
        <div class="block-space block-space--layout--after-header"></div>
        <div class="block">
            <div class="container container--max--xl">
                <div class="row">
                    <div class="col-12 col-lg-3 d-flex">
                        @include('profile.sidebar',['selected'=>'edit'])
                    </div>
                    <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                        <div class="card">
                            <div class="card-header"><h5>Edit Profile</h5></div>
                            <div class="card-divider"></div>
                            <div class="card-body card-body--padding--2">
                                <div class="row no-gutters">
                                    <div class="col-12 col-lg-7 col-xl-6">
                                        <form action="/profile/edit" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group"><label for="profile-first-name">FIO</label>
                                                <input type="text" name="name" class="form-control"
                                                       id="profile-first-name"
                                                       placeholder="FIO" value="{{ $user->name }}"></div>
                                            <div class="form-group"><label for="profile-last-name">Pochta</label> <input
                                                        type="email" name="email" class="form-control"
                                                        id="profile-last-name"
                                                        placeholder="Pochta" value="{{ $user->email }}"></div>
                                            <div class="form-group"><label for="profile-phone">Telefon</label> <input
                                                        type="text" name="phone" class="form-control" id="profile-phone"
                                                        placeholder="Telefon" value="{{ $user->phone }}"></div>
                                            <div class="form-group"><label for="profile-email">Profil rasmi</label>
                                                <input
                                                        type="file" name="avatar" class="form-control"
                                                        id="profile-email"
                                                        placeholder="Email Address"></div>
                                            <div class="form-group mb-0">
                                                <button class="btn btn-primary mt-3">Saqlash</button>
                                            </div>
                                        </form>
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

