#!/php -q
<?php
## la fonction prend un moment de la journée : matin, aprem ou journee
## et renvoie OK si il ne pleut pas pendant ce moment la ou KO sinon.
function getMeteo ($ladate, $moment,$ville){ 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "api.openweathermap.org/data/2.5/forecast?q=$ville&APPID=29a2cb0ddcd0c77e45de399ebf790b9e");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);
curl_close($ch);
$rep = json_decode ($data);
$case_error = $rep->message;
if (!strcmp ($case_error,"city not found")){
	return $case_error;

}
# par soucis de simplification nous considerons que la météo du matin est donnée par celle de 9h00 renvoyée par l'api
# et que celle de l'après-midi est donnée par la météo de 15h00 renvoyée par l'api
 switch ($moment){
                case matin:
                        $heure_tmp ="09:00:00";
                        break;

                case aprem:
                        $heure_tmp= "15:00:00";
                        break;
           }




$date_tmp=$ladate . " ". $heure_tmp;

if (!strcmp($moment,"matin") or !strcmp($moment,"aprem")){
foreach ($rep->list as $journee ){

  	if(!strcmp($journee->dt_txt,$date_tmp)){
	#	echo "matin ou aprem";
	#	echo  $journee->weather[0]->main;
		if (!strcmp($journee->weather[0]->main,"Rain")){
			return "KO";
		}else {
			return "OK";
		}
	}
}	
	
## dans le cas ou le demande de la prévision concerne la journée entiere 

## la fonction renvoie KO si il pleut aumoins le matin ou l'apres-midi sinon OK
 
}else if (!strcmp($moment,"journee")){

	$date_tmp=$ladate." "."09:00:00";
	foreach ($rep->list as $journee ){

        	if(!strcmp($journee->dt_txt,$date_tmp)){

                	$meteo= $journee->weather[0]->main;
        	}
	}	
	$date_tmp=$ladate." "."15:00:00";
        foreach ($rep->list as $journee ){

                if(!strcmp($journee->dt_txt,$date_tmp)){

                        $meteo.= $journee->weather[0]->main;
        	}
	}
	#echo "********$meteo********";
	if (stripos($meteo,"Rain")===false){
		return "OK";
		
	}else{
		return "KO";
	}
}
 
}  




#$tmp=getMeteo ("2018-03-30","journee","Paris");
#echo "======$tmp=======";
?>
