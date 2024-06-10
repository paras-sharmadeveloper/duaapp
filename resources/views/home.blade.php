@extends('layouts.app')
@section('content')
    <section class="section dashboard ">

        <style>
            .custom-table {
                width: 100%;
            }

            .custom-table th {
                text-align: center;
            }

            tr.highlighted td {
                background: darkgray;
            }

            input#venue_date {
                width: 50%;
            }

            tr.highlighted td {
                background: darkgray;
                color: white;
                font-weight: 700;
            }
            #website-total-dua , #website-total ,#website-total-dum , #grand-total,#website-total-wldua,#website-total-wldum{
            text-align: center;
        }
                    #spinner-div {
                position: absolute;
                display: none;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                text-align: center;
                background-color: rgba(255, 255, 255, 0.8);
                z-index: 2;
            }

            .dt-buttons {
                float: right !important;
            }
        </style>

        <div class="row">
            @can('user-management-access')
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>Filtered Entries</h5>
                            <input type="date" name="venue_date" id="venue_date" value="{{ date('Y-m-d') }}"
                                class="form-control filter-input w-80">
                            <button type="button" id="filterBtn" class="btn btn-info"> filter </button>
                            <button id="generatePdfBtn" class="btn btn-dark">Generate PDF</button>



                        </div>
                        <div class="card-body">
                            <div id="spinner-div" class="pt-5">
                                <div class="spinner-border text-primary" role="status">
                                </div>
                            </div>
                            <table class="table custom-table" id="tokenTable">

                                <thead>
                                    <tr class=" ">
                                        <td></td>
                                        <td>
                                            <div class="title text-center">
                                                <img src="{{ asset('assets/theme/img/logo.png') }}" alt=""
                                                    style="height: 70px; width:70px">
                                                <h4>DUA / DUM TOKENS SUMMARY - <span id="tbdate">{{ date('l d-M-Y') }}</span>
                                                </h4>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left">Row Label</th>
                                        <th>Count of Token</th>
                                        <th>Check-in</th>
                                        <th>Print</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr class="highlighted">
                                        <td>Website</td>
                                        <td id="website-total">0</td>
                                        <td id="website-checkIn">0</td>
                                        <td id="website-printToken">0</td>
                                        <td id="website-total-percentage">0%</td>
                                    </tr>
                                    <tr>
                                        <td>Website (Dua)</td>
                                        <td id="website-total-dua">0</td>
                                        <td id="website-checkIn-dua">0</td>
                                        <td id="website-printToken-dua">0</td>
                                        <td id="website-total-percentage-dua">0%</td>
                                    </tr>
                                    <tr>
                                        <td>Website (Dum)</td>
                                        <td id="website-total-dum">0</td>
                                        <td id="website-checkIn-dum">0</td>
                                        <td id="website-printToken-dum">0</td>
                                        <td id="website-total-percentage-dum">0%</td>
                                    </tr>

                                    <tr>
                                        <td>Website (Working Lady Dua)</td>
                                        <td id="website-total-wldua">0</td>
                                        <td id="website-checkIn-wldua">0</td>
                                        <td id="website-printToken-wldua">0</td>
                                        <td id="website-total-percentage-wldua">0%</td>
                                    </tr>
                                    <tr>
                                        <td>Website (Working Lady Dum)</td>
                                        <td id="website-total-wldum">0</td>
                                        <td id="website-checkIn-wldum">0</td>
                                        <td id="website-printToken-wldum">0</td>
                                        <td id="website-total-percentage-wldum">0%</td>
                                    </tr>

                                    {{-- <tr class="highlighted">
                                        <td>WhatsApp</td>
                                        <td id="whatsapp-total">0</td>
                                        <td id="whatsapp-total-percentage">0%</td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <td>WhatsApp (Dua)</td>
                                        <td id="whatsapp-total-dua">0</td>
                                        <td id="whatsapp-total-percentage-dua">0%</td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <td>WhatsApp (Dum)</td>
                                        <td id="whatsapp-total-dum">0</td>
                                        <td id="whatsapp-total-percentage-dum">0%</td>
                                    </tr> --}}

                                    <tr class="highlighted">
                                        <td>Grand Total</td>
                                        <td id="grand-total">0</td>
                                        <td id="grand-checkIn">0</td>
                                        <td id="grand-printToken">0</td>
                                        <td id="grand-percentage">0%</td>
                                    </tr>
                                </tbody>
                            </table>



                        </div>
                    </div>
                </div>


                <div class="col-md-6 d-none">
                    <div class="card">
                        <div class="card-header">Percentage Calculation</div>
                        <div class="card-body">
                            <p id="whatsappPercentage">WhatsApp Percentage: <span></span></p>
                            <p id="websitePercentage">Website Percentage: <span></span></p>
                            <p id="websiteDua">Website Dua Percentage: <span></span></p>
                            <p id="websiteDum">Website Dum Percentage: <span></span></p>
                            <p id="whatsAppDua">whatsApp Dua Percentage: <span></span></p>
                            <p id="whatsAppDum">whatsApp Dum Percentage: <span></span></p>

                            <p id="duatoken">Total Dua: <span></span></p>
                            <p id="dumtoken">Total Dum: <span></span></p>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>Tokens</h5>
                            <select class="form-control" name="dua_type" id="dua_type" style="width: 40%">
                                <option value=""> All</option>
                                <option value="dua"> Dua</option>
                                <option value="dum"> Dum</option>
                            </select>
                            <input type="date" name="created_at" id="table_date" class="form-control w-80"
                                style="width: 40%">
                            <button type="button" id="filtertable" class="btn btn-info"> filter </button>

                        </div>
                        <div class="card-body">
                            <div id="spinner-div" class="pt-5">
                                <div class="spinner-border text-primary" role="status">
                                </div>
                            </div>
                            <table id="datatable" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Token</th>
                                        <th>Date</th>
                                        <th>Dua Ghar</th>
                                        {{-- <th>Country Code</th> --}}
                                        <th>Phone</th>
                                        <th>Source</th>
                                        {{-- <th>Session Image </th> --}}
                                        <th>Token Url Link</th>

                                    </tr>
                                </thead>
                            </table>





                        </div>
                    </div>
                </div>
            </div>
        @endcan
        <div class="row d-none">
            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">


                    <!-- Revenue Card -->


                    <!-- End Revenue Card -->

                    <!-- Customers Card -->
                    <div class="col-xxl-4 col-xl-12  ">

                        <div class="card info-card customers-card">

                            <div class="filter">

                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Vistors <span>Total Vistors</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $visitos }}</h6>
                                        {{-- <span class="text-danger small pt-1 fw-bold">12% </span> <span class="text-muted small pt-2 ps-1">decrease</span> --}}

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-xxl-4 col-xl-12  ">

                        <div class="card info-card customers-card">

                            <div class="filter">

                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Sahib-e-Dua<span>Total Sahib-e-Dua</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $userCountWiththripistRole }}</h6>
                                        {{-- <span class="text-danger small pt-1 fw-bold">12% </span> <span class="text-muted small pt-2 ps-1">decrease</span> --}}

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-xxl-4 col-xl-12  ">

                        <div class="card info-card customers-card">

                            <div class="filter">

                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Field Admin <span>Total Field Admin</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $userCountWithsiteadminRole }}</h6>
                                        {{-- <span class="text-danger small pt-1 fw-bold">12% </span> <span class="text-muted small pt-2 ps-1">decrease</span> --}}

                                    </div>
                                </div>

                            </div>


                        </div>

                    </div>
                    <!-- Reports -->
                    <div class="col-12 d-none">
                        <div class="card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Reports <span>/Today</span></h5>

                                <!-- Line Chart -->
                                <div id="reportsChart"></div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new ApexCharts(document.querySelector("#reportsChart"), {
                                            series: [{
                                                name: 'Sales',
                                                data: [31, 40, 28, 51, 42, 82, 56],
                                            }, {
                                                name: 'Revenue',
                                                data: [11, 32, 45, 32, 34, 52, 41]
                                            }, {
                                                name: 'Customers',
                                                data: [15, 11, 32, 18, 9, 24, 11]
                                            }],
                                            chart: {
                                                height: 350,
                                                type: 'area',
                                                toolbar: {
                                                    show: false
                                                },
                                            },
                                            markers: {
                                                size: 4
                                            },
                                            colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                            fill: {
                                                type: "gradient",
                                                gradient: {
                                                    shadeIntensity: 1,
                                                    opacityFrom: 0.3,
                                                    opacityTo: 0.4,
                                                    stops: [0, 90, 100]
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'smooth',
                                                width: 2
                                            },
                                            xaxis: {
                                                type: 'datetime',
                                                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z",
                                                    "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z",
                                                    "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z",
                                                    "2018-09-19T06:30:00.000Z"
                                                ]
                                            },
                                            tooltip: {
                                                x: {
                                                    format: 'dd/MM/yy HH:mm'
                                                },
                                            }
                                        }).render();
                                    });
                                </script>
                                <!-- End Line Chart -->

                            </div>

                        </div>
                    </div><!-- End Reports -->

                    <!-- Recent Sales -->
                    <div class="col-12 d-none">
                        <div class="card recent-sales overflow-auto">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Recent Sales <span>| Today</span></h5>

                                <table class="table table-borderless datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Customer</th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row"><a href="#">#2457</a></th>
                                            <td>Brandon Jacob</td>
                                            <td><a href="#" class="text-primary">At praesentium minu</a></td>
                                            <td>$64</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#">#2147</a></th>
                                            <td>Bridie Kessler</td>
                                            <td><a href="#" class="text-primary">Blanditiis dolor omnis
                                                    similique</a></td>
                                            <td>$47</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#">#2049</a></th>
                                            <td>Ashleigh Langosh</td>
                                            <td><a href="#" class="text-primary">At recusandae consectetur</a></td>
                                            <td>$147</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#">#2644</a></th>
                                            <td>Angus Grady</td>
                                            <td><a href="#" class="text-primar">Ut voluptatem id earum et</a></td>
                                            <td>$67</td>
                                            <td><span class="badge bg-danger">Rejected</span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#">#2644</a></th>
                                            <td>Raheem Lehner</td>
                                            <td><a href="#" class="text-primary">Sunt similique distinctio</a></td>
                                            <td>$165</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div><!-- End Recent Sales -->

                    <!-- Top Selling -->
                    <div class="col-12 d-none">
                        <div class="card top-selling overflow-auto">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body pb-0">
                                <h5 class="card-title">Top Selling <span>| Today</span></h5>

                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th scope="col">Preview</th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Sold</th>
                                            <th scope="col">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-1.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Ut inventore ipsa voluptas
                                                    nulla</a></td>
                                            <td>$64</td>
                                            <td class="fw-bold">124</td>
                                            <td>$5,828</td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-2.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Exercitationem similique
                                                    doloremque</a></td>
                                            <td>$46</td>
                                            <td class="fw-bold">98</td>
                                            <td>$4,508</td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-3.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Doloribus nisi
                                                    exercitationem</a></td>
                                            <td>$59</td>
                                            <td class="fw-bold">74</td>
                                            <td>$4,366</td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-4.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Officiis quaerat sint rerum
                                                    error</a></td>
                                            <td>$32</td>
                                            <td class="fw-bold">63</td>
                                            <td>$2,016</td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-5.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Sit unde debitis delectus
                                                    repellendus</a></td>
                                            <td>$79</td>
                                            <td class="fw-bold">41</td>
                                            <td>$3,239</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div><!-- End Top Selling -->

                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4 d-none">

                <!-- Recent Activity -->
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Recent Activity <span>| Today</span></h5>

                        <div class="activity">

                            <div class="activity-item d-flex">
                                <div class="activite-label">32 min</div>
                                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                <div class="activity-content">
                                    Quia quae rerum <a href="#" class="fw-bold text-dark">explicabo officiis</a>
                                    beatae
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">56 min</div>
                                <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                                <div class="activity-content">
                                    Voluptatem blanditiis blanditiis eveniet
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">2 hrs</div>
                                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                <div class="activity-content">
                                    Voluptates corrupti molestias voluptatem
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">1 day</div>
                                <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                                <div class="activity-content">
                                    Tempore autem saepe <a href="#" class="fw-bold text-dark">occaecati
                                        voluptatem</a> tempore
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">2 days</div>
                                <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                                <div class="activity-content">
                                    Est sit eum reiciendis exercitationem
                                </div>
                            </div><!-- End activity item-->

                            <div class="activity-item d-flex">
                                <div class="activite-label">4 weeks</div>
                                <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                                <div class="activity-content">
                                    Dicta dolorem harum nulla eius. Ut quidem quidem sit quas
                                </div>
                            </div><!-- End activity item-->

                        </div>

                    </div>
                </div><!-- End Recent Activity -->

                <!-- Budget Report -->
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">Budget Report <span>| This Month</span></h5>

                        <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                var budgetChart = echarts.init(document.querySelector("#budgetChart")).setOption({
                                    legend: {
                                        data: ['Allocated Budget', 'Actual Spending']
                                    },
                                    radar: {
                                        // shape: 'circle',
                                        indicator: [{
                                                name: 'Sales',
                                                max: 6500
                                            },
                                            {
                                                name: 'Administration',
                                                max: 16000
                                            },
                                            {
                                                name: 'Information Technology',
                                                max: 30000
                                            },
                                            {
                                                name: 'Customer Support',
                                                max: 38000
                                            },
                                            {
                                                name: 'Development',
                                                max: 52000
                                            },
                                            {
                                                name: 'Marketing',
                                                max: 25000
                                            }
                                        ]
                                    },
                                    series: [{
                                        name: 'Budget vs spending',
                                        type: 'radar',
                                        data: [{
                                                value: [4200, 3000, 20000, 35000, 50000, 18000],
                                                name: 'Allocated Budget'
                                            },
                                            {
                                                value: [5000, 14000, 28000, 26000, 42000, 21000],
                                                name: 'Actual Spending'
                                            }
                                        ]
                                    }]
                                });
                            });
                        </script>

                    </div>
                </div><!-- End Budget Report -->

                <!-- Website Traffic -->
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">Website Traffic <span>| Today</span></h5>

                        <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                echarts.init(document.querySelector("#trafficChart")).setOption({
                                    tooltip: {
                                        trigger: 'item'
                                    },
                                    legend: {
                                        top: '5%',
                                        left: 'center'
                                    },
                                    series: [{
                                        name: 'Access From',
                                        type: 'pie',
                                        radius: ['40%', '70%'],
                                        avoidLabelOverlap: false,
                                        label: {
                                            show: false,
                                            position: 'center'
                                        },
                                        emphasis: {
                                            label: {
                                                show: true,
                                                fontSize: '18',
                                                fontWeight: 'bold'
                                            }
                                        },
                                        labelLine: {
                                            show: false
                                        },
                                        data: [{
                                                value: 1048,
                                                name: 'Search Engine'
                                            },
                                            {
                                                value: 735,
                                                name: 'Direct'
                                            },
                                            {
                                                value: 580,
                                                name: 'Email'
                                            },
                                            {
                                                value: 484,
                                                name: 'Union Ads'
                                            },
                                            {
                                                value: 300,
                                                name: 'Video Ads'
                                            }
                                        ]
                                    }]
                                });
                            });
                        </script>

                    </div>
                </div><!-- End Website Traffic -->

                <!-- News & Updates Traffic -->
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">News &amp; Updates <span>| Today</span></h5>
                        <div class="news">
                            <div class="post-item clearfix">
                                <img src="assets/img/news-1.jpg" alt="">
                                <h4><a href="#">Nihil blanditiis at in nihil autem</a></h4>
                                <p>Sit recusandae non aspernatur laboriosam. Quia enim eligendi sed ut harum...</p>
                            </div>

                            <div class="post-item clearfix">
                                <img src="assets/img/news-2.jpg" alt="">
                                <h4><a href="#">Quidem autem et impedit</a></h4>
                                <p>Illo nemo neque maiores vitae officiis cum eum turos elan dries werona nande...</p>
                            </div>

                            <div class="post-item clearfix">
                                <img src="assets/img/news-3.jpg" alt="">
                                <h4><a href="#">Id quia et et ut maxime similique occaecati ut</a></h4>
                                <p>Fugiat voluptas vero eaque accusantium eos. Consequuntur sed ipsam et totam...</p>
                            </div>

                            <div class="post-item clearfix">
                                <img src="assets/img/news-4.jpg" alt="">
                                <h4><a href="#">Laborum corporis quo dara net para</a></h4>
                                <p>Qui enim quia optio. Eligendi aut asperiores enim repellendusvel rerum cuder...</p>
                            </div>

                            <div class="post-item clearfix">
                                <img src="assets/img/news-5.jpg" alt="">
                                <h4><a href="#">Et dolores corrupti quae illo quod dolor</a></h4>
                                <p>Odit ut eveniet modi reiciendis. Atque cupiditate libero beatae dignissimos eius...</p>
                            </div>

                        </div><!-- End sidebar recent posts-->

                    </div>
                </div><!-- End News & Updates -->

            </div><!-- End Right side columns -->

        </div>
    </section>
