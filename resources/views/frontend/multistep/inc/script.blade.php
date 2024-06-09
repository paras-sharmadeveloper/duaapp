<script>
    // $(".form-business").hide();
    var imagePath = "{{ env('AWS_GENERAL_PATH') . 'images/' }}";
    var NoImage = "{{ asset('assets/theme/img/avatar.png') }}";

    $(".language-selection").click(function() {
        var link = $(this).attr('data-lang');

        location.href = link
    })


    $("#startBooking").click(function() {
        var html = '';

        var loadingText = $(this).attr('data-loading');
        var successText = $(this).attr('data-success');
        var defaultText = $(this).attr('data-default');

        $(this).find('span').show()
        $(this).find('b').text(loadingText)
        $.ajax({
            url: "{{ route('booking.get.users') }}", // Get the form's action URL
            type: 'Post', // Get the form's HTTP method (POST in this case)
            // data: formData, // Use the serialized form data
            success: function(response) {
                $("#slot-information-user").find('label').text("Your Current Timezone:" + response
                    .currentTimezone);
                $("#timezone-hidden").val(response.currentTimezone)
                if (response.status) {
                    $(this).find('span').hide()
                    $(this).find('b').text(defaultText)
                    var phoneCode = response.phoneCode;
                    console.log("country_code", phoneCode)
                    $("#country_code").attr("data-ud", phoneCode)
                    $("#country_code").val(phoneCode).trigger('change');
                    $.each(response.data, function(key, item) {
                        var img = '';

                        if (item.profile_pic) {
                            var fullImg = imagePath + item.profile_pic;
                            img = `<img src="${fullImg}">`;
                        } else {
                            img = '<img src="/assets/theme/img/avatar.png">';
                        }
                        html += `<div class="col-xs-6 col-sm-4 col-md-4 col-lg-3 col">
                                <div class="card text-center h-60  shadow-sm  city-selection" data-id="${item.id}">

                                    <div class="card-body px-0 p-2">
                                        <h5 class="card-title title-binding"><strong>${item.name}</strong></h5>
                                        <p class="card-text">
                                    </p></div>
                                </div></div>`;

                    });


                } else {
                    html =
                        `<div class="col-lg-12 text-center"><p class="no-data"> No Venue Created yet </p> </div>`;
                }
                $("#booknowStart").fadeOut();
                $("#cardSection").fadeIn(500)
                $("#wizardRow").fadeIn(500)

                $("#thripist-main").html(html)



            },
            error: function(error, xhr) {


            }
        });



    })

    $("#timezone").select2({
        placeholder: "Your Preferred Timezone",
        allowClear: true
    });
    $("#country_code").select2({
        placeholder: "Select country",
        allowClear: true
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(document).ready(function() {
        var currentUrl = window.location.href;
        $(".next").on({
            click: function() {
                $this = $(this);
                var getValue = $(this).parents(".row").find(".card").hasClass("active-card");
                $activeCard = $(this).parents(".row").find(".active-card");
                if (getValue) {
                    var oldTitle = $("#remeber-steps-app").val();

                    var title = $activeCard.find(".title-binding").text();
                    var cardId = $activeCard.attr("data-id");
                    var CityName = $activeCard.attr("data-city");
                    var event = $activeCard;


                    if (event.hasClass('dua-section')) {
                        $("#dua_type").val(cardId);
                        var selectionType = $("#dua_type").attr('data-type');
                        var city = $("#citySelection").val();
                        var duaType = event.attr('data-type');

                        if (selectionType == 'working_lady' && duaType == 'dua') {
                            duaType = 'working_lady_dua';
                            $("#dua_type").val('working_lady_dua')
                        } else if (selectionType == 'working_lady' && duaType == 'dum') {
                            duaType = 'working_lady_dum';

                            $("#dua_type").val('working_lady_dum')
                        } else {
                            $("#dua_type").val(duaType)
                        }

                        // getAjax(cardId, 'get_slot_book', $this, optional = '', cardId)
                        // getAjax(cardId, 'get_slot_book', $this)
                        getAjax(cardId, 'get_slot_book', $this, city, duaType)
                    } else if (event.hasClass('working_lady')) {
                        var cardType = $activeCard.attr("data-id");
                        $("#selection_type").val(cardType)
                        $("#dua_type").attr('data-type', cardType)
                        getAjax(cardId, cardType, $this)

                    } else if (event.hasClass('normal_person')) {
                        var cardType = $activeCard.attr("data-id");
                        $("#selection_type").val(cardType)
                        getAjax(cardId, cardType, $this)

                    } else if (event.hasClass('city-selection')) {

                        var duaType = $("#dua_type").val();
                        $("#citySelection").val(event.attr('data-city'))
                        $(".dua-section").attr('data-id', cardId)
                        $(".dua-section").attr('data-duaType', duaType)

                        getAjax(cardId, 'get_date', $this, CityName, duaType)

                    } else if (event.hasClass('qr-code')) {
                        var cardType = $activeCard.attr("data-id");
                        getAjax(cardId, cardType, $this, CityName, duaType)

                    }





                    if (oldTitle == '') {
                        oldTitle = title;
                    } else {
                        oldTitle += " > " + title;
                    }

                    $(this).parents(".row").find(".head>label").text(oldTitle);
                    $("#remeber-steps-app").val(oldTitle);

                    $(this).parents('.justify-content-center').find('.head>label').text(oldTitle);

                    $("#progress-bar").find(".active").next().addClass("active").prev().removeClass(
                        'active');

                    $(this).parents(".row").find(".alertBox").addClass("d-none")


                } else {


                    $("#loader").hide();
                    $(this).parents(".row").find(".alertBox").removeClass("d-none").text(
                        "Please select any card , only then you can move further!")

                    // $("alertBox").removeClass("d-none").find("div").text("Please select any card , only then you can move further!");
                }
            }
        });

        function updateQueryStringParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return uri + separator + key + "=" + value;
            }
        }
        // back button
        $(".back").on({
            click: function() {
                $(".next").show();
                $("#progress-bar").find(".active").removeClass('active').prev().addClass('active')

                var currentTitle = $('#remeber-steps-app').val();
                currentTitle = currentTitle.split(' > ');
                currentTitle.pop();

                var newString = currentTitle.join(' > ');
                $('#remeber-steps-app').val(newString)

                // $(this).next(".row").show();
                $(this).parents(".row").fadeOut("slow", function() {
                    $("#loader").hide();
                    $(this).prev(".row").fadeIn();
                    $(this).prev(".row").find(".head").find("label").text(newString)
                });
            }
        });
        //finish button
        $(document).on("click", ".card", function() {

            $(this).toggleClass("active-card");
            var cardId = $(this).attr('data-id');
            $(this).parent(".col").siblings().children(".card").removeClass("active-card");
        })
        //back to wizard
        $(".back-to-wizard").on({
            click: function() {
                location.reload(true);
            }
        });
    });


    function getAjax(id, type, nextBtn, optional = '', duaType = '') {

        var loadingText = nextBtn.attr('data-loading');
        var successText = nextBtn.attr('data-success');
        var defaultText = nextBtn.attr('data-default');

        nextBtn.find('span').show()
        nextBtn.find('b').text(loadingText)

        $.ajax({
            type: 'POST',
            url: "{{ route('booking.ajax') }}",
            data: {
                "id": id,
                "type": type,
                'optional': optional,
                "timezone": $("#timezone-hidden").val(),
                'duaType': duaType
            },
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {


                if (type == 'get_country') {
                    var country = '';
                    if (response.status) {

                        $.each(response.data.country, function(key, item) {
                            var meetingType = 'Online';
                            if (item.type == 'on-site') {
                                meetingType = item.name;
                            }
                            country += `<div class="col col-lg-3 col-md-7">
                                    <div class="card text-center h-60 py-2 shadow-sm city-selection" data-id="${item.venue_id}">
                                        <img src="${item.flag_path}" alt="Flag Image">
                                        <div class="card-body px-0">
                                            <h5 class="card-title title-binding">${meetingType}</h5>

                                        </div>
                                    </div>
                                </div>`;
                        })
                    } else {
                        country = '<p class="no-data"> No Data Found </p>';
                    }
                    $("#country-listing").html(country);
                    nextBtn.find('b').text(defaultText)

                }

                if (type == 'get_city' || type == 'normal_person' || type == 'working_lady') {

                    var city = '';
                    if (response.status) {
                        var i = 1;
                        var cityArray = Object.values(response.data.city);
                        cityArray.sort(function(a, b) {
                            return a.seq - b.seq;
                        });


                        $.each(cityArray, function(key, item) {

                            var meetingType = 'Online';
                            if (item.type == 'on-site') {
                                meetingType = translations[item.name] || item.name;
                            }


                            // <img src="${item.flag_path}" alt="Flag Image">
                            city += `<div data-sq="${item.seq}" class="col col-lg-3 col-md-7 country-enable-n country-enable-${item.id}">
                                    <div class="card text-center h-60 py-2 shadow-sm city-selection" data-id="${item.id}" data-city="${item.name}">

                                        <div class="card-body px-0">
                                            <h5 class="card-title title-binding">${meetingType}</h5>

                                        </div>
                                    </div>
                                </div>`;
                        })


                    } else {
                        if (response.message && lang == 'en') {
                            city = `<p class="no-data"> ${response.message}</p>`;
                        } else if (response.message_ur && lang == 'ur') {
                            city = `<p class="no-data"> ${response.message_ur}</p>`;
                        } else {
                            city = `<p class="no-data"> ${translations['no_dum_dua']}</p>`;
                        }

                    }
                    $("#city-listing-main").find(".head>h3").text('Select Dua Ghar')
                    $("#city-listing").html(city).fadeIn();

                    nextBtn.find('b').text(defaultText)


                }

                if (type == 'working_lady') {
                    // alert('working_lady')
                    if ($("#qr-listing").hasClass('form-business1')) {
                        $("#qr-listing").removeClass('form-business1').addClass('form-business');
                    }
                }


                if (type == 'normal_person') {
                    // alert('normak')
                    if ($("#qr-listing").hasClass('form-business')) {
                        $("#qr-listing").removeClass('form-business').addClass('form-business1');
                    }


                }

                // if (type == 'working_lady') {

                //     $("#city-listing-main").find(".head>h3").text('Choose your QR Id')
                //     $("#city-listing").fadeOut();
                //     $("#qr-code-listing").fadeIn();
                //     $("#city-listing-normal").fadeOut();
                // }



                if (type == 'get_slot_book') {
                    var dAte = '';






                    if (response.venueId) {

                        $("#cityname").val(response.city)
                        $("#duaType").val(response.duaType)
                        $("#timezoneNew").val(response.timezone)
                        $("#venueId").val(response.venueId)

                        $("#booking-form").show();
                        $("#submitBtn").show();
                        // $("#slot_id_booked").val(response.slot_id);
                        if (lang == 'en') {
                            $("#successForm").find(".alert").text(response.message).addClass('d-none')
                        } else {
                            $("#successForm").find(".alert").text(response.message_ur).addClass('d-none')
                        }

                    } else if (response.status == false) {
                        dAte = '<p class="no-data">' + response.message + '</p>';

                        $("#booking-form").hide();
                        $("#submitBtn").hide();
                        var redirect = response.refresh;
                        if (lang == 'en') {
                            $("#successForm").find(".alert").text(response.message).removeClass('d-none')
                        } else {
                            $("#successForm").find(".alert").text(response.message_ur).removeClass('d-none')
                        }
                        if (redirect) {
                            $("#submitBtn").prop("disabled", true);
                            // $("#submitBtn").hide();
                        }
                        // $("#successForm").find(".alert").text(response.message).removeClass('d-none')

                    } else if (response.status == false) {
                        $("#booking-form").hide();
                        $("#submitBtn").hide();
                        if (lang == 'en') {
                            $("#successForm").find(".alert").text(response.message).removeClass('d-none')
                        } else {
                            $("#successForm").find(".alert").text(response.message_ur).removeClass('d-none')
                        }
                        // $("#successForm").find(".alert").text(response.message).removeClass('d-none')


                        dAte = '<p class="no-data">' + response.message + '</p>';

                    }
                    $("#date-listing").html(dAte);
                }


                nextBtn.find('span').hide()
                var oldTitle = $("#remeber-steps-app").val();



                nextBtn.parents(".row").fadeOut("slow", function() {

                    if ($(this).next(".row").hasClass('form-business')) {
                        // alert("row class found");
                        $(this).next(".form-business").fadeIn();
                        $(this).next(".form-business").find('.head>label').text(oldTitle)
                    } else {
                        // alert("row not found skipping");

                        $(this).next(".row").next().fadeIn();

                        $(this).next(".row").find('.head>label').text(oldTitle)

                    }



                });


            }
        });


    }
