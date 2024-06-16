@extends('layouts.guest')
@section('content')
<style>
    .center {
  margin: 0 auto;
  text-align: center;
}
#processing {
  position: relative;
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 15px;
  background-color: rgb(29, 29, 29);
}
#processing.uncomplete .gear-wrapper-1 {
  -webkit-animation: gearEnter1 0.5s ease 1s forwards;
          animation: gearEnter1 0.5s ease 1s forwards;
}
#processing.uncomplete .gear-wrapper-2 {
  -webkit-animation: gearEnter2 0.5s ease 1.5s forwards;
          animation: gearEnter2 0.5s ease 1.5s forwards;
}
#processing.uncomplete .gear-wrapper-3 {
  -webkit-animation: gearEnter3 0.5s ease 1.25s forwards;
          animation: gearEnter3 0.5s ease 1.25s forwards;
}
#processing.uncomplete .gear-wrapper-4 {
  -webkit-animation: gearEnter4 0.5s ease 0.75s forwards;
          animation: gearEnter4 0.5s ease 0.75s forwards;
}
#processing.complete .gears {
  background-color: transparent;
  transition: background-color 0.25s ease 0.75s;
}
#processing.complete .gear-wrapper-1 {
  transform: rotate(10deg) translate3d(-5px, -5px, 0);
  -webkit-animation: gearLeave1 0.5s ease 0.5s forwards;
          animation: gearLeave1 0.5s ease 0.5s forwards;
}
#processing.complete .gear-wrapper-2 {
  transform: rotate(25deg) translate3d(20px, -50px, 0);
  -webkit-animation: gearLeave2 0.5s ease 0.5s forwards;
          animation: gearLeave2 0.5s ease 0.5s forwards;
}
#processing.complete .gear-wrapper-3 {
  transform: rotate(15deg) translate3d(-25px, -15px, 0);
  -webkit-animation: gearLeave3 0.5s ease 0.5s forwards;
          animation: gearLeave3 0.5s ease 0.5s forwards;
}
#processing.complete .gear-wrapper-4 {
  transform: translate3d(0, 0, 0);
  -webkit-animation: gearLeave4 0.5s ease 0.5s forwards;
          animation: gearLeave4 0.5s ease 0.5s forwards;
}
#processing.complete .loading-bar {
  -webkit-animation: hideLoading 0.5s forwards;
          animation: hideLoading 0.5s forwards;
}
#processing.complete .checkmark.checkmark-success {
  -webkit-animation: fillCheckSuccess 0.4s cubic-bezier(0.65, 0, 0.45, 1) 1s forwards, beatCheck 0.2s ease-out 1.5s forwards, echoSuccess 1.25s ease-out 1.5s forwards;
          animation: fillCheckSuccess 0.4s cubic-bezier(0.65, 0, 0.45, 1) 1s forwards, beatCheck 0.2s ease-out 1.5s forwards, echoSuccess 1.25s ease-out 1.5s forwards;
}
#processing.complete .checkmark.checkmark-success .checkmark-circle {
  stroke: #3c763d;
}
#processing.complete .checkmark.checkmark-error {
  -webkit-animation: fillCheckError 0.4s cubic-bezier(0.65, 0, 0.45, 1) 1s forwards, beatCheck 0.2s ease-out 1.5s forwards, echoError 1.25s ease-out 1.5s forwards;
          animation: fillCheckError 0.4s cubic-bezier(0.65, 0, 0.45, 1) 1s forwards, beatCheck 0.2s ease-out 1.5s forwards, echoError 1.25s ease-out 1.5s forwards;
}
#processing.complete .checkmark.checkmark-error .checkmark-circle {
  stroke: #a94442;
}
#processing.complete .checkmark-circle {
  -webkit-animation: strokeCheck 0.5s cubic-bezier(0.65, 0, 0.45, 1) 0.75s forwards;
          animation: strokeCheck 0.5s cubic-bezier(0.65, 0, 0.45, 1) 0.75s forwards;
}
#processing.complete .checkmark-check {
  -webkit-animation: strokeCheck 0.3s cubic-bezier(0.65, 0, 0.45, 1) 1.25s forwards;
          animation: strokeCheck 0.3s cubic-bezier(0.65, 0, 0.45, 1) 1.25s forwards;
}
h1 {
  color: #fff;
  font-weight: 400;
}
h2 {
  color: #fff;
  font-weight: 300;
}
.wrapper {
  position: relative;
  margin: 30px auto;
}
.gears {
  width: 200px;
  height: 200px;
  background-color: #999;
  border-radius: 50%;
  overflow: hidden;
  transform: scale(0);
  -webkit-animation: scale 0.5s ease 0.5s forwards;
          animation: scale 0.5s ease 0.5s forwards;
}
@-webkit-keyframes scale {
  to {
    transform: scale(1);
  }
}
@keyframes scale {
  to {
    transform: scale(1);
  }
}
.gear-wrapper {
  position: absolute;
}
.gear-wrapper.gear-wrapper-1 {
  top: 0;
  left: 0;
  transform: rotate(10deg) translate3d(-40px, -60px, 0);
}
.gear-wrapper.gear-wrapper-2 {
  top: 0;
  right: 0;
  transform: rotate(25deg) translate3d(70px, -130px, 0);
}
.gear-wrapper.gear-wrapper-3 {
  bottom: 0;
  right: 0;
  transform: rotate(15deg) translate3d(30px, 20px, 0);
}
.gear-wrapper.gear-wrapper-4 {
  bottom: 0;
  left: 0;
  transform: translate3d(-70px, 70px, 0);
}
@-webkit-keyframes gearEnter1 {
  to {
    transform: rotate(10deg) translate3d(-5px, -5px, 0);
  }
}
@keyframes gearEnter1 {
  to {
    transform: rotate(10deg) translate3d(-5px, -5px, 0);
  }
}
@-webkit-keyframes gearEnter2 {
  to {
    transform: rotate(25deg) translate3d(20px, -50px, 0);
  }
}
@keyframes gearEnter2 {
  to {
    transform: rotate(25deg) translate3d(20px, -50px, 0);
  }
}
@-webkit-keyframes gearEnter3 {
  to {
    transform: rotate(15deg) translate3d(-25px, -15px, 0);
  }
}
@keyframes gearEnter3 {
  to {
    transform: rotate(15deg) translate3d(-25px, -15px, 0);
  }
}
@-webkit-keyframes gearEnter4 {
  to {
    transform: translate3d(0, 0, 0);
  }
}
@keyframes gearEnter4 {
  to {
    transform: translate3d(0, 0, 0);
  }
}
@-webkit-keyframes gearLeave1 {
  from {
    transform: rotate(10deg) translate3d(-5px, -5px, 0);
  }
  to {
    transform: rotate(10deg) translate3d(-40px, -60px, 0);
  }
}
@keyframes gearLeave1 {
  from {
    transform: rotate(10deg) translate3d(-5px, -5px, 0);
  }
  to {
    transform: rotate(10deg) translate3d(-40px, -60px, 0);
  }
}
@-webkit-keyframes gearLeave2 {
  from {
    transform: rotate(25deg) translate3d(20px, -50px, 0);
  }
  to {
    transform: rotate(25deg) translate3d(70px, -130px, 0);
  }
}
@keyframes gearLeave2 {
  from {
    transform: rotate(25deg) translate3d(20px, -50px, 0);
  }
  to {
    transform: rotate(25deg) translate3d(70px, -130px, 0);
  }
}
@-webkit-keyframes gearLeave3 {
  from {
    transform: rotate(15deg) translate3d(-25px, -15px, 0);
  }
  to {
    transform: rotate(15deg) translate3d(30px, 20px, 0);
  }
}
@keyframes gearLeave3 {
  from {
    transform: rotate(15deg) translate3d(-25px, -15px, 0);
  }
  to {
    transform: rotate(15deg) translate3d(30px, 20px, 0);
  }
}
@-webkit-keyframes gearLeave4 {
  from {
    transform: translate3d(0, 0, 0);
  }
  to {
    transform: translate3d(-70px, 70px, 0);
  }
}
@keyframes gearLeave4 {
  from {
    transform: translate3d(0, 0, 0);
  }
  to {
    transform: translate3d(-70px, 70px, 0);
  }
}
.gear {
  fill: #e4e4e4;
  transform-origin: 50% 50%;
}
.gear.gear-1 {
  width: 90px;
  height: 90px;
  animation: gearRotate 1s linear 2s infinite reverse;
}
.gear.gear-2 {
  width: 150px;
  height: 150px;
  -webkit-animation: gearRotate 1.5s linear 2s infinite;
          animation: gearRotate 1.5s linear 2s infinite;
}
.gear.gear-3 {
  width: 60px;
  height: 60px;
  animation: gearRotate 0.75s linear 2s infinite reverse;
}
.gear.gear-4 {
  width: 120px;
  height: 110px;
  -webkit-animation: gearRotate 1.25s linear 2s infinite;
          animation: gearRotate 1.25s linear 2s infinite;
}
@-webkit-keyframes gearRotate {
  to {
    transform: rotate(360deg);
  }
}
@keyframes gearRotate {
  to {
    transform: rotate(360deg);
  }
}
.loading-bar {
  position: relative;
  width: 200px;
  height: 10px;
  margin-top: 50px;
  background-color: #e4e4e4;
  border-radius: 10px;
  overflow: hidden;
}
.loading-bar span {
  position: absolute;
  top: 0;
  left: 0;
  width: 0;
  height: 100%;
  background-color: #999;
  transition: width 0.5s ease;
}
@-webkit-keyframes hideLoading {
  to {
    height: 0;
    margin: 0;
    opacity: 0;
  }
}
@keyframes hideLoading {
  to {
    height: 0;
    margin: 0;
    opacity: 0;
  }
}
.checkmark {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100px;
  height: 100px;
  margin-top: -50px;
  margin-left: -50px;
  display: block;
  border-radius: 50%;
  stroke-width: 1px;
  stroke: #fff;
  stroke-miterlimit: 10px;
}
.checkmark-circle {
  fill: none;
  stroke-dasharray: 200px;
  stroke-dashoffset: 200px;
  stroke-width: 1px;
  stroke-miterlimit: 10px;
}
@-webkit-keyframes fillCheckSuccess {
  to {
    box-shadow: inset 0 0 0 100px #3c763d;
  }
}
@keyframes fillCheckSuccess {
  to {
    box-shadow: inset 0 0 0 100px #3c763d;
  }
}
@-webkit-keyframes fillCheckError {
  to {
    box-shadow: inset 0 0 0 100px #a94442;
  }
}
@keyframes fillCheckError {
  to {
    box-shadow: inset 0 0 0 100px #a94442;
  }
}
@-webkit-keyframes beatCheck {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}
@keyframes beatCheck {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}
@-webkit-keyframes echoSuccess {
  from {
    box-shadow: inset 0 0 0 100px #3c763d, 0 0 0 0 #3c763d;
  }
  to {
    box-shadow: inset 0 0 0 100px #3c763d, 0 0 0 50px transparent;
  }
}
@keyframes echoSuccess {
  from {
    box-shadow: inset 0 0 0 100px #3c763d, 0 0 0 0 #3c763d;
  }
  to {
    box-shadow: inset 0 0 0 100px #3c763d, 0 0 0 50px transparent;
  }
}
@-webkit-keyframes echoError {
  from {
    box-shadow: inset 0 0 0 100px #a94442, 0 0 0 0 #a94442;
  }
  to {
    box-shadow: inset 0 0 0 100px #a94442, 0 0 0 50px transparent;
  }
}
@keyframes echoError {
  from {
    box-shadow: inset 0 0 0 100px #a94442, 0 0 0 0 #a94442;
  }
  to {
    box-shadow: inset 0 0 0 100px #a94442, 0 0 0 50px transparent;
  }
}
.checkmark-check {
  stroke: #fff;
  stroke-dasharray: 100px;
  stroke-dashoffset: 100px;
  transform-origin: 50% 50%;
}
@-webkit-keyframes strokeCheck {
  to {
    stroke-dashoffset: 0px;
  }
}
@keyframes strokeCheck {
  to {
    stroke-dashoffset: 0px;
  }
}

