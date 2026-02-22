
<div class="mobile-product__close" style="display: flex; justify-content: flex-end; padding: 1rem;">
    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 13L13 1M1 1L13 13" stroke="#fffccc" stroke-width="1.5"/>
    </svg>
</div>
<div class="mobile-product__wrap">
    <div class="mobile-product__content">
        <h2 class="product__title">{{ $listing->name }}</h2>
        <div class="mobile-product-item__img" style="height: auto;  display:flex; overflow: scroll;">

            <div class="mobile-product-item__img" style="height: 20rem;  display:flex; overflow: scroll;">
                @foreach(explode(',', $listing->images) as $img)
                    <img src="{{ $img }}" class="single-mobile__img" alt="product" style="height: 100%;margin-right: 7px;max-width: none;">
                @endforeach
            </div>
        </div>
        <div class="product-item__wrap">
            <div class="product-item__name">{{ $listing->address }}</div>
            {!! $listing->params !!}
            {{--                button for call and write message--}}
            <div class="mobile-product-item__btns" style="display: flex; justify-content: space-between; margin-top: 1rem;">
                <button class="mobile-product-item__btn" style="width: 49%; background: #024a57; color: #fff; border: none; padding: 0.5rem 0; border-radius: 5px;">
                    Qo'ng'iroq
                </button>
                <button class="mobile-product-item__btn" style="width: 49%; background: #048198; color: #fff; border: none; padding: 0.5rem 0; border-radius: 5px;">
                    Xabar yozish
                </button>
            </div>

            <div id="map-{{ $listing->id }}" style="height: 300px; margin: 1rem 0;"></div>

            @if($listing->is_rent)
                <div class="product-item__info" style="display: flex; padding: 1rem; align-items: end; @if(!$listing->is_sell) border:1px 0 solid #ccc; @endif">
                    <div class="product-item__info-key" style="width: 50%; font-weight: 400; font-size:0.8rem">Аренда</div>
                    <div class="product-item__info-value" style="width: 50%; font-weight: 600"><b>{{ $listing->rent_price }}</b> so'm {!! $listing->rent_type == 'monthly' ? 'за месяц' : 'за м<sup>2</sup>' !!}</div>
                </div>
            @endif
            @if($listing->is_sell)
                <div class="product-item__info" style="display: flex; padding: 1rem; align-items: end; @if($listing->is_rent) border-top:1px solid #ccc; @endif">
                    <div class="product-item__info-key" style="width: 50%; font-weight: 400; font-size:0.8rem">Продажа</div>
                    <div class="product-item__info-value" style="width: 50%; font-weight: 600"><b>{{ $listing->sell_price }}</b> so'm {!! $listing->sell_type == 'all' ? 'за все' : 'за м<sup>2</sup>' !!}</div>
                </div>
            @endif

            <div class="mobile-product-item__name" style="margin-bottom: 10rem;">
                {!! $listing->description !!}
            </div>
        </div>
    </div>
</div>

<script>

    $('.mobile-product__close').click(function() {
        $('.mobile-product').slideToggle(700)
        $('body').css('overflow', 'auto');
    });

    $(document).ready(function(){
        $('.owl-carousel').owlCarousel({
            margin:20,
            loop:true,
            autoWidth:true,
            items:4,
            dots: false,
        })
    });
</script>

<script>
    ymaps.ready(init);
    function init(){
        myMap = new ymaps.Map("map-{{ $listing->id }}", {
            {{--center: [{{ $listing->geolocation }}],--}}
            center: [{{ $listing->geolocation }}],
            zoom: 15
        });
        placemark = new ymaps.Placemark([{{ $listing->geolocation }}], {
            hintContent: '{{ $listing->name }}',
            balloonContent: '{{ $listing->name }}'
        });
        myMap.geoObjects.add(placemark);
    }
</script>

