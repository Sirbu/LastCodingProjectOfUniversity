<?php

class Demande{

    function __construct($libelle, $idPers, $horaires){
        $this->nomDmd = $libelle;
        $this->idPersonneD = $idPers;
        $this->horaireD = $horaires;
        $this->matched = false;
    }

    public $nomDmd;
    public $idPersonneD;
    public $horaireD;
    public $matched;
}
