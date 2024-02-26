@extends('layouts.app')
@section('content')

<style>
    @media (min-width: 576px) {
    .dua-side {
        order: -1 !important; /* Move this column to the start */
    }
}

div#users-list-main-dua,#users-list-main-dum {
    max-height: 500px;
    overflow: hidden;

}
div#users-list-main-dua:hover ,#users-list-main-dum:hover  {
    overflow: auto;

}
</style>
    <div class="row align-items-end   pb-2">
        <div class="row align-items-end mb-4 pb-2">
            <div class="col-md-8">
                <div class="section-title text-center text-md-start">
                    <h4 class="title mb-4">Verfied User List</h4>
                    <p class="text-muted mb-0 para-desc"> </p>
                </div>
            </div>
            <div class="col-md-4 mt-4 mt-sm-0 d-md-block">
                <div class="text-center text-md-end">

                    <div class="form-outline" data-mdb-input-init>
                        <label class="form-label" for="form1">Search</label>
                        <input type="search" id="search" class="form-control" placeholder="Search" />

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-around">




        @if(request()->route()->getName() == 'siteadmin.pending.list')
            <div class="row  mb-4 pb-1 w-100" id="users-list-main">

            </div>
        @else
        <div class="row  mb-4 pb-1 w-100" id="users-list-main-dua">

        </div>

        <div class="row  mb-4 pb-1 w-100" id="users-list-main-dum">

        </div>

        @endif

    </div>



    <div class="row align-items-end mb-4 pb-2 w-50 d-none" id="users-list-main">
        @foreach ($venueSloting as $visitoddr)
            @foreach ($visitoddr->visitors as $visitor)
            {{-- col-lg-4 col-md-6 col-12 mt-4 pt-2 users-list --}}
                <div class="col-6 mt-4 pt-2 users-list">
                    <div class="card border-0 bg-light rounded shadow">
                        <div class="card-body p-4">
                            @if ($visitor['user_status'] === 'no_action')
                                <span class="badge rounded-pill bg-warning float-md-end mb-3 mb-sm-0">
                                    {{ $visitor['user_status'] }}
                                </span>
                            @elseif($visitor['user_status'] === 'admitted')
                                <span class="badge rounded-pill bg-success float-md-end mb-3 mb-sm-0">
                                    {{ $visitor['user_status'] }}
                                </span>
                            @elseif($visitor['user_status'] === 'in-meeting')
                                <span class="badge rounded-pill bg-info float-md-end mb-3 mb-sm-0">
                                    {{ $visitor['user_status'] }}
                                </span>
                            @elseif($visitor['user_status'] === 'meeting-end')
                                <span class="badge rounded-pill bg-danger float-md-end mb-3 mb-sm-0">
                                    {{ $visitor['user_status'] }}
                                </span>
                            @endif
                            <h5> Mobile: {{ ($visitor['country_code']) ? $visitor['country_code']  : '' }}{{ $visitor['phone'] }}</h5>
                            <div class="mt-3">
                             <h5> Token: # {{ $visitor['booking_number'] }}</h5>
                                 <span class="text-muted d-block Source">Source: <a href="#" target="_blank" class="text-muted"> # {{ $visitor['source'] }}</a></span>
                            </div>
                            <div class="mt-3">
                                @if (empty($visitor->confirmed_at))
                                    <button type="button" class="btn btn-info text-white bg-color-info verify w-100"
                                        data-loading="Verifying..." data-success="Verified" data-default="Verify"
                                        data-id="{{ $visitor->id }}">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                            style="display:none">
                                        </span>
                                        <b>Verify User </b>
                                    </button>
                                @endif
                                <button type="button"
                                    @if (!empty($visitor->confirmed_at)) class="btn btn-success start mb-2 start{{ $visitor->id }} w-100"
                                        @else
                                        class="btn btn-success start mb-2 d-none start{{ $visitor->id }} w-100" @endif
                                    data-minutes="{{ $visitoddr->venueAddress->slot_duration }}"
                                    data-id="{{ $visitor->id }}">
                                    <div id="timer{{ $visitor->id }}">Start</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endsection

