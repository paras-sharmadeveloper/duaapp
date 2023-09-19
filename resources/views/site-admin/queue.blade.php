@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">

        <div class="action-top float-end mb-3"> 
            <a class="btn btn-outline-primary" href="{{ route('roles.index') }}"> <i class="bi bi-skip-backward-circle me-1"></i> Back</a>
        </div>
         
    </div>
</div>

@if (count($errors) > 0)
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
       @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
       @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-1"></i>
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
 
@endif
<div class="card">
    <div class="card-body">
      <h5 class="card-title">Manage Queue</h5>
      @if(request()->route()->getName() == 'siteadmin.queue.show')
      <section class="services section-bg" id="services">
        <div class="container">
          <header class="section-header">
            <h3>Venues</h3>
      
            {{-- <p>Laudem latine persequeris id sed, ex fabulas delectus quo. No vel partiendo abhorreant vituperatoribus.</p> --}}
          </header>
          
          <div class="row">
            @foreach($venueAddress as $venueAdd )
            @php 

            $startTimeFormatted = \Carbon\Carbon::parse($venueAdd->slot_starts_at)->format('h:i A');
            $endTimeFormatted = \Carbon\Carbon::parse($venueAdd->slot_ends_at)->format('h:i A');
        @endphp
        <a href="{{ route('siteadmin.queue.list',[$venueAdd->id])}}">
            <div class="col-md-6 col-lg-4">
              <div class="box">
                <div class="icon" >
                    @if (!empty($venueAdd->user->profile_pic) && Storage::disk('s3_general')->exists('images/' . $venueAdd->user->profile_pic))
                    <img src="{{ env('AWS_GENERAL_PATH').'images/'.$venueAdd->user->profile_pic }}" class="imgh" alt="Flag Image" style="height: 65px; width: 65px;">
                    @else
                        <img src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg" class="imgh" alt="Default Image"
                            style="height: 65px; width: 65px;">
                    @endif
                  {{-- <i class="fa fa-briefcase service-icon" style="color: #c59c35;"></i> --}}
                </div> 
                <h4 class="title">{{ $venueAdd->user->name }}</h4>
                <h2 class="sub-title">{{ $venueAdd->venue_date }}</h2>
                <h3 class="sub-title">{{ $startTimeFormatted }} - {{ $endTimeFormatted }}</h3>
                <span class="sr"><strong>{{ $venueAdd->venue->country_name }} ({{  $venueAdd->state  }})</strong></span>
                <p class="description text-center">{{ $venueAdd->city }}</p>
                <p class="description text-center">{{ $venueAdd->address }}</p>
              </div>
            </div> 
        </a>
            @endforeach
      
          </div>
        </div>
      </section>
      @endif

      @if(request()->route()->getName() == 'siteadmin.queue.list')
 
        <table class="table table-bordered datatable table-striped ">
          <thead>
            <tr>
              <th scope="col">BookingId</th>
              <th scope="col">UserName</th>
              <th scope="col">BookingTime</th>
              <th scope="col">Confirmed</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($venueSloting  as $slots)
              @foreach($slots->visitors  as $slot)
            <tr>
              <th scope="row">{{ $slot->booking_number }}</th>
              <td>{{ $slot->fname.' ' .$slot->lname }}</td>
              <td>{{ $slots->slot_time }}</td>
              <td>
                @if($slot->is_available == 'confirmed')
                    <span class="badge bg-success"> Confirmed ({{ \Carbon\Carbon::parse($slot->confirmed_at)->format('d-m-y h:i A')  }})</span>
                @else
                <span class="badge bg-danger"> Not Confirmed </span>
                @endif
              </td>
              <td>
                <button type="button" class="btn btn-primary"><i class="far fa-eye"></i></button>
                <button type="button" class="btn btn-success"><i class="fas fa-edit"></i></button>
              <button type="button" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
              </td>
            </tr>
              @endforeach
            @endforeach
             
          </tbody>
        </table>
        @endif
      </div>
    </div>
  </div>
<style>
a{ color: #444444;}
.container {
  max-width: 1320px;
}
section {
  overflow: hidden;
}

/* .section-bg {
  background: #f5f8fd;
} */

.section-header h3 {
  font-size: 36px;
  color: #413e66;
  text-align: center;
  font-weight: 700;
  position: relative;
  font-family: "Montserrat", sans-serif;
}

.section-header p {
  text-align: center;
  margin: auto;
  font-size: 15px;
  padding-bottom: 60px;
  color: #535074;
  width: 50%;
}

@media (max-width: 767px) {
  .section-header p {
    width: 100%;
  }
}

#services {
  padding: 60px 0 40px 0;
}

#services .box {
  padding: 30px;
  position: relative;
  overflow: hidden;
  border-radius: 10px;
  margin: 0 10px 40px 10px;
  background: #fff;
  box-shadow: 0 10px 29px 0 rgba(68, 88, 144, 0.1);
  transition: all 0.3s ease-in-out;
  text-align: center;
}

#services .box:hover {
  transform: scale(1.1);
}

#services .icon {
  margin: 0 auto 15px auto;
  padding-top: 12px;
  display: inline-block;
  text-align: center;
  border-radius: 50%;
  width: 60px;
  height: 60px;
}

#services .icon .service-icon {
  font-size: 36px;
  line-height: 1;
}

#services .title {
  font-weight: 700;
  margin-bottom: 15px;
  font-size: 18px;
}

#services .title a {
  color: #111;
}

#services .box:hover .title a {
  color: #c59c35;
}
#services .box:hover .title a:hover {
  text-decoration: none;
}
#services .description {
  font-size: 14px;
  line-height: 28px;
  margin-bottom: 0;
  text-align: left;
}
</style>
@endsection