@component('mail::message')
# Bonjour {{ $user->name }}

@component('mail::panel')
@if ($objet=="Crétaion des accès chez groupe synapse")
   Félicitation, vos accès sont crée comme administrateur pour vous donnez accès à la partie admin du groupe synapse.
   Pour vous connectez utiliser votre adresse mail suivant <b>{{ $user->email }}</b> avec le mot de passe reçu chez l'administrateur.
@endif
@if ($objet=="Verification de l'adresse mail modifier")
   Votre adresse mail est mis à été modifier avec succès, si ce n'est pas vous merci de contacter l'administrateur.
@endif
@if ($objet=="Mise à jour de vos information chez Groupe synapse")
   Votre profil est mis à jour avec succès, si ce n'est pas vous merci de contacter l'administrateur.
@endif
@if ($objet=="Verification de l'adresse mail réussi")
  Votre adresse mail a bien été vérifier
@endif

@if ($objet=="Ajout dans la liste des notifiable modifier")
  Vous êtes ajouter dans la liste des notifiable en cas d'un message envoyer par un visiteur sur le site <b>Groupe synapse</b> 
@endif
@if ($objet=="Soustrait dans la liste des notifiable modifier")
Vous êtes soustrait dans la liste des notifiable en cas d'un message envoyer par un visiteur sur le site <b>Groupe synapse</b> 
@endif
@endcomponent

@component('mail::button', ['url' => config('app.url')."/login"])
Aller sur la partie admin
@endcomponent
 
Cordialement,<br>
{{ config('app.name') }}
@endcomponent
