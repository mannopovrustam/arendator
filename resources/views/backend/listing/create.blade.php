@extends('layouts.app')


@section('style')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>

    <style>
        .input-group-text:hover {
            color: #fff;
            background: red;
            transition: all 0.3s;
        }
        /*
        .appointments {
            display: none;
            flex-wrap: wrap;
            opacity: 1;
        }
        .appointments > div {
            display: flex;
            flex: 1 1 30%;
        }
        */
        .checkbox-list-item{
            margin-left: 2px;
        }
        .checkbox-list-item:hover, .checkbox-list-item.active, .form-check-label:hover, .form-check-label.active{
            color: #000;
        }
        .object_category {
            display: none;
        }

        label {
            margin-top: 0 !important;
        }
        table.table td:first-child{
            text-align: right;
            width: 20%;
        }
        @media (max-width: 768px) {
            .appointments {
                flex-direction: column;
            }
        }
        #object_params >:not(caption)>*>* {
            /*border: 0 !important;*/
        }
    </style>

    <style>
        /* styles.css */
        #imageList {
            min-height: 100px;
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
        label.btn-outline-primary{
            margin-left: 3px;
        }
    </style>

@endsection
@section('script')
    <script src="{{ asset('assets/libs/jquery-ui-dist/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>


    <script>
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $(document).on('change', 'input[name="type_id"]', function () {
            $('.object_category').hide();
            $('.object_type_' + $(this).val()).show();
            if ($(this).val() != 3) {
                $('.properties').hide();
            } else $(".pty").html('')
        });
        $(document).on('change', 'input[name="category_id"]', function () {

            if(jQuery.inArray($(this).val(), ["7","8","9","10","12","36"]) !== -1){
                $('.properties').show();
                $('.pty').show();
                $.ajax({
                    url: '{{ asset('data/property') }}',
                    data: {obj: $(this).val()},
                    success: function (data) {
                        $(".pty").html(data)
                    }
                })
            } else {
                $('.properties').hide();
                $(".pty").html('')
                $('.pty').hide();
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
                    url: '{{ asset('backend/listings/object-params') }}',
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
    </script>

{{--    Kadastr bilan ishlash    --}}
    <script>
        function selectCadastr(cadastrs) {

            let list = '';
            cadastrs.forEach(v => {
                list += `
                    <div class="mb-1 d-flex align-items-center justify-content-between">
                        <input class="btn-check" type="radio" name="cadastr" id="selectCad${v.id}" value="${v.id}" data-cad="${v.cad_number}" data-payload='${JSON.stringify(v)}'>
                        <label class="btn btn-outline-secondary" style="text-align: left" for="selectCad${v.id}"><b>${v.cad_number}</b> <br><small><i>${v.address}</i></small></label>
                    </div>
                `;
            });

            $.confirm({
                title: 'Kadastr tanlang',
                content: `<div id="cadastrForm">${list}</div>`,
                onContentReady: function () {
                    let jc = this; // confirm instance
                    $('#cadastrForm').on('change', 'input[name="cadastr"]', function () {
                        let el = $(this);

                        let selected = el.data('cad');
                        let payload  = el.attr('data-payload');
                        payload = JSON.parse(payload);

                        renderSelectedCadastr(selected, payload);
                        jc.close(); // ✅ CONFIRM OYNANI YOPISH
                        // agar modalni avtomatik yopmoqchi bo‘lsangiz:
                        // this.close();

                        // change #region_id and #district_id based on payload
                        $('#region_id').val(payload.id_region).trigger('change');
                        // wait for district_id to be populated
                        setTimeout(function() {
                            $('#district_id').val(payload.id_district);
                        }, 1000);

                        $('#address_text').html(payload.address);
                        $('#address').val(payload.address);
                    });
                },
                buttons: {
                    bekor: function () {}
                },
/*
                buttons: {
                    tanlash: {
                        text: 'Tanlash',
                        btnClass: 'btn-primary',
                        action: function () {
                            let selected = $('#cadastrForm input[name="cadastr"]:checked').data('cad');
                            let selected_payload = $('#cadastrForm input[name="cadastr"]:checked').data('payload');
                            if (!selected) {
                                $.alert('Kadastr tanlang!');
                                return false;
                            }
                            renderSelectedCadastr(selected,selected_payload);
                        }
                    },
                    bekor: function () {}
                }
*/
            });
        }

        function renderSelectedCadastr(cadastr, payload) {
            $('#cadastr-cell').html(`
        <div class="d-flex align-items-center gap-2">
            <b>${cadastr},</b> <i>${payload.address}</i>
            <abbr title="Almashtirish"><button class="btn btn-sm btn-warning" type="button" onclick="changeCadastr()" aria-label="Almashtirish"><i class="fas fa-retweet"></i></button></abbr>
            <abbr title="Tozalash"><button class="btn btn-sm btn-danger" type="button" onclick="clearCadastr()" aria-label="Tozalash"><i class="fa fa-trash"></i></button></abbr>
            <input type="hidden" name="cadastr" value="${payload.id}">
        </div>
    `);
        }

        function changeCadastr() {
            selectCadastr(<?php echo json_encode($cadastrs); ?>);
        }

        function clearCadastr() {
            var cadastrs = <?php echo json_encode($cadastrs); ?>;
            $('#cadastr-cell').html(`
        <button type="button" class="btn btn-primary" onclick="selectCadastr({{json_encode($cadastrs)}})">
            Kadastr tanlash
        </button>
    `);
            // clear #region_id and #district_id
            $('#region_id').val('').trigger('change');
            $('#district_id').val('');
        }
    </script>

    <script>
        var imageItems = [];
        function handleImageUpload(files) {
            if (files.length > 0) {
                $.each(files, function(index, file) {
                    // Validate file type
                    if (!file.type.match('image.*')) {
                        alert('Please upload an image file.');
                        return;
                    }

                    // Read the file and create a preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imageSrc = e.target.result;

                        $.ajax({
                            url: '/backend/listings/upload-image',
                            method: 'POST',
                            data: { image: imageSrc, _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                imageItems.push(response.imageUrl);
                                console.log(response.imageUrl);
                                var imageItem = '';
                                imageItem += '<div class="col image-item">';
                                imageItem += '<img src="'+response.imageUrl+'" alt="Uploaded Image">';
                                imageItem += '<button class="delete-btn" type="button" title="Delete Image">X</button>';
                                imageItem += '</div>';
                                $('#imageList').append(imageItem);
                                $('input[name="images"]').val(imageItems);
                            },
                            error: function(xhr, status, error) {
                                console.error('Image upload error:', error);
                            }
                        });

                        // $('input[name="images"]').val(imageItems);

                        // save image to the server using AJAX to /backend/listings/upload-image
                    };
                    reader.readAsDataURL(file);
                });
            }
        }


        // Handle dropdown image upload
        $('#imageUpload').on('change', function(event) {
            const files = event.target.files;
            handleImageUpload(files);
            $(this).val(''); // Reset the file input
        });
        // script.js
        // Handle drag-and-drop image upload
        const dropArea = $('#dropArea');
        const dragImageUpload = $('#dragImageUpload');

        // Allow clicking the drop area to trigger file input
        dropArea.on('click', function() {
            // dragImageUpload.click();
        });

        dragImageUpload.on('change', function(event) {
            const files = event.target.files;
            handleImageUpload(files);
            $(this).val(''); // Reset the file input
        });

        // Drag-and-drop events
        dropArea.on('dragover', function(event) {
            event.preventDefault();
            $(this).addClass('drag-over');
        });

        dropArea.on('dragleave', function(event) {
            event.preventDefault();
            $(this).removeClass('drag-over');
        });

        dropArea.on('drop', function(event) {
            event.preventDefault();
            $(this).removeClass('drag-over');
            const files = event.originalEvent.dataTransfer.files;
            handleImageUpload(files);
        });

        // Enable drag-and-drop sorting
        $('#imageList').sortable({
            placeholder: 'ui-sortable-placeholder',
            update: function(event, ui) {
                // Log the new order of images
                const imageOrder = $('#imageList .image-item img').map(function() {
                    return $(this).attr('src');
                }).get();
                console.log('New image order:', imageOrder);
            }
        }).disableSelection();

        function pasteImageToList(imageSrc) {
            $.ajax({
                url: '/backend/listings/upload-image',
                method: 'POST',
                data: { image: imageSrc, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    imageItems.push(response.imageUrl);
                    console.log(response.imageUrl);
                    var imageItem = '';
                    imageItem += '<div class="col image-item">';
                    imageItem += '<img src="'+response.imageUrl+'" alt="Uploaded Image">';
                    imageItem += '<button class="delete-btn" type="button" title="Delete Image">X</button>';
                    imageItem += '</div>';
                    $('#imageList').append(imageItem);
                    $('input[name="images"]').val(imageItems);
                },
                error: function(xhr, status, error) {
                    console.error('Image upload error:', error);
                }
            });

            // $('input[name="images"]').val(imageItems);
        }

        // Handle image deletion
        $('#imageList').on('click', '.delete-btn', function() {
            var imageSrc = $(this).prev('img').attr('src');
            $.ajax({
                url: '/backend/listings/delete-image',
                method: 'POST',
                data: { image: imageSrc, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    console.log('Image deleted:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Image deletion error:', error);
                }
            });
            // remove from imageItems
            imageItems = imageItems.filter(function(item) {
                console.log(item, imageSrc);
                return item !== imageSrc;
            });

            $(this).closest('.image-item').remove();

            console.log('Image items:', imageItems);
        });

        $(document).on('paste', function(event) {
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
                        reader.onload = function(e) {
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
                        img.onload = function() {
                            pasteImageToList(imgElement.src);
                        };
                        img.onerror = function() {
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
                            img.onload = function() {
                                pasteImageToList(textData);
                            };
                            img.onerror = function() {
                                alert('Failed to load the pasted image URL. Please try another method.');
                            };
                            img.src = textData;
                        }
                    }
                }
            }

            // Step 4: If no image is found, show an alert
            if (!imageFound) {
                // alert('No image found in the clipboard. Please copy an image or image URL and try again.');
            }
        });

    </script>

    <!-- Yandex Maps API (Replace YOUR_API_KEY with your actual API key) -->
    <script src="https://api-maps.yandex.ru/2.1/?apikey=b5335094-c96c-42e3-b856-499ff1aaf16a&lang=ru_RU" type="text/javascript"></script>
    <script>
        // Initialize Yandex Map
        let myMap;
        let placemark;

        ymaps.ready(function() {
            // Create the map
            myMap = new ymaps.Map('map', {
                center: [41.295378589581,69.2477123573415], // Default center (Moscow)
                zoom: 10,
                controls: ['zoomControl', 'geolocationControl']
            });

            // Handle map click to set a placemark and get the address
            myMap.events.add('click', function(e) {
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
                    ymaps.geocode(coords, { kind: 'house' }).then(function(res) {
                        const firstGeoObject = res.geoObjects.get(0);
                        console.log(firstGeoObject);
                        if (firstGeoObject) {
                            const address = firstGeoObject.getAddressLine();
                            @if(count($cadastrs) == 0)
                            $('#address_text').html(address); // Set the address in the input field
                            $('#address').val(address); // Set the address in the input field
                            @endif
                            $('#geolocation').val(coords.join(',')); // Set the coordinates in the input field
                        } else {
                            @if(count($cadastrs) == 0)
                            $('#address_text').html('Address not found');
                            $('#address').val('Address not found');
                            @endif
                        }
                    }).catch(function(err) {
                        console.error('Geocoding error:', err);
                        $('#address').val('Error retrieving address');
                    });
            });
        });
    </script>