@endsection

@section('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
    <script>
        $(document).ready(function() {
            var today = "{{ date('Y-m-d') }}";
            filterDuaEntries(today, '')
            // Function to fetch filtered dua entries
            function filterDuaEntries(date, type) {
                $.ajax({
                    url: '{{ route('dashboard.filter') }}',
                    method: 'POST',
                    data: {
                        date: date,
                        type: type,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $("#spinner-div").hide();
                        var res = response.calculations;

                        $.each(res, function(target, item) {

                            $("#" + target).text(item)

                        })


                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            // Function to fetch percentage calculation
            function fetchPercentage(date) {
                $.ajax({
                    url: '{{ route('dashboard.percentage') }}',
                    method: 'POST',
                    data: {
                        date: date,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#websiteDua span').text(response.websiteDua.toFixed(2) + '%');
                        $('#websiteDum span').text(response.websiteDum.toFixed(2) + '%');

                        $('#whatsAppDua span').text(response.whatsAppDua.toFixed(2) + '%');
                        $('#whatsAppDum span').text(response.whatsAppDum.toFixed(2) + '%');

                        $('#duatoken span').text(response.totalBookDua + ' / ' + response.duatoken);
                        $('#dumtoken span').text(response.totalBookDum + ' / ' + response.dumtoken);


                        $('#whatsappPercentage span').text(response.whatsapp_percentage.toFixed(2) +'%');
                        $('#websitePercentage span').text(response.website_percentage.toFixed(2) + '%');
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            // Call the fetchPercentage function on page load
            // fetchPercentage();

            // Example: Call filterDuaEntries function on filter button click
            $('#filterBtn').click(function() {
                $("#spinner-div").show();
                var date = $('#venue_date').val();
                var type = $('#typeFilter').val();
                $("#tbdate").text(new Date(date).toDateString());
                filterDuaEntries(date, type);
                // fetchPercentage(date)
            });
        });
    </script>
    <script>
        $("#generatePdfBtn").click(function() {

            $("#spinner-div").show();


            var is = downloadPdf()


        })

        function downloadPdf() {

            const element = document.getElementById('tokenTable');

            const formattedDate = new Date().toLocaleDateString('en-GB').replace(/\//g, '-');
            const options = {
                margin: 10,
                format: 'a4',
                filename: "{{ date('dMY') }}" + '-report.pdf',
                // image: {
                //     type: 'jpeg',
                //     quality: 1.0
                // },
                html2canvas: {
                    scale: 1
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };
            $(".myheading").addClass('d-none')
            $("#spinner-div").hide();

            return html2pdf(element, options);
        }

        $(document).ready(function() {
            var filename;

            $(document).ready(function() {
                $('#filtertable').on('click', function() {
                    var duaType = $('#dua_type').find(':selected').val();
                    var venueDate = $('#table_date').val();
                    $(this).attr('data-type', duaType)
                    $(this).attr('data-date', venueDate)
                    filename = 'DUA/DUM TOKENS - '+new Date().toDateString() +'- LAHORE   DUA GHAR';
                    if(duaType == 'dua'){
                        filename = 'DUA TOKENS - '+new Date().toDateString() +'- LAHORE   DUA GHAR';
                    }
                    if(duaType == 'dum'){
                        filename = 'DUM TOKENS - '+new Date().toDateString() +'- LAHORE   DUA GHAR';
                    }
                    document.title  = filename

                    $('#datatable').DataTable().search(duaType + ' ' + venueDate).draw();


                });
            });


            $('#datatable').DataTable({
                "dom": 'lBfrtip',
                scrollX: true,
                "processing": true,
                "pageLength": 100,
                "serverSide": true,
                "ajax": "{{ route('dashboard.data') }}",
                "columns": [{
                        "data": "token"
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "dua_ghar"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": "source"
                    },
                    // {
                    //     "data": "recognized_code",
                    //     "render": function(data, type, row, meta) {
                    //         var image  =  '';
                    //         if(data!==''){
                    //             image = `<img src="data:image/jpeg;base64,${data}" alt="Preview Image"
                    //                  style="height: 150px; width:150px;border-radius:20%">`;
                    //         }else{
                    //            image  =`<img style="height: 150px; width:150px;border-radius:20%" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAMAAADDpiTIAAAC+lBMVEUAAAAfHxkdHRscHBoeHh4dHRscHBodHRsdHRsdHRsYGBgdHR0fHx8fHx8dHRsdHRsdHRscHBoTExMdHRsXFxccHBscHBobGxscHBkcHBsdHRodHRocHBocHBscHBscHBwcHBscHBwfHxceHhkbGxsdHRsVFRUcHBocHBocHBskJCQcHBodHRocHBobGxsdHRofHx8cHBocHBskJBIdHRscHBwdHRscHBscHBocHBsdHRsdHRkcHBscHBscHBscHBwdHRocHBsdHRsdHRogIBgdHRsdHRkcHBocHBseHhgcHBwaGhocHBodHRocHBsbGxsdHRscHBscHBsdHRocHBodHRobGxsdHRsdHRsdHRocHBoeHh4dHRscHBsZGRkeHhscHBkcHBoeHhshIRYdHRodHRsdHRseHhkdHRodHR0eHhscHBsdHRscHBocHBsaGhodHRsbGxsdHRobGxscHBwdHRocHBscHBwdHRocHBsdHRocHBsdHRodHRkcHBodHRodHRscHBocHBodHRkdHRscHBodHRscHBwdHRocHBkcHBobGxsdHRscHBodHRoeHhodHRsdHRscHBwcHBodHRocHBsaGhofHxobGxsfHxgcHBodHR0dHRscHBocHBwcHBwcHBkcHBscHBodHRodHRsdHRocHBodHRodHRsbGxsdHRodHRodHRscHBocHBwcHBodHRoeHhscHBseHhsdHRobGxsaGhocHBocHBoeHhodHRoeHhscHBodHRodHRocHBscHBodHRscHBkcHBsdHRseHhkdHRsdHRocHBsdHRocHBwcHBwcHBsdHRodHRobGxsbGxsaGhoaGhocHBodHRscHBocHBwdHRodHRodHRodHRseHhoZGRkcHBodHRsbGxseHhccHBwcHBodHRodHRscHBocHBsiIiIdHRsdHRoXFxcdHRscHBozMzMeHhsdHR0cHBocHBsdHRsZGRkdHRoqKiodHRkAAAAcHBwAAAAAAAAAAAAeHh4eHhthjin5AAAA/nRSTlMAKP/nIv74/fv8FRoQCPPZgPcN9Qvfhy5sxb/2+bDCCfFRIDNSpgxYc8wHq1eiSbcYa7oObxvY1vCX8k+osaBHvtV60x+KPYjgKjY57prNHIGqvMjU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" />`;

                    //         }

                    //         return image;
                    //     }
                    // },
                    {
                        "data": "token_url_link",
                        "render": function(data, type, row, meta) {
                            $("#filtertable").attr('data-duaghar', row.dua_ghar);
                            return '<a href="' + data + '" target="_blank">' + data + '</a>';
                        }
                    }

                ],
                "buttons": [{
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        filename: filename,
                        customize: function(doc) {
                            // Add logo
                            var logo ='data:image/png;base64,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'; // Base64 encoded logo image
                            doc.content.splice(0, 0, {
                                image: logo,
                                width: 70,
                                alignment: 'center'
                            });

                            // Add dynamic heading
                            var type = $('#filtertable').attr('data-type');
                            var date = $('#filtertable').attr('data-date');
                            var duaghar = $('#filtertable').attr('data-duaghar');
                            console.log("duaghar",duaghar)
                            duaghar = duaghar.replace("/ dua", "");
                            duaghar = duaghar.replace("/ dum", "");



                            var today = new Date();
                            var heading = 'DUA/DUM TOKENS - ' + today.toDateString() + ' - ' +
                                duaghar.toUpperCase() + ' DUA GHAR';

                            if (type !== undefined && date !== undefined) {
                                var todayDate = new Date(date);
                                heading = type.toUpperCase() + ' TOKENS - ' + todayDate
                                    .toDateString() + ' - ' + duaghar.toUpperCase() + ' DUA GHAR';
                            }


                            document.title  = heading
                            $("#filtertable").attr('data-filename' ,heading)
                            // doc.content.splice(1, 2);

                            doc.content.splice(1, 0, {
                                text: heading,
                                fontSize: 16,
                                alignment: 'center',
                                margin: [0, 0, 0, 20] // Top, right, bottom, left margins
                            });
                            doc.content.splice(2, 1);
                        }
                    },
                    'csv', 'excel'
                ],

                "columnDefs": [{
                    "targets": [5], // index of the token_url_link column
                    "width": "500px" // set width to 100%
                }],
                "lengthMenu": ['10', '25', '50', '100', '500', '1000', '1500', '2000', '2500', '3000']

            });

        });
    </script>
@endsection