</script>
<script>
    $(document).ready(function() {
        $("#myalert").addClass('d-none')
        // Add an event listener to the form's submit event
        $('#booking-form').submit(function(event) {
            $this = $("#submitBtn");
            var loadingText = $this.attr('data-loading');
            var successText = $this.attr('data-success');
            var defaultText = $this.attr('data-default');

            $this.find('span').show()
            $this.find('b').text(loadingText)
            event.preventDefault(); // Prevent the default form submission
            $("#loader").show();
            // Serialize the form data
            var formData = $(this).serialize();
            $('#modal-loading').modal('show');
            // Perform the AJAX request
            $.ajax({
                url: $(this).attr('action'), // Get the form's action URL
                type: $(this).attr('method'), // Get the form's HTTP method (POST in this case)
                data: formData, // Use the serialized form data
                success: function(response) {
                    $("#errors").html('');
                    $this.find('span').show()
                    $this.find('b').text(successText)
                    // stopCamera();
                    setTimeout(() => {
                        $this.find('b').text(defaultText)
                        $("#wizardRow").fadeOut(300);
                        $("#successForm").fadeOut(300);
                    }, 1000);
                    // 'thankyou-page
                    $('#modal-loading').modal('hide');

                    $("#loader").hide();
                    window.location.href = response.redirect_url;



                },
                error: function(xhr, textStatus , errorThrown) {



                    var errors = xhr.responseJSON.errors;
                    var reQStatus = xhr.status;

                    $('#modal-loading').modal('hide');
                    if (reQStatus == 406 ||reQStatus == 422) {
                        $("#myalert").html(errors.message).removeClass('d-none');

                    }

                    console.log("textStatus", textStatus)
                    console.log("errors2", errors)
                    console.log("xhr",reQStatus)
                    $this.find('b').text(defaultText)
                    if (xhr.responseJSON || xhr.responseJSON.errors) {

                        $this.find('b').text(defaultText)
                        $this.find('span').hide()
                        if (errors.status == false) {

                            $this.find('b').text('Opps Error..')
                            setTimeout(() => {
                                $this.find('b').text(defaultText)
                            }, 2000);
                        }

                        $("#myalert").html(errors.message).removeClass('d-none');

                        $(".error").remove();



                            $.each(errors, function(field, messages) {

                                console.log("f",field )
                                console.log("messages",messages )

                                var inputElement = $('[name="' + field + '"]');
                                inputElement.addClass('is-invalid');
                                if (field == 'country_code') {
                                    $("#countryCodeDiv").find('.error').remove();
                                    $("#countryCodeDiv").last().append('<div class="error ' + field + '">' + messages.join('<br>') + '</div>');
                                }else {
                                    $("#myalert").html( messages.join('<br>') ).removeClass('d-none');
                                    inputElement.after('<div class="error ' + field + '">' + messages.join('<br>') + '</div>');
                                }


                            });


                    }
                }
            });
        });
    });


    $(".change-timezone").change(function() {

        $this = $(this);
        var timezone = $this.find("option:selected").val();
        var id = $("#slot-information-user").attr('data-id');
        $.ajax({
            url: "{{ route('get-slots-timezone') }}",
            type: 'POST',
            data: {
                timezone: timezone,
                id: id,

            },
            success: function(response) {

                var html = '';

                if (response.status) {
                    $.each(response.slots, function(key, item) {
                        html += `<div class="col col-lg-3 col-md-6">
                            <div class="card text-center h-10 py-0 shadow-sm slot-capture checkSlot" data-id="${item.id}">

                                <div class="card-body px-0">
                                    <h5 class="card-title title-binding">${convertTimeTo12HourFormat(item.slot_time)}</h5>
                                    <img class="load-img" src="{{ asset('assets/sm-loader.gif') }}" style="display:none">

                                </div>
                            </div>
                            </div>`;
                    });

                    $("#slot-information-user").find('label').text("Your Current Timezone:" +
                        response.timezone);
                    $("#timezone-hidden").val(response.timezone)
                    $("#myalert").html(html).removeClass('d-none');
                    $(".confirm").show();
                    $(".back").show();
                } else {
                    $("#myalert").html("<h1>" + response.message + "</h1>").removeClass('d-none');
                    $(".confirm").hide();
                    $(".back").show();

                }


            },
            error: function(xhr) {
                // email-contaniner
                // $("#otpVerifiedMessage").find('p').removeClass('text-success').addClass('text-danger').text(xhr.responseJSON.message);
                // $("#mobile-number").find('p').removeClass('text-success').addClass('text-danger').text(xhr.responseJSON.message);

            }
        });


    });
