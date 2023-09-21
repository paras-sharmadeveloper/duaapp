@extends('layouts.app')

@section('content')
<div class="call-view">
    <div class="call-view__tracks">
      <div class="remote-track--container">
        <div style="width: 100%; height: 100%; background-image: url('https://avyannaattracttactic.review/wp-content/uploads/2017/08/1503266040_maxresdefault.jpg'); background-repeat: no-repeat; background-position: center; background-size: cover;"></div>
      </div>
  
      <div class="remote-track--container">
        <div class="remote-track--picture-placeholder--container">
          <div class="remote-track--picture-placeholder__background" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Hope_Sports.jpg/220px-Hope_Sports.jpg');"></div>
          <div class="remote-track--picture-placeholder" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Hope_Sports.jpg/220px-Hope_Sports.jpg');"></div>
        </div>
  
        <div class="input-status-container">
          <div class="input-status">
            <i class="material-icons-round" style="color: #FAFAFA;">mic_off</i>
          </div>
          <div class="input-status">
            <i class="material-icons-round" style="color: #FAFAFA;">videocam_off</i>
          </div>
        </div>
      </div>
  
      <div class="remote-track--container">
        <div class="remote-track--picture-placeholder--container">
          <div class="remote-track--picture-placeholder__background" style="background-image: url('https://www.nzaf.org.nz/assets/ee-uploads/cache/6e456c4c746cba65/Guy-sample_376_268_s_c1.jpg');"></div>
          <div class="remote-track--picture-placeholder speaking" style="background-image: url('https://www.nzaf.org.nz/assets/ee-uploads/cache/6e456c4c746cba65/Guy-sample_376_268_s_c1.jpg');"></div>
        </div>
  
        <div class="input-status-container">
          <div class="input-status">
            <i class="material-icons-round" style="color: #FAFAFA;">videocam_off</i>
          </div>
        </div>
      </div>
      
      <div class="call-view__tracks__local-track-container">
        <div class="call-view__tracks__local-track">
          <video></video>
        </div>
      </div>
    </div>
  
    <div class="call-view__controls-container">
      <div class="call-view__controls">
        <div id="btn--end-call" class="call-view__controls__icon-btn important">
          <i class="material-icons-round" style="color: #FAFAFA;">call_end</i>
        </div>
        <div id="btn--toggle-mic" class="call-view__controls__icon-btn">
          <i class="material-icons-round" style="color: #FF3346;">mic_off</i>
        </div>
        <div id="btn--toggle-cam" class="call-view__controls__icon-btn">
          <i class="material-icons-round" style="color: #FF3346;">videocam_off</i>
        </div>
        <div id="btn--toggle-screen-sharing" class="call-view__controls__icon-btn">
          <i class="material-icons-round" style="color: #27A4FD;">screen_share</i>
        </div>
        <div id="btn--settings" class="call-view__controls__icon-btn">
          <i class="material-icons-round" style="color: #27A4FD;">settings</i>
        </div>
      </div>
    </div>
  </div>
  @endsection