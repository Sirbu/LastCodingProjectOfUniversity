#!/php -q
<?php
// Run from command prompt > php -q chatbot.demo.php
include "websocket.class.php";
include "competence.class.php";

// Extended basic WebSocket as ChatBot
class ChatBot extends WebSocket{

    public $competences = array();
    public $demandes = array();

    function process($user,$msg){
        $this->say("< ".$msg);

        if(strstr($msg, 'hello')){
            $this->send($user->socket,"hello human");
        }else if(strstr($msg, 'competence')){
            $this->declareCompetence($user, $msg);
        }else{
            $this->send($user->socket,$msg." not understood");
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
        }

        $this->send($user->socket, 'you declared yourself a competent ' . $cpt_args[1]);

        $this->debug(); 
    }


    function debug(){
        var_dump($this->competences);
        var_dump($this->demandes);
    }
}

$master = new ChatBot("0.0.0.0",12345);
