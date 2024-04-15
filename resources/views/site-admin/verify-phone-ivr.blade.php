@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="row">
            <div class="col-lg-6 mt-2">
                <input type="number" min="0" inputmode="numeric" pattern="[0-9]*" id="search"
                    placeholder="Search Token" class="form-control w-100">
            </div>
            <div class="col-lg-6 mt-2"> <input type="text" id="globalsearch" placeholder="Search Global"
                    class="form-control w-100"></div>
        </div>

        <div class="col-lg-12">
            <div class="cards d-flex">


            </div>
            <div class="card1 users-list">



                @foreach ($venueSloting as $visitoddr)
                    @foreach ($visitoddr->visitors as $visitor)
                        <div class="row align-items-center mb-4 pb-2 w-100 " id="">

                            {{-- col-lg-4 col-md-6 col-12 mt-4 pt-2 users-list --}}
                            <div class="col-12 mt-4 pt-2 users-list">
                                <div class="card1 border-0 bg-light rounded shadow">
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
                                        <h5> Mobile:
                                            {{ $visitor['country_code'] ? $visitor['country_code'] : '' }}{{ $visitor['phone'] }}
                                        </h5>
                                        <div class="mt-3">
                                            <h5> Token: # {{ $visitor['booking_number'] }}</h5>
                                            <span class="text-muted d-block Source">Source: <a href="#"
                                                    target="_blank" class="text-muted"> #
                                                    {{ $visitor['source'] }}</a></span>
                                        </div>
                                        <div class="mt-3">
                                            @if (empty($visitor->confirmed_at) && $visitor->user_status == 'no_action')
                                                <button type="button"
                                                    class="btn btn-info text-white bg-color-info verify w-100"
                                                    data-loading="Verifying..." data-success="Verified"
                                                    data-default="Verify" data-id="{{ $visitor->id }}">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true" style="display:none">
                                                    </span>
                                                    <b>Verify User </b>
                                                </button>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                @endforeach

            </div>
        </div>
        <input type="hidden" id="currentvenue" value="{{ request()->id }}">
    </div>
@endsection


@section('page-script')
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                var searchQuery = $(this).val();
                var id = $("#currentvenue").val();

                    $.ajax({
                        url: "{{ route('search.visitors') }}",
                        method: 'GET',
                        data: {
                            search: searchQuery,
                            id: id,
                            'type': 'token'
                        },
                        success: function(response) {
                            $('.users-list').html(response);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });


            });

            $('#globalsearch').on('keyup', function() {
                var searchQuery = $(this).val();
                var id = $("#currentvenue").val();

                $.ajax({
                    url: "{{ route('search.visitors') }}",
                    method: 'GET',
                    data: {
                        search: searchQuery,
                        id: id,
                        'type': 'global'
                    },
                    success: function(response) {
                        $('.users-list').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });



            });
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
                    event.find('b').text(successText)
                    setTimeout(() => {
                        event.find('b').text(defaultText)
                    }, 1500);
                    event.find('span').hide()
                    $(".start" + id).addClass('d-none')
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
    </script>
@endsection
