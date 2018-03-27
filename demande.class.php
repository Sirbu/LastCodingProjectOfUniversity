<?php

// 10:00 11:00 21/12/2012

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