#error-container {
            border: 1px solid #ff0000;
            background-color: #ffe0e0;
            padding: 10px;
            margin-top: 10px;
            display: none; /* Initially hide error container */
        }
        #error-message {
            color: #ff0000;
            font-weight: bold;
        }
        #error-message-ur {
            color: #ff0000;
            font-style: italic;
        }

        div#countdown {
    font-size: 46px;
    color: #fff;
}
</style>
    <!-- <button id="trigger">Complete/Reverse (debug)</button> -->
<div id="processing" class="uncomplete center" data-url="{{ route('job.status.check', ['id' => $id]) }}" data-duaRoute="{{ route('book.show')}}">
    <div id="error-container">
        <p id="error-message"></p>
        <p id="error-message-ur"></p>
    </div>

	<div class="headings">
		<h1>Token processing...</h1>
		<h2>Please wait</h2>
        <div id="countdown"></div>

	</div>
	<div class="wrapper">
		<div class="gears">
			<div class="gear-wrapper gear-wrapper-1">
				<svg version="1.1" class="gear gear-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
					<path class="st0" d="M507.6,232.1c0,0-9.3-3.5-36.2-6c-32.9-3-42.8-15.4-53.7-30.7h-0.2c-1.4-3.6-2.8-7.2-4.4-10.8l0.1-0.1
		c-3.1-18.6-4.8-34.3,16.3-59.7C446.7,104,450.8,95,450.8,95c-4-10.3-13.8-20-13.8-20s-9.7-9.7-20-13.8c0,0-9.1,4.1-29.8,21.4
		c-25.4,21.1-41.1,19.4-59.7,16.3l-0.1,0.1c-3.5-1.6-7.1-3.1-10.8-4.4v-0.2c-15.3-10.9-27.7-20.9-30.7-53.7c-2.5-26.9-6-36.2-6-36.2
		C269.8,0,256,0,256,0s-13.8,0-23.9,4.4c0,0-3.5,9.3-6,36.2c-3,32.9-15.4,42.8-30.7,53.7v0.2c-3.6,1.4-7.3,2.8-10.8,4.4l-0.1-0.1
		c-18.6,3.1-34.3,4.8-59.7-16.3C104,65.3,95,61.2,95,61.2C84.7,65.3,75,75,75,75s-9.7,9.7-13.8,20c0,0,4.1,9.1,21.4,29.8
		c21.1,25.4,19.4,41.1,16.3,59.7l0.1,0.1c-1.6,3.5-3.1,7.1-4.4,10.8h-0.2c-10.9,15.4-20.9,27.7-53.7,30.7c-26.9,2.5-36.2,6-36.2,6
		C0,242.3,0,256,0,256s0,13.8,4.4,23.9c0,0,9.3,3.5,36.2,6c32.9,3,42.8,15.4,53.7,30.7h0.2c1.4,3.7,2.8,7.3,4.4,10.8l-0.1,0.1
		c3.1,18.6,4.8,34.3-16.3,59.7C65.3,408,61.2,417,61.2,417c4.1,10.3,13.8,20,13.8,20s9.7,9.7,20,13.8c0,0,9-4.1,29.8-21.4
		c25.4-21.1,41.1-19.4,59.7-16.3l0.1-0.1c3.5,1.6,7.1,3.1,10.8,4.4v0.2c15.3,10.9,27.7,20.9,30.7,53.7c2.5,26.9,6,36.2,6,36.2
		C242.3,512,256,512,256,512s13.8,0,23.9-4.4c0,0,3.5-9.3,6-36.2c3-32.9,15.4-42.8,30.7-53.7v-0.2c3.7-1.4,7.3-2.8,10.8-4.4l0.1,0.1
		c18.6-3.1,34.3-4.8,59.7,16.3c20.8,17.3,29.8,21.4,29.8,21.4c10.3-4.1,20-13.8,20-13.8s9.7-9.7,13.8-20c0,0-4.1-9.1-21.4-29.8
		c-21.1-25.4-19.4-41.1-16.3-59.7l-0.1-0.1c1.6-3.5,3.1-7.1,4.4-10.8h0.2c10.9-15.3,20.9-27.7,53.7-30.7c26.9-2.5,36.2-6,36.2-6
		C512,269.8,512,256,512,256S512,242.2,507.6,232.1z M256,375.7c-66.1,0-119.7-53.6-119.7-119.7S189.9,136.3,256,136.3
		c66.1,0,119.7,53.6,119.7,119.7S322.1,375.7,256,375.7z" />
				</svg>
			</div>
			<div class="gear-wrapper gear-wrapper-2">
				<svg version="1.1" class="gear gear-2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
					<path class="st0" d="M507.6,232.1c0,0-9.3-3.5-36.2-6c-32.9-3-42.8-15.4-53.7-30.7h-0.2c-1.4-3.6-2.8-7.2-4.4-10.8l0.1-0.1
		c-3.1-18.6-4.8-34.3,16.3-59.7C446.7,104,450.8,95,450.8,95c-4-10.3-13.8-20-13.8-20s-9.7-9.7-20-13.8c0,0-9.1,4.1-29.8,21.4
		c-25.4,21.1-41.1,19.4-59.7,16.3l-0.1,0.1c-3.5-1.6-7.1-3.1-10.8-4.4v-0.2c-15.3-10.9-27.7-20.9-30.7-53.7c-2.5-26.9-6-36.2-6-36.2
		C269.8,0,256,0,256,0s-13.8,0-23.9,4.4c0,0-3.5,9.3-6,36.2c-3,32.9-15.4,42.8-30.7,53.7v0.2c-3.6,1.4-7.3,2.8-10.8,4.4l-0.1-0.1
		c-18.6,3.1-34.3,4.8-59.7-16.3C104,65.3,95,61.2,95,61.2C84.7,65.3,75,75,75,75s-9.7,9.7-13.8,20c0,0,4.1,9.1,21.4,29.8
		c21.1,25.4,19.4,41.1,16.3,59.7l0.1,0.1c-1.6,3.5-3.1,7.1-4.4,10.8h-0.2c-10.9,15.4-20.9,27.7-53.7,30.7c-26.9,2.5-36.2,6-36.2,6
		C0,242.3,0,256,0,256s0,13.8,4.4,23.9c0,0,9.3,3.5,36.2,6c32.9,3,42.8,15.4,53.7,30.7h0.2c1.4,3.7,2.8,7.3,4.4,10.8l-0.1,0.1
		c3.1,18.6,4.8,34.3-16.3,59.7C65.3,408,61.2,417,61.2,417c4.1,10.3,13.8,20,13.8,20s9.7,9.7,20,13.8c0,0,9-4.1,29.8-21.4
		c25.4-21.1,41.1-19.4,59.7-16.3l0.1-0.1c3.5,1.6,7.1,3.1,10.8,4.4v0.2c15.3,10.9,27.7,20.9,30.7,53.7c2.5,26.9,6,36.2,6,36.2
		C242.3,512,256,512,256,512s13.8,0,23.9-4.4c0,0,3.5-9.3,6-36.2c3-32.9,15.4-42.8,30.7-53.7v-0.2c3.7-1.4,7.3-2.8,10.8-4.4l0.1,0.1
		c18.6-3.1,34.3-4.8,59.7,16.3c20.8,17.3,29.8,21.4,29.8,21.4c10.3-4.1,20-13.8,20-13.8s9.7-9.7,13.8-20c0,0-4.1-9.1-21.4-29.8
		c-21.1-25.4-19.4-41.1-16.3-59.7l-0.1-0.1c1.6-3.5,3.1-7.1,4.4-10.8h0.2c10.9-15.3,20.9-27.7,53.7-30.7c26.9-2.5,36.2-6,36.2-6
		C512,269.8,512,256,512,256S512,242.2,507.6,232.1z M256,375.7c-66.1,0-119.7-53.6-119.7-119.7S189.9,136.3,256,136.3
		c66.1,0,119.7,53.6,119.7,119.7S322.1,375.7,256,375.7z" />
				</svg>
			</div>
			<div class="gear-wrapper gear-wrapper-3">
				<svg version="1.1" class="gear gear-3" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
					<path class="st0" d="M507.6,232.1c0,0-9.3-3.5-36.2-6c-32.9-3-42.8-15.4-53.7-30.7h-0.2c-1.4-3.6-2.8-7.2-4.4-10.8l0.1-0.1
		c-3.1-18.6-4.8-34.3,16.3-59.7C446.7,104,450.8,95,450.8,95c-4-10.3-13.8-20-13.8-20s-9.7-9.7-20-13.8c0,0-9.1,4.1-29.8,21.4
		c-25.4,21.1-41.1,19.4-59.7,16.3l-0.1,0.1c-3.5-1.6-7.1-3.1-10.8-4.4v-0.2c-15.3-10.9-27.7-20.9-30.7-53.7c-2.5-26.9-6-36.2-6-36.2
		C269.8,0,256,0,256,0s-13.8,0-23.9,4.4c0,0-3.5,9.3-6,36.2c-3,32.9-15.4,42.8-30.7,53.7v0.2c-3.6,1.4-7.3,2.8-10.8,4.4l-0.1-0.1
		c-18.6,3.1-34.3,4.8-59.7-16.3C104,65.3,95,61.2,95,61.2C84.7,65.3,75,75,75,75s-9.7,9.7-13.8,20c0,0,4.1,9.1,21.4,29.8
		c21.1,25.4,19.4,41.1,16.3,59.7l0.1,0.1c-1.6,3.5-3.1,7.1-4.4,10.8h-0.2c-10.9,15.4-20.9,27.7-53.7,30.7c-26.9,2.5-36.2,6-36.2,6
		C0,242.3,0,256,0,256s0,13.8,4.4,23.9c0,0,9.3,3.5,36.2,6c32.9,3,42.8,15.4,53.7,30.7h0.2c1.4,3.7,2.8,7.3,4.4,10.8l-0.1,0.1
		c3.1,18.6,4.8,34.3-16.3,59.7C65.3,408,61.2,417,61.2,417c4.1,10.3,13.8,20,13.8,20s9.7,9.7,20,13.8c0,0,9-4.1,29.8-21.4
		c25.4-21.1,41.1-19.4,59.7-16.3l0.1-0.1c3.5,1.6,7.1,3.1,10.8,4.4v0.2c15.3,10.9,27.7,20.9,30.7,53.7c2.5,26.9,6,36.2,6,36.2
		C242.3,512,256,512,256,512s13.8,0,23.9-4.4c0,0,3.5-9.3,6-36.2c3-32.9,15.4-42.8,30.7-53.7v-0.2c3.7-1.4,7.3-2.8,10.8-4.4l0.1,0.1
		c18.6-3.1,34.3-4.8,59.7,16.3c20.8,17.3,29.8,21.4,29.8,21.4c10.3-4.1,20-13.8,20-13.8s9.7-9.7,13.8-20c0,0-4.1-9.1-21.4-29.8
		c-21.1-25.4-19.4-41.1-16.3-59.7l-0.1-0.1c1.6-3.5,3.1-7.1,4.4-10.8h0.2c10.9-15.3,20.9-27.7,53.7-30.7c26.9-2.5,36.2-6,36.2-6
		C512,269.8,512,256,512,256S512,242.2,507.6,232.1z M256,375.7c-66.1,0-119.7-53.6-119.7-119.7S189.9,136.3,256,136.3
		c66.1,0,119.7,53.6,119.7,119.7S322.1,375.7,256,375.7z" />
				</svg>
			</div>
			<div class="gear-wrapper gear-wrapper-4">
				<svg version="1.1" class="gear gear-4" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
					<path class="st0" d="M507.6,232.1c0,0-9.3-3.5-36.2-6c-32.9-3-42.8-15.4-53.7-30.7h-0.2c-1.4-3.6-2.8-7.2-4.4-10.8l0.1-0.1
		c-3.1-18.6-4.8-34.3,16.3-59.7C446.7,104,450.8,95,450.8,95c-4-10.3-13.8-20-13.8-20s-9.7-9.7-20-13.8c0,0-9.1,4.1-29.8,21.4
		c-25.4,21.1-41.1,19.4-59.7,16.3l-0.1,0.1c-3.5-1.6-7.1-3.1-10.8-4.4v-0.2c-15.3-10.9-27.7-20.9-30.7-53.7c-2.5-26.9-6-36.2-6-36.2
		C269.8,0,256,0,256,0s-13.8,0-23.9,4.4c0,0-3.5,9.3-6,36.2c-3,32.9-15.4,42.8-30.7,53.7v0.2c-3.6,1.4-7.3,2.8-10.8,4.4l-0.1-0.1
		c-18.6,3.1-34.3,4.8-59.7-16.3C104,65.3,95,61.2,95,61.2C84.7,65.3,75,75,75,75s-9.7,9.7-13.8,20c0,0,4.1,9.1,21.4,29.8
		c21.1,25.4,19.4,41.1,16.3,59.7l0.1,0.1c-1.6,3.5-3.1,7.1-4.4,10.8h-0.2c-10.9,15.4-20.9,27.7-53.7,30.7c-26.9,2.5-36.2,6-36.2,6
		C0,242.3,0,256,0,256s0,13.8,4.4,23.9c0,0,9.3,3.5,36.2,6c32.9,3,42.8,15.4,53.7,30.7h0.2c1.4,3.7,2.8,7.3,4.4,10.8l-0.1,0.1
		c3.1,18.6,4.8,34.3-16.3,59.7C65.3,408,61.2,417,61.2,417c4.1,10.3,13.8,20,13.8,20s9.7,9.7,20,13.8c0,0,9-4.1,29.8-21.4
		c25.4-21.1,41.1-19.4,59.7-16.3l0.1-0.1c3.5,1.6,7.1,3.1,10.8,4.4v0.2c15.3,10.9,27.7,20.9,30.7,53.7c2.5,26.9,6,36.2,6,36.2
		C242.3,512,256,512,256,512s13.8,0,23.9-4.4c0,0,3.5-9.3,6-36.2c3-32.9,15.4-42.8,30.7-53.7v-0.2c3.7-1.4,7.3-2.8,10.8-4.4l0.1,0.1
		c18.6-3.1,34.3-4.8,59.7,16.3c20.8,17.3,29.8,21.4,29.8,21.4c10.3-4.1,20-13.8,20-13.8s9.7-9.7,13.8-20c0,0-4.1-9.1-21.4-29.8
		c-21.1-25.4-19.4-41.1-16.3-59.7l-0.1-0.1c1.6-3.5,3.1-7.1,4.4-10.8h0.2c10.9-15.3,20.9-27.7,53.7-30.7c26.9-2.5,36.2-6,36.2-6
		C512,269.8,512,256,512,256S512,242.2,507.6,232.1z M256,375.7c-66.1,0-119.7-53.6-119.7-119.7S189.9,136.3,256,136.3
		c66.1,0,119.7,53.6,119.7,119.7S322.1,375.7,256,375.7z" />
				</svg>
			</div>
		</div>
		<div class="loading-bar" data-progress="0"><span></span></div>
		<svg class="checkmark checkmark-success" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
			<circle class="checkmark-circle" cx="25" cy="25" r="25" fill="none" />
			<path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
		</svg>
	</div>
