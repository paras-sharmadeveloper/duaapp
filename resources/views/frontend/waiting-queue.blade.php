@extends('layouts.guest')
@section('content') 
<style>
     .a {
            width: 55%;
        }

        .b {
            width: 40%;
        }

        .b .card {
            height: 100vh;
        }

        .token span {
            /* border: 1px solid; */
            padding: 10px;
            /* font-size: 28px; */
            font-weight: bold;
        }

        .users-list-header .card {
            background-color: #00BCD4;
            color: #fff;
        }

        .curnt-token-runing {
            font-size: 11rem;
        }
</style>
<div class="container-fluid d-flex justify-content-around mt-4">
    <div class="a">
        <div class="row">


            <div class="col-xl-12 mb-4 users-list-header">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="tokn">
                                <span class="fw-bold rounded-circle text-center">Upcoming</span>
                            </div>
                            <div class="ms-3">
                                <p class="fw-bold mb-1">Info</p>
                            </div>
                            <span class="fw-bold">Status</span>
                            <span class="fw-bold">Estimated Time</span>
                        </div>
                    </div>

                </div>
            </div>



            <div class="col-xl-12 mb-4 users-list">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="token">
                                <span class="rounded-circle text-center h2">1040</span>
                            </div>
                            <div class="ms-3">
                                <p class="fw-bold mb-1 h2">John Deo</p>
                                <p class="text-muted mb-0 h6">Jhon@example.com</p>
                            </div>

                            <span class="badge rounded-pill badge-success h2">Active</span>
                            <span class="badge badge-warning rounded-pill d-inline h2">Awating..</span>
                        </div>
                    </div>

                </div>
            </div>
             
            <div class="col-xl-12 mb-4 users-list">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="token">
                                <span class="rounded-circle text-center h2">1040</span>
                            </div>
                            <div class="ms-3">
                                <p class="fw-bold mb-1 h2">John Deo</p>
                                <p class="text-muted mb-0 h6">Jhon@example.com</p>
                            </div>

                            <span class="badge rounded-pill badge-success h2">Active</span>
                            <span class="badge badge-warning rounded-pill d-inline h2">Awating..</span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-xl-12 mb-4 users-list">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="token">
                                <span class="rounded-circle text-center h2">1040</span>
                            </div>
                            <div class="ms-3">
                                <p class="fw-bold mb-1 h2">John Deo</p>
                                <p class="text-muted mb-0 h6">Jhon@example.com</p>
                            </div>

                            <span class="badge rounded-pill badge-success h2">Active</span>
                            <span class="badge badge-warning rounded-pill d-inline h2">Awating..</span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-xl-12 mb-4 users-list">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="token">
                                <span class="rounded-circle text-center h2">1040</span>
                            </div>
                            <div class="ms-3">
                                <p class="fw-bold mb-1 h2">John Deo</p>
                                <p class="text-muted mb-0 h6">Jhon@example.com</p>
                            </div>

                            <span class="badge rounded-pill badge-success h2">Active</span>
                            <span class="badge badge-warning rounded-pill d-inline h2">Awating..</span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-xl-12 mb-4 users-list">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="token">
                                <span class="rounded-circle text-center h2">1040</span>
                            </div>
                            <div class="ms-3">
                                <p class="fw-bold mb-1 h2">John Deo</p>
                                <p class="text-muted mb-0 h6">Jhon@example.com</p>
                            </div>

                            <span class="badge rounded-pill badge-success h2">Active</span>
                            <span class="badge badge-warning rounded-pill d-inline h2">Awating..</span>
                        </div>
                    </div>

                </div>
            </div>



        </div>
    </div>
    <div class="b">
        <div class="row">
            <div class="col-xl-12 mb-4 current-token">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="card-title text-center">Active Token</h5>
                        <p class="curnt-token-time"> 00:00:00 </p>
                        <span class="curnt-token-runing badge badge-success"> 1040 </span>
                    </div>
                </div>
            </div>
        </div>
    </div>





</div>
@endsection