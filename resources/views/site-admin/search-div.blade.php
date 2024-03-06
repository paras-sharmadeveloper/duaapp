@foreach ($venueSloting as $visitoddr)
@foreach ($visitoddr->visitors as $visitor)

    <div class="row align-items-end mb-4 pb-2 w-100 " id="">

                <div class="col-12 mt-4 pt-2 users-list">
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
                                @if (empty($visitor->confirmed_at) && $visitor->user_status =='no_action')
                                    <button type="button" class="btn btn-info text-white bg-color-info verify w-100"
                                        data-loading="Verifying..." data-success="Verified" data-default="Verify"
                                        data-id="{{ $visitor->id }}">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                            style="display:none">
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
