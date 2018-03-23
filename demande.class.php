<?php

class Demande{

    function __construct($libelle, $idPers, $horaires){
        $this->nomDmd = $libelle;
        $this->idPersonneD = $idPers;
        $this->horairesD = $horaires;
        $this->matched = false;
    }

    public $nomDmd;
    public $idPersonneD;
    public $horairesD;
    public $matched;
}