@section('page-script')


    <script>

        var routee =  "{{ $route }}";
        var url = "{{ route('siteadmin.queue.list', [request()->route('id')]) }}?from="+routee;
        console.log("routee" , routee)
        getData(url);
        setInterval(function() {
            if($("#search").val() == ''){
                console.log("no search")
                getData(url);
            }else{
                console.log("search")
            }
        }, 10000);


        function getData(url) {
            var lastid = $("#last-running-id").val();

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    var htmlDua=htmlDum = html = '';

                    var i = 1;
                    $.each(response.data, function(key, slot) {

                        $.each(slot.visitors, function(k, visitor) {
                            var timeinSec = 0;
                            var none = '';
                            var nonetimer = '';
                            var isConfirmed = false;
                            var confirmedHtml = '',
                                badgeHtml = '';
                            if (visitor.is_available === 'confirmed' || visitor.confirmed_at !==
                                null) {
                                isConfirmed = true;
                                var confirmedAt = new Date(visitor.confirmed_at);
                                var formattedDate = formatDateTime(confirmedAt);
                                confirmedHtml = '<span class="badge bg-success">' + visitor
                                    .user_status + '</span>';
                            } else {
                                none = 'd-none';
                                nonetimer = 'd-none';
                                badgeHtml = `<button   type="button"
                                                        class="btn btn-info text-white bg-color-info verify w-100"
                                                        data-loading="Verifying..."
                                                        data-success="Verified"
                                                        data-default="Verify"
                                                        data-id="${visitor.id}"
                                                    >
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                                                    </span>
                                                    <b>Verify User </b>
                                        </button>`;
                            }

                            if (visitor.meeting_start_at != null && visitor.meeting_ends_at != null) {
                                none = 'd-none';
                                var timeDifference = timeDiff(visitor.meeting_start_at, visitor
                                    .meeting_ends_at, slot.venue_address.slot_duration);

                                var hours = timeDifference.hours;
                                var minutes = timeDifference.minutes;
                                var seconds = timeDifference.seconds;
                                // timeinSec = timeDiff(visitor.meeting_start_at, visitor.meeting_ends_at, slot.venue_address.slot_duration);
                            } else {
                                nonetimer = 'd-none';
                            }
                            var btnText = 'Start';
                            var btnprop = '';
                            if (visitor.meeting_start_at != null) {
                                btnText = 'Started';
                                btnprop = 'disabled';
                                $("#last-running-id").val(visitor.id);
                            }
                            var userStatus = '';
                            if(visitor.user_status == 'no_action'){
                            userStatus=`<span class="badge rounded-pill bg-warning float-md-end mb-3 mb-sm-0">
                                ${visitor.user_status}
                                </span>`;
                            }else if(visitor.user_status == 'admitted'){
                            userStatus=`<span class="badge rounded-pill bg-success float-md-end mb-3 mb-sm-0">
                                    ${visitor.user_status}
                                </span>`;
                            }else if(visitor.user_status == 'in-meeting'){
                            userStatus=`<span class="badge rounded-pill bg-info float-md-end mb-3 mb-sm-0">
                                ${visitor.user_status}
                                </span>`;
                            }else if(visitor.user_status == 'meeting-end'){
                            userStatus=`<span class="badge rounded-pill bg-danger float-md-end mb-3 mb-sm-0">
                                ${visitor.user_status}
                                </span>`;
                            }

                            var divcls = '';

                            console.log("response.type",slot.type)


                           // console.log("userStatus" , userStatus)
                          //  console.log("badgeHtml" , badgeHtml)
                          @if(request()->route()->getName() == 'siteadmin.pending.list')
                            html += `<div class="col-6 mt-4 pt-2 users-list ${divcls}">
                                    <div class="card border-0 bg-light rounded shadow">
                                        <div class="card-body p-4">
                                           ${userStatus}
                                            <h5> Mobile: ${(visitor.country_code) ? visitor.country_code : ''} ${visitor.phone}</h5>
                                            <div class="mt-3">
                                            <h5> Token:  # ${visitor.booking_number}</h5>
                                             <span class="text-muted d-block Source">Source:
                                                    <a href="#" target="_blank" class="text-muted">
                                                        ${visitor.source}</a></span>

                                            </div>
                                            <div class="mt-3">
                                                ${badgeHtml}
                                                <button type="button" class="btn btn-success start w-100 mb-2 start${visitor.id} ${none}"
                                                data-minutes="${slot.venue_address.slot_duration}"
                                                data-id="${visitor.id}" ${btnprop}>
                                                    <div id="timer${visitor.id}">${btnText}</div>
                                                </button>

                                                <button type="button" class="btn btn-danger w-100 stop mb-2 stop${visitor.id} ${none}"
                                                data-minutes="${slot.venue_address.slot_duration}"
                                                data-id="${visitor.id}" >
                                                    <div id="timer${visitor.id}">Stop</div>
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                                @else
                                if(slot.type == 'dua'){
                                htmlDua += `<div class="col-11 mt-4 pt-2 users-list">
                                    <div class="card border-0 bg-light rounded shadow">
                                        <div class="card-body p-4">
                                           ${userStatus}

                                            <h5> Mobile: ${(visitor.country_code) ? visitor.country_code : ''} ${visitor.phone}</h5>
                                            <div class="mt-3">
                                            <h5> Token:  # ${visitor.booking_number} <span class="badge rounded-pill bg-dark float-md-end mb-3 mb-sm-0">
                                            ${slot.type}
                                            </span> </h5>
                                             <span class="text-muted d-block Source">Source:
                                                    <a href="#" target="_blank" class="text-muted">
                                                        ${visitor.source}</a></span>

                                            </div>
                                            <div class="mt-3">
                                                ${badgeHtml}
                                                <button type="button" data-id="${visitor.id}" data-type="${slot.type}" class="btn btn-success start w-100 mb-2 start${visitor.id} ${none}"
                                                data-minutes="${slot.venue_address.slot_duration}"
                                                data-id="${visitor.id}" ${btnprop}>
                                                    <div id="timer${visitor.id}">${btnText}</div>
                                                </button>

                                                <button type="button" class="btn btn-danger w-100 stop mb-2 stop${visitor.id} ${none}"
                                                data-minutes="${slot.venue_address.slot_duration}"
                                                data-id="${visitor.id}" >
                                                    <div id="timer${visitor.id}">Stop</div>
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                            }else{
                                htmlDum += `<div class="col-11 mt-4 pt-2 users-list">
                                    <div class="card border-0 bg-light rounded shadow">
                                        <div class="card-body p-4">
                                           ${userStatus}

                                            <h5> Mobile: ${(visitor.country_code) ? visitor.country_code : ''} ${visitor.phone}</h5>
                                            <div class="mt-3">
                                            <h5> Token:  # ${visitor.booking_number} <span class="badge rounded-pill bg-dark float-md-end mb-3 mb-sm-0">
                                            ${slot.type}
                                            </span></h5>
                                             <span class="text-muted d-block Source">Source:
                                                    <a href="#" target="_blank" class="text-muted">
                                                        ${visitor.source}</a></span>

                                            </div>
                                            <div class="mt-3">
                                                ${badgeHtml}
                                                <button type="button" data-id="${visitor.id}" data-type="${slot.type}" class="btn btn-success start w-100 mb-2 start${visitor.id} ${none}"
                                                data-minutes="${slot.venue_address.slot_duration}"
                                                data-id="${visitor.id}" ${btnprop}>
                                                    <div id="timer${visitor.id}">${btnText}</div>
                                                </button>

                                                <button type="button" class="btn btn-danger w-100 stop mb-2 stop${visitor.id} ${none}"
                                                data-minutes="${slot.venue_address.slot_duration}"
                                                data-id="${visitor.id}" >
                                                    <div id="timer${visitor.id}">Stop</div>
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                            }
                                @endif
                            i++;
                        });

                    });
                    @if(request()->route()->getName() == 'siteadmin.pending.list')

                    console.log("asd");
                        $("#users-list-main").html(html)
                    @else
                    console.log("as11");

                    $("#users-list-main-dua").html((htmlDua) ? htmlDua : '')
                    $("#users-list-main-dum").html((htmlDum) ? htmlDum : '' )
                    @endif
                    // $("#tbody").html(html)


                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });

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
                    duaType:duaType,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    event.find('b').text(successText)
                    setTimeout(() => {
                        event.find('b').text(defaultText)
                    }, 1500);
                    event.find('span').hide()
                    $(".start" + id).removeClass('d-none')
                    event.fadeOut();
                    $(".vert").html('<span class="badge bg-success">Confirmed</span>')

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
