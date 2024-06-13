@extends('layouts.guest')

@section('guest-content')

        <!-- ======= Hero Section ======= -->
        <section id="hero" class="d-flex flex-column justify-content-center">
            <div class="container" data-aos="zoom-in" data-aos-delay="100">
                <h1>Silas Développe</h1>
                <p>Expertise en <span class="typed" data-typed-items="Programmation, Dévelopement Web et Mobile, Design, Community Management"></span></p>
                <div class="social-links">
                    <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                    <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                </div>
            </div>
        </section><!-- End Hero -->

        <main id="main">
            <!-- ======= About Section ======= -->
            <section id="about" class="about">
                <div class="container" data-aos="fade-up">
                    <div class="section-title">
                        <h2>A propos</h2>
                    </div>

                    <div class="row g-lg-5">
                        <div class="col-lg-3">
                            <img src="{{ asset('assets/img/logo-bg-2.png') }}" class="img-fluid rounded-5" alt="">
                        </div>

                        <div class="col-lg-9 pt-4 pt-lg-0 content d-inline-flex align-items-center">
                            <p class="fs-4">SDEV est une société offrant des solutions informatiques, des accompagnements et conseil en stratégie marketing digitale et assure la couverture médiatique des évènements de tout genre.</p>
                        </div>
                    </div>
                </div>
            </section><!-- End About Section -->

            <!-- ======= Skills Section ======= -->
            <section id="skills" class="skills section-bg bg-light">
                <div class="container" data-aos="fade-up">
                    <div class="section-title">
                        <h2>Compétences</h2>
                    </div>

                    <div class="row g-lg-5 g-4 skills-content">
                        <div class="col-lg-4">
                            <div class="progress">
                                <span class="skill">HTML <i class="val">90%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill">CSS <i class="val">90%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill">JavaScript <i class="val">75%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="progress">
                                <span class="skill">PHP <i class="val">70%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill">Bootstrap <i class="val">60%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill">Laravel <i class="val">75%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="progress">
                                <span class="skill">React JS <i class="val">65%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill">React Native <i class="val">90%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill">Photoshop <i class="val">85%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section><!-- End Skills Section -->

            <!-- ======= Services Section ======= -->
            <section id="services" class="services">
                <div class="container" data-aos="fade-up">
                    <div class="section-title">
                        <h2>Services</h2>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                            <div class="icon-box iconbox-orange py-5 border border-default">
                                <div class="icon">
                                    <svg width="100" height="100" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke="none" stroke-width="0" fill="#f5f5f5" d="M300,521.0016835830174C376.1290562159157,517.8887921683347,466.0731472004068,529.7835943286574,510.70327084640275,468.03025145048787C554.3714126377745,407.6079735673963,508.03601936045806,328.9844924480964,491.2728898941984,256.3432110539036C474.5976632858925,184.082847569629,479.9380746630129,96.60480741107993,416.23090153303,58.64404602377083C348.86323505073057,18.502131276798302,261.93793281208167,40.57373210992963,193.5410806939664,78.93577620505333C130.42746243093433,114.334589627462,98.30271207620316,179.96522072025542,76.75703585869454,249.04625023123273C51.97151888228291,328.5150500222984,13.704378332031375,421.85034740162234,66.52175969318436,486.19268352777647C119.04800174914682,550.1803526380478,217.28368757567262,524.383925680826,300,521.0016835830174"></path>
                                    </svg>

                                    <i class="bi bi-globe"></i>
                                </div>

                                <h4><a href="">Création site web</a></h4>
                                <p>Nous créons et hébergeons des sites web pour les entreprises et les personnes.</p>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="200">
                            <div class="icon-box iconbox-teal py-5 border border-default">
                                <div class="icon">
                                    <svg width="100" height="100" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke="none" stroke-width="0" fill="#f5f5f5" d="M300,503.46388370962813C374.79870501325706,506.71871716319447,464.8034551963731,527.1746412648533,510.4981551193396,467.86667711651364C555.9287308511215,408.9015244558933,512.6030010748507,327.5744911775523,490.211057578863,256.5855673507754C471.097692560561,195.9906835881958,447.69079081568157,138.11976852964426,395.19560036434837,102.3242989838813C329.3053358748298,57.3949838291264,248.02791733380457,8.279543830951368,175.87071277845988,42.242879143198664C103.41431057327972,76.34704239035025,93.79494320519305,170.9812938413882,81.28167332365135,250.07896920659033C70.17666984294237,320.27484674793965,64.84698225790005,396.69656628748305,111.28512138212992,450.4950937839243C156.20124167950087,502.5303643271138,231.32542653798444,500.4755392045468,300,503.46388370962813"></path>
                                    </svg>

                                    <i class="bi bi-android2"></i>
                                </div>

                                <h4><a href="">Création applis</a></h4>
                                <p>Nous créons des applis mobiles Android et iOS pour les entreprises et les personnes.</p>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="300">
                            <div class="icon-box iconbox-pink py-5 border border-default">
                                <div class="icon">
                                    <svg width="100" height="100" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke="none" stroke-width="0" fill="#f5f5f5" d="M300,541.5067337569781C382.14930387511276,545.0595476570109,479.8736841581634,548.3450877840088,526.4010558755058,480.5488172755941C571.5218469581645,414.80211281144784,517.5187510058486,332.0715597781072,496.52539010469104,255.14436215662573C477.37192572678356,184.95920475031193,473.57363656557914,105.61284051026155,413.0603344069578,65.22779650032875C343.27470386102294,18.654635553484475,251.2091493199835,5.337323636656869,175.0934190732945,40.62881213300186C97.87086631185822,76.43348514350839,51.98124368387456,156.15599469081315,36.44837278890362,239.84606092416172C21.716077023791087,319.22268207091537,43.775223500013084,401.1760424656574,96.891909868211,461.97329694683043C147.22146801428983,519.5804099606455,223.5754009179313,538.201503339737,300,541.5067337569781"></path>
                                    </svg>

                                    <i class="bi bi-flag"></i>
                                </div>

                                <h4><a href="">Marketing</a></h4>
                                <p>Nous facilitons la visibilité de vos produits chez vos clients physiquements et en ligne.</p>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                            <div class="icon-box iconbox-blue py-5 border border-default">
                                <div class="icon">
                                    <svg width="100" height="100" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke="none" stroke-width="0" fill="#f5f5f5" d="M300,582.0697525312426C382.5290701553225,586.8405444964366,449.9789794690241,525.3245884688669,502.5850820975895,461.55621195738473C556.606425686781,396.0723002908107,615.8543463187945,314.28637112970534,586.6730223649479,234.56875336149918C558.9533121215079,158.8439757836574,454.9685369536778,164.00468322053177,381.49747125262974,130.76875717737553C312.15926192815925,99.40240125094834,248.97055460311594,18.661163978235184,179.8680185752513,50.54337015887873C110.5421016452524,82.52863877960104,119.82277516462835,180.83849132639028,109.12597500060166,256.43424936330496C100.08760227029461,320.3096726198365,92.17705696193138,384.0621239912766,124.79988738764834,439.7174275375508C164.83382741302287,508.01625554203684,220.96474134820875,577.5009287672846,300,582.0697525312426"></path>
                                    </svg>

                                    <i class="bi bi-palette"></i>
                                </div>

                                <h4><a href="">Design & Montage</a></h4>
                                <p>Nous concevons des affiches, des tracts, des invitations, des logos et autres.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section><!-- End Services Section -->

             <!-- ======= Portfolio Section ======= -->
    <section id="portfolio" class="portfolio section-bg">
        <div class="container" data-aos="fade-up">

          <div class="section-title">
            <h2>Portfolio</h2>
            <p>
                Dans cette expérience nous avons eu à réaliser pas mal des projets que nous presentons ci-dessous,
                du moins pour ceux qui sont permis d'être publier par le client.
            </p>
          </div>

          <div class="row">
            <div class="col-lg-12 d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
              <ul id="portfolio-flters">
                <li data-filter="*" class="filter-active">All</li>
                <li data-filter=".filter-app">Application</li>
                <li data-filter=".filter-card">Conception graphique</li>
                <li data-filter=".filter-web">Site Web</li>
              </ul>
            </div>
          </div>

          <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">

            <div class="col-lg-4 col-md-6 portfolio-item filter-app">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-1.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>App 1</h4>
                  <p>App</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-1.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="App 1"><i class="bx bx-plus"></i></a>
                    <a href="{{ route('detail',['id'=>1]) }}" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6 portfolio-item filter-web">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-2.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Web 3</h4>
                  <p>Web</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-2.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Web 3"><i class="bx bx-plus"></i></a>
                    <a href="{{ route('detail',['id'=>2]) }}" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6 portfolio-item filter-app">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-3.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>App 2</h4>
                  <p>App</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-3.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="App 2"><i class="bx bx-plus"></i></a>
                    <a href="{{ route('detail',['id'=>3]) }}" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6 portfolio-item filter-card">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-4.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Card 2</h4>
                  <p>Card</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-4.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Card 2"><i class="bx bx-plus"></i></a>
                    <a href="{{ route('detail',['id'=>4]) }}" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6 portfolio-item filter-web">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-5.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Web 2</h4>
                  <p>Web</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-5.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Web 2"><i class="bx bx-plus"></i></a>
                    <a href="{{ route('detail',['id'=>5]) }}" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6 portfolio-item filter-app">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-6.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>App 3</h4>
                  <p>App</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-6.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="App 3"><i class="bx bx-plus"></i></a>
                    <a href="{{ route('detail',['id'=>6]) }}" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div>

            {{-- <div class="col-lg-4 col-md-6 portfolio-item filter-card">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-7.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Card 1</h4>
                  <p>Card</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-7.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Card 1"><i class="bx bx-plus"></i></a>
                    <a href="portfolio-details.html" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6 portfolio-item filter-card">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-8.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Card 3</h4>
                  <p>Card</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-8.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Card 3"><i class="bx bx-plus"></i></a>
                    <a href="portfolio-details.html" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6 portfolio-item filter-web">
              <div class="portfolio-wrap">
                <img src="assets/img/portfolio/portfolio-9.jpg" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Web 3</h4>
                  <p>Web</p>
                  <div class="portfolio-links">
                    <a href="assets/img/portfolio/portfolio-9.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Web 3"><i class="bx bx-plus"></i></a>
                    <a href="portfolio-details.html" class="portfolio-details-lightbox" data-glightbox="type: external" title="Portfolio Details"><i class="bx bx-link"></i></a>
                  </div>
                </div>
              </div>
            </div> --}}

          </div>

        </div>
      </section><!-- End Portfolio Section -->
            <!-- ======= Contact Section ======= -->
            <section id="contact" class="contact">
                <div class="container" data-aos="fade-up">
                    <div class="section-title">
                        <h2>Contact</h2>
                    </div>

                    <div class="row mt-1">
                        <div class="col-lg-4">
                            <div class="info">
                                <div class="address">
                                    <i class="bi bi-geo-alt"></i>
                                    <h4>Adresse :</h4>
                                    <p>01, av. des Oliviers, Limete 7<small class="d-inline-block align-top">ème</small> Rue</p>
                                </div>

                                <div class="email">
                                    <i class="bi bi-envelope"></i>
                                    <h4>E-mail :</h4>
                                    <p>ir-masimango@silasmas.com</p>
                                </div>

                                <div class="phone">
                                    <i class="bi bi-phone"></i>
                                    <h4>Appel :</h4>
                                    <p>(+243) 827 839 232 / 993 107 499</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8 mt-5 mt-lg-0">
                            <form action="" method="post" role="form" class="php-email-form">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <input type="text" name="name" class="form-control" id="name" placeholder="Nom complet" required>
                                    </div>

                                    <div class="col-md-6 form-group mt-3 mt-md-0">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" required>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Sujet" required>
                                </div>

                                <div class="form-group mt-3">
                                    <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
                                </div>

                                <div class="my-3">
                                    <div class="loading">Chargement ...</div>
                                    <div class="error-message"></div>
                                    <div class="sent-message">Message envoyé. Merci !</div>
                                </div>

                                <div class="text-center"><button type="submit">Envoyer message</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </section><!-- End Contact Section -->
        </main><!-- End #main -->

@endsection
