@include("parties.entete")

        <!-- ======= Mobile nav toggle button ======= -->
        <!-- <button type="button" class="mobile-nav-toggle d-xl-none"><i class="bi bi-list mobile-nav-toggle"></i></button> -->
        <i class="bi bi-list mobile-nav-toggle d-lg-none"></i>
        <!-- ======= Header ======= -->
        <header id="header" class="d-flex flex-column justify-content-center">
            <nav id="navbar" class="navbar nav-menu">
                <ul>
                    <li><a href="#hero" class="nav-link scrollto active"><i class="bx bx-home"></i> <span>Accueil</span></a></li>
                    <li><a href="#about" class="nav-link scrollto"><i class="bi bi-question-circle"></i> <span>A propos</span></a></li>
                    <li><a href="#services" class="nav-link scrollto"><i class="bx bx-server"></i> <span>Services</span></a></li>
                    <li><a href="#portfolio" class="nav-link scrollto"><i class="bx bx-book-content"></i> <span>Portfolio</span></a></li>
                    <li><a href="#contact" class="nav-link scrollto"><i class="bx bx-envelope"></i> <span>Contact</span></a></li>
                </ul>
            </nav><!-- .nav-menu -->
        </header><!-- End Header -->

@yield('guest-content')

        <!-- ======= Footer ======= -->
        <footer id="footer">
            <div class="container">
                <p class="mb-3"><img src="{{ asset('assets/img/logo-bg-1.png') }}" alt="" class="rounded-circle" width="100"></p>

                <div class="row">
                    <div class="col-lg-6 col-lg-8 col-12 mx-auto">
                        <h3>Silas développe</h3>
                        <p>
                            Nous vous offrons des solutions informatiques, des accompagnements et conseil en stratégie marketing digitale et assure la couverture médiatique des évènements de tout genre.
                        </p>
                    </div>
                </div>

                <div class="social-links">
                    <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                    <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                </div>

                <div class="copyright">
                    &copy; Copyright <strong><span>SDEV</span></strong>. Tous droits réservés
                </div>

                <div class="credits">
                    Designed by <a href="#">Sdev</a>
                </div>
            </div>
        </footer><!-- End Footer -->

        <div id="preloader"></div>
        <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
        <script async src='https://stackwhats.com/pixel/a1d290565dc8bd3570b1c86106085f'></script>
        @include("parties.pied")