</script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var scannerPaused = false;

    const html5QrCode = new Html5Qrcode("reader");


    const fileinput = document.getElementById('qr-input-file');
    fileinput.addEventListener('change', e => {
        if (e.target.files.length == 0) {
            // No file selected, ignore
            return;
        }

        // Use the first item in the list
        const imageFile = e.target.files[0];
        html5QrCode.scanFile(imageFile, /* showImage= */ true)
            .then(qrCodeMessage => {
                $('#modal-loading2').modal('show');

                // alert(qrCodeMessage);

                $.ajax({
                    url: "{{ route('get-working-lady-deatils') }}",
                    method: 'post',
                    data: {
                        id: qrCodeMessage,
                        'token': "{{ csrf_token() }}"

                    },
                    success: function(response) {
                        $('#modal-loading2').modal('hide');
                        // Handle success
                        if (response.status) {
                            $("#QrCodeId").val(qrCodeMessage)
                            // alert('true')
                            $("#qr-code-listing").find(".card").addClass('active-card')
                            $("#mobile").val(response.data.mobile)
                            $("#working_lady_id").val(response.data.id)
                            $("#cadr-1r").text('').hide()

                        } else {

                            $("#cadr-1r").text(response.message).show()


                        }

                    },
                    error: function(error) {
                        html5QrCode.pause();
                        // Handle error
                        toastr.error('Error: Unable to process the scan.');
                    }
                });
                // success, use qrCodeMessage

            })
            .catch(err => {
                // failure, handle it.
                console.log(`Error scanning file. Reason: ${err}`)
            });
    });

    // html5QrCode.render(onScanSuccess, onScanError);

    // html5QrcodeScanner.render(onScanSuccess);

    $(document).ready(function() {
        var video = document.getElementById('video');
        var startCameraButton = $('#start-camera');
        var captureSelfieButton = $('#capture-selfie');
        var restartCameraButton = $('#restart-camera');
        var capturedImageDiv = $('#captured-image');
        var selfieImage = $('#selfie-image');
        var selfieInput = $('#selfie');
        // Add an event listener to the "Start Camera" button

        function startCamera() {
            $("#camera-view").show();
            // Access the camera and display the feed in the video element
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    video.srcObject = stream;

                    // When the stream is loaded, start playing the video
                    video.onloadedmetadata = function(e) {
                        video.play();
                        // Show the "Capture Selfie" button and hide the "Start Camera" button
                        startCameraButton.hide();
                        captureSelfieButton.show();
                        restartCameraButton.show();
                    };
                })
                .catch(function(error) {
                    console.error('Error accessing camera:', error);
                });
        }

        startCameraButton.on('click', function() {
            startCamera();
        });

        restartCameraButton.on('click', function() {
            // Hide the captured image and show the camera view
            capturedImageDiv.hide();
            video.srcObject = null;
            startCamera();
        });

        // Add an event listener to the "Capture Selfie" button
        captureSelfieButton.on('click', function() {
            // Create a canvas to capture the current frame
            var canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            var context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert the captured frame to base64 and set it in the hidden input field
            var selfieDataUrl = canvas.toDataURL('image/jpeg');
            selfieInput.val(selfieDataUrl);


            // Stop the video stream and hide the camera view
            var stream = video.srcObject;
            if (stream) {
                var tracks = stream.getTracks();
                tracks.forEach(function(track) {
                    track.stop();
                });
            }
            video.srcObject = null;
            $("#camera-view").hide();
            capturedImageDiv.show();

            selfieImage.attr('src', selfieDataUrl);

            // Show the "Restart Camera" button and hide the "Capture Selfie" button
            captureSelfieButton.hide();
            restartCameraButton.show();
            restartCameraButton.prop('disabled', false);
            $("#loader-main").show()
            $("#submitBtn").hide();
            $.ajax({
                url: '/detect-liveness', // Update the URL to your Laravel endpoint
                method: 'POST',
                data: {
                    image: selfieDataUrl, // Send the base64-encoded image data
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    $("#loader-main").hide()
                    $("#submitBtn").show();

                    $("#submitBtn").show();
                    $("#error").removeClass('danger');
                    $("#error").addClass('success').text("Perfect. You can proceed");



                },
                error: function(error) {
                    $("#loader-main").hide()
                    // Handle errors
                    if (error.responseJSON.status == false) {
                        $("#error").removeClass('success');
                        $("#error").addClass('danger').text(
                            "We are unable to detect a face. It look like this is something object or other. Please retry again."
                        )
                        $("#submitBtn").hide();
                    }

                }
            });
        });

    });
