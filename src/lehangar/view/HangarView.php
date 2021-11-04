<?php

namespace lehangar\view;

use lehangar\model\Categorie;
use lehangar\model\Producteur;
use mf\router\Router;
use mf\utils\HttpRequest;

class HangarView extends \mf\view\AbstractView
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    protected function renderBody($selector)
    {
        /**
         * Switch pour le choix du render.
         */
        $html = $this->renderHeader();

        switch ($selector){
            case 'producteur':
                $html .= $this->renderProducteur();
                break;
            case 'cart':
                $html .= $this->renderCart();
                break;
            case 'produit';
                $html .= $this->renderProduit();
                break;
            case 'coord';
                $html .= $this->renderCoord();
                break;
            case 'view';
                $html .= $this->renderView();
                break;
        }
        return $html;
    }

    private function renderView(){
        $res = $this->data;
        $categorie = Categorie::where('id','=',$res->categorie_id)->first();
        $producteur = Producteur::where('id', 'like', $res->prod_id)->first();
        $html = "<div>
                    <div>
                        <img src='https://www.breakingnews.fr/wp-content/uploads/2020/08/pommes-de-terre-crues.jpg'>
                        <p>$res->description</p>
                    </div>
                    <div>
                        <p>Produit : $res->nom</p>
                        <p>Type : $categorie->nom</p>
                        <p>Prix : $res->tarif_unitaire</p>
                        <p>Producteur : $producteur->nom</p>
                    </div>
                    <form action='../AjouterPanier/' method='post'>
                                    <input type='hidden' name='produit' value='$res'>
                                    <select name='quantite'>
                                        <option value=''>--Please choose an option--</option>";

        for ($i = 0; $i < 21; $i++){
            $html .= "<option value='$i'>$i</option>";
        }
        $html .= "</select>
                                        <input type='submit' value='Ajouter au panier'>
                                    </form>
                 </div>";
        return $html;

    }
  
    private function renderCoord(){
        return "<div>
                    <h2>Vos coordonnées ☎️ :</h2>
                    <div>
                        <form action='../sendCoord/' method='post'>
                                Prénom :<input type='text' name='prenom' required>
                                Nom :<input type='text' name='nom' required>
                                <br />
                                Téléphone :<input type='number' name='tel' required>
                                <br />
                                Mail :<input type='email' name='email' required>
                                <br />
                            <p>⚠️ Le paiement s'effectue lors du retrait de la commande</p>                    
                            <button type='submit'>Valider</button>
                        </form>
                    </div>
                </div>";
    }
    private function renderProducteur(){
        $html = '<section>
                    <div>
                        <h1>Découvrez nos producteurs ! 🌾</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque quis lorem ut purus posuere tempus nec at ante. Suspendisse ornare pulvinar pellentesque. Nullam sed viverra velit. Aliquam et nulla ut leo accumsan viverra nec non sapien. Suspendisse vel sapien leo. Nullam convallis ultricies nibh, vel facilisis arcu efficitur vel. Sed sapien risus, lacinia vitae lacus eget, porttitor tempor nibh.</p>
                    </div>';
        foreach ($this->data as $producteur) {
            $html .= '<div>
                        <img src="https://www.tutorialspoint.com/assets/profiles/13558/profile/60_76068-1512713229.jpg">
                        <p>' . $producteur->nom . '</p>
                        <p>' . $producteur->mail . '</p>
                        <p>' . $producteur->localisation . '</p>
                      </div>';
        }
        $html .= '</section>';

        return $html;
    }
    
    protected function renderHeader(){
        $r = new Router();
        return "
            <header>
                <div>
                    <h1>LeHangar.local 🥕</h1>
                </div>
                <nav>
                    <a href=". $r->urlFor('accueil', []) .">Accueil</a>
                    <a href=". $r->urlFor('producteurs', []). ">Producteurs</a>
                    <a href='#'>Panier</a>
                </nav>
            </header>
        ";
    }

    protected function renderCart(){
        $prixTotal = 0;
        $html = "
            <section>
                <h2>Votre panier 🛒:</h2>
                <div>
                    <div> <!-- div avec overflow: scroll -->
                        ";

                    foreach ($this->data as $article){
                        $prixTotal += $article[2];
                        $html .= "
                            <div>
                                <div>
                                    <p>Quantité: $article->quantite</p>
                                </div>
                                <div>
                                    <p>Produit : $article->nom</p>
                                    <p>Prix : $article->prix</p>
                                    <p>Producteur : $article->nom</p>
                                </div>
                                <div>
                                    <p>Total: $article[2]</p>
                                </div>
                            </div>
                        ";
                    }

        $html .="
                    </div>
                    
                    <div>
                        <p>Total: $prixTotal €</p>
                        <a href='#'>Valider</a>
                    </div>
                </div>
            </section>
        ";

        return $html;
    }
  
   private function renderProduit()
    {
        $http_req = new HttpRequest();
        $r = new Router();
        $html = "
            <section>
                        ";

        foreach ($this->data as $produit) {
            $html .= "
                            <div>
                                <a href=". $r->urlFor('view',['id' => $produit->id]).">
                                    <div>
                                    <img src='$http_req->root/html/img/$produit->img'>   
                                    </div>                          
                                    <div>
                                        <p>$produit->nom <br>$produit->tarif_unitaire €</p>
                                        <p>" . $produit->producteur->nom ."</p>
                                    </div>
                                </a>
                                    <div>
                                        <form action='../AjouterPanier/' method='post'>
                                        <input type='hidden' name='produit' value='$produit'>
                                        <select name='quantite'>
                                            <option value=''> 0 </option>";

            for ($i = 1; $i < 21; $i++){
                $html .= "<option value='$i'>$i</option>";
            }
            $html .= "</select>
                                            <input type='submit' value='Ajouter au panier'>
                                        </form>
                                    </div>
                            </div>
                        ";
        }

        $html .= "
            </section>
        ";

        return $html;
    }
}