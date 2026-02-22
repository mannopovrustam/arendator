{{--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/dist/jquery-confirm.min.css">--}}
{{--<script src="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/dist/jquery-confirm.min.js"></script>--}}




<style>
    .product--layout--full .product__body {
        display: block;
        grid-template-columns: auto 1fr 370px;
        grid-template-rows: -webkit-max-content auto auto 1fr;
        grid-template-rows: max-content auto auto 1fr;
    }
    .product--layout--full .product__gallery{
        margin: auto;
        /*width: 500px;*/
    }
    .product--layout--full .product__info{
        margin-left: 0;
    }
</style>
<div class="container">
    <div class="product product--layout--full">
        <div class="product__body container">
            <div class="product__header"><h1 class="product__title">{{ $listing->name }}</h1>
                <div class="product__subtitle">
                    <div class="product__rating">
                        <div class="product__rating-stars">
                            <div class="rating">
                                <div class="rating__body">
                                    <div class="rating__star rating__star--active"></div>
                                    <div class="rating__star rating__star--active"></div>
                                    <div class="rating__star rating__star--active"></div>
                                    <div class="rating__star rating__star--active"></div>
                                    <div class="rating__star"></div>
                                </div>
                            </div>
                        </div>
                        <div class="product__rating-label"><a href="">3.5 on 7 reviews</a></div>
                    </div>
                    {{--<div class="status-badge status-badge--style--success product__fit status-badge--has-icon status-badge--has-text">
                        <div class="status-badge__body">
                            <div class="status-badge__icon">
                                <svg width="13" height="13">
                                    <path d="M12,4.4L5.5,11L1,6.5l1.4-1.4l3.1,3.1L10.6,3L12,4.4z"></path>
                                </svg>
                            </div>
                            <div class="status-badge__text">Part Fit for 2011 Ford Focus S</div>
                            <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip" title=""
                                 data-original-title="Part Fit for 2011 Ford Focus S"></div>
                        </div>
                    </div>--}}
                </div>
            </div>
            <div class="product-gallery product-gallery--layout--product-full product__gallery"
                 data-layout="product-full">
                <div class="product-gallery__featured">
                    <button type="button" class="product-gallery__zoom">
                        <svg width="24" height="24">
                            <path d="M15,18c-2,0-3.8-0.6-5.2-1.7c-1,1.3-2.1,2.8-3.5,4.6c-2.2,2.8-3.4,1.9-3.4,1.9s-0.6-0.3-1.1-0.7
	c-0.4-0.4-0.7-1-0.7-1s-0.9-1.2,1.9-3.3c1.8-1.4,3.3-2.5,4.6-3.5C6.6,12.8,6,11,6,9c0-5,4-9,9-9s9,4,9,9S20,18,15,18z M15,2
	c-3.9,0-7,3.1-7,7s3.1,7,7,7s7-3.1,7-7S18.9,2,15,2z M16,13h-2v-3h-3V8h3V5h2v3h3v2h-3V13z"></path>
                        </svg>
                    </button>
                    <div class="owl-carousel owl-loaded owl-drag">
                        <div class="owl-stage-outer">
                            <div class="owl-stage"
                                 style="transform: translate3d(0px, 0px, 0px); transition: all; width: 1848px;">
                                @foreach(explode(',', $listing->images) as $img)

                                    <div class="owl-item @if($loop->first) active @endif" style="width: 462px;"><a
                                            class="image image--type--product"
                                            href="{{ $img }}" target="_blank"
                                            data-width="700" data-height="700">
                                            <div class="image__body"><img class="image__tag"
                                                                          src="{{ !empty($img) ? $img : ('/images/type/'.$listing->type_id.'.svg') }}"
                                                                          alt=""></div>
                                        </a></div>
                                @endforeach
                            </div>
                        </div>
                        <div class="owl-nav disabled">
                            <button type="button" role="presentation" class="owl-prev"><span
                                    aria-label="Previous">‹</span></button>
                            <button type="button" role="presentation" class="owl-next"><span
                                    aria-label="Next">›</span></button>
                        </div>
                        <div class="owl-dots disabled"></div>
                    </div>
                </div>
                <div class="product-gallery__thumbnails">
                    <div class="owl-carousel owl-loaded owl-drag">
                        <div class="owl-stage-outer">
                            <div class="owl-stage"
                                 style="transform: translate3d(0px, 0px, 0px); transition: all; width: 314px;">
                                @foreach(explode(',', $listing->images) as $img)
                                    <div class="owl-item @if($loop->first) active @endif" style="width: 70.333px; margin-right: 8px;">
                                        <div class="product-gallery__thumbnails-item image image--type--product @if($loop->first)product-gallery__thumbnails-item--active @endif">
                                            <div class="image__body"><img class="image__tag" src="{{ !empty($img) ? $img : ('/images/type/'.$listing->type_id.'.svg') }}" alt=""></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="owl-nav disabled">
                            <button type="button" role="presentation" class="owl-prev"><span
                                    aria-label="Previous">‹</span></button>
                            <button type="button" role="presentation" class="owl-next"><span
                                    aria-label="Next">›</span></button>
                        </div>
                        <div class="owl-dots disabled"></div>
                    </div>
                </div>
            </div>

            <div class="product__main">{!! $listing->description !!}</div>
            <div class="product__main">
                {{ $listing->region_name }}, {{ $listing->district_name }}
                <br>
                {!! $listing->address !!}
            </div>
            <div class="product__info">
                <div class="product__info-card">
                    <div class="product__info-body">
                        <table class="analogs-table">
                            <tbody>
                            <tr>
                                @if($listing->is_rent)
                                    <td class="analogs-table__column analogs-table__column--name"><a href="" class="analogs-table__product-name">{{ __('rent') }}</a><br>
                                    </td>
                                    <td class="analogs-table__column analogs-table__column--vendor"
                                        data-title="Vendor">{{ $listing->rent_price }}
                                    </td>
                                    <td class="analogs-table__column analogs-table__column--price">{{ __('for_'.$listing->rent_type) }}</td>
                                @endif
                                @if($listing->is_sell)
                                    <td class="analogs-table__column analogs-table__column--name"><a href="" class="analogs-table__product-name">{{ __('sell') }}</a><br>
                                    </td>
                                    <td class="analogs-table__column analogs-table__column--vendor"
                                        data-title="Vendor">{{ $listing->sell_price }}
                                    </td>
                                    <td class="analogs-table__column analogs-table__column--price">{{ __('for_'.$listing->sell_type) }}</td>
                                @endif
                            </tr>
                            </tbody>
                        </table>
                        <br>
                        <div class="product__meta">
                            <table>
                                <tbody>
                                <tr>
                                    <td class="spec__name">{{ __('floor') }}</td>
                                    <td class="spec__value">{{ $listing->floor }}</td>
                                </tr>
                                <tr>
                                    <td class="spec__name">{{ __('floors_qty') }}</td>
                                    <td class="spec__value">{{ $listing->floors_qty }}</td>
                                </tr>
                                <tr>
                                    <td class="spec__name">{{ __('rooms_qty') }}</td>
                                    <td class="spec__value">{{ $listing->rooms_qty }}</td>
                                </tr>
                                <tr>
                                    <td class="spec__name">{{ __('area') }}</td>
                                    <td class="spec__value">{{ $listing->area }}</td>
                                </tr>
                                @if($listing->beds_qty)
                                    <tr>
                                        <th>{{ $listing->beds_qty }}</th>
                                        <td>{{ __('beds_qty') }}</td>
                                    </tr>
                                @endif
                                @if($listing->land_area)
                                    <tr>
                                        <th>{{ $listing->land_area }}</th>
                                        <td>{{ __('land_area') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="product-form product__form">
                        <div class="product-form__body">
                            <div class="product-form__row">
                                <div class="product-form__control">
                                    <div class="input-radio-label">
                                        <div class="input-radio-label__list">
                                            <span class="input-radio-label__title mr-2">{{ $listing->type_name }}</span>
                                            <span class="input-radio-label__title mr-2">{{ $listing->category_name }}</span>
                                            <span class="input-radio-label__title mr-2">{{ __($listing->building_material) }}</span>
                                            <span class="input-radio-label__title">{{ __($listing->repairment) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product__actions product__actions--layout--full justify-content-between">
                        <button class="btn btn-primary"><i class="fa fa-phone-volume"></i> +998 97 782 08 09</button>
                        <div>
                            <button class="btn btn-primary open-booking"
                                    data-listing_id="{{ $listing->id }}">
                                Xabar qoldirish
                            </button>

                            <button class="btn" type="button">
                                <svg width="16" height="16">
                                    <path d="M13.9,8.4l-5.4,5.4c-0.3,0.3-0.7,0.3-1,0L2.1,8.4c-1.5-1.5-1.5-3.8,0-5.3C2.8,2.4,3.8,2,4.8,2s1.9,0.4,2.6,1.1L8,3.7
	l0.6-0.6C9.3,2.4,10.3,2,11.3,2c1,0,1.9,0.4,2.6,1.1C15.4,4.6,15.4,6.9,13.9,8.4z"></path>
                                </svg>
                                <span>Add to wishlist</span></button>
                            <button class="btn" type="button">
                                <svg width="16" height="16">
                                    <path d="M9,15H7c-0.6,0-1-0.4-1-1V2c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C10,14.6,9.6,15,9,15z"></path>
                                    <path d="M1,9h2c0.6,0,1,0.4,1,1v4c0,0.6-0.4,1-1,1H1c-0.6,0-1-0.4-1-1v-4C0,9.4,0.4,9,1,9z"></path>
                                    <path d="M15,5h-2c-0.6,0-1,0.4-1,1v8c0,0.6,0.4,1,1,1h2c0.6,0,1-0.4,1-1V6C16,5.4,15.6,5,15,5z"></path>
                                </svg>
                                <span>Add to compare</span></button>
                        </div>
                    </div>
                    <div class="product__tags-and-share-links">
                        <div class="product__tags tags tags--sm">
                            <div class="tags__list"><a href="">Brake Kit</a> <a href="">Brandix</a> <a href="">Filter</a>
                                <a href="">Bumper</a> <a href="">Transmission</a> <a href="">Hood</a></div>
                        </div>
                        <div class="product__share-links share-links">
                            <ul class="share-links__list">
                                <li class="share-links__item share-links__item--type--like"><a href="">Like</a></li>
                                <li class="share-links__item share-links__item--type--tweet"><a href="">Tweet</a>
                                </li>
                                <li class="share-links__item share-links__item--type--pin"><a href="">Pin It</a>
                                </li>
                                <li class="share-links__item share-links__item--type--counter"><a href="">4K</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