</script>

<script>
    document.title = "Book Dua Meeting | KahayFaqeer.org";
    $(document).ready(function() {

        $('#mobile').on('input', function() {
            // Get the value of the phone input
            let phoneNumber = $(this).val();

            // Remove any non-digit characters (e.g., spaces, dashes)
            phoneNumber = phoneNumber.replace(/\D/g, '');

            // Check if the phone number has reached 10 digits
            if (phoneNumber.length === 10) {
                $("#submitBtn").show();
                $("#mobile-number").find('p').text('');
                // $("#opt-form-confirm").fadeIn(500);
                // $("#mobile-number").removeClass('col-lg-7').addClass('col-lg-5');
                $("#mobile-number").removeClass('col-lg-6').addClass('col-lg-6');
            } else {
                $("#submitBtn").hide();
                //  $("#opt-form-confirm").fadeOut(500);
                $("#mobile-number").find('p').text('Please enter 10 digit Number').addClass('error');
                $("#mobile-number").removeClass('col-lg-6').addClass('col-lg-6');
                // $("#mobile-number").removeClass('col-lg-5').addClass('col-lg-7');
            }
        });
        $("#sendOtp").click(function() {

            $this = $(this);

            var loadingText = $this.attr('data-loading');
            var successText = $this.attr('data-success');
            var defaultText = $this.attr('data-default');

            $this.find('span').show()
            $this.find('label').text(loadingText)



            $.ajax({
                url: "{{ route('send-otp') }}",
                type: 'POST',
                data: {
                    country_code: $("#country_code").val(),
                    mobile: $("#mobile").val(),
                    email: $("#email").val(),

                },
                success: function(response) {
                    $this.find('label').text(successText)
                    $this.find('span').hide()
                    // setTimeout(() => {
                    //     $this.find('label').text(defaultText)
                    // }, 2500);

                    $("#opt-form").show();
                    $("#submitBtn").hide();
                    $("#otpVerifiedMessage").find('p').removeClass('text-danger').addClass(
                        'text-success').text(response.message);
                    $this.find('label').text("Resend")

                },
                error: function(xhr) {

                    $this.find('span').hide()
                    $this.find('label').text(defaultText)
                    $("#otpVerifiedMessage").find('p').removeClass('text-success').addClass(
                        'text-danger').text(xhr.responseJSON.message);
                    // $("#email-contaniner").find('p').removeClass('text-success').addClass('text-danger').text(xhr.responseJSON.message);
                    // $("#mobile-number").find('p').removeClass('text-success').addClass('text-danger').text(xhr
                    //     .responseJSON.message);

                }
            });
        })

        $("#submit-otp").click(function() {
            $this = $(this);

            var loadingText = $this.attr('data-loading');
            var successText = $this.attr('data-success');
            var defaultText = $this.attr('data-default');

            $this.find('span').show()
            $this.find('label').text(loadingText)

            $("#opt-form").show();

            $("#submitBtn").hide();
            $.ajax({
                url: "{{ route('verify-otp') }}",
                type: 'POST',
                data: {
                    otp: $("#otp").val()
                },
                success: function(response) {
                    $this.find('label').text(successText)
                    $this.find('span').hide()
                    $("#loader-otp2").hide();
                    $("#opt-form-confirm").hide();
                    $("#submitBtn").show(); // Display a success message
                    $("#opt-form").hide();
                    $("#otpVerifiedMessage").find('p').removeClass('text-danger').addClass(
                        'text-success').text('One-time password (OTP) Verified');
                    // $("#mobile-number").find('p').addClass('text-success').text('Mobile Number Verified')
                    $("#otp-verified").val('verified');
                    // You can proceed with form submission here
                },
                error: function(xhr) {
                    $this.find('span').hide()
                    $this.find('label').text(defaultText)
                    $("#otp-verified").val('');
                    $("#opt-form").find('p').addClass('text-danger').text(xhr.responseJSON
                        .error);
                }
            });
        })
    });

    function convertTimeTo12HourFormat(inputTime) {
        var timeParts = inputTime.split(":");
        var hours = parseInt(timeParts[0], 10);
        var minutes = parseInt(timeParts[1], 10);
        var ampm = hours >= 12 ? "PM" : "AM";

        hours = hours % 12;
        hours = hours ? hours : 12;

        var formattedTime = hours + ":" + (minutes < 10 ? "0" : "") + minutes + " " + ampm;
        return formattedTime;
    }

    function convertDateToCustomFormat(inputDate) {
        var dateParts = inputDate.split("-");
        var year = dateParts[0];
        var month = dateParts[1];
        var day = dateParts[2];

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        var formattedDate = day + "-" + monthNames[parseInt(month, 10) - 1] + "-" + year;
        return formattedDate;
    }

    function stopCamera() {
        var startCameraButton = $('#start-camera');
        var captureSelfieButton = $('#capture-selfie');
        var restartCameraButton = $('#restart-camera');
        // Get the video element
        var video = document.getElementById('camera-view');

        // Pause the video
        if (video.srcObject) {
            var tracks = video.srcObject.getTracks();
            tracks.forEach(function(track) {
                track.stop();
            });
            video.srcObject = null;
        }

        // Hide the camera view
        $("#camera-view").hide();


        // Hide the "Capture Selfie" and "Restart Camera" buttons, and show the "Start Camera" button
        startCameraButton.show();
        captureSelfieButton.hide();
        restartCameraButton.hide();
    }



    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "60000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
