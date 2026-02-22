@extends('layouts.site')

@section('styles')
    <style>
        .appointments {
            display: none;
            flex-wrap: wrap;
            opacity: 1;
        }

        .appointments > div {
            display: flex;
            flex: 1 1 30%;
        }

        @media (max-width: 768px) {
            .appointments {
                flex-direction: column;
            }
        }
    </style>
    <style>
        /* styles.css */
        #imageList {
            /*min-height: 100px;*/
        }

        .image-item {
            position: relative;
            cursor: move;
            border: 1px solid #ddd;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .image-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: darkred;
        }

        /* Placeholder style during drag */
        .ui-sortable-placeholder {
            border: 2px dashed #ccc;
            visibility: visible !important;
            background: #f0f0f0;
            height: 200px;
            margin: 10px;
        }

        /* Drag-and-Drop Area */
        .drop-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .drop-area p {
            margin: 0;
            font-size: 1.1rem;
            color: #6c757d;
        }

        .drop-area.drag-over {
            border-color: #007bff;
            background: #e9ecef;
            transform: scale(1.02);
        }

        /* Map Section */
        #map {
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        label.btn-outline-primary {
            margin-left: 3px;
        }
    </style>
@endsection

@section('content')
    <div class="site">

        {{--        send data to layouts.header--}}
        @include('layouts.header', ['pageTitle' => 'E\'lon qo\'shish', 'user' => auth()->user()])
        {{--        @include('layouts.header')--}}

        <div class="site__body">
            <div class="block-header block-header--has-breadcrumb block-header--has-title">
                <div class="block-split block-header__breadcrumb">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                                <div class="card">
                                    <div class="card-header"><h5>E'lon qo‘shish</h5></div>
                                    <div class="card-divider"></div>
                                    <div class="card-body card-body--padding--2">
                                        <form action="{{ asset('listings') }}" method="post" enctype="multipart/form-data"
                                              id="add_listing_form">
                                            @csrf

                                            <input type="hidden" name="add_hotel" value="1">
                                            <div class="row no-gutters">
                                                <div class="col-12">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-12">
                                                            <label for="address-first-name">Joylashtirish
                                                                vositasi</label>
                                                            <div class="form-group d-flex">
                                                                <select name="hotel_id" class="form-control"
                                                                        style="width: 100%;" id="hotel_id"></select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="address-first-name">Obyekt kategoriyasi</label>
                                                            <div class="form-group d-flex flex-wrap appointments">
                                                                @foreach(\DB::table('object_category')->where('id_object_type',4)->get() as $cat)
                                                                    <label
                                                                        class="payment-methods__item-header btn-secondary mt-2 mr-2 object_category object_type_{{$cat->id_object_type}}"
                                                                        for="object_cat_{{ $cat->id }}">
                                                                    <span
                                                                        class="payment-methods__item-radio input-radio">
                                                                        <span class="input-radio__body">
                                                                            <input class="input-radio__input"
                                                                                   name="category_id"
                                                                                   id="object_cat_{{ $cat->id }}"
                                                                                   value="{{ $cat->id }}" type="radio">
                                                                            <span class="input-radio__circle"></span>
                                                                        </span>
                                                                    </span>
                                                                        <span
                                                                            class="payment-methods__item-title">{{ $cat->name }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group properties" style="display:none">
                                                        <div><label class="form-label" for="main_pty">Obyekt</label>
                                                        </div>
                                                        <div>
                                                            @foreach([7,8,9,10,12,13,14,15,16,17,18,19,20,21,22,23,24,25,36] as $obj)
                                                                    <?php
                                                                    $property[$obj] = explode(',', \DB::table('object_category')->where('id', $obj)->first()->property);
                                                                    $object_pty[$obj] = \DB::table('object_pty')->whereIn('id', $property[$obj])->get();
                                                                    ?>
                                                                <div class="form-group pty pty_{{ $obj }}"
                                                                     style="display:none">
                                                                    <select class="form-control form-control-select2"
                                                                            name="main_pty" id="main_pty"
                                                                            style="width: 100%;">
                                                                        <option>*** Tanlash ***</option>
                                                                        @foreach($object_pty[$obj] as $o)
                                                                            <option
                                                                                value="{{ $o->id }}">{{ $o->name_uz }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="region_id">Viloyat</label>
                                                        <select id="region_id"
                                                                class="form-control form-control-select2">
                                                            <option value="">*** Viloyat tanlang ***</option>
                                                            @foreach(\DB::table('regions')->get() as $region)
                                                                <option
                                                                    value="{{ $region->id }}">{{ $region->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="district_id">Tuman</label>
                                                        <select name="district_id" id="district_id"
                                                                class="form-control form-control-select2">
                                                            <option value="">*** Tuman tanlang ***</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="address">Manzil</label>
                                                        <input type="text" class="form-control" id="address"
                                                               name="address" placeholder="Адрес">
                                                        <input type="hidden" class="form-control" id="geolocation"
                                                               value="41.297035695878684,69.26002616938129"
                                                               name="geolocation">
                                                    </div>
                                                    <div class="form-group">
                                                        <div id="map" style="width: 100%; height: 400px;"></div>
                                                    </div>

                                                    <div id="object_params">
                                                        <table class="table"></table>
                                                    </div>

                                                    <div class="form-group">
                                                        {{--                                                        <label>Kurs</label>--}}
                                                        <div class="d-flex justify-content-between">
                                                            {{--<div class="d-flex">
                                                                <label class="filter-list__item btn btn-secondary mr-2"><span
                                                                        class="input-check filter-list__input"><span
                                                                            class="input-check__body"><input class="input-check__input"
                                                                                                             id="rent" name="price_type[]" value="rent"
                                                                                                             type="checkbox">
                                                                    <span class="input-check__box"></span>
                                                                    <span class="input-check__icon"><svg width="9px" height="7px"><path
                                                                                d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z"></path></svg> </span></span></span><span
                                                                        class="filter-list__title">Ijara </span>
                                                                </label>
                                                                <label class="filter-list__item btn btn-secondary"><span
                                                                        class="input-check filter-list__input"><span
                                                                            class="input-check__body"><input class="input-check__input"
                                                                                                             id="sell" name="price_type[]" value="sell"
                                                                                                             type="checkbox">
                                                                    <span class="input-check__box"></span>
                                                                    <span class="input-check__icon"><svg width="9px" height="7px"><path
                                                                                d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z"></path></svg> </span></span></span><span
                                                                        class="filter-list__title">Sotuv </span>
                                                                </label>
                                                            </div>--}}
                                                            <input class="input-check__input" id="rent"
                                                                   name="price_type[]" value="rent" type="hidden">

                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="d-flex justify-between align-items-center mb-3">
                                                            <div>Ijara narxi</div>
                                                            <div class="d-flex mr-3 col-md-4">
                                                                <label
                                                                    class="payment-methods__item-header btn-secondary mr-2"
                                                                    for="month_price"><span
                                                                        class="payment-methods__item-radio input-radio"><span
                                                                            class="input-radio__body"><input
                                                                                class="input-radio__input"
                                                                                name="price[rent_type]"
                                                                                id="month_price" value="monthly"
                                                                                type="radio"> <span
                                                                                class="input-radio__circle"></span> </span></span><span
                                                                        class="payment-methods__item-title">oylik</span></label>
                                                                <label
                                                                    class="payment-methods__item-header btn-secondary mr-2"
                                                                    for="day_price"><span
                                                                        class="payment-methods__item-radio input-radio"><span
                                                                            class="input-radio__body"><input
                                                                                class="input-radio__input"
                                                                                name="price[rent_type]"
                                                                                id="day_price" value="daily"
                                                                                type="radio"> <span
                                                                                class="input-radio__circle"></span> </span></span><span
                                                                        class="payment-methods__item-title">kunlik</span></label>
                                                            </div>
                                                        </div>
                                                        <div id="rent_price"
                                                             style="display:flex; justify-content: space-between;flex-wrap: wrap;align-items: center;">
                                                            <input type="text" class="form-control only_digit col-md-7"
                                                                   id="price" value="" name="price[rent]"
                                                                   placeholder="Narxi" autocomplete="off">
                                                            <div class="d-flex">
                                                                @foreach(\DB::table('sp_currencies')->get() as $cur)
                                                                    <label
                                                                        class="payment-methods__item-header btn-secondary mr-2 mt-2 mb-2"
                                                                        for="currency_{{ $cur->id }}"><span
                                                                            class="payment-methods__item-radio input-radio"><span
                                                                                class="input-radio__body"><input
                                                                                    class="input-radio__input"
                                                                                    name="id_currency"
                                                                                    id="currency_{{ $cur->id }}"
                                                                                    value="{{ $cur->id }}" required
                                                                                    type="radio"> <span
                                                                                    class="input-radio__circle"></span> </span></span><span
                                                                            class="payment-methods__item-title">{{ $cur->name }}</span></label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div id="sell_price" style="display:none; align-items:center">
                                                            <div>Narxi</div>
                                                            <div class="d-flex justify-between row">

                                                                <div class="d-flex mr-3 col-md-4">
                                                                    <label
                                                                        class="payment-methods__item-header btn-secondary mr-2"
                                                                        for="all_price"><span
                                                                            class="payment-methods__item-radio input-radio"><span
                                                                                class="input-radio__body"><input
                                                                                    class="input-radio__input"
                                                                                    name="price[sell_type]"
                                                                                    id="all_price" value="all"
                                                                                    type="radio"> <span
                                                                                    class="input-radio__circle"></span> </span></span><span
                                                                            class="payment-methods__item-title">hammasiga</span></label>
                                                                    <label
                                                                        class="payment-methods__item-header btn-secondary mr-2"
                                                                        for="square_price"><span
                                                                            class="payment-methods__item-radio input-radio"><span
                                                                                class="input-radio__body"><input
                                                                                    class="input-radio__input"
                                                                                    name="price[sell_type]"
                                                                                    id="square_price" value="square"
                                                                                    type="radio"> <span
                                                                                    class="input-radio__circle"></span> </span></span><span
                                                                            class="payment-methods__item-title">m<sup>2</sup></span></label>
                                                                </div>
                                                                <input type="text"
                                                                       class="form-control ml-3 only_digit col-md-7"
                                                                       id="address" value="" name="price[sell]"
                                                                       placeholder="Цена" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label for="description">Izoh</label>
                                                        <textarea
                                                            id="description" class="form-control" rows="4"></textarea>
                                                    </div>


                                                    <div class="form-group">
                                                        <div>Rasm</div>
                                                        <div>
                                                            <div id="dropArea" class="drop-area mb-3">
                                                                <label class="" for="dragImageUpload">Rasmlarni bu yerga
                                                                    sudrab olib tashlang, buferni joylashtiring yoki
                                                                    tanlash uchun bosing</label>
                                                                <input type="file" id="dragImageUpload" accept="image/*"
                                                                       multiple style="display: none;">
                                                            </div>
                                                            <input type="hidden" name="images">
                                                            <!-- Image preview and sorting area -->
                                                            <div id="imageList"
                                                                 class="row row-cols-1 row-cols-md-3 g-4">
                                                                <!-- Images will be dynamically added here -->
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary">Saqlash</button>
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

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/vendor/owl-carousel/owl.carousel.min.js"></script>
    <script src="/vendor/select2/js/select2.min.js"></script>
    <script src="/js/number.js"></script>
    <script src="/js/main.js"></script>
    <script src="/js/inputmask/jquery.inputmask.min.js"></script>
    <script src="/js/inputmask/inputmask.min.js"></script>
    <script src="/js/mask.init.js"></script>

    <script>

        $("#hotel_id").select2({
            tags: true,
            multiple: false,
            tokenSeparators: [',', ' '],
            minimumInputLength: 3,
            minimumResultsForSearch: 10,
            ajax: {
                url: '/data/search-hotels',
                dataType: "json",
                type: "GET",
                data: function (params) {
                    return {term: params.term};
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.name,
                                id_region: item.id_region,
                                id_district: item.id_district,
                                address: item.address,
                                geo: item.geo,
                                hotel_type_id: item.hotel_type_id
                            };
                        })
                    };
                }
            }
        });

        // Hotel tanlanganida viloyat, tuman, manzil va geolokatsiyani to'ldirish
        $("#hotel_id").on('select2:select', function (e) {
            var data = e.params.data;

            // Viloyatni o'rnatish
            if (data.id_region) {
                $('#region_id').val(data.id_region).trigger('change');
            }

            // Tumanni o'rnatish
            if (data.id_district) {
                var regionId = data.id_region;
                var districtId = data.id_district;
                if (regionId) {
                    $.ajax({
                        url: '{{ asset('data/districts') }}',
                        data: {region_id: regionId},
                        success: function (html) {
                            $('#district_id').html(html);
                            $('#district_id').val(districtId).trigger('change');
                        }
                    });
                } else {
                    $('#district_id').val(districtId).trigger('change');
                }
            }

            // Manzilni o'rnatish
            if (data.address) {
                $('#address').val(data.address);
            }

            // Geolokatsiyani o'rnatish va xaritada markerni yangilash
            if (data.geo) {
                $('#geolocation').val(data.geo);
                var coords = data.geo.split(',').map(Number);
                if (coords.length === 2 && myMap) {
                    if (placemark) {
                        myMap.geoObjects.remove(placemark);
                    }
                    placemark = new ymaps.Placemark(coords, {}, {
                        preset: 'islands#redDotIcon'
                    });
                    myMap.geoObjects.add(placemark);
                    myMap.setCenter(coords, 15);
                }
            }

            var hotelTypeToCategory = {
                27: 13,
                1: 15,
                29: 16,
                7: 17,
                8: 18,
                35: 19,
                10: 20,
                18: 21,
                6: 22,
                33: 23,
                22: 24,
                28: 25
            };

            if (data.hotel_type_id && hotelTypeToCategory[data.hotel_type_id]) {
                var categoryId = hotelTypeToCategory[data.hotel_type_id];
                // Show the appointments section
                $('.appointments').css({display: 'flex'});
                // Select the corresponding radio button
                $('#object_cat_' + categoryId).prop('checked', true).trigger('change');
            }

        });
    </script>

    <script>

        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $(document).on('change', 'input[name="type_id"]', function () {
            $('.object_category').hide();
            $('.object_type_' + $(this).val()).show();
            // if $(this).val() in array 3,4
            if (jQuery.inArray($(this).val(), ["3", "4"]) !== -1) {
                $('.properties').hide();
            }
        });

        $(document).on('change', 'input[name="category_id"]', function () {
            if (jQuery.inArray($(this).val(), ["7", "8", "9", "10", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "36"]) !== -1) {
                $('.properties').show();
            } else {
                $('.properties').hide();
            }
            $('.pty').hide();
            $('.pty_' + $(this).val()).css({display: 'block'});
            if ($(this).val() == 12) {
                $('.appointments').css({display: 'flex'});
            }
        });

        $(document).on('change', 'input[name="objs_properties[]"]', function () {
            if ($(this).is(':checked')) {
                $('label[for="objs_properties_' + $(this).val() + '"]').addClass('active');
            } else {
                $('label[for="objs_properties_' + $(this).val() + '"]').removeClass('active');
            }
        });
        $('#region_id').on('change', function () {
            $.ajax({
                url: '{{ asset('data/districts') }}',
                data: {region_id: $('#region_id').val()},
                success: function (data) {
                    $("#district_id").html(data)
                }
            })
        })


        $('.only_digit_int').on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $('.only_digit').on('input', function () {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });

        $(document).on('change', 'input[name="type_id"]', function () {
            $('input[name="category_id"]').prop('checked', false);
            $('#object_params').html('');
        });


        $(document).on('change', 'input[name="type_id"], input[name="category_id"]', function () {
            let type_id = $('input[name="type_id"]:checked').val();
            let category_id = $('input[name="category_id"]:checked').val();
            // unchecked all checkboxes, empty all inputs
            $('#params input[type="checkbox"]').prop('checked', false);
            $('#params input[type="text"]').val('');


            if (type_id && category_id) {
                $.ajax({
                    url: '{{ asset('listings/object-params') }}',
                    data: {type_id: type_id, category_id: category_id},
                    success: function (data) {
                        $('#object_params').html(data);
                    }
                });
            }
        });
        $(document).on('change', '#rent,#sell', function () {
            if ($('#rent').is(':checked')) {
                $('#rent_price').show();
            } else {
                $('#rent_price').hide();
            }
            if ($('#sell').is(':checked')) {
                $('#sell_price').show();
            } else {
                $('#sell_price').hide();
            }
        });

        // change payment-methods__item-header btn-secondary to btn-primary
        $('.payment-methods__item-header').click(function () {
            $('.payment-methods__item-header').removeClass('btn-primary').addClass('btn-secondary');
            $(this).removeClass('btn-secondary').addClass('btn-primary');
        });

    </script>
    <script>
        var imageItems = [];

        function handleImageUpload(files) {
            if (files.length > 0) {
                $.each(files, function (index, file) {
                    // Validate file type
                    if (!file.type.match('image.*')) {
                        alert('Please upload an image file.');
                        return;
                    }

                    // Read the file and create a preview
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imageSrc = e.target.result;

                        $.ajax({
                            url: '/listings/upload-image',
                            method: 'POST',
                            data: {image: imageSrc, _token: '{{ csrf_token() }}'},
                            success: function (response) {
                                imageItems.push(response.imageUrl);
                                console.log(response.imageUrl);
                                var imageItem = '';
                                imageItem += '<div class="col image-item">';
                                imageItem += '<img src="' + response.imageUrl + '" alt="Uploaded Image">';
                                imageItem += '<button class="delete-btn" type="button" title="Delete Image">X</button>';
                                imageItem += '</div>';
                                $('#imageList').append(imageItem);
                                $('input[name="images"]').val(imageItems);
                            },
                            error: function (xhr, status, error) {
                                console.error('Image upload error:', error);
                            }
                        });

                        // $('input[name="images"]').val(imageItems);

                        // save image to the server using AJAX to /listings/upload-image
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        // Handle dropdown image upload
        $('#imageUpload').on('change', function (event) {
            const files = event.target.files;
            handleImageUpload(files);
            $(this).val(''); // Reset the file input
        });
        // script.js
        // Handle drag-and-drop image upload
        const dropArea = $('#dropArea');
        const dragImageUpload = $('#dragImageUpload');

        // Allow clicking the drop area to trigger file input
        dropArea.on('click', function () {
            // dragImageUpload.click();
        });

        dragImageUpload.on('change', function (event) {
            const files = event.target.files;
            handleImageUpload(files);
            $(this).val(''); // Reset the file input
        });

        // Drag-and-drop events
        dropArea.on('dragover', function (event) {
            event.preventDefault();
            $(this).addClass('drag-over');
        });

        dropArea.on('dragleave', function (event) {
            event.preventDefault();
            $(this).removeClass('drag-over');
        });

        dropArea.on('drop', function (event) {
            event.preventDefault();
            $(this).removeClass('drag-over');
            const files = event.originalEvent.dataTransfer.files;
            handleImageUpload(files);
        });

        // Enable drag-and-drop sorting
        $('#imageList').sortable({
            placeholder: 'ui-sortable-placeholder',
            update: function (event, ui) {
                // Log the new order of images
                const imageOrder = $('#imageList .image-item img').map(function () {
                    return $(this).attr('src');
                }).get();
                console.log('New image order:', imageOrder);
            }
        }).disableSelection();

        function pasteImageToList(imageSrc) {
            $.ajax({
                url: '/listings/upload-image',
                method: 'POST',
                data: {image: imageSrc, _token: '{{ csrf_token() }}'},
                success: function (response) {
                    imageItems.push(response.imageUrl);
                    console.log(response.imageUrl);
                    var imageItem = '';
                    imageItem += '<div class="col image-item">';
                    imageItem += '<img src="' + response.imageUrl + '" alt="Uploaded Image">';
                    imageItem += '<button class="delete-btn" type="button" title="Delete Image">X</button>';
                    imageItem += '</div>';
                    $('#imageList').append(imageItem);
                    $('input[name="images"]').val(imageItems);
                },
                error: function (xhr, status, error) {
                    console.error('Image upload error:', error);
                }
            });

            // $('input[name="images"]').val(imageItems);
        }

        // Handle image deletion
        $('#imageList').on('click', '.delete-btn', function () {
            var imageSrc = $(this).prev('img').attr('src');
            $.ajax({
                url: '/listings/delete-image',
                method: 'POST',
                data: {image: imageSrc, _token: '{{ csrf_token() }}'},
                success: function (response) {
                    console.log('Image deleted:', response);
                },
                error: function (xhr, status, error) {
                    console.error('Image deletion error:', error);
                }
            });
            // remove from imageItems
            imageItems = imageItems.filter(function (item) {
                console.log(item, imageSrc);
                return item !== imageSrc;
            });

            $(this).closest('.image-item').remove();

            console.log('Image items:', imageItems);
        });

        $(document).on('paste', function (event) {
            const clipboardData = (event.originalEvent || event).clipboardData;
            if (!clipboardData) {
                alert('Unable to access clipboard data.');
                return;
            }

            // Step 1: Check for raw image files in the clipboard
            const items = clipboardData.items;
            let imageFound = false;

            if (items && items.length > 0) {
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    if (item.type.indexOf('image') === 0) {
                        imageFound = true;
                        const blob = item.getAsFile();
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            pasteImageToList(e.target.result);
                        };
                        reader.readAsDataURL(blob);
                    }
                }
            }

            // Step 2: If no raw image is found, check for text or HTML data
            if (!imageFound) {
                // Check for HTML content (e.g., <img src="...">)
                const htmlData = clipboardData.getData('text/html');
                if (htmlData) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(htmlData, 'text/html');
                    const imgElement = doc.querySelector('img');
                    if (imgElement && imgElement.src) {
                        imageFound = true;
                        // Validate the image URL
                        const img = new Image();
                        img.onload = function () {
                            pasteImageToList(imgElement.src);
                        };
                        img.onerror = function () {
                            alert('Failed to load the pasted image. Please try another method.');
                        };
                        img.src = imgElement.src;
                    }
                }

                // Step 3: If no HTML content, check for plain text (e.g., a URL)
                if (!imageFound) {
                    const textData = clipboardData.getData('text/plain');
                    if (textData) {
                        // Check if the text is a valid image URL
                        const urlPattern = /^(https?:\/\/.*\.(?:png|jpg|jpeg|gif|webp))/i;
                        if (urlPattern.test(textData)) {
                            imageFound = true;
                            const img = new Image();
                            img.onload = function () {
                                pasteImageToList(textData);
                            };
                            img.onerror = function () {
                                alert('Failed to load the pasted image URL. Please try another method.');
                            };
                            img.src = textData;
                        }
                    }
                }
            }

            // Step 4: If no image is found, show an alert
            if (!imageFound) {
                alert('No image found in the clipboard. Please copy an image or image URL and try again.');
            }
        });

    </script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=b5335094-c96c-42e3-b856-499ff1aaf16a&lang=ru_RU"
            type="text/javascript"></script>
    <script>
        // Initialize Yandex Map
        let myMap;
        let placemark;

        ymaps.ready(function () {
            // Create the map
            myMap = new ymaps.Map('map', {
                center: [41.295378589581, 69.2477123573415], // Default center (Moscow)
                zoom: 10,
                controls: ['zoomControl', 'geolocationControl']
            });

            // Handle map click to set a placemark and get the address
            myMap.events.add('click', function (e) {
                const coords = e.get('coords'); // Get the coordinates of the clicked point

                // Remove the previous placemark if it exists
                if (placemark) {
                    myMap.geoObjects.remove(placemark);
                }
                // Add a new placemark at the clicked location
                placemark = new ymaps.Placemark(coords, {}, {
                    preset: 'islands#redDotIcon'
                });
                myMap.geoObjects.add(placemark);

                // Use reverse geocoding to get the address
                ymaps.geocode(coords, {kind: 'house'}).then(function (res) {
                    const firstGeoObject = res.geoObjects.get(0);
                    console.log(firstGeoObject);
                    if (firstGeoObject) {
                        const address = firstGeoObject.getAddressLine();
                        $('#address').val(address); // Set the address in the input field
                        $('#geolocation').val(coords.join(',')); // Set the coordinates in the input field
                    } else {
                        $('#address').val('Address not found');
                    }
                }).catch(function (err) {
                    console.error('Geocoding error:', err);
                    $('#address').val('Error retrieving address');
                });
            });
        });
    </script>
@endsection
