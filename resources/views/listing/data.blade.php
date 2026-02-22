@foreach($data as $d)
    <div class="products-list__item" onclick="openShow('{{$d->id}}','{{$d->name}}')">
        <div class="product-card">
            <div class="product-card__actions-list">
                <button class="product-card__action "
                        type="button" aria-label="Quick view">
                    <svg width="16" height="16">
                        <path d="M14,15h-4v-2h3v-3h2v4C15,14.6,14.6,15,14,15z M13,3h-3V1h4c0.6,0,1,0.4,1,1v4h-2V3z M6,3H3v3H1V2c0-0.6,0.4-1,1-1h4V3z
	 M3,13h3v2H2c-0.6,0-1-0.4-1-1v-4h2V13z"/>
                    </svg>
                </button>
                <button class="product-card__action product-card__action--wishlist"
                        type="button" aria-label="Add to wish list">
                    <svg width="16" height="16">
                        <path d="M13.9,8.4l-5.4,5.4c-0.3,0.3-0.7,0.3-1,0L2.1,8.4c-1.5-1.5-1.5-3.8,0-5.3C2.8,2.4,3.8,2,4.8,2s1.9,0.4,2.6,1.1L8,3.7
	l0.6-0.6C9.3,2.4,10.3,2,11.3,2c1,0,1.9,0.4,2.6,1.1C15.4,4.6,15.4,6.9,13.9,8.4z"/>
                    </svg>
                </button>
                <button class="product-card__action product-card__action--compare"
                        type="button" aria-label="Add to compare">
                    <svg width="16" height="16">
                        <path d="M9,15H7c-0.6,0-1-0.4-1-1V2c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C10,14.6,9.6,15,9,15z"/>
                        <path d="M1,9h2c0.6,0,1,0.4,1,1v4c0,0.6-0.4,1-1,1H1c-0.6,0-1-0.4-1-1v-4C0,9.4,0.4,9,1,9z"/>
                        <path d="M15,5h-2c-0.6,0-1,0.4-1,1v8c0,0.6,0.4,1,1,1h2c0.6,0,1-0.4,1-1V6C16,5.4,15.6,5,15,5z"/>
                    </svg>
                </button>
            </div>
            <div class="product-card__image">
                <div class="image image--type--product"><a href="#" class="image__body product-card__action--quickview"><img class="image__tag"
                            src="{{ $d->images != '' ? explode(",", $d->images)[0] : ('/images/type/'.$d->type_id.'.svg') }}" alt=""></a>
                </div>
            </div>
            <div class="product-card__info">
                <div class="product-card__meta">
                    <span class="product-card__meta-title">ID:</span>
                    {{ $d->region_id }}-{{ $d->id }}
                </div>
                <div class="product-card__name">
                    <div>
                        <div class="product-card__badges">
                            @isset($d->sale) <div class="tag-badge tag-badge--sale">sale</div> @endif
                            @isset($d->new) <div class="tag-badge tag-badge--new">new</div> @endif
                            @isset($d->hot) <div class="tag-badge tag-badge--hot">hot</div> @endif
                        </div>
                        <a href="#" class="product-card__action--quickview">{{ $d->name }}</a></div>
                </div>
                <div class="product-card__rating">
                    <div class="rating product-card__rating-stars">
                        <div class="rating__body">
                            <div class="rating__star rating__star--active"></div>
                            <div class="rating__star rating__star--active"></div>
                            <div class="rating__star rating__star--active"></div>
                            <div class="rating__star rating__star--active"></div>
                            <div class="rating__star"></div>
                        </div>
                    </div>
                    <div class="product-card__rating-label">4 on 3 reviews</div>
                </div>
                <div class="product-card__features">
                    <p>{{ $d->address }}</p>
                </div>
            </div>
            <div class="product-card__footer justify-content-between">
                <div class="product-card__info">
                    @if($d->sell_price)
                    <div class="product-card__name product-card__price--current">
                        <div><a href="#">{{ $d->sell_price }}</a></div>
                    </div>
                    <div class="product-card__rating">
                        <div class="product-card__rating-label">So’m за {!! $d->sell_type == 'all' ? 'всё' : 'м<sup>2</sup>' !!}</div>
                    </div>
                    @endif
                    @if($d->rent_price)
                    <div class="product-card__name product-card__price--current">
                        <div><a href="#">{{ $d->rent_price }}</a></div>
                    </div>
                    <div class="product-card__rating">
                        <div class="product-card__rating-label">So’m за {{ $d->rent_type == 'daily' ? 'сутки' : 'месяц' }}</div>
                    </div>
                    @endif
                </div>
                <div class="product-card__info">
                    <div class="product-card__name">
                        <div><a href="#">{{ $d->area }} м<sup>2</sup></a></div>
                    </div>
                    <div class="product-card__rating">
                        <div class="product-card__rating-label">Этаж: {{ $d->floor }} из {{ $d->floors_qty }}</div>
                    </div>
                </div>
            </div>
            <div class="product-card__footer justify-content-center">
                <button type="submit" class="btn btn-primary">+998 97 782-08-09</button>
                <button type="submit" class="btn btn-outline-secondary"><i class="fa fa-envelope"></i></button>
            </div>
        </div>
    </div>
