#!/php -q
<?php
// Run from command prompt > php -q chatbot.demo.php
include "websocket.class.php";
include "competence.class.php";
include "demande.class.php";
include "googlemapapi.php";

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
            $this->send($user->socket,$msg." not understood. You better make no mistake next time dude.");
        }
    }

    // declareCompetence adds a competence in the great array of competences
    function declareCompetence($user, $msg){
        // we parse the cmd arguments
        $cpt_args = explode(" ", $msg);

        var_dump($cpt_args);

        if(count($cpt_args) < 3){
            $this->send($user->socket, "Your command is garbage dude !");
            return;
        }

        // TODO: improve hours system
        // we parse the hours and we create a competence instance for each hour
        // $horaires = explode(",", $cpt_args[1]);
        $cpt = new Competence($user->id, $cpt_args[1], $cpt_args[2], $cpt_args[3]);
        $this->competences[] = $cpt;
        $match = $this->searchCptMatch($cpt);
        
        $this->send($user->socket, 'You declared yourself a competent ' . $cpt_args[2]);
        if(count($match) == 0){
            $this->send($user->socket, 'There is no matching demand yet.');
        }else{
            foreach ($match as $demand) {
                foreach ($this->users as $userDmd) {
                    if($user->id == $demand->idPersonneC){
                        $demand->matched = true;
                        
                        // sending notify to the asker
                        $msg = 'Someone matched with your demand : ';
                        $msg .= $competence->nomCpt;
                        $msg .= ' '. $competence->horaireC;
                        $this->send($userCpt->socket, $msg);
                        // sending notify to the competent person
                        $msg = 'Someone matched with your demand : ';
                        $msg .= $competence->nomCpt;
                        $msg .= ' '. $competence->horaireC;
                        $this->send($user->socket, $msg);
                    }
                }
            }
        }
    }


    function declareDemande($user, $msg){
        // we parse the cmd arguments
        $cpt_args = explode(" ", $msg);

        var_dump($cpt_args);

        // we deal with bad cmd
        if(count($cpt_args) < 3){
            $this->send($user->socket, "Your command is garbage dude !");
            return;
        }

        // we parse the hours and we create a competence instance for each hour
        // $horaires = explode(",", $cpt_args[1]);
        $cpt = new Demande($user->id, $cpt_args[1], $cpt_args[2], $cpt_args[3]);
        $this->demandes[] = $cpt; 
        // searching for a match
        $match = $this->searchDmdMatch($cpt);
        
        $this->send($user->socket, 'You asked for a competent ' . $cpt_args[2]);
        if(count($match) == 0){
            $this->send($user->socket, 'There is no matching competence yet.');
        }else{
            foreach ($match as $competence) {
                foreach ($this->users as $userCpt) {
                    if($userCpt->id == $competence->idPersonneC){
                        $competence->reserve = true;
                        // sending notify to the competent person
                        $msg = 'Someone matched with your competence : ';
                        $msg .= $competence->nomCpt;
                        $msg .= ' '. $competence->horaireC;
                        $this->send($userCpt->socket, $msg);
                        // sending notify to the asker
                        $msg = 'Someone matched with your demand :';
                        $msg .= $competence->nomCpt;
                        $msg .= ' '. $competence->horaireC;
                        $this->send($user->socket, $msg);
                    }    
                }
            }
        }

        var_dump($this->demandes);
        var_dump($match);
    }


    // TODO: there should be a way to factorize those two...
    // search for a competence match in the demands
    // Maybe if both competences and demands have attributes with same names ?
    // returns an array of demands matching the competence
    function searchCptMatch($cpt){
        $dmdMatched = array();
        
        foreach($this->demandes as $demande){
            if($demande->matched == false){
                // the competence name is compared
                if(strcmp($demande->nomDmd == $cpt->nomCpt) == 0){
                    // then the date is compared
                    if(strcmp($demande->horaireD == $cpt->horaireC) == 0){
                        // here we check the distance
                        $distance = calculDistance($demande->ville, $cpt->ville);
                        if($distance < 50){
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
    function searchDmdMatch($dmd){
        $cptMatched = array();
        
        foreach($this->competences as $competence){
            if($competence->reserve == false){
                if(strcmp($competence->nomCpt, $dmd->nomDmd) == 0){
                    if(strcmp($competence->horaireC, $dmd->horaireD) == 0){
                        $distance = calculDistance($competence->ville, $dmd->ville);
                        if($distance < 50){
                            $cptMatched[] = $competence;
                        }
                    }
                }
            }
        }

        return $cptMatched; 
    }

    function debug(){
        var_dump($this->competences);
        var_dump($this->demandes);
    }
}

$master = new ChatBot("0.0.0.0",12345);
