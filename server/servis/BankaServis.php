<?php

 class BankaServis{

    private $broker;

    public function __construct($b){
        $this->broker=$b;
    }

    public function vratiSve(){
        $data= $this->broker->ucitaj("select b.*, t.naziv as 'tip_banke' from banka b inner join tip_banke t on (t.id=b.tip_id)");
        $res=[];
        foreach($data as $element){
            $res[]=$this->transformisiBanku($element);
        }
        return $res;
    }

    public function vratiJedan($id){
        $data= $this->broker->ucitaj("select b.*, t.naziv as 'tip_banke' from banka b inner join tip_banke t on (t.id=b.tip_id) where b.id=".$id);
        $banka=$this->transformisiBanku($data[0]);
        $banka["ekspoziture"]=$this->broker->ucitaj("select id, adresa, broj_telefona as 'brojTelefona' from ekspozitura where banka_id=".$id);
        return $banka;
    }

    public function kreiraj($naziv,$prefiks,$sediste,$tipId) {
        if(!intval($prefiks) || strlen($prefiks)!=3){
            throw new Exception('Prefiks mora imati 3 cifre');
        }
        $banke=$this->broker->ucitaj("select id from banka where racun_prefiks=".$prefiks);
        if(count($banke)>0){
            throw new Exception('Banka sa datim prefiksom vec postoji');
        }
        $this->broker->upisi("insert into banka(naziv,racun_prefiks,sediste,tip_id) values('".$naziv."','".$prefiks."','".$sediste."',".$tipId.")");
    }


    public function obrisi($id){
        $this->broker->upisi("delete from banka where id=".$id);
    }

    public function izmeni($id,$naziv,$prefiks,$sediste,$tipId){
        if(!intval($prefiks) || strlen($prefiks)!=3){
            throw new Exception('Prefiks mora imati 3 cifre');
        }
        $banke=$this->broker->ucitaj("select id from banka where racun_prefiks=".$prefiks);
        foreach($banke as $b){
            if(intval($b->id)!=intval($id)){
                throw new Exception('Banka sa datim prefiksom vec postoji');
            }
        }
        $this->broker->upisi("update banka set naziv='".$naziv."', sediste='".$sediste."', racun_prefiks='".$prefiks."', tip_id=".$tipId."  where".$id);
    }

    public function kreirajEkspozituru($idBanke,$adresa,$brojTelefona){
        $this->broker->upisi("insert into ekspozitura(adresa,broj_telefona,banka_id) values('".$adresa."','".$brojTelefona."',".$idBanke.")");
    }
    public function obrisiEkspozituru($id,$idBanke){
        $this->broker->upisi("delete from ekspozitura where id=".$id." and banka_id=".$idBanke);
    }
    private function transformisiBanku($element){
        return [
            "id"=>$element->id,
            "naziv"=>$element->naziv,
            "sediste"=>$element->sediste,
            "racunPrefiks"=>$element->racun_prefiks,
            "tip"=>[
                "id"=>$element->tip_id,
                "naziv"=>$element->tip_banke
            ]
        ];
    } 
    
}

?>