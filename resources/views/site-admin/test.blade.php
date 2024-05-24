@extends('layouts.app')
@section('content')
    <style>
        @media (min-width: 576px) {
            .dua-side {
                order: -1 !important;
                /* Move this column to the start */
            }
        }

        div#users-list-main-dua,
        #users-list-main-dum {
            max-height: 500px;
            overflow: hidden;

        }

        div#users-list-main-dua:hover,
        #users-list-main-dum:hover {
            overflow: auto;
        }
        .launch-dum,
        .launch-dua {
            padding: 40px !important;
            font-size: 24px;
            color: #FFF;
            font-weight: 600;
        }
    </style>

    <div class="row align-items-end   pb-2">
        <div class="row align-items-end pb-2">
            <div class="col-md-8">
                <div class="section-title text-center text-md-start">
                    <h4 class="title">Launch Token Area</h4>
                    <p class="text-muted mb-0 para-desc"> </p>
                </div>
            </div>
            <div class="col-md-4 mt-4 mt-sm-0 d-md-block">
                <div class="text-center text-md-end">
                    {{-- <div class="form-outline" data-mdb-input-init>
                        <label class="form-label" for="form1">Search</label>
                        <input type="tel" id="search" class="form-control" placeholder="Search" />
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex1 justify-content-around1">

        @if (request()->route()->getName() == 'siteadmin.pending.list')
            <div class="row  mb-4 pb-1 w-100" id="users-list-main">
            </div>
        @else
            <div class="row  mb-1 pb-1 w-100" id="users-list-main-dua">
            </div>
            <div class="row  mb-1 pb-1 w-100" id="users-list-main-dum">
            </div>
        @endif

    </div>
@endsection

@section('page-script')
    <script>
        var routee = "{{ $route }}";
        // var url = "{{ route('siteadmin.queue.list', [request()->route('id')]) }}?from="+routee;
        var url = "{{ route('siteadmin.fetch.token') }}";

        console.log("routee", routee)
        getData(url);
        // setInterval(function() {
        //     if ($("#search").val() == '') {
        //         console.log("no search")
        //         getData(url);
        //     } else {
        //         console.log("search")
        //     }
        // }, 5000);


        function getData(url) {
            var lastid = $("#last-running-id").val();

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function(response) {

                    var visitor = response.data.dua;
                    var visitorDum = response.data.dum;

                    @if (request()->route()->getName() == 'siteadmin.pending.list')
                        $("#users-list-main").html(html)
                    @else
                        plotData(visitor, visitorDum)
                        if(response.data.working_dum || response.data.working_dua  ){
                            plotData(response.data.working_dua, response.data.working_dum)
                        }
                    @endif

                    // $("#tbody").html(html)


                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });

        }

        function plotData(visitor, visitorDum) {

            var htmlDua = htmlDum = ''
            if (visitor !== null) {
                if(visitor.user_status == 'in-meeting'){
                    htmlDua += `<div class="col-12 mt-2 pt-1 users-list">
                    <div class="card border-0 bg-light rounded shadow">
                        <button class="btn btn-danger launch-dua stop"
                        data-id="${visitor.id}" data-type="${visitor.dua_type}"
                        >END DUA TOKEN # ${visitor.booking_number}</button>
                    </div>
                </div>`;
                }else{
                    htmlDua += `<div class="col-12 mt-2 pt-1 users-list">
                    <div class="card border-0 bg-light rounded shadow">
                        <button class="btn btn-success launch-dua start"
                        data-id="${visitor.id}" data-type="${visitor.dua_type}"
                        >LAUNCH DUA TOKEN # ${visitor.booking_number}</button>
                    </div>
                </div>`;
                }

            }
            if (visitor == 'last') {
                 $("#users-list-main-dua").find("button").removeClass('btn-success').addClass('btn-danger');
                 $("#users-list-main-dua").find("button").removeClass('start').addClass('stop');
            }
            if (visitorDum == 'last') {
                $("#users-list-main-dum").find("button").removeClass('btn-success').addClass('btn-danger');
                $("#users-list-main-dum").find("button").addClass('stop');
            }

            if (visitorDum !== null) {
                if(visitorDum.user_status == 'in-meeting'){
                    htmlDum += `<div class="col-12 mt-4 pt-2 users-list">
                            <div class="card border-0 bg-light rounded shadow">
                                <button class="btn btn-danger  launch-dum stop"
                                        data-id="${visitorDum.id}" data-id="${visitorDum.id}" data-type="${visitorDum.dua_type}">
                                        END DUM TOKEN # ${visitorDum.booking_number}</button>
                            </div>
                        </div>`;
                }else{
                    htmlDum += `<div class="col-12 mt-4 pt-2 users-list">
                            <div class="card border-0 bg-light rounded shadow">
                                <button class="btn btn-primary  launch-dum start"
                                        data-id="${visitorDum.id}" data-id="${visitorDum.id}" data-type="${visitorDum.dua_type}">
                                        LAUNCH DUM TOKEN # ${visitorDum.booking_number}</button>
                            </div>
                        </div>`;
                }

            }

            $("#users-list-main-dua").html((htmlDua) ? htmlDua : '')
            $("#users-list-main-dum").html((htmlDum) ? htmlDum : '')

        }

        function formatDateTime(dateTimeString) {
            const options = {
                // year: 'numeric',
                //  month: 'numeric',
                // day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
            };

            const formattedDate = new Date(dateTimeString).toLocaleString('en-US', options);
            return formattedDate;
        }


        function convertTo12HourFormat(time24) {
            const [hour, minute, second] = time24.split(':');
            let period = 'AM';

            // Convert to 12-hour format and set the period (AM or PM)
            let hour12 = parseInt(hour, 10);
            if (hour12 >= 12) {
                period = 'PM';
                if (hour12 > 12) {
                    hour12 -= 12;
                }
            }

            // Format with leading zeros (e.g., "03:15 PM")
            // const time12 = `${hour12.toString().padStart(2, '0')}:${minute}:${second} ${period}`;
            const time12 = `${hour12.toString().padStart(2, '0')}:${minute} ${period}`;
            return time12;
        }
        var timerInterval;
        $(document).on("click", ".start", function() {
            $this = $(this);
            var id = $this.attr('data-id');
            var type = $this.attr('data-type');
            var lastid = $("#last-running-id").val();

            var duration = parseInt($this.attr('data-minutes')); // Parse the value as an integer

            var startTime = new Date().getTime();
            var totalTime = duration * 60000;
            var timeInterval = duration * 1000;
            var endTime = startTime + totalTime;
            $("#last-running-id").val(id);

            postAjax(id, 'start', $this);
            if (lastid > 0) {
                postAjax(lastid, 'end', $this);
            }

            // $(this).prop("disabled", true);

            // Update the timer every second
            var timerInterval;

            timerInterval = setInterval(function() {
                var currentTime = new Date().getTime();
                var remainingTime = endTime - currentTime;

                if (remainingTime <= 0) {
                    clearInterval(timerInterval);
                    $("#timer" + id).text("Time's up!");


                } else {
                    var seconds = Math.floor(remainingTime / 1000) % 60;
                    var minutes = Math.floor(remainingTime / 1000 / 60);
                    $("#timer" + id).text(minutes + "m " + seconds + "s");
                }
            }, 1000);
        });
        $(document).on("click", ".stop", function() {
            $this = $(this);
            clearInterval(timerInterval);
            var id = $(this).attr('data-id');
            $(this).parents(".action-td").find(".start").prop("disabled", true);
            $(this).prop("disabled", true);
            $(this).parents(".action-td").find(".hold").prop("disabled", true);
            // $("#timer"+id).;
            postAjax(id, 'end', $this);
        });
        $(document).on("click", ".verify", function() {
            $this = $(this);
            var id = $(this).attr('data-id');
            postAjax(id, 'verify', $this);
        });





        function postAjax(id, type, event) {

            var loadingText = event.attr('data-loading');
            var successText = event.attr('data-success');
            var defaultText = event.attr('data-default');
            var url = "{{ route('siteadmin.queue.vistor.update', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            event.find('span').show()
            var duaType = event.attr('data-type');

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    type: type,
                    duaType: duaType,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    var visitor = response.data.dua;
                    var visitorDum = response.data.dum;

                    if(response.data.working_dum || response.data.working_dua  ){
                        plotData(response.data.working_dua, response.data.working_dum)
                    }else{
                        plotData(visitor, visitorDum)
                    }


                    // event.find('b').text(successText)
                    // setTimeout(() => {
                    //     event.find('b').text(defaultText)
                    // }, 1500);
                    // event.find('span').hide()
                    // $(".start" + id).removeClass('d-none')
                    // event.fadeOut();
                    // $(".vert").html('<span class="badge bg-success">Confirmed</span>')

                    console.log(response);
                },
                error: function(xhr, status, error) {
                    event.find('span').hide()
                    $(".start" + id).addClass('d-none')
                    event.find('b').text(defaultText)
                    console.error(error);
                }
            });

        }

        function timeDiff(startTimeStr, endTimeStr, duration) {
            var seconds = duration * 1000;
            var startTime = new Date(startTimeStr);
            var endTime = new Date(endTimeStr);
            var timeDifference = endTime - startTime;

            var hours = Math.floor(timeDifference / (seconds * 60 * 60));
            var minutes = Math.floor((timeDifference % (seconds * 60 * 60)) / (seconds * 60));
            var remainingSeconds = Math.floor((timeDifference % (seconds * 60)) / seconds);

            return {
                hours: hours,
                minutes: minutes,
                seconds: remainingSeconds
            };
        }

        $(document).ready(function() {
            // Add an input event listener to the search input
            $("#search").on("input", function() {
                clearInterval(timerInterval);
                // Get the search input value
                var searchText = $(this).val().toLowerCase();

                // Loop through each card
                $(".users-list .card").each(function() {
                    // Get the text content of each card
                    var cardText = $(this).text().toLowerCase();

                    // Check if the card text contains the search text
                    if (cardText.includes(searchText)) {
                        // Show the card if it matches
                        $(this).show();
                    } else {
                        // Hide the card if it doesn't match
                        $(this).hide();
                    }
                });
            });
        });
    </script>
@endsection
