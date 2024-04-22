<section class="page_404">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 ">
          <div class="col-sm-10 col-sm-offset-1  text-center">
            <div class="four_zero_four_bg">
              <h1 class="text-center ">404</h1>

            </div>

            <div class="contant_box_404">
              <h2 class="h2">
                {{ $message  }}
                @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
             @endif
             @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
             @endif


              </h2>
              <a href="{{ route('book.show') }}" class="link_404">Book Seat</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
<style>
  body {
    background-color: rgb(29, 29, 29);
    font-family: Karla, sans-serif;
}
  .page_404 {
  padding: 40px 0;
  background: #fff;
  font-family: "Arvo", serif;
}
.container {
    text-align: center;
}

.page_404 img {
  width: 100%;
}

.four_zero_four_bg {
    background-image: url('https://i.postimg.cc/WzpmyBzX/logo-1.jpg');
    height: 500px;
    background-position: center;
    background-repeat: no-repeat;
}
.four_zero_four_bg h1 {
  font-size: 80px;
}

.four_zero_four_bg h3 {
  font-size: 80px;
}

.link_404 {
  color: #fff !important;
  padding: 10px 20px;
  background: #39ac31;
  margin: 20px 0;
  display: inline-block;
}
.contant_box_404 {
  margin-top: -50px;
}

</style>
<script>
  document.title = "kahayFaqeer.org | 404";
</script>
