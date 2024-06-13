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
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/aos/aos.css') }}">
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
@if (Route::is('register'))
            Inscription
@endif
@if (Route::is('login'))
            Connexion
@endif
@if (!empty($exception))
            {{ $exception->getMessage() }}
@endif
        </title>
    </head>

    <body>
        <!-- ======= About Section ======= -->
        <section id="about" class="about pt-5 pb-2">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="sdev" width="100" class="mx-auto mb-3">

@yield('auth-content')

        <!-- ======= Footer ======= -->
        <footer id="footer" class="bg-transparent pb-5">
            <div class="container">
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
        <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
        <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
        <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/typed.js/typed.umd.js') }}"></script>
        <script src="{{ asset('assets/vendor/waypoints/noframework.waypoints.js') }}"></script>
        <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
        <script src="{{ asset('assets/vendor/autosize/js/autosize.min.js') }}"></script>

        <!-- Main JS File -->
        <script src="{{ asset('assets/js/main.js') }}"></script>

        <script type="text/javascript">
            autosize($('textarea'));
            $('#register_birthdate').datepicker({
                dateFormat: 'dd/mm/yy',
                onSelect: function () {
                    $(this).focus();
                }
            });
        </script>
    </body>
</html>
