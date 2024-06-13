
@include("parties.entete")
<main id="main">
    <!-- ======= Portfolio Details Section ======= -->
    
    <section id="portfolio-details" class="portfolio-details">
      <div class="container">
        @switch($i)
            @case("1")
                @include('parties.projets.p1')
                @break
            @case("2")
            @include('parties.projets.p2')
                @break
            @case("3")
            @include('parties.projets.p3')
                @break
            @case("4")
            @include('parties.projets.p4')
                @break
            @case("5")
            @include('parties.projets.p5')
                @break
            @case("6")
            @include('parties.projets.p6')
                @break
            @default
                
        @endswitch
        

      </div>
    </section><!-- End Portfolio Details Section -->

  </main>
  <!-- End #main -->

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  @include("parties.pied")