@endforeach

<style>
    .modal {
        pointer-events: none;
    }
    .jconfirm {
        z-index: 2000 !important;
    }
    .jconfirm-holder {
        z-index: 1991 !important;
    }
    .modal {
        transform: none !important;
    }
</style>
<script>
    <?php $user = \Session::get('user',auth()->user()); ?>

    function openBooking(listing_id) {

        // $('#quickview-modal').modal('hide');
        $(document).off('focusin.bs.modal');

        setTimeout(function () {
        $.confirm({
            title: 'Ariza qoldirish / Bron qilish',
            boxWidth: '600px',
            useBootstrap: false,
            content: '<form id="bookingForm" style="z-index:2001"><div class=""><div class="col-md-12 mb-2"><label for="name">FIO</label><input type="text" id="name" name="name" class="form-control" value="{{ $user ? $user->name : '' }}"></div><div class="col-md-12 mb-2"><label for="date_from">Kirish *</label><input type="date" name="date_from" id="date_from" class="form-control date_from" min="{{ date('Y-m-d') }}" required></div><div class="col-md-12 mb-2"><label for="date_to">Chiqish *</label><input type="date" name="date_to" id="date_to" class="form-control date_to" min="{{ date('Y-m-d') }}" required></div><div class="col-md-12 mb-2"><label for="contact_phone">Telefon *</label><input type="text" name="contact_phone" id="contact_phone" class="form-control phone-mask" value="{{ $user ? str_replace('+','',$user->phone) : '998' }}" required></div><div class="col-md-12 mb-2"><label for="comments">Xabar</label><textarea id="comments" name="comments" class="form-control comments"></textarea></div></div></form>',
            onContentReady: function () {
                var jc = this;
                this.$content.find('#bookingForm').off('submit').on('submit', function (e) {
                    e.preventDefault();
                    jc.$$save.trigger('click');
                });
            },
            buttons: {
                save: {
                    text: 'Yuborish',
                    btnClass: 'btn-success',
                    action: function () {
                        // filter form data
                        if (!this.$content.find('input[name="date_from"]').val()) {
                            $.alert('Iltimos, [Kirish] sanani kiriting (Kirish)');
                            return false;
                        }
                        if (!this.$content.find('input[name="date_to"]').val()) {
                            $.alert('Iltimos, [Chiqish] sanani kiriting (Chiqish)');
                            return false;
                        }
                        if (!this.$content.find('input[name="contact_phone"]').val() || this.$content.find('input[name="contact_phone"]').val().length < 12) {
                            $.alert('Iltimos, [Telefon] raqamingizni to\'liq kiriting');
                            return false;
                        }
                        if (new Date(this.$content.find('input[name="date_from"]').val()) >= new Date(this.$content.find('input[name="date_to"]').val())) {
                            $.alert('Kirish sanasi Chiqish sanasidan katta bo\'lishi kerak');
                            return false;
                        }
                        // date_to min today and max date_from + 1 year
                        let today = new Date().toISOString().split('T')[0];
                        this.$content.find('input[name="date_from"]').attr('min', today);
                        this.$content.find('input[name="date_to"]').attr('min', today);
                        let date_from = new Date(this.$content.find('input[name="date_from"]').val());
                        let max_date_to = new Date(date_from);
                        max_date_to.setFullYear(max_date_to.getFullYear() + 1);
                        this.$content.find('input[name="date_to"]').attr('max', max_date_to.toISOString().split('T')[0]);
                        if (new Date(this.$content.find('input[name="date_to"]').val()) > max_date_to) {
                            $.alert('[Kirish] sanasi [Chiqish] sanasidan 1 yildan oshmasligi kerak');
                            return false;
                        }
                        // date_from min today
                        if (new Date(this.$content.find('input[name="date_from"]').val()) < new Date(today)) {
                            $.alert('[Kirish sanasi] bugungi kundan kichik bo\'lishi mumkin emas');
                            return false;
                        }

                        let formData = $('#bookingForm').serialize();
                        // add listing_id to formData
                        formData += '&listing_id=' + listing_id;

                        $.ajax({
                            url: '/bookings/store',
                            type: 'POST',
                            data: formData + '&_token={{ csrf_token() }}',
                            success: function (res) {
                                console.log(res);
                                $.alert(res.message);
                                return true;
                            },
                            error: function (res) {
                                console.log(res);
                                $.alert(res.responseJSON.message);
                                return false;
                            }
                        });
                    }
                },
                close: {
                    text: 'Yopish',
                    btnClass: 'btn-danger',
                    action: function () {
                        close();
                    }
                }
            }
        });

        }, 300);
    }

</script>
