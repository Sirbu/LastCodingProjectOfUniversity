#!/php -q
<?php
// Run from command prompt > php -q chatbot.demo.php
include "websocket.class.php";
include "competence.class.php";
include "demande.class.php";


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

    function declareCompetence($user, $msg){
        // we parse the cmd arguments
        $cpt_args = explode(" ", $msg);

        // we deal with bad cmd
        if(count($cpt_args) < 3){
            $this->send($user->socket, "Your command is garbage dude !");
            return;
        }

        // we parse the hours and we create a competence instance for each hour
        $horaires = explode(",", $cpt_args[2]);

        foreach ($horaires as $horaire) {
            $cpt = new Competence($cpt_args[1], $user->id, $horaire, false);
            $this->competences[] = $cpt;

            $match = $this->searchCptMatch($dmd);
        }

        $this->send($user->socket, 'you declared yourself a competent ' . $cpt_args[1]);
    }

    function declareDemande($user, $msg){
        // we parse the cmd arguments
        $cpt_args = explode(" ", $msg);

        // we deal with bad cmd
        if(count($cpt_args) < 3){
            $this->send($user->socket, "Your command is garbage dude !");
            return;
        }

        // we parse the hours and we create a competence instance for each hour
        $horaires = explode(",", $cpt_args[2]);

        foreach ($horaires as $horaire) {
            $cpt = new Demande($cpt_args[1], $user->id, $horaire, false);
            $this->demandes[] = $cpt;
    
            // searching for a match
            $match = $this->searchDmdMatch($cpt);
        }

        $this->send($user->socket, 'You asked for a competent ' . $cpt_args[1]);

        echo("Dump Match  ");
        var_dump($match);
    }


    // TODO: there should be a way to factorize those two...
    // search for a competence match in the demands
    // returns an array of demands matching the competence
    function searchCptMatch($cpt){
        $dmdMatched = array();
        
        foreach($this->demandes as $demande){
            if(strcmp($demande->nomDmd == $cpt->nomCpt) == 0){
                if(strcmp($demande->horaireD == $cpt->horaireC) == 0){
                    $dmdMatched[] = $demande;
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
            if(strcmp($competence->nomCpt == $dmd->nomDmd) == 0){
                if(strcmp($competence->horaireC, $dmd->horaireD) == 0){
                    $cptMatched[] = $competence;
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
