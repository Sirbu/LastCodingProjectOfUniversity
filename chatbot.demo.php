#!/php -q
<?php
// Run from command prompt > php -q chatbot.demo.php
include "websocket.class.php";
include "competence.class.php";
include "demande.class.php";
include "googlemapapi.php";
include "weather.php";

// Extended basic WebSocket as ChatBot
class ChatBot extends WebSocket{

    public $competences = array();
    public $demandes = array();

    function process($user,$msg){
        $this->say("< ".$msg);

        if(strstr($msg, 'hello')){ // just a test message
            $this->send($user->socket,"hello human");
        }else if(strstr($msg, 'CPT')){ // competence declaration
            $this->declareCompetence($user, $msg);
        }else if(strstr($msg, 'DMD')){    // demand declaration
            $this->declareDemande($user, $msg); 
        }else{                                // error handling
            $this->send($user->socket,$msg." incompréhensible. Tu ferais mieux de faire gaffe mec.");
        }
    }

    // declareCompetence adds a competence in the great array of competences
    function declareCompetence($user, $msg){
        // we parse the cmd arguments
        $cpt_args = explode(" ", $msg);

        if(count($cpt_args) < 4){
            $this->send($user->socket, "Ta commande est pourrie...");
            return;
        }

        // we parse the hours and we create a competence instance for each hour
        $horaires = explode(",", $cpt_args[1]);
    
        foreach ($horaires as $horaire) {
            $cpt = new Competence($user->id, $horaire, $cpt_args[2], $cpt_args[3]);
            $this->competences[] = $cpt;
            $detail_date = explode(')(', $horaire);
            $match = $this->searchCptMatch($cpt, $detail_date);

            var_dump($match);

            $this->send($user->socket, 'Tu as déclaré être un ' . $cpt_args[2] . ' disponible le ' . $detail_date[1] . ' ' . $detail_date[0] . ' à ' . $cpt_args[3]);
            if(count($match) == 0){
                $this->send($user->socket, 'Il n\'y a aucune correspondance pour l\'instant');
            }else{
                foreach ($match as $demand) {
                    foreach ($this->users as $userDmd) {
                        if($userDmd->id == $demand->idPersonneD){
                            $demand->matched = true;
                            // sending notify to the asker
                            $msg = 'Il y a une correspondance avec votre demande : ';
                            $msg .= $demand->nomDmd;
                            $msg .= ' '. $detail_date[1] . ' ' . $detail_date[0];  // $competence->horaireC;
                            $this->send($userDmd->socket, $msg);
                            // sending notify to the competent person
                            $msg = 'Il y a une correspondance avec votre compétence : ';
                            $msg .= $demand->nomDmd;
                            $msg .= ' '. $detail_date[1] . ' ' . $detail_date[0];  // $competence->horaireC;
                            $this->send($user->socket, $msg);
                        }
                    }
                }
            }
        }
        
    }


    function declareDemande($user, $msg){
        // we parse the cmd arguments
        // cpt_args[0] : cmd
        // cpt_args[1] : horaires
        // cpt_args[2] : compétence
        // cpt_args[3] : ville

        $cpt_args = explode(" ", $msg);

        // we deal with bad cmd
        if(count($cpt_args) < 4){
            $this->send($user->socket, "Ta commande est pourrie...");
            return;
        }

        // we parse the hours and we create a competence instance for each hour
        $horaires = explode(",", $cpt_args[1]);
        
        foreach ($horaires as $horaire) {
            
            $cpt = new Demande($user->id, $cpt_args[1], $cpt_args[2], $cpt_args[3]);
            $this->demandes[] = $cpt; 
            // searching for a match
            $detail_date = explode(')(', $horaire);
            $match = $this->searchDmdMatch($cpt, $detail_date);

            var_dump($match);

            $this->send($user->socket, 'Tu as déclaré vouloir un ' . $cpt_args[2] . ' disponible le ' . $detail_date[1] . ' ' . $detail_date[0] . ' à ' . $cpt_args[3]);
            if(count($match) == 0){
                $this->send($user->socket, 'Il n\'y a aucune correspondance pour le moment.');
            }else{
                foreach ($match as $competence) {
                    foreach ($this->users as $userCpt) {
                        if($userCpt->id == $competence->idPersonneC){
                            $competence->reserve = true;
                            // sending notify to the competent person
                            $msg = 'Il y a une correspondance avec votre compétence : ';
                            $msg .= $competence->nomCpt;
                            $msg .= ' '. $detail_date[1] . ' ' . $detail_date[0];  // $competence->horaireC;
                            $this->send($userCpt->socket, $msg);
                            // sending notify to the asker
                            $msg = 'Il y a une correspondance avec votre demande : ';
                            $msg .= $competence->nomCpt;
                            $msg .= ' '. $detail_date[1] . ' ' . $detail_date[0];  // $competence->horaireC;
                            $this->send($user->socket, $msg);
                        }    
                    }
                }
            }
        }

    }


    // TODO: there should be a way to factorize those two...
    // search for a competence match in the demands
    // Maybe if both competences and demands have attributes with same names ?
    // returns an array of demands matching the competence
    function searchCptMatch($cpt, $date_details){
        $dmdMatched = array();
        
        foreach($this->demandes as $demande){
            if($demande->matched == false){
                // the competence name is compared
                if(strcmp($demande->nomDmd, $cpt->nomCpt) == 0){
                    // then the date is compared
                    if(strcmp($demande->horaireD, $cpt->horaireC) == 0){
                        // here we check the distance and weather
                        $date_tmp = explode('-', $date_details[1]);
                        $date = $date_tmp[2].'-'.$date_tmp[1].'-'.$date_tmp[0];
                        echo($date.'||||'.$date_details[0]);
                        $weather = getMeteo($date, $date_details[0], $demande->ville);
                        echo('OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO'.$weather);
                        $distance = calculDistance($demande->ville, $cpt->ville);
                        if($distance < 50 || $weather == 'KO'){
                            $dmdMatched[] = $demande;
                        }
                    }
                }
            }
        }

        return $dmdMatched; 
    }
    
    // search for a demand match in the competences
    // returns an array of competences matching the demand
    function searchDmdMatch($dmd, $date_details){
        $cptMatched = array();
        
        foreach($this->competences as $competence){
            if($competence->reserve == false){
                if(strcmp($competence->nomCpt, $dmd->nomDmd) == 0){
                    if(strcmp($competence->horaireC, $dmd->horaireD) == 0){
                        $date_tmp = explode('-', $date_details[1]);
                        $date = $date_tmp[2].'-'.$date_tmp[1].'-'.$date_tmp[0];
                        $weather = getMeteo($date, $date_details[0], $competence->ville);
                        echo($date.'||||'.$date_details[0]);
                        echo('OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO'.$weather);
                        $distance = calculDistance($competence->ville, $dmd->ville);
                        if($distance < 50 || ($weather == 'KO' || $weather == 'city not found')){
                            $cptMatched[] = $competence;
                        }
                    }
                }
            }
        }

        return $cptMatched; 
    }

}

$master = new ChatBot("0.0.0.0",12345);
