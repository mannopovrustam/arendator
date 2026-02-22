{{--

{
"id": 4,
"parent_id": 4,
"name": null,
"type_id": 3,
"category_id": 7,
"main_pty": 76,
"objs_properties": null,
"region_id": 1,
"district_id": 1,
"id_cad": 1,
"address": "Ташкент, улица Айбека, 38",
"geolocation": "41.29194773392257,69.27998469620867",
"rooms_qty": null,
"area": "35.00",
"floor": 2,
"floors_qty": 4,
"repairment": "euro",
"building_material": "panel",
"living_complex": null,
"land_area": null,
"beds_qty": null,
"is_rent": 1,
"is_sell": 1,
"rent_type": null,
"rent_price": "400.00",
"sell_type": null,
"sell_price": null,
"id_currency": 1,
"description": "Оперативный показ! Звоните!\r\n\r\nОтделка:\r\nВыполнена стандартная офисная отделка.\r\nПланировка:\r\nОткрытая.\r\nПарковка:\r\nБесплатная парковка рядом со входом, генерирующая дополнительный трафик.\r\nДополнительная информация:\r\n1 этаж, 1 минута пешком от станции метро Аэропорт, выход на первую линию Ленинградского проспекта, зона охвата на 80000 человек, мощность 35 кв, наличие мокрой точки и вентиляции, широкие рекламные возможности",
"status": "active",
"images": "images/permanent/image-Da0KDFp0CJ.png,images/permanent/image-2ei5RW5K5w.png",
"entry_by": 1,
"created_dt": "2025-06-07",
"created_at": "2025-06-07 10:13:00",
"category_name": "Ofis",
"type_name": "Biznes",
"region_name": "ANDIJON",
"district_name": "ANDIJON SHAHRI",
"params": "<div class=\"product-item__info\" style=\"display: flex; padding: 1rem; align-items: end;\"><div class=\"product-item__info-key\" style=\"width: 50%; font-weight: 400; font-size:0.8rem\">Ofis maydoni</div><div class=\"product-item__info-value\" style=\"width: 50%; font-weight: 600\">35.00 м<sup>2</sup></div></div><div class=\"product-item__info\" style=\"display: flex; padding: 1rem; align-items: end;border-top:1px solid #ccc;\"><div class=\"product-item__info-key\" style=\"width: 50%; font-weight: 400; font-size:0.8rem\">Qavat</div><div class=\"product-item__info-value\" style=\"width: 50%; font-weight: 600\">2 </div></div><div class=\"product-item__info\" style=\"display: flex; padding: 1rem; align-items: end;border-top:1px solid #ccc;\"><div class=\"product-item__info-key\" style=\"width: 50%; font-weight: 400; font-size:0.8rem\">Binoning qavatlar soni</div><div class=\"product-item__info-value\" style=\"width: 50%; font-weight: 600\">4 </div></div><div class=\"product-item__info\" style=\"display: flex; padding: 1rem; align-items: end;border-top:1px solid #ccc;\"><div class=\"product-item__info-key\" style=\"width: 50%; font-weight: 400; font-size:0.8rem\">Ta'mirlash</div><div class=\"product-item__info-value\" style=\"width: 50%; font-weight: 600\">Yevropa uslubida</div></div><div class=\"product-item__info\" style=\"display: flex; padding: 1rem; align-items: end;border-top:1px solid #ccc;\"><div class=\"product-item__info-key\" style=\"width: 50%; font-weight: 400; font-size:0.8rem\">Qurilish materiali</div><div class=\"product-item__info-value\" style=\"width: 50%; font-weight: 600\">Panel</div></div>"
}

--}}

