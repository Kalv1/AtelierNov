<?php

namespace lehangar\control;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use lehangar\model\Produit;
use lehangar\model\Producteur;
use lehangar\model\Categorie;
use lehangar\view\HangarView;
use mf\control\AbstractController;
use lehangar\model\Commande;
use lehangar\model\Contenu;
use mf\router\Router;

class HangarController extends AbstractController
{
    public function construct() {
        parent::__construct();
    }

    public function sendCoord(){
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        $prenom = filter_var(trim($prenom), FILTER_SANITIZE_STRING);
        $nom = filter_var(trim($nom), FILTER_SANITIZE_STRING);
        $tel = filter_var(trim($tel), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($email), FILTER_SANITIZE_STRING);

        //Calculer le montant total
        
        $montant = 0;
        $quantite = 0;
        foreach($_SESSION['cart'] as $cart) {
            $montant += $cart[2];
            $quantite += $cart;
        }     

        $command = new Commande();
        $command->nom_client = $nom;
        $command->prenom_client = $prenom;
        $command->mail_client = $email;
        $command->tel_client = $tel;
        $command->montant = $montant;
        $command->etat = 0;
        $command->save();

        /*
        foreach(){
            $contenu = new Contenu();
            $contenu->quantite = $quantite;
            $contenu->
            $contenu->save();
            header('Location: home/');
        }
        */
        header('Location: /home/');
    }

    public function viewProduit(){
        $produits = Produit::select()->get();
        $view = new HangarView($produits);
        $view->addStyleSheet('/html/css/style.css');
        $view->render('produit');
    }
  
    public function viewProd(){
        $prod = Producteur::get();
        $view = new HangarView($prod);
        $view->render('producteur');

    }

    public function addToCart(){
        $quantite = filter_var($_POST['quantite'], FILTER_VALIDATE_INT);
        $produit = $_POST['produit'];
        if (isset($quantite)){
            $prixLot = $produit->tarif_unitaire * $quantite;
            array_push($_SESSION['cart'], [$produit, $quantite, $prixLot]);
        }
    }

    public function viewArticle(){
        try {
            $res = Produit::where('id','=',$_GET['id'])->firstOrFail();
            $view = new HangarView($res);
            $view->render('view');
        } catch (ModelNotFoundException $e) {
            echo "Incorrect product number";
        }

    }
}