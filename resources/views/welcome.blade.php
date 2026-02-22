@extends('layouts.site')

@section('styles')
    <style>
        #filter_bar > button {
            white-space: nowrap;
        }
        .jconfirm .form-control,
        .jconfirm textarea,
        .jconfirm input {
            pointer-events: auto;
        }
        .modal {
            pointer-events: none;
        }
    </style>
@endsection
@section('content')

<div class="site">
    @include('layouts.header')
    <div class="site__body">
        <div class="block-header block-header--has-breadcrumb block-header--has-title">
            <div class="block-split block-header__breadcrumb">
                <div class="container">
                    <div class="block-split__row no-gutters">
                        <div class="block-split__item block-split__item-content col-auto">
                            <div class="block">
                                <div class="products-view">
                                    <div class="products-view__options view-options view-options--offcanvas--always">
                                        <div class="view-options__body">
                                            <div class="view-options__spring d-flex" style="overflow-x:auto">
                                                <div class="mr-3">
                                                    <button
                                                        class="btn btn-secondary search__button--start search__deal_type"
                                                        type="button">{{ __('deal_type') }}</button>

                                                    <div class="search__dropdown search__dropdown--deal_type">
                                                        <div class="search__dropdown-arrow"></div>
                                                        <div
                                                            class="vehicle-picker__panel vehicle-picker__panel--list vehicle-picker__panel--active"
                                                            data-panel="list">
                                                            <div class="vehicle-picker__panel-body">
                                                                <div class="vehicles-list">
                                                                    <div class="vehicles-list__body">
                                                                        <label class="vehicles-list__item">
                                                                            <span
                                                                                class="vehicles-list__item-radio input-radio">
                                                                                <span class="input-radio__body">
                                                                                    <input
                                                                                        class="input-radio__input filter__deal_type"
                                                                                        name="filter[deal_type]"
                                                                                        type="radio" value="rent">
                                                                                    <span
                                                                                        class="input-radio__circle"></span>
                                                                                </span>
                                                                            </span>
                                                                            <span
                                                                                class="deal_type-text">{{__('rent')}}</span>
                                                                        </label>
                                                                        <label class="vehicles-list__item">
                                                                            <span
                                                                                class="vehicles-list__item-radio input-radio">
                                                                                <span class="input-radio__body">
                                                                                    <input
                                                                                        class="input-radio__input filter__deal_type"
                                                                                        name="filter[deal_type]"
                                                                                        type="radio" value="sell">
                                                                                    <span
                                                                                        class="input-radio__circle"></span>
                                                                                </span>
                                                                            </span>
                                                                            <span
                                                                                class="deal_type-text">{{__('sell')}}</span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mr-3">
                                                    <button class="btn btn-secondary search__button--start search__price"
                                                            type="button">{{ __('price') }}
                                                    </button>

                                                    <div class="search__dropdown search__dropdown--price">
                                                        <div class="search__dropdown-arrow"></div>
                                                        <div
                                                            class="vehicle-picker__panel vehicle-picker__panel--list vehicle-picker__panel--active"
                                                            data-panel="list">
                                                            <div class="vehicle-picker__panel-body">

                                                                <ul class="filter-vehicle__list sell_price_type"
                                                                    style="display: none; justify-content: start;">
                                                                    <li class="filter-vehicle__item"
                                                                        style="margin-right: 15px;"><label
                                                                            class="filter-vehicle__item-label"><span
                                                                                class="filter-list__input input-radio"><span
                                                                                    class="input-radio__body"><input
                                                                                        class="input-radio__input"
                                                                                        name="filter[price_type]"
                                                                                        type="radio" value="all"> <span
                                                                                        class="input-radio__circle"></span> </span></span><span
                                                                                class="filter_price_type-text">За всё</span></label>
                                                                    </li>
                                                                    <li class="filter-vehicle__item"><label
                                                                            class="filter-vehicle__item-label"><span
                                                                                class="filter-list__input input-radio"><span
                                                                                    class="input-radio__body"><input
                                                                                        class="input-radio__input"
                                                                                        name="filter[price_type]"
                                                                                        type="radio"
                                                                                        value="square"> <span
                                                                                        class="input-radio__circle"></span> </span></span><span
                                                                                class="filter_price_type-text">За м<sup>2</sup></span></label>
                                                                    </li>
                                                                </ul>
                                                                <ul class="filter-vehicle__list rent_price_type"
                                                                    style="display: none; justify-content: start;">
                                                                    <li class="filter-vehicle__item"
                                                                        style="margin-right: 15px;"><label
                                                                            class="filter-vehicle__item-label"><span
                                                                                class="filter-list__input input-radio"><span
                                                                                    class="input-radio__body"><input
                                                                                        class="input-radio__input"
                                                                                        name="filter[price_type]"
                                                                                        type="radio"
                                                                                        value="monthly"> <span
                                                                                        class="input-radio__circle"></span> </span></span><span
                                                                                class="filter_price_type-text">За месяц</span></label>
                                                                    </li>
                                                                    <li class="filter-vehicle__item"><label
                                                                            class="filter-vehicle__item-label"><span
                                                                                class="filter-list__input input-radio"><span
                                                                                    class="input-radio__body"><input
                                                                                        class="input-radio__input"
                                                                                        name="filter[price_type]"
                                                                                        type="radio"
                                                                                        value="daily"> <span
                                                                                        class="input-radio__circle"></span> </span></span><span
                                                                                class="filter_price_type-text">За сутки</span></label>
                                                                    </li>
                                                                </ul>

                                                                <div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center"
                                                                        style="margin-top:1rem;">
                                                                        <input type="text"
                                                                               class="form-control mr-2 only_digit"
                                                                               id="filter_price_min"
                                                                               name="filter[price_min]"
                                                                               placeholder="Мин.Сумма">
                                                                        <i class="fa fa-sm fa-minus"></i>
                                                                        <input type="text"
                                                                               class="form-control ml-2 only_digit"
                                                                               id="filter_price_max"
                                                                               name="filter[price_max]"
                                                                               placeholder="Мах.Сумма">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mr-3">
                                                    <button
                                                        class="btn btn-secondary search__button--start search__object_type"
                                                        type="button">{{ __('object_type') }}
                                                    </button>

                                                    <div class="search__dropdown search__dropdown--object_type"
                                                         style="z-index:100;max-width:200px">
                                                        <div class="search__dropdown-arrow"></div>
                                                        <div
                                                            class="vehicle-picker__panel vehicle-picker__panel--list vehicle-picker__panel--active"
                                                            data-panel="list">
                                                            <div class="vehicle-picker__panel-body">
                                                                <div class="vehicles-list">
                                                                    <div class="vehicles-list__body">
                                                                        <ul class="filter-vehicle__list">
                                                                            @foreach(\DB::table('object_types')->get() as $k => $ot)
                                                                                <li class="filter-vehicle__item"><label
                                                                                        class="filter-vehicle__item-label"><span
                                                                                            class="filter-list__input input-radio"><span
                                                                                                class="input-radio__body"><input
                                                                                                    class="input-radio__input"
                                                                                                    name="filter[object_type]"
                                                                                                    value="{{ $ot->id }}"
                                                                                                    type="radio"> <span
                                                                                                    class="input-radio__circle"></span> </span></span>
                                                                                        <span
                                                                                            class="object_type-text">{{ $ot->name }} </span></label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-options__body view-options__body--filters d-flex"
                                             style="flex-wrap:nowrap; overflow-x: auto">
                                            <div class="view-options__label"></div>
                                            <div class="view-options__spring d-flex" id="filter_bar_selected"></div>
                                            <div class="view-options__spring d-flex" id="filter_bar">
                                                @foreach(\DB::table('filters')->whereNull('parent_id')->get() as $filter)
                                                    <button type="button"
                                                            onclick="filterBar('{{ $filter->id }}','{{ $filter->type }}','{{ $filter->object_type }}','{{ $filter->object_category }}','{{ $filter->object_pty }}')"
                                                            id="filter_{{$filter->id}}"
                                                            data-name="{{ $filter->icon }} {{ $filter->$name }}"
                                                            class="btn btn-secondary mr-3"><span
                                                            class="filters-button__title">{{ $filter->icon }} {{ $filter->$name }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="products-view__list products-list products-list--grid--4"
                                         data-layout="grid"
                                         data-with-features="true">
                                        <div class="products-list__head"></div>
                                        <div class="products-list__content product-wrap"></div>
                                    </div>
                                    <div class="products-view__pagination">
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination">
                                                <li class="page-item disabled"><a
                                                        class="page-link page-link--with-arrow"
                                                        href="shop-grid-4-columns-full.html"
                                                        aria-label="Previous"><span
                                                            class="page-link__arrow page-link__arrow--left"
                                                            aria-hidden="true"><svg
                                                                width="7" height="11"><path
                                                                    d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z"/></svg></span></a>
                                                </li>
                                                <li class="page-item"><a class="page-link"
                                                                         href="shop-grid-4-columns-full.html#">1</a>
                                                </li>
                                                <li class="page-item active" aria-current="page"><span
                                                        class="page-link">2 <span
                                                            class="sr-only">(current)</span></span>
                                                </li>
                                                <li class="page-item"><a class="page-link"
                                                                         href="shop-grid-4-columns-full.html#">3</a>
                                                </li>
                                                <li class="page-item"><a class="page-link"
                                                                         href="shop-grid-4-columns-full.html#">4</a>
                                                </li>
                                                <li class="page-item page-item--dots">
                                                    <div class="pagination__dots"></div>
                                                </li>
                                                <li class="page-item"><a class="page-link"
                                                                         href="shop-grid-4-columns-full.html#">9</a>
                                                </li>
                                                <li class="page-item"><a class="page-link page-link--with-arrow"
                                                                         href="shop-grid-4-columns-full.html"
                                                                         aria-label="Next"><span
                                                            class="page-link__arrow page-link__arrow--right"
                                                            aria-hidden="true"><svg
                                                                width="7" height="11"><path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9
	C-0.1,9.8-0.1,10.4,0.3,10.7z"/></svg></span></a></li>
                                            </ul>
                                        </nav>
                                        <div class="products-view__pagination-legend">Showing 6 of 98 products</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-space block-space--layout--before-footer"></div>
                </div>
            </div>
        </div><!-- site__body / end --><!-- site__footer -->
        <footer class="site__footer">
            <div class="site-footer">
                <div class="decor site-footer__decor decor--type--bottom">
                    <div class="decor__body">
                        <div class="decor__start"></div>
                        <div class="decor__end"></div>
                        <div class="decor__center"></div>
                    </div>
                </div>
                <div class="site-footer__widgets">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-xl-4"></div>
                            <div class="col-6 col-md-3 col-xl-2"></div>
                            <div class="col-6 col-md-3 col-xl-2"></div>
                            <div class="col-12 col-md-6 col-xl-4"></div>
                        </div>
                    </div>
                </div>
                <div class="site-footer__bottom">
                    <div class="container">
                        <div class="site-footer__bottom-row">
                            <div class="site-footer__payments"><img src="images/payments.png" alt=""></div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
<!-- mobile-menu / end --><!-- quickview-modal -->
<div id="quickview-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                <!--<button class="pswp__button pswp__button&#45;&#45;share" title="Share"></button>-->
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="/vendor/jquery/jquery.min.js"></script>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/vendor/owl-carousel/owl.carousel.min.js"></script>
<script src="/vendor/nouislider/nouislider.min.js"></script>
<script src="/vendor/photoswipe/photoswipe.min.js"></script>
<script src="/vendor/photoswipe/photoswipe-ui-default.min.js"></script>
<script src="/vendor/select2/js/select2.min.js"></script>
<script src="/js/number.js"></script>
<script src="/js/main.js"></script>
<script>

    var __FILTER = {};

    function filterListings() {
        __FILTER = {
            "deal_type": $('input[name=deal_type]:checked').map(function () {
                return $(this).val();
            }).get(),
            "object_type": $('input[name=object_type]:checked').map(function () {
                return $(this).val();
            }).get(),
            // "object_type": $('input[name=object_type]').val(),
            "object_category": $('input[name=object_category]').val(),
            "price_type": $('input[name="filter[price_type]"]:checked').map(function () {
                return $(this).val();
            }).get(),
            "filter_price_max": $('input[name="filter[price_max]"]').val(),
            "filter_price_min": $('input[name="filter[price_min]"]').val(),
            "filter_area_max": $('input[name="filter[area_max]"]').val(),
            "filter_area_min": $('input[name="filter[area_min]"]').val(),
            "filter_floor_min": $('input[name="filter[floor_min]"]').val(),
            "filter_floor_max": $('input[name="filter[floor_max]"]').val(),
            "rooms_qty": $('input[name="rooms_qty"]:checked').map(function () {
                return $(this).val();
            }).get(),
            "repairment": $('input[name="repairment"]:checked').map(function () {
                return $(this).val();
            }).get(),
            "building_material": $('input[name="building_material"]:checked').map(function () {
                return $(this).val();
            }).get()
        }

        // $('.product-wrap').html(loadData);
        loadData().then(function (response) {
            $('.product-wrap').html(response)
        }).catch(function (error) {
            console.error('Ошибка запроса:', error);
        });

    }

    $(document).ready(function () {
        $('input[name="object_category"]').on('change', function () {
            containFilter();
        });
    });
    var filter_row = '';

    function containFilter(key, value) {
        filter_row += '<li class="applied-filters__item"><a href="#" class="applied-filters__button applied-filters__button--filter">' + key + ': ' + value + ' <svg width="9" height="9"> <path d="M9,8.5L8.5,9l-4-4l-4,4L0,8.5l4-4l-4-4L0.5,0l4,4l4-4L9,0.5l-4,4L9,8.5z"/></svg></a></li>';
        // $('.applied-filters__list').append(filter_row);
    }

    function filterBar(id, type, object_type, object_category, object_pty) {
        var name = $('#filter_' + id).data('name');
        $.ajax({
            url: '/data/filter-bar',
            type: 'GET',
            data: {
                id: id,
                type: type,
                object_type: object_type,
                object_category: object_category,
                object_pty: object_pty,
                _token: '{{ csrf_token() }}' // CSRF-токен Laravel
            },
            success: function (response) {
                // Пример добавления кнопки (можно кастомизировать под ответ сервера)
                $('#filter_' + id).remove();

                var filterBtn = '';
                $.each(response, function (index, value) {
                    filterBtn += `<button type="button"
                         onclick="filterBar('${value.id}','${value.type}','${value.object_type}','${value.object_category}','${value.object_pty}')"
                         id="filter_${value.id}" data-name="${value.name}" class="btn btn-secondary mr-3">
                     <span class="filters-button__title">${value.name}</span></button>`;
                });

                $('#filter_bar').html(filterBtn);

                $('#filter_bar_selected').append(
                    `<button type="button" onclick="filterBarDeselect('${id}','${type}','${object_type}','${object_category}','${object_pty}')"
                         id="filter_selected_${id}_${object_type}_${object_category}_${object_pty}" data-name="${name}"
                            data-filter="${id}-_-${type}-_-${object_type}-_-${object_category}-_-${object_pty}"
                         class="btn view-options__filters-button filters-button bg-primary"><span
                         class="filters-button__title text-white">${name}</span> <span
                         class="filters-button__counter"><svg width="9" height="9">
                             <path d="M9,8.5L8.5,9l-4-4l-4,4L0,8.5l4-4l-4-4L0.5,0l4,4l4-4L9,0.5l-4,4L9,8.5z" fill="white"></path>
                         </svg></span></button>`
                );

                if (type) {
                    __FILTER.deal_type = type;
                    $('.filter__deal_type').val(type).trigger('change');
                }
                __FILTER.object_type = object_type;
                __FILTER.object_category = object_category;
                __FILTER.object_pty = object_pty;

                loadData().then(function (response) {
                    $('.product-wrap').html(response);
                }).catch(function (error) {
                    console.error('Ошибка запроса:', error);
                });

            },
            error: function (xhr) {
                console.error('Ошибка фильтрации:', xhr.responseText);
            }
        });
    }

    function filterBarDeselect(id, type, object_type, object_category, object_pty) {

        // Находим элемент по id
        var $target = $('#filter_selected_' + id + '_' + object_type + '_' + object_category + '_' + object_pty);
        if (!$target.length) {
            console.error('Элемент с id ' + $target + ' не найден');
            return;
        }
        var index = $target.index();
        if (index == 0) {
            var prevFilter = ['', '', '', '', ''];
            $('input[name="filter[object_type]"]').prop('checked', false);
            $('.search__object_type .search__button-title').text('{{ __('object_type') }}');
        } else var prevFilter = $target.parent().children().eq(index - 1).data('filter').split('-_-');

        console.log('prevFilter', prevFilter);
        $.ajax({
            url: '/data/filter-bar',
            type: 'GET',
            data: {
                id: prevFilter[0],
                type: prevFilter[1],
                object_type: prevFilter[2],
                object_category: prevFilter[3],
                object_pty: prevFilter[4],
                _token: '{{ csrf_token() }}' // CSRF-токен Laravel
            },
            success: function (response) {

                var filterBtn = '';
                $.each(response, function (index, value) {
                    filterBtn += `<button type="button"
                         onclick="filterBar('${value.id}','${value.type}','${value.object_type}','${value.object_category}','${value.object_pty}')"
                         id="filter_${value.id}" data-name="${value.name}" class="btn btn-secondary mr-3">
                     <span class="filters-button__title">${value.name}</span></button>`;
                });

                $('#filter_bar').html(filterBtn);

                $('#filter_bar_selected').append(
                    `<button type="button" onclick="filterBarDeselect('${id}','${type}','${object_type}','${object_category}','${object_pty}')"
                         id="filter_selected_${id}_${object_type}_${object_category}_${object_pty}" data-name="${name}"
                            data-filter="${id}-_-${type}-_-${object_type}-_-${object_category}-_-${object_pty}"
                         class="view-options__filters-button filters-button bg-primary"><span
                         class="filters-button__title text-white">${name}</span> <span
                         class="filters-button__counter"><svg width="9" height="9">
                             <path d="M9,8.5L8.5,9l-4-4l-4,4L0,8.5l4-4l-4-4L0.5,0l4,4l4-4L9,0.5l-4,4L9,8.5z" fill="white"></path>
                         </svg></span></button>`
                );

                // Удаляем элемент и все последующие
                $target.parent().children().slice(index).remove();

                // Удаляем из __FILTER and loadData
                if (prevFilter[1]) {
                    __FILTER.deal_type = prevFilter[1];
                    $('.filter__deal_type').val(prevFilter[1]).trigger('change');
                } else {
                    __FILTER.deal_type = null;
                    $('.search__deal_type .search__button-title').text('{{ __('deal_type') }}');
                }
                __FILTER.object_type = prevFilter[2] || null;
                __FILTER.object_category = prevFilter[3] || null;
                __FILTER.object_pty = prevFilter[4] || null;
                loadData().then(function (response) {
                    $('.product-wrap').html(response);
                }).catch(function (error) {
                    console.error('Ошибка запроса:', error);
                });


            },
            error: function (xhr) {
                console.error('Ошибка фильтрации:', xhr.responseText);
            }
        });
    }

    function loadData() {
        return $.ajax({
            url: '/listings/data',
            type: 'get',
            data: __FILTER
        });
    }

    $(document).ready(function () {
        $('.product-item').click(function () {
            $('.mobile-product').slideToggle(700);
            $('body').css('overflow', 'hidden');
        })
        // if click out of mobile-product__wrap and in mobile-product slide toggle
        $('.mobile-product__wrap').click(function (e) {
            e.stopPropagation()
        })

        // load data
        loadData().then(function (response) {
            $('.product-wrap').html(response)
        }).catch(function (error) {
            console.error('Ошибка запроса:', error);
        });

    });
    // onscroll page load more content to .product-wrap from /listings/data
    $(window).scroll(function () {
        // when scrolled 80% of the page
        if ($(window).scrollTop() + $(window).height() > $(document).height() * 0.9) {
            loadData().then(function (response) {
                $('.product-wrap').append(response)
                var totalItems = $('.products-list__item').length;
                if (totalItems > 60) {
                    var removeCount = totalItems - 60;
                    $('.products-list__item').slice(0, removeCount).remove();
                }
            }).catch(function (error) {
                console.error('Ошибка запроса:', error);
            });
        }
    });


</script>
<script>
    $(document).ready(function () {
        $('.filter__deal_type').on('change', function () {
            var selectedText = $(this).closest('.vehicles-list__item').find('.deal_type-text').text();
            $('.search__deal_type .search__button-title').text(selectedText);

            var deal_type = $(this).val();
            $('input[name="filter[price_type]"]').prop('checked', false);

            $('.sell_price_type,.rent_price_type').css({'display': 'none'});
            $('.' + deal_type + '_price_type').css({'display': 'flex'});

            __FILTER.deal_type = deal_type;
            __FILTER.price_type = __FILTER.filter_price_min = __FILTER.filter_price_max = null;
            loadData().then(function (response) {
                $('.product-wrap').html(response);
            }).catch(function (error) {
                console.error('Ошибка запроса:', error);
            });
        });
    });

    $(document).ready(function () {
        function updateButtonText() {
            var priceType = $('input[name="filter[price_type]"]:checked').closest('.filter-vehicle__item').find('.filter_price_type-text').text();
            var priceTypeVal = $('input[name="filter[price_type]"]:checked').val();
            var minPrice = $('#filter_price_min').val();
            var maxPrice = $('#filter_price_max').val();

            var displayText = '{{ __('price') }} ';

            displayText += priceType;

            if (minPrice || maxPrice) {
                if (minPrice) displayText += ' от ' + minPrice;
                if (maxPrice) displayText += ' до ' + maxPrice;
                displayText += ' Сум ';
            }

            $('.search__price .search__button-title').text(displayText);

            if (minPrice || maxPrice || priceType) {
                $('.search__price').removeClass('disabled');
            } else {
                $('.search__price').addClass('disabled');
            }

            // add __FILTER.price_type then loadData
            __FILTER.price_type = priceTypeVal;
            __FILTER.filter_price_min = minPrice;
            __FILTER.filter_price_max = maxPrice;
            loadData().then(function (response) {
                $('.product-wrap').html(response);
            }).catch(function (error) {
                console.error('Ошибка запроса:', error);
            });
        }

        $('input[name="filter[price_type]"]').on('change', function () {
            updateButtonText();
        });
        $('#filter_price_min, #filter_price_max').on('keyup', function () {
            updateButtonText();
        });
    });

    $(document).ready(function () {
        $('input[name="filter[object_type]"]').on('change', function () {
            var selectedText = $(this).closest('.filter-vehicle__item').find('.object_type-text').text();
            $('.search__object_type .search__button-title').text(selectedText);
            var object_type = $(this).val();
            var deal_type = __FILTER.deal_type || null;

            var name = selectedText;
            $.ajax({
                url: '/data/filter-bar',
                type: 'GET',
                data: {
                    id: object_type,
                    type: deal_type,
                    object_type: object_type,
                    object_category: '',
                    object_pty: '',
                    _token: '{{ csrf_token() }}' // CSRF-токен Laravel
                },
                success: function (response) {
                    // Пример добавления кнопки (можно кастомизировать под ответ сервера)
                    $('#filter_' + object_type).remove();

                    var filterBtn = '';
                    $.each(response, function (index, value) {
                        filterBtn += `<button type="button"
                         onclick="filterBar('${value.id}','${value.type}','${value.object_type}','${value.object_category}','${value.object_pty}')"
                         id="filter_${value.id}" data-name="${value.name}" class="btn btn-secondary mr-3">
                     <span class="filters-button__title">${value.name}</span></button>`;
                    });

                    $('#filter_bar').html(filterBtn);

                    $('#filter_bar_selected').html(
                        `<button type="button" onclick="filterBarDeselect('${object_type}','${deal_type}','${object_type}','','')"
                         id="filter_selected_${object_type}_${object_type}__" data-name="${name}"
                            data-filter="${object_type}-_-${deal_type}-_-${object_type}-_--_-"
                         class="btn view-options__filters-button filters-button bg-primary"><span
                         class="filters-button__title text-white">${name}</span> <span
                         class="filters-button__counter"><svg width="9" height="9">
                             <path d="M9,8.5L8.5,9l-4-4l-4,4L0,8.5l4-4l-4-4L0.5,0l4,4l4-4L9,0.5l-4,4L9,8.5z" fill="white"></path>
                         </svg></span></button>`
                    );

                    __FILTER.object_type = object_type;
                    loadData().then(function (response) {
                        $('.product-wrap').html(response);
                    }).catch(function (error) {
                        console.error('Ошибка запроса:', error);
                    });

                },
                error: function (xhr) {
                    console.error('Ошибка фильтрации:', xhr.responseText);
                }
            });
        });
    });

</script>
<script>
    $('.only_digit_int').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    $('.only_digit').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/dist/jquery-confirm.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/dist/jquery-confirm.min.js"></script>

<script>
    <?php $user = \Session::get('user',auth()->user()); ?>

    // .phone-mask type only number and 12 digits
    $(document).on('input', '.phone-mask', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 12) {
            this.value = this.value.slice(0, 12);
        }
        // first 3 digits add 998
        if (this.value.length > 0 && this.value.slice(0, 3) !== '998') {
            this.value = '998' + this.value.slice(0, 3);
        }
    });


</script>

<script>

    function openShow(id,name) {
        $('.mobile-product').slideToggle(700);
/*        $.alert({
            title: name,
            content: 'url:/listings/single-product/' + id,
            columnClass: 'mobile-product',
            backgroundDismiss: true,
            buttons: {
                close: {
                    text: 'Закрыть',
                    btnClass: 'btn btn-secondary quickview__close',
                    action: function () {
                        $('.mobile-product').slideToggle(700);
                        $('body').css('overflow', 'auto');
                    }
                }
            },
            onOpenBefore: function () {
                $('body').css('overflow', 'hidden');
            },
            onClose: function () {
                $('.mobile-product').slideToggle(700);
                $('body').css('overflow', 'auto');
            }
        })*/
        $.ajax({
            url: '/listings/single-product/' + id,
            type: 'get',
        }).then(function (response) {
            $('#quickview-modal').html(response);
            $('#quickview-modal').modal('show');
        }).catch(function (error) {
            console.error('Ошибка запроса:', error);
        });
    }

    $('.quickview__close').on('click', function () {
        $('.mobile-product').slideToggle(700);
        $('body').css('overflow', 'auto');
        $('#quickview-modal').modal('hide');
    });
</script>
@endsection

