<?php
include "broker.php";
include "./servis/TipBankeServis.php";
include "./servis/BankaServis.php";
class Controller{

    private $broker;
    private $tipBankeServis;
    private $bankaServis;
    private static $controller;
    
    private function __construct(){
        $this->broker=new Broker("localhost","root",'',"banke");
        $this->tipBankeServis=new TipBankeServis($this->broker);
        $this->bankaServis=new BankaServis($this->broker);
    }

    public static function getController(){
        if(!isset($controller)){
            $controller=new Controller();
        }
        return $controller;
    }
 
    public function obradiZahtev(){
        try {
           return $this->vratiOdgovor($this->izvrsi());
        } catch (Exception $ex) {
            return $this->vratiGresku($ex->getMessage());
        }
    }

    private function izvrsi(){
        $akcija=$_GET["akcija"];
        $metoda=$_SERVER['REQUEST_METHOD'];

        if($akcija=='tip.read'){
            if($metoda!="GET"){
                throw new Exception("Akcija se moze pozvati samo GET metodom");
            }
            return $this->tipBankeServis->vratiSve();
        }
        if($akcija=='banka.read'){
            if($metoda!="GET"){
                throw new Exception("Akcija se moze pozvati samo GET metodom");
            }
            return $this->bankaServis->vratiSve();
        }
        if($akcija=='banka.one'){
            if($metoda!="GET"){
                throw new Exception("Akcija se moze pozvati samo GET metodom");
            }
            return $this->bankaServis->vratiJedan($_GET["id"]);
        }
        if($akcija=='banka.create'){
            if($metoda!="POST"){
                throw new Exception("Akcija se moze pozvati samo POST metodom");
            }
            $this->bankaServis->kreiraj($_POST["naziv"],$_POST["prefiks"],$_POST["sediste"],$_POST["tipId"]);
            return null;
        }
        if($akcija=='banka.update'){
            if($metoda!="POST"){
                throw new Exception("Akcija se moze pozvati samo POST metodom");
            }
            $this->bankaServis->izmeni($_GET['id'],$_POST["naziv"],$_POST["prefiks"],$_POST["sediste"],$_POST["tipId"]);
            return null;
        }
        if($akcija=='banka.delete'){
            if($metoda!="POST"){
                throw new Exception("Akcija se moze pozvati samo POST metodom");
            }
            $this->bankaServis->obrisi($_GET["id"]);
            return null;
        }
        if($akcija=='ekspozitura.create'){
            if($metoda!="POST"){
                throw new Exception("Akcija se moze pozvati samo POST metodom");
            }
            $this->bankaServis->kreirajEkspozituru($_GET["bankaId"],$_POST["adresa"],$_POST["brojTelefona"]);
            return null;
        }
        if($akcija=='ekspozitura.delete'){
            if($metoda!="POST"){
                throw new Exception("Akcija se moze pozvati samo POST metodom");
            }
            $this->bankaServis->obrisiEkspozituru($_GET["id"],$_GET["bankaId"]);
            return null;
        }
        throw new Exception("Akcija nije podrzana");
    }

     private function vratiOdgovor($podaci){
        if(!isset($podaci)){
            return[
                "status"=>true,
            ];
        }
        return[
            "status"=>true,
            "data"=>$podaci
        ];
    }
     private function vratiGresku($greska){
        return[
            "status"=>false,
            "error"=>$greska
        ];
    }
}


?>