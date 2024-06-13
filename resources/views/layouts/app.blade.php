<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta content="" name="description">
        <meta content="sdev, silasdev, silasmas, développement, web, mobile, community, manager" name="keywords">

        <!-- Favicon -->
        <link rel="apple-touch-icon" type="image/png" sizes="180x180" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('assets/img/favicon/site.webmanifest') }}">

        <!-- Vendor CSS Files -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/jquery/jquery-ui/jquery-ui.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/cropper/css/cropper.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/aos/aos.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/mdb/css/mdb.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}">

        <!-- Google Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i">

        <!-- Fonts -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}">

        <!-- Template Main CSS File -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">

        <title>
            Sdev | 
@if (Route::is('admin.home'))
            Administration
@endif
@if (Route::is('admin.account'))
            {{ Auth::user()->firstname }}
@endif
@if (Route::is('admin.message'))
            Messages
@endif
@if (Route::is('admin.project'))
            Projets
@endif

@if (Route::is('admin.team'))
            Membres de l'équipe
@endif
        </title>
    </head>

    <body>
        <!-- ======= Mobile nav toggle button ======= -->
        <!-- <button type="button" class="mobile-nav-toggle d-xl-none"><i class="bi bi-list mobile-nav-toggle"></i></button> -->
        <i class="bi bi-list mobile-nav-toggle d-lg-none"></i>
        <!-- ======= Header ======= -->
        <header id="header" class="d-flex flex-column justify-content-center">
            <nav id="navbar" class="navbar nav-menu shadow-0">
                <ul>
                    <li><a href="{{ route('admin.home') }}" class="nav-link{{ Route::is('admin.home') ? ' active' : '' }}"><i class="bi bi-speedometer"></i> <span>Accueil</span></a></li>
                    <li><a href="{{ route('admin.message') }}" class="nav-link{{ Route::is('admin.message') ? ' active' : '' }}"><i class="bi bi-chat-left-dots"></i> <span>Messages</span></a></li>
                    <li><a href="{{ route('admin.project') }}" class="nav-link{{ Route::is('admin.project') ? ' active' : '' }}"><i class="bi bi-person-workspace"></i> <span>Projets</span></a></li>
                    <li><a href="{{ route('admin.team') }}" class="nav-link{{ Route::is('admin.team') ? ' active' : '' }}"><i class="bi bi-people"></i> <span>Equipe</span></a></li>
                    <li><a href="{{ route('admin.account') }}" class="nav-link{{ Route::is('admin.account') ? ' active' : '' }}"><i class="bi bi-person-gear"></i> <span>Mon compte</span></a></li>
                </ul>
            </nav><!-- .nav-menu -->
        </header><!-- End Header -->

@yield('app-content')

        <!-- ======= Footer ======= -->
        <footer id="footer" class="pt-0 bg-transparent">
            <div class="container pt-4 border-top border-default">
                <div class="copyright">
                    &copy; Copyright <strong><span>SDEV</span></strong>. Tous droits réservés
                </div>

                <div class="credits">
                    Designed by <a href="https://www.linkedin.com/in/xanders-samoth-b2770737/">Xanders Samoth</a>
                </div>
            </div>
        </footer><!-- End Footer -->

        <div id="preloader"></div>
        <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

        <!-- Vendor JS Files -->
        <script src="{{ asset('assets/vendor/jquery/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/jquery/jquery-ui/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
        <script src="{{ asset('assets/vendor/mdb/js/mdb.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/typed.js/typed.umd.js') }}"></script>
        <script src="{{ asset('assets/vendor/waypoints/noframework.waypoints.js') }}"></script>
        <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
        <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
        <script src="{{ asset('assets/vendor/cropper/js/cropper.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/autosize/js/autosize.min.js') }}"></script>

        <!-- Main JS File -->
        <script src="{{ asset('assets/js/main.js') }}"></script>

        <script type="text/javascript">
            autosize($('textarea'));
        </script>
    </body>
</html>
