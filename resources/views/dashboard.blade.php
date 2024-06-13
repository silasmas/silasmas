@extends('layouts.app')

@section('app-content')

<?php $count = 0; ?>
        <!-- ======= Hero Section ======= -->
        <section id="hero" class="d-flex flex-column justify-content-center">
            <div class="container" data-aos="zoom-in" data-aos-delay="100">
                <h1>Admin SDEV</h1>
                <p>Gestion des <span class="typed" data-typed-items="messages, projets, membres de l'équipe"></span></p>
            </div>
        </section><!-- End Hero -->

        <main id="main" class="ms-0">
            <!-- ======= Message Section ======= -->
            <section id="about" class="about">
                <div class="container" data-aos="fade-up">
                    <div class="section-title">
                        <h2>Messages récents</h2>
                    </div>

                    <div class="row g-lg-5">
                        <div class="col-12">
                        </div>
                    </div>
                </div>
            </section><!-- End Message Section -->

            <!-- ======= Project Section ======= -->
            <section id="about" class="about py-0">
                <div class="container py-5 border-top border-bottom border-default" data-aos="fade-up">
                    <div class="section-title">
                        <h2>Projets récents</h2>
                    </div>

                    <div class="row g-lg-3 g-4">
    @forelse ($projects as $project)
        <?php if ($count == 10) break; ?>
                        <div class="col-lg-6 col-sm-9 mx-auto">
                            <div class="card border border-default shadow-0">
                                <div class="card-body">
                                    <div class="row g-sm-4">
                                        <div class="col-lg-3 col-sm-3 col-6 mx-auto">
                                            <div class="bg-image rounded-0">
                                                <img src="{{ $project->logo_url != null ? $project->logo_url : asset('assets/img/blank.png') }}" alt="" class="img-fluid">
                                                <div class="mask{{ $project->logo_url == null ? ' d-flex justify-content-center align-items-center' : '' }}">
        @if ($project->logo_url == null)
                                                    <i class="bi bi-person-workspace fs-4 text-secondary"></i>
        @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-5 col-sm-5 col-12 text-sm-start text-center">
                                            <p class="mt-sm-0 mt-2 mb-2 text-uppercase fw-bold lh-1">{{ $project->project_name }}</p>
                                            <p class="mb-2 small">
        @if ($project->web_url != null)
                                                <a href="{{ $project->web_url }}" target="_blank" class="btn btn-outline-dark shadow-0"><i class="bi bi-globe2"></i></a>            
        @endif
        @if ($project->android_url != null)
                                                <a href="{{ $project->android_url }}" target="_blank" class="btn btn-success mx-2 shadow-0"><i class="bi bi-android2"></i></a>
        @endif
        @if ($project->ios_url != null)
                                                <a href="{{ $project->ios_url }}" target="_blank" class="btn btn-dark shadow-0"><i class="bi bi-apple"></i></a>
        @endif
                                            </p>
                                            <p class="mb-sm-1 mb-0 small">{{ $project->project_description }}</p>
                                        </div>

                                        <div class="col-lg-4 col-sm-4 col-12 text-sm-start text-center">
                                            <p class="mt-sm-2 mt-0 mb-2 small">
                                                <strong><u>Année</u></strong> : 
                                                {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project->created_at)->year }}
                                            </p>
                                            <p class="mb-0 small">
                                                <strong><u>Référence</u></strong>
                                                <br><span class="text-uppercase">{{ $project->user->firstname . ' ' . $project->user->lastname }}</span>
                                                <br>{{ $project->user->phone }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        <?php $count++; ?>
    @empty
                        <div class="col-12">
                            <h5 class="text-center text-primary">La liste est encore vide</h5>
                        </div>
    @endforelse

                        <div class="col-12 text-center">
                            <a href="{{ route('admin.project') }}" class="btn btn-sm btn-primary mt-3 shadow-0">Gérer les projets</a>
                        </div>
                    </div>
                </div>
            </section><!-- End Project Section -->

            <!-- ======= Team Section ======= -->
            <section id="about" class="about">
                <div class="container" data-aos="fade-up">
                    <div class="section-title">
                        <h2>Membres récents</h2>
                    </div>

                    <div class="row g-lg-5">
                        <div class="col-12">
                        </div>
                    </div>
                </div>
            </section><!-- End Team Section -->
        </main><!-- End #main -->

@endsection