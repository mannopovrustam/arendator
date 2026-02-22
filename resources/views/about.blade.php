@extends('layouts.site')

@section('content')
<div class="site">

    @include('layouts.header')

    <div class="site__body">
        <div class="block-space block-space--layout--spaceship-ledge-height"></div>
        <div class="block">
            <div class="container">
                <div class="document">
                    <div class="document__header"><h1 class="document__title">About Us</h1>
                        <div class="document__subtitle">This Agreement was last modified on 27 May 2018.</div>
                    </div>
                    <div class="document__content card">
                        <div class="typography"><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec
                                facilisis neque ut purus fermentum, ac pretium nibh facilisis. Vivamus venenatis viverra
                                iaculis. Suspendisse tempor orci non sapien ullamcorper dapibus. Suspendisse at velit
                                diam. Donec pharetra nec enim blandit vulputate. Suspendisse potenti. Pellentesque et
                                molestie ante. In feugiat ante vitae ultricies malesuada.</p>
                            <h2>Definitions</h2>
                            <ol>
                                <li><strong>Risus</strong> — Morbi posuere eleifend sollicitudin. Praesent eget ante in
                                    enim scelerisque scelerisque. Donec mi lorem, molestie a sapien non, laoreet
                                    convallis felis. In semper felis in lacus venenatis, sit amet commodo leo interdum.
                                    Maecenas congue ut leo et auctor.
                                </li>
                                <li><strong>Praesent</strong> — Class aptent taciti sociosqu ad litora torquent per
                                    conubia nostra, per inceptos himenaeos. Nulla orci ante, viverra in imperdiet in,
                                    pharetra et leo
                                </li>
                                <li><strong>Vestibulum</strong> — Vestibulum arcu tellus, aliquam vel fermentum
                                    vestibulum, lacinia pulvinar ipsum. In hac habitasse platea dictumst. Integer felis
                                    libero, blandit scelerisque mauris eget, porta elementum sapien. Mauris luctus arcu
                                    non enim lobortis gravida.
                                </li>
                            </ol>
                            <h2>Ornare dolor</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec facilisis neque ut purus
                                fermentum, ac pretium nibh facilisis. Vivamus venenatis viverra iaculis. Suspendisse
                                tempor orci non sapien ullamcorper dapibus. Suspendisse at velit diam. Donec pharetra
                                nec enim blandit vulputate. Suspendisse potenti. Pellentesque et molestie ante. In
                                feugiat ante vitae ultricies malesuada.</p>
                            <p>For information about how to contact us, please visit our <a href="contact-us.html">contact
                                    page</a>.</p>
                            <div class="document__signature"><img src="images/signature.jpg" width="160" height="55"
                                                                  alt=""></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="block-space block-space--layout--before-footer"></div>
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
        if ($(this).val() == 3) {
            $('.properties').hide();
        }
    });

    $(document).on('change', 'input[name="category_id"]', function () {
        if(jQuery.inArray($(this).val(), ["7","8","9","10","12","36"]) !== -1){
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
        $.ajax({
            url: '/listings/data',
            type: 'get',
            success: function (response) {
                $('.product-wrap').html(response);
            }
        });
    });
    // onscroll page load more content to .product-wrap from /listings/data
    $(window).scroll(function () {
        // when scrolled 80% of the page
        if ($(window).scrollTop() + $(window).height() > $(document).height() * 0.9) {
            $.ajax({
                url: '/listings/data',
                type: 'get',
                success: function (response) {
                    $('.product-wrap').append(response);
                }
            });
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
            alert('No image found in the clipboard. Please copy an image or image URL and try again.');
        }
    });

</script>
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
                    $('#address').val(address); // Set the address in the input field
                    $('#geolocation').val(coords.join(',')); // Set the coordinates in the input field
                } else {
                    $('#address').val('Address not found');
                }
            }).catch(function(err) {
                console.error('Geocoding error:', err);
                $('#address').val('Error retrieving address');
            });
        });
    });
</script>
@endsection
