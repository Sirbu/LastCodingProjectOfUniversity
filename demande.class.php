<?php

// CPT mat)(21/12/2012,jou)(25/12/2018 jardinier toulouse

class Demande{

    function __construct($idPers, $horaire, $libelle, $ville){
        $this->nomDmd = $libelle;
        $this->idPersonneD = $idPers;
        $this->horaireD = $horaire;
        $this->ville = $ville;
        $this->matched = false;
    }

    public $nomDmd;
    public $idPersonneD;
    public $horaireD;
    public $ville;
    public $matched;
}
