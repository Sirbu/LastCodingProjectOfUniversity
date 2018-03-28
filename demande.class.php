<?php

// CPT matin.2012-12-21,journee.2018-12-25 jardinier toulouse

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
