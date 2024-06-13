@extends('layouts.auth')

@section('auth-content')

                    <h2>Se connecter</h2>
                </div>

                <div class="row g-lg-5">
                    <div class="col-lg-5 col-sm-7 mx-auto">
                        <form action="{{ route('login') }}" method="post">
    @csrf
                            <div class="form-group">
                                <input type="text" name="username" id="username" class="form-control" placeholder="E-mail ou n° de téléphone" aria-describedby="username_error_message" value="{{ !empty($inputs['username']) ? $inputs['username'] : '' }}" {{ !empty($inputs['username']) ? '' : 'autofocus' }}>
    @if (!empty($response_error) AND $response_error->message == $inputs['username'])
                                <p id="username_error_message" class="small mt-1 text-center text-danger">{{ $response_error->data }}</p>
    @endif
                            </div>

                            <div class="form-group mt-3">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe" aria-describedby="password_error_message" {{ !empty($inputs['username']) ? 'autofocus' : '' }}>
    @if (!empty($response_error) AND $response_error->message == $inputs['password'])
                                <p id="password_error_message" class="small mt-1 text-center text-danger">{{ $response_error->data }}</p>
    @endif
                            </div>

                            <div class="form-check my-4 text-center">
                                <span class="d-inline-block">
                                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                    <label role="button" for="remember" class="form-check-label">Se souvenir de moi</label>
                                </span>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn w-100 btn-primary">Connexion</button>
    @if ($users->success == false)
                                <p class="mt-3 mb-0 text-center"><a href="{{ route('register') }}">S'inscrire</a></p>
    @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

@endsection