@component('mail::message')
# Bonjour la team GLODEMAP,
Vous avez reçu un message provenant d'un visiteur sur votre site dont les details sont ci-dessous :
@component('mail::panel')
Nom : {{ $nom }} <br>
Email : {{ $email }} <br>
Téléphone : {{ $phone }} <br>
Objet : {{ $objet }} <br>
Message : {{ $message }} <br>
@endcomponent


Merci,<br>
{{ config('app.name') }}
@endcomponent
