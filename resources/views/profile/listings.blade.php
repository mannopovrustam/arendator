@extends('layouts.site')

@section('content')
    @include('layouts.header')
    <div class="site__body">
        <div class="block-space block-space--layout--after-header"></div>
        <div class="block">
            <div class="container container--max--xl">
                <div class="row">
                    <div class="col-12 col-lg-3 d-flex">
                        @include('profile.sidebar',['selected'=>'listings'])
                    </div>
                    <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                        <div class="addresses-list">
                            <a href="/listings/create" class="addresses-list__item addresses-list__item--new">
                                <div class="addresses-list__plus"></div>
                                <div class="btn btn-secondary btn-sm">E'lon berish</div>
                            </a>
                            <div class="addresses-list__divider"></div>

                            @foreach($listings as $listing)
                                {{-- rent_type enum ('monthly', 'daily') --}}
                                {{-- sell_type enum ('all', 'square') --}}
                                <div class="addresses-list__item card address-card">
                                    <div class="address-card__body">
                                        <div class="address-card__name">{{ $listing->name }}</div>
                                        <div class="address-card__row">{{ strlen($listing->description) > 100 ? substr($listing->description, 0, 100) . '...' : $listing->description }}</div>
                                        {{--is_rent is_sell rent_type rent_price sell_type sell_price currency--}}
                                        @if($listing->is_rent)
                                            <div class="address-card__row">
                                                <div class="address-card__row-title">Ijaraga berish</div>
                                                <div class="address-card__row-content">{{ $listing->rent_price ?? 'kelishiladi' }} {{ $listing->currency }} / {{ $listing->rent_type == 'monthly' ? 'oylik' : 'kunlik' }}</div>
                                            </div>
                                        @endif
                                        @if($listing->is_sell)
                                            <div class="address-card__row">
                                                <div class="address-card__row-title">Sotish</div>
                                                <div class="address-card__row-content">{{ $listing->sell_price ?? 'kelishiladi' }} {{ $listing->currency }} / {{ $listing->sell_type == 'all' ? 'hammasi' : 'kvadrat' }}</div>
                                            </div>
                                        @endif
                                        <div class="address-card__footer"><a href="/listings/{{ $listing->id }}/edit">Edit</a>&nbsp;&nbsp; <a
                                                href="/listings/{{ $listing->id }}/delete">Remove</a></div>
                                    </div>
                                </div>
                                <div class="addresses-list__divider"></div>
                            @endforeach
                            <div class="addresses-list__item card address-card">
                                <div class="address-card__body">
                                    <div class="address-card__name">Helena Garcia</div>
                                    <div class="address-card__row">Random Federation<br>115302, Moscow<br>ul.
                                        Varshavskaya, 15-2-178
                                    </div>
                                    <div class="address-card__row">
                                        <div class="address-card__row-title">Phone Number</div>
                                        <div class="address-card__row-content">38 972 588-42-36</div>
                                    </div>
                                    <div class="address-card__row">
                                        <div class="address-card__row-title">Email Address</div>
                                        <div class="address-card__row-content">helena@example.com</div>
                                    </div>
                                    <div class="address-card__footer"><a href="">Edit</a>&nbsp;&nbsp; <a
                                            href="">Remove</a></div>
                                </div>
                            </div>
                            <div class="addresses-list__divider"></div>

                            <div class="addresses-list__item card address-card">
                                <div class="address-card__body">
                                    <div class="address-card__name">Jupiter Saturnov</div>
                                    <div class="address-card__row">RandomLand<br>4b4f53, MarsGrad<br>Sun Orbit,
                                        43.3241-85.239
                                    </div>
                                    <div class="address-card__row">
                                        <div class="address-card__row-title">Phone Number</div>
                                        <div class="address-card__row-content">ZX 971 972-57-26</div>
                                    </div>
                                    <div class="address-card__row">
                                        <div class="address-card__row-title">Email Address</div>
                                        <div class="address-card__row-content">jupiter@example.com</div>
                                    </div>
                                    <div class="address-card__footer"><a href="">Edit</a>&nbsp;&nbsp; <a
                                            href="">Remove</a></div>
                                </div>
                            </div>
                            <div class="addresses-list__divider"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

