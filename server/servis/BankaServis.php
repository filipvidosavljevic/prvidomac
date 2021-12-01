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
            $res[]=$this->kreirajBanku($element);
        }
        return $res;
    }

    public function vratiJedan($id){
        $data= $this->broker->ucitaj("select b.*, t.naziv as 'tip_banke' from banka b inner join tip_banke t on (t.id=b.tip_id) where b.id=".$id);
        $banka=$this->kreirajBanku($data[0]);
        $banka["ekspoziture"]=$this->broker->ucitaj("select id, adresa, broj_telefona as 'brojTelefona' from ekspozitura where banka_id=".$id);
        return $banka;
    }

    public function kreiraj($naziv,$prefiks,$sediste,$tipId) {
        $this->broker->izmeni("insert into banka(naziv,racun_prefiks,sediste,tip_id) values('".$naziv."','".$prefiks."','".$sediste."',".$tipId.")");
    }


    public function obrisi($id){
        $this->broker->izmeni("delete from banka where id=".$id);
    }

    public function izmeni($id,$naziv,$prefiks,$sediste,$tipId){
        $this->broker->izmeni("update banka set naziv='".$naziv."', sediste='".$sediste."', racun_prefiks='".$prefiks."', tip_id=".$tipId."  where".$id);
    }

    private function kreirajBanku($element){
        return [
            "id"=>$element->id,
            "naziv"=>$element->naziv,
            "racunPrefiks"=>$element->racun_prefiks,
            "tip"=>[
                "id"=>$element->tip_id,
                "naziv"=>$element->tip_banke
            ]
        ];
    } 
}

?>