</script>

<script>
    const dropArea = document.querySelector(".drop_box"),
        button = dropArea.querySelector("button"),
        dragText = dropArea.querySelector("header"),
        input = dropArea.querySelector("input");
    let file;
    var filename;

    button.onclick = () => {
        input.click();
    };


    document.addEventListener("DOMContentLoaded", function() {
        const permissionButton = document.getElementById("startBooking");

        permissionButton.addEventListener("click", function() {
            // Check if camera permission is already granted
            navigator.permissions.query({
                    name: 'camera'
                })
                .then(function(permissionStatus) {
                    if (permissionStatus.state === 'granted') {
                        startCamerDa();
                    } else {
                        requestCameraPermission();
                    }
                });


        });
    });

    function requestCameraPermission() {
        const imgElement = document.getElementById("img");
        const videoElement = document.getElementById("video");
        const submitButton = document.querySelector(".confirm");

        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "user"
                }
            })
            .then(function(stream) {
                // Display the video stream
                videoElement.srcObject = stream;
                videoElement.style.display = "none";

                // When the user clicks capture
                submitButton.addEventListener("click", function captureImage() {
                    // Create a canvas element to capture the frame
                    const canvas = document.createElement("canvas");
                    const context = canvas.getContext("2d");
                    canvas.width = videoElement.videoWidth;
                    canvas.height = videoElement.videoHeight;

                    // Draw the current frame from the video onto the canvas
                    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

                    // Convert canvas to base64 image data
                    const imageData = canvas.toDataURL("image/png");

                    // Set the captured image as the src of the img element
                    imgElement.src = imageData;

                    $("#image-input").val(imageData)
                    $("#imginpuyte").attr('src', imageData)
                    $("#showhere").val(imageData)

                    // Stop the video stream
                    stream.getTracks().forEach(track => track.stop());

                    // Remove the event listener to prevent capturing multiple images
                    submitButton.removeEventListener("click", captureImage);
                });
            })
            .catch(function(error) {
                navigator.permissions.query({
                        name: 'camera'
                    })
                    .then(function(permissionStatus) {
                        if (permissionStatus.state === 'denied') {
                            // Show alert for permission denied
                            alert("Camera permission denied. Please allow camera access to use this feature.");
                        } else {
                            // Handle other errors
                            console.error("Error accessing camera:", error);
                        }
                    });



                // Handle permission denied or error
                console.error("Camera permission denied or error:", error);
            });
    }

    function startCamerDa() {
        // Camera permission is already granted
        requestCameraPermission();
    }



    document.addEventListener("DOMContentLoaded", function() {
        const submitButton = document.querySelector(".confirm");
        const videoElement = document.getElementById("video");
        const imgElement = document.getElementById("img");

        submitButton.addEventListener("click", function() {
            // Request camera permission
            // navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    // Display the video stream
                    videoElement.srcObject = stream;
                    videoElement.style.display = "none";

                    // When the user clicks capture
                    submitButton.addEventListener("click", function captureImage() {
                        // Create a canvas element to capture the frame
                        const canvas = document.createElement("canvas");
                        const context = canvas.getContext("2d");
                        canvas.width = videoElement.videoWidth;
                        canvas.height = videoElement.videoHeight;

                        // Draw the current frame from the video onto the canvas
                        context.drawImage(videoElement, 0, 0, canvas.width,
                            canvas.height);

                        // Convert canvas to base64 image data
                        const imageData = canvas.toDataURL("image/png");

                        // Set the captured image as the src of the img element
                        imgElement.src = imageData;

                        $("#image-input").val(imageData)

                        // Stop the video stream
                        stream.getTracks().forEach(track => track.stop());

                        // Remove the event listener to prevent capturing multiple images
                        submitButton.removeEventListener("click", captureImage);
                    });
                })
                .catch(function(error) {
                    alert("You are not allowed to book without Permission");
                    location.reload();
                    // Handle permission denied or error
                    console.error("Camera permission denied or error:", error);
                });
        });
    });
</script>
