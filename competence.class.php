<?php

class Competence{

    function __construct($libelle, $idPers, $horaires, $reserve){
        $this->nomCpt = $libelle;
        $this->idPersonneC = $idPers;
        $this->horairesC = $horaires;
        $this->reserve = $reserve;
    }

    public $nomCpt;
    public $idPersonneC;
    public $horairesC;
    public $reserve;
}