<div class="quickview modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <button type="button" class="quickview__close">
            <svg width="12" height="12">
                <path d="M10.8,10.8L10.8,10.8c-0.4,0.4-1,0.4-1.4,0L6,7.4l-3.4,3.4c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L4.6,6L1.2,2.6
	c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L6,4.6l3.4-3.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L7.4,6l3.4,3.4
	C11.2,9.8,11.2,10.4,10.8,10.8z"/>
            </svg>
        </button>
        <div class="quickview__body">
            <div class="product-gallery product-gallery--layout--quickview quickview__gallery" data-layout="quickview">
                <div class="product-gallery__featured">
                    <button type="button" class="product-gallery__zoom">
                        <svg width="24" height="24">
                            <path d="M15,18c-2,0-3.8-0.6-5.2-1.7c-1,1.3-2.1,2.8-3.5,4.6c-2.2,2.8-3.4,1.9-3.4,1.9s-0.6-0.3-1.1-0.7
	c-0.4-0.4-0.7-1-0.7-1s-0.9-1.2,1.9-3.3c1.8-1.4,3.3-2.5,4.6-3.5C6.6,12.8,6,11,6,9c0-5,4-9,9-9s9,4,9,9S20,18,15,18z M15,2
	c-3.9,0-7,3.1-7,7s3.1,7,7,7s7-3.1,7-7S18.9,2,15,2z M16,13h-2v-3h-3V8h3V5h2v3h3v2h-3V13z"/>
                        </svg>
                    </button>
                    <div class="owl-carousel">
                        @foreach($listing->images ? explode(",", $listing->images) : [] as $image)
                            <a class="image image--type--product"
                               href="{{ $image }}" target="_blank"
                               data-width="500" data-height="500">
                                <div class="image__body"><img class="image__tag"
                                                              src="{{ $image }}" alt=""></div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="product-gallery__thumbnails">
                    <div class="owl-carousel">
                        @foreach($listing->images ? explode(",", $listing->images) : [] as $image)
                            <div class="product-gallery__thumbnails-item image image--type--product">
                                <div class="image__body"><img class="image__tag" src="{{ $image }}" alt=""></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="quickview__product">
                <div class="quickview__product-name">{{ $listing->name }}</div>
{{--
                <div class="quickview__product-rating">
                    <div class="quickview__product-rating-stars">
                        <div class="rating">
                            <div class="rating__body">
                                <div class="rating__star rating__star--active"></div>
                                <div class="rating__star rating__star--active"></div>
                                <div class="rating__star rating__star--active"></div>
                                <div class="rating__star"></div>
                                <div class="rating__star"></div>
                            </div>
                        </div>
                    </div>
                    <div class="quickview__product-rating-title">14 reviews</div>
                </div>
--}}
                <div class="quickview__product-meta">
                    <table>

                        <tr>
                            <th>Viloyat</th>
                            <td><a href="">{{ $listing->region_name }}</a></td>
                        </tr>
                        <tr>
                            <th>Tuman</th>
                            <td><a href="">{{ $listing->district_name }}</a></td>
                        </tr>

                        <tr>
                            <th>Kategoriya</th>
                            <td><a href="">{{ $listing->category_name }}</a></td>
                        </tr>
                        <tr>
                            <th>Turi</th>
                            <td><a href="">{{ $listing->type_name }}</a></td>
                        </tr>
                    </table>
                </div>
                <div class="quickview__product-description">{!! $listing->description !!}</div>
                <div class="quickview__product-prices-stock">
                    <div class="quickview__product-prices">
                        @if($listing->sell_price)
                            <div class="quickview__product-price">{{ number_format($listing->sell_price,2) }} {{ $listing->currency_name }}
                                @if($listing->sell_type == 'all') Butun @else м<sup>2</sup> @endif
                            </div>
                        @endif
                        @if($listing->rent_price)
                            <div class="quickview__product-price">
                                @if($listing->rent_type == 'daily') Kuniga @else Oyiga @endif
                                {{ number_format($listing->rent_price,2) }} {{ $listing->currency_name }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="product-form quickview__product-form">
                    <div class="product-form__body">
                        <div class="product-form__row">
                            <div class="product-form__title">Material</div>
                            <div class="product-form__control">
                                <div class="input-radio-label">
                                    {{--
"rooms_qty": 5,
"area": "97.00",
"floor": 2,
"floors_qty": 5,
"repairment": "middle",
"building_material": "brick",--}}
                                    <div class="product__meta">
                                        <table>
                                            <tbody>
                                            @if($listing->rooms_qty)
                                            <tr>
                                                <th>Xonalar soni</th>
                                                <td>{{ $listing->rooms_qty }}</td>
                                            </tr>
                                            @endif
                                            @if($listing->area)
                                            <tr>
                                                <th>Maydon</th>
                                                <td>{{ $listing->area }}</td>
                                            </tr>
                                            @endif
                                            @if($listing->floor)
                                            <tr>
                                                <th>Etaj</th>
                                                <td>{{ $listing->floor }}</td>
                                            </tr>
                                            @endif
                                            @if($listing->floors_qty)
                                            <tr>
                                                <th>Etajlar soni</th>
                                                <td>{{ $listing->floors_qty }}</td>
                                            </tr>
                                            @endif
                                            @if($listing->repairment)
                                            <tr>
                                                <th>Ta'mir</th>
                                                <td>{{ __($listing->repairment) }}</td>
                                            </tr>
                                            @endif
                                            @if($listing->building_material)
                                            <tr>
                                                <th>Material</th>
                                                <td>{{ __($listing->building_material) }}</td>
                                            </tr>
                                            @endif
                                            @if($listing->living_complex)
                                            <tr>
                                                <th>Yashash kompleksi</th>
                                                <td>{{ __($listing->living_complex) }}</td>
                                            </tr>
                                            @endif
                                            @if($listing->land_area)
                                            <tr>
                                                <th>Yer maydoni</th>
                                                <td>{{ __($listing->land_area) }}</td>
                                            </tr>
                                            @endif
                                            @if($listing->beds_qty)
                                            <tr>
                                                <th>Yotoqlar soni</th>
                                                <td>{{ __($listing->beds_qty) }}</td>
                                            </tr>
                                            @endif
                                            </tbody>
                                        </table>

                                        <table class="table">
                                        @if($listing->address)
                                            <tr>
                                                <th>Manzil</th>
                                                <td><i>{{ $listing->address }}</i></td>
                                            </tr>
                                        @endif
                                        @if($listing->geolocation)
                                                <tr>
                                                    <th>Lokatsiya</th>
                                                    <td>
                                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $listing->geolocation }}"
                                                           type="button" class="btn btn-sm btn-default"
                                                           target="_blank"><i class="fa fa-map-marker"></i> Google Maps</a>

                                                        <a href="https://yandex.uz/maps/?text={{ $listing->geolocation }}"
                                                           target="_blank" class="btn btn-sm btn-default"><i
                                                                class="fa fa-map-marker"></i> Yandex Maps</a>
                                                    </td>
                                                </tr>
                                        @endif
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="quickview__product-actions">
                    <div class="quickview__product-actions-item quickview__product-actions-item--addtocart">
                        <button class="btn btn-primary btn-block open-booking" onclick="openBooking({{ $listing->id }})">Bron qilish</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.quickview__close').on('click', function () {
        $('.mobile-product').slideToggle(700);
        $('body').css('overflow', 'auto');
        $('#quickview-modal').modal('hide');
    });
</script>
