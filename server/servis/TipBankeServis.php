<?php

 class TipBankeServis{

    private $broker;

    public function __construct($b){
        $this->broker=$b;
    }

    public function vratiSve(){
      return $this->broker->ucitaj("select * from tip_banke");
    }
}

?>