@endsection
@section('content')

    <div class="container-fluid">
        <form action="/{{ \Request::segment(1) }}/{{ \Request::segment(2) }}" method="post"
              enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card" id="user">
                        <div class="card-header d-flex justify-content-between">
                            <h3>Yangi obyekt qo'shish</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <!-- Tab panes -->
                    <div class="tab py-3 text-muted">
                        <div>
                            <div class="card" id="user">
                                <div class="card-body">
                                    <input type="hidden" name="data_id" value="">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table">
                                                <tr>
                                                    <td><label class="form-label" for="name">Obyekt turi</label></td>
                                                    <td>
                                                        <div class="form-group">
                                                            @foreach(\DB::table('object_types')->get() as $obt)
                                                                <input type="radio" class="btn-check" name="type_id" id="object_type_{{ $obt->id }}" autocomplete="off" value="{{ $obt->id }}">
                                                                <label class="btn btn-outline-primary" for="object_type_{{ $obt->id }}">{{ $obt->name }}</label>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><label class="form-label" for="name">Obyekt kategoriyasi</label></td>
                                                    <td>
                                                        <div class="form-group">
                                                            @foreach(\DB::table('object_category')->get() as $cat)
                                                                <input type="radio" class="btn-check"
                                                                       name="category_id"
                                                                       id="object_category_{{ $cat->id }}"
                                                                       autocomplete="off" value="{{ $cat->id }}">
                                                                <label
                                                                    class="btn btn-outline-info mb-1 object_category object_type_{{$cat->id_object_type}}"
                                                                    for="object_category_{{ $cat->id }}"
                                                                    style="display:none">{{ $cat->name }}</label>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="properties" style="display:none">
                                                    <td><label class="form-label" for="name">Ob'ekt</label></td>
                                                    <td>
                                                        <div class="col-md-12 pty" style="display:none"></div>
                                                    </td>
                                                </tr>
                                                @if(auth()->user()->one_id)
                                                    <tr id="cadastr-row">
                                                        <td>
                                                            <label class="form-label">Kadastr</label>
                                                        </td>
                                                        <td id="cadastr-cell">
                                                            <button type="button" class="btn btn-primary" onclick="selectCadastr({{ json_encode($cadastrs) }})">
                                                                Kadastr tanlash
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td>
                                                        <label class="form-label" for="region_id">Viloyat</label>
                                                    </td>
                                                    <td>
                                                        <select class="form-select" name="region_id" id="region_id" required="">
                                                            <option value="">*** Viloyat tanlang ***</option>
                                                            @foreach(\DB::table('regions')->get() as $region)
                                                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="form-label" for="district_id">Tuman</label>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-select" name="district_id" id="district_id">
                                                                <option value="">*** Tuman tanlang ***</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="form-label" for="photo">Manzil</label>
                                                    </td>
                                                    <td>
{{--                                                        <p id="address_text"></p>--}}
                                                        <input type="text" class="form-control" id="address"
                                                               value=""
                                                               name="address" placeholder="Manzil">
                                                        <br>
                                                        <input type="hidden" class="form-control" id="geolocation"
                                                               value=""
                                                               name="geolocation" placeholder="Локация">
                                                        <br>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <div id="map" style="width: 100%; height: 400px;"></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="card">
                                <div class="card-body" id="params">
                                    <section id="object_params">
                                        <table class="table"></table>
                                    </section>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <td>Taklif turi</td>
                                            <td class="d-flex justify-content-between">
                                                <div class="d-flex">
                                                    <div>
                                                        <input type="checkbox" class="btn-check" id="rent" name="price_type[]" value="rent" autocomplete="off">
                                                        <label class="btn btn-outline-warning w-lg" for="rent">Ijara</label>
                                                    </div>
                                                    <div>
                                                        <input type="checkbox" class="btn-check" id="sell" name="price_type[]" value="sell" autocomplete="off">
                                                        <label class="btn btn-outline-warning w-lg" for="sell">Sotish</label>
                                                    </div>
                                                </div>
                                                <div class="d-flex">
                                                    @foreach(\DB::table('sp_currencies')->get() as $cur)
                                                    <div>
                                                        <input type="radio" class="btn-check" id="currency_{{ $cur->id }}" name="id_currency" value="{{ $cur->id }}" autocomplete="off">
                                                        <label class="btn btn-outline-info" for="currency_{{ $cur->id }}">{{ $cur->name }}</label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="rent_price" style="display:none">
                                            <td>Ijara narxi</td>
                                            <td class="d-flex justify-between">
                                                <div class="d-flex mr-3">
                                                    <div>
                                                        <input type="radio" class="btn-check" id="month_price" name="price[rent_type]" value="monthly" autocomplete="off">
                                                        <label class="btn btn-outline-warning w-sm" for="month_price">Oyiga</label>
                                                    </div>
                                                    <div class="ml-3">
                                                        <input type="radio" class="btn-check" id="day_price" name="price[rent_type]" value="daily" autocomplete="off">
                                                        <label class="btn btn-outline-warning w-sm" for="day_price">Kuniga</label>
                                                    </div>
                                                </div>

                                                <input type="text" class="form-control ml-3 only_digit" id="address" value="" name="price[rent]" placeholder="Цена">
                                            </td>
                                        </tr>
                                        <tr id="sell_price" style="display:none">
                                            <td>Narxi</td>
                                            <td class="d-flex justify-between">
                                                <div class="d-flex mr-3">
                                                    <div>
                                                        <input type="radio" class="btn-check" id="all_price" name="price[sell_type]" value="all" autocomplete="off">
                                                        <label class="btn btn-outline-warning w-sm" for="all_price">Hammasi</label>
                                                    </div>
                                                    <div class="ml-3">
                                                        <input type="radio" class="btn-check" id="square_price" name="price[sell_type]" value="square" autocomplete="off">
                                                        <label class="btn btn-outline-warning w-sm" for="square_price">m<sup>2</sup></label>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control ml-3 only_digit" id="address" value="" name="price[sell]" placeholder="Цена">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tavsif</td>
                                            <td>
                                                <textarea class="form-control" name="description" id="description" cols="20" rows="4"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Rasmlar</td>
                                            <td>
                                                <div id="dropArea" class="drop-area mb-3">
                                                    <label class="" for="dragImageUpload">Rasmlarni bu yerga sudrab tashlang yoki tanlash uchun bosing</label>
                                                    <input type="file" id="dragImageUpload" accept="image/*" multiple style="display: none;">
                                                </div>
                                                <input type="hidden" name="images">
                                                <!-- Image preview and sorting area -->
                                                <div id="imageList" class="row row-cols-1 row-cols-md-3 g-4">
                                                    <!-- Images will be dynamically added here -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="float:right"><i class="fa fa-save"></i> Saqlash
                    </button>

                </div>

            </div>
        </form>
@endsection