</div>
@endsection
@section('page-script')
<script>
     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function pingBackend() {
        var url = $("#processing").attr('data-url');
        $.ajax({
            url: url,
            type: 'post', // or 'POST' depending on your backend setup
            success: function(response) {
                window.location.href = response.redirect_url;
            },
            error: function(xhr, status, error) {


                if (xhr.status === 455) {
                    var errorMessage = xhr.responseJSON.errors.message;
                    var errorMessageUr = xhr.responseJSON.errors.message_ur;

                    // Example: Update waiting page with error messages
                    $('#error-message').text(errorMessage);
                    $('#error-message-ur').text(errorMessageUr);
                    $('#error-container').show(); // Show error message container
                    setTimeout(() => {
                    var redirectRoute =  $("#processing").attr('data-duaRoute');
                    window.location.href = redirectRoute;

                    }, 5000);
                } else {
                    console.error('Error pinging backend:', error);
                }




                // Handle errors from the AJAX request
                console.error('Error pinging backend:', error);
            }
        });
    }
    setInterval(pingBackend, 8000);
</script>
<script>
    // Set the countdown time to 5 minutes (300 seconds)
    // let countdownTime = 300; // 5 minutes * 60 seconds
    let countdownTime = 600; // 5 minutes * 60 seconds

    // Get the countdown display element
    const countdownElement = document.getElementById('countdown');

    // Function to update the countdown timer
    function updateCountdown() {
        // Calculate minutes and seconds
        let minutes = Math.floor(countdownTime / 60);
        let seconds = countdownTime % 60;

        // Format minutes and seconds to display with leading zeros if necessary
        let displayMinutes = minutes < 10 ? `0${minutes}` : minutes;
        let displaySeconds = seconds < 10 ? `0${seconds}` : seconds;

        // Update the countdown display
        countdownElement.textContent = `${displayMinutes}:${displaySeconds}`;

        // Decrement countdown time by 1 second
        countdownTime--;

        // Check if countdown has reached zero
        if (countdownTime < 0) {
            clearInterval(intervalId); // Stop the countdown
            countdownElement.textContent = 'Process Done'; // Optional message

            $('#error-message').text("We are sorry, we are experience very high traffic to your token may not book Please try again");
            $('#error-message-ur').text('ہم معذرت خواہ ہیں، ہمیں آپ کے ٹوکن پر بہت زیادہ ٹریفک کا سامنا ہے، ہو سکتا ہے بک نہ ہو، براہ کرم دوبارہ کوشش کریں۔');
            $('#error-container').show();
            // You can perform any action here when countdown reaches zero
        }
    }

    // Initial call to update countdown
    updateCountdown();

    // Update the countdown every second (1000 milliseconds)
    let intervalId = setInterval(updateCountdown, 1000);
</script>

@endsection
