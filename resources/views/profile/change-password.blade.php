@extends('layouts.site')

@section('content')
    @include('layouts.header')
    <div class="site__body">
        <div class="block-space block-space--layout--after-header"></div>
        <div class="block">
            <div class="container container--max--xl">
                <div class="row">
                    <div class="col-12 col-lg-3 d-flex">
                        @include('profile.sidebar',['selected'=>'change-password'])
                    </div>
                    <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                        <div class="card">
                            <div class="card-header"><h5>Parolni o‘zgartirish</h5></div>
                            <div class="card-divider"></div>
                            <div class="card-body card-body--padding--2">
                                <form action="/profile/change-password" method="post">
                                    <div class="row no-gutters">
                                        @csrf
                                        <div class="col-12 col-lg-7 col-xl-6">
                                            <div class="form-group"><label for="password-current">Joriy Parol</label>
                                                <input type="password" class="form-control" id="password-current"
                                                       placeholder="Current Password"></div>
                                            <div class="form-group"><label for="password-new">Joriy Parol</label> <input
                                                    type="password" class="form-control" id="password-new"
                                                    placeholder="New Password"></div>
                                            <div class="form-group"><label for="password-confirm">Parolni
                                                    tasdiqlash</label> <input type="password" class="form-control"
                                                                              id="password-confirm"
                                                                              placeholder="Parolni tasdiqlash"></div>
                                            <div class="form-group mb-0">
                                                <button class="btn btn-primary mt-3">Saqlash</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

