@component('mail::message')
# Bonjour {{ $user->name }}

@component('mail::panel')
Vous avez une nouvelle commande provenant du site.
Merci de vous connectez comme administrateur pour le voir.
@endcomponent
@component('mail::table')
| NOM           | PRIX          | QUANTITE | CODE |
| ------------- |:-------------:| --------:|---------:|
|{{ $cmd->produit->nom }} | {{ $cmd->produit->prix." ".$cmd->produit->monaie }}| {{ $cmd->qte }}|{{ $cmd->code }}|

@endcomponent
{{-- @component('mail::panel')

        @forelse ($cmd as $pt)
        <tr>


            <td class="product-quantity text-center">
                <div class="product-quantity mt-10 mb-10">
                    <div class="product-quantity-form cp-cart-quantity">
                        <form action="#">
                            @if ($cmd->qte>1)
                            <button class="cart-minus" id="{{ $cmd->produit->id }}"
                                onclick="addNbrCard(this,'moins')">
                                <i class="far fa-minus"></i></button>
                            @endif
                            <input class="cart-input" type="text" value="{{ $cmd->qte }}">
                            <button class="cart-plus" id="{{ $cmd->produit->id }}"
                                onclick="addNbrCard(this,'plus')"><i
                                    class="far fa-plus"></i></button>
                        </form>
                    </div>
                </div>
            </td>
            <td>{{ $cmd->produit->prix." ".$cmd->produit->monaie }}</td>
            <td>{{$cmd->produit->prix*$cmd->qte." ".$cmd->produit->monaie}}</td>
            <td>
                <a href="#" alt="Rétirer du panier" id="{{ $cmd->id }}"
                    onclick="deletTpCard(this)">
                    <i class="far fa-trash-alt" alt="Rétirer du panier"></i></a>
            </td>
        </tr>

@endcomponent --}}

@component('mail::button', ['url' => config('app.url')])
Aller sur la partie admin
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
