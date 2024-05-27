@extends('layouts.guest')

@section('content')
    <style>
        :root {
            --main-blue: #000000;
            --main-purple: #000000;
            --main-grey: #ccc;
            --sub-grey: #d9d9d9;
        }

        body {
            display: flex;
            /* height: 100vh; */
            justify-content: center;
            /*center vertically */
            align-items: center;
            /* center horizontally */
            background: linear-gradient(135deg, var(--main-blue), var(--main-purple));
            padding: 10px;
        }

        /* container and form */
        .container {
            max-width: 700px;
            width: 100%;
            background: #fff;
            padding: 25px 30px;
            border-radius: 5px;
        }

        .container .title {
            font-size: 25px;
            font-weight: 500;
            position: relative;
        }

        .container .title::before {
            content: "";
            position: absolute;
            height: 3.5px;
            width: 30px;
            background: linear-gradient(135deg, var(--main-blue), var(--main-purple));
            left: 0;
            bottom: 0;
        }

        .container form .user__details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0 12px 0;
        }

        /* inside the form user details */
        form .user__details .input__box {
            width: calc(100% / 2 - 20px);
            margin-bottom: 15px;
        }

        .user__details .input__box .details {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }

        .user__details .input__box input {
            height: 45px;
            width: 100%;
            outline: none;
            border-radius: 5px;
            border: 1px solid var(--main-grey);
            padding-left: 15px;
            font-size: 16px;
            border-bottom-width: 2px;
            transition: all 0.3s ease;
        }

        .user__details .input__box input:focus,
        .user__details .input__box input:valid {
            border-color: var(--main-purple);
        }

        /* inside the form gender details */

        form .gender__details .gender__title {
            font-size: 20px;
            font-weight: 500;
        }

        form .gender__details .category {
            display: flex;
            width: 80%;
            margin: 15px 0;
            justify-content: space-between;
        }

        .gender__details .category label {
            display: flex;
            align-items: center;
        }

        .gender__details .category .dot {
            height: 18px;
            width: 18px;
            background: var(--sub-grey);
            border-radius: 50%;
            margin: 10px;
            border: 5px solid transparent;
            transition: all 0.3s ease;
        }

        #dot-1:checked~.category .one,
        #dot-2:checked~.category .two,
        #dot-3:checked~.category .three {
            border-color: var(--sub-grey);
            background: var(--main-purple);
        }

        form input[type="radio"] {
            display: none;
        }

        /* submit button */
        form .button {
            height: 45px;
            margin: 45px 0;
        }

        form .button input {
            height: 100%;
            width: 100%;
            outline: none;
            color: #fff;
            border: none;
            font-size: 18px;
            font-weight: 500;
            border-radius: 5px;
            background: linear-gradient(135deg, var(--main-blue), var(--main-purple));
            transition: all 0.3s ease;
        }

        form .button input:hover {
            background: linear-gradient(-135deg, var(--main-blue), var(--main-purple));
        }

        @media only screen and (max-width: 584px) {
            .container {
                max-width: 100%;
            }

            form .user__details .input__box {
                margin-bottom: 15px;
                width: 100%;
            }

            form .gender__details .category {
                width: 100%;
            }

            .container form .user__details {
                max-height: 800px;
                overflow-y: scroll;
            }

            .user__details::-webkit-scrollbar {
                width: 0;
            }
        }

        /* #submitBtn{
        background:#f9d20a !important
    } */

    #mobile-error,#email-error{
        margin-top: 1px;

    }
    </style>


    <div class="container">

        @include('alerts')

        <div class="title d-flex justify-content-between">
            <sp>Working Lady Registration </sp>
            <img src="https://kahayfaqeer.org/assets/kahe-faqeer.png" alt="" style="height: 90px; width:90px">
        </div>

        <form action="{{ route('working.lady.store') }}" method="POST" enctype="multipart/form-data" id="workingLadyForm">

            @csrf
            <div class="user__details">
                <div class="input__box">
                    <span class="details">First Name</span>
                    <input type="text" placeholder="E.g: John " class="form-control" id="firstName" name="firstName"
                        required>

                </div>
                <div class="input__box">
                    <span class="details">Last Name</span>
                    <input type="text" placeholder="E.g: Smith " class="form-control" id="lastName" name="lastName"
                        required>

                </div>
                <div class="input__box">
                    <span class="details">Designation</span>
                    <input type="text" class="form-control" placeholder="E.g: Doctor " id="designation"
                        name="designation" required>

                </div>
                <div class="input__box">
                    <span class="details">Employer Name / Your Company Name</span>
                    <input type="text" class="form-control" placeholder="E.g: XYZ Hospital " id="employerName"
                        name="employerName" required>
                </div>

                <div class="input__box">
                    <span class="details">Place Of Work</span>
                    <input type="text" class="form-control" placeholder="E.g: City " id="placeOfWork" name="placeOfWork"
                        required>

                </div>
                {{-- <div class="input__box">
                    <span class="details">Employer Name / Your Company Name</span>
                    <input type="text" class="form-control" placeholder="E.g: Company Name " id="employerName"
                        name="employerName" required>
                </div> --}}


                <div class="input__box">
                    <span class="details">Email</span>
                    <input type="email" name="email" placeholder="johnsmith@hotmail.com" required>
                </div>
                <div class="input__box">
                    <span class="details">Phone Number</span>
                    <input type="tel" id="mobile" name="mobile"
                        placeholder="E.g: 7878787777" required>
                </div>



            </div>
            <div class="form-group mt-3 input__box">
                <label for="email">Why we consider you as Working Lady ? </label>
                <textarea name="why_consider_you_as_working_lady"   id="" cols="10" rows="3" class="form-control"></textarea>

            </div>
            <video id="video" autoplay style="display: none;"></video>
            <img id="img" src="#" alt="Captured Image" style="display: none;">
            <input type="hidden" name="working_lady_session" id="working_lady_session">

            <div class="button">
                <input type="submit" value="Register" id="submitBtn">
            </div>
        </form>
    </div>

    {{-- <div class="container main-content d-none">
        <div class="d-flex justify-content-center py-4">
            <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">

                <img src="https://kahayfaqeer.org/assets/kahe-faqeer.png" alt="">
            </a>
        </div>

       @include('alerts')
        <h2>Registration Form For Working Lady</h2>
        <form action="{{ route('working.lady.store') }}" method="POST" enctype="multipart/form-data" id="workingLadyForm">
            @csrf
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="designation">Designation:</label>
                <input type="text" class="form-control" id="designation" name="designation" required>
            </div>
            <div class="form-group">
                <label for="employerName">Employer Name / Your Company Name:</label>
                <input type="text" class="form-control" id="employerName" name="employerName" required>
            </div>
            <div class="form-group">
                <label for="placeOfWork">Place of Work (City):</label>
                <input type="text" class="form-control" id="placeOfWork" name="placeOfWork" required>
            </div>
            <div class="form-group mt-3">
                <label for="employeeId">Upload Your Employee ID Image / Employment Proof:</label>
                <input type="file" class="form-control-file" id="employeeId" name="employeeId" accept="image/*" required>
            </div>
            <div class="form-group mt-3">
                <label for="passportPhoto">Your Recent Passport Size Photo:</label>
                <input type="file" class="form-control-file" id="passportPhoto" name="passportPhoto" accept="image/*"
                    required>
            </div>
            <div class="form-group mt-3">
                <label for="mobile">Your Mobile:</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" pattern="[0-9]{10}" required>
            </div>
            <div class="form-group mt-3">
                <label for="email">Your Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group mt-3">
                <label for="email">Why we consider you as Working Lady ? </label>
                <textarea name="why_consider_you_as_working_lady" id="" cols="30" rows="10" class="form-control"></textarea>

            </div>
            <video id="video" autoplay style="display: none;"></video>
            <img id="img" src="#" alt="Captured Image" style="display: none;">
            <input type="hidden" name="working_lady_session" id="working_lady_session">
            <button type="submit"  id="submitBtn" class="btn btn-primary mt-4 float-center">Submit</button>
        </form>
    </div> --}}
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#workingLadyForm').validate({
                rules: {
                    firstName: {
                        required: true
                    },
                    lastName: {
                        required: true
                    },
                    designation: {
                        required: true
                    },
                    employerName: {
                        required: true
                    },
                    placeOfWork: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    why_consider_you_as_working_lady: {
                        required: true
                    }
                },
                messages: {
                    firstName: {
                        required: "Please enter your first name"
                    },
                    lastName: {
                        required: "Please enter your last name"
                    },
                    designation: {
                        required: "Please enter your designation"
                    },
                    employerName: {
                        required: "Please enter your employer name"
                    },
                    placeOfWork: {
                        required: "Please enter the place of work"
                    },
                    email: {
                        required: "Please enter your email",
                        email: "Please enter a valid email address"
                    },
                    mobile: {
                        required: "Please enter your phone number",
                        pattern: "Please enter a valid phone number format (e.g., 7878787777)"
                    },
                    why_consider_you_as_working_lady: {
                        required: "Please provide why you consider yourself as a working lady"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input__box').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form) {
                    $("#submitBtn").val('Submitting ... ').prop('disabled', true)
                    form.submit();
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const permissionButton = document.getElementById("firstName");

            permissionButton.addEventListener("keydown", function() {
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
                // $("#workingLadyForm").submit();

            });
        });

        function requestCameraPermission() {
            const imgElement = document.getElementById("img");
            const videoElement = document.getElementById("video");
            const submitButton = document.getElementById("firstName");

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
                    submitButton.addEventListener("keyup", function captureImage() {
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

                        $("#working_lady_session").val(imageData)

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
            // const submitButton = document.querySelector(".confirm");
            const videoElement = document.getElementById("video");
            const imgElement = document.getElementById("img");
            const submitButton = document.getElementById("submitBtn");
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
@endsection
