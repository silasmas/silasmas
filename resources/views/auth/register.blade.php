@extends('layouts.auth')

@section('auth-content')

                    <h2>Inscription</h2>
                </div>

                <div class="row g-lg-5">
                    <div class="col-lg-5 col-sm-7 mx-auto">
                        <form action="{{ route('register') }}" method="post">
    @csrf
                            <input type="hidden" name="status_id" value="3">
                            <input type="hidden" name="roles_ids[]" value="1">

    @if (!empty($response_error))
                            <p class="small text-center text-danger">{{ !empty($response_error->data) ? $response_error->data : $response_error->message }}</p>
    @endif

                            <div class="row g-lg-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="register_firstname" id="register_firstname" class="form-control{{ !empty($response_error) && $response_error->message == $inputs['firstname'] ? ' border-danger' : '' }}" placeholder="Prénom" value="{{ !empty($inputs['firstname']) ? $inputs['firstname'] : '' }}">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mt-lg-0 mt-3">
                                        <input type="text" name="register_lastname" id="register_lastname" class="form-control" placeholder="Nom" value="{{ !empty($inputs['lastname']) ? $inputs['lastname'] : '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-lg-3">
                                <div class="col-lg-6">
                                    <div class="form-group mt-3">
                                        <input type="text" name="register_surname" id="register_surname" class="form-control" placeholder="Post-nom" value="{{ !empty($inputs['surname']) ? $inputs['surname'] : '' }}">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mt-3">
                                        <select name="register_gender" class="form-select">
                                            <option class="small" selected disabled>Sexe</option>
                                            <option value="M">Homme</option>
                                            <option value="F">Femme</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-lg-3">
                                <div class="col-lg-6 mx-auto">
                                    <div class="form-group mt-3">
                                        <input type="text" name="register_birthdate" id="register_birthdate" class="form-control" placeholder="Date de naissance">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-lg-3">
                                <div class="col-lg-6">
                                    <div class="form-group mt-3">
                                        <input type="text" name="register_phone" id="register_phone" class="form-control{{ !empty($response_error) && $response_error->message == $inputs['phone'] ? ' border-danger' : '' }}" placeholder="Téléphone" value="{{ !empty($inputs['phone']) ? $inputs['phone'] : '' }}">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mt-3">
                                        <input type="text" name="register_email" id="register_email" class="form-control{{ !empty($response_error) && $response_error->message == $inputs['email'] ? ' border-danger' : '' }}" placeholder="E-mail" value="{{ !empty($inputs['email']) ? $inputs['email'] : '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-lg-3">
                                <div class="col-lg-6">
                                    <div class="form-group mt-3">
                                        <input type="password" name="register_password" id="register_password" class="form-control{{ !empty($response_error) && $response_error->message == $inputs['password'] ? ' border-danger' : '' }}" placeholder="Mot de passe">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mt-3">
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control{{ !empty($response_error) && $response_error->message == $inputs['confirm_password'] ? ' border-danger' : '' }}" placeholder="Confirmer mot de passe">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn w-100 btn-warning">Enregistrer</button>
                                <p class="mt-3 mb-0 text-center"><a href="{{ route('login') }}">Se connecter</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

@endsection