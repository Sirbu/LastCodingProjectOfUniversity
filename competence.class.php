<?php

class Competence{

    function __construct($idPers, $horaire, $libelle, $ville){
        $this->nomCpt = $libelle;
        $this->idPersonneC = $idPers;
        $this->horaireC = $horaire;
        $this->ville = $ville;
        $this->reserve = false;
    }

    public $nomCpt;
    public $idPersonneC;
    public $horaireC;
    public $ville;
    public $reserve;
}
