#!/php -q
<?php

function getMeteo ($ladate, $moment){ 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'api.openweathermap.org/data/2.5/forecast?q=toulouse&APPID=29a2cb0ddcd0c77e45de399ebf790b9e');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);
curl_close($ch);
$rep = json_decode ($data);
 switch ($moment){
                case matin:
                        $heure_tmp ="09:00:00";
                        break;

                case aprem:
                        $heure_tmp= "15:00:00";
                        break;

                case journee:

                        break;
           }
$date_tmp=$ladate . " ". $heure_tmp;
foreach ($rep->list as $journee ){
	#echo $journee->dt_txt;
	#echo "\n";
  	if(!strcmp($journee->dt_txt,$date_tmp)){
		#echo "\n=====créneau trouvé=====\n";
		#cho "\n il fera:";
		return $journee->weather[0]->main;
	}	
       
}

}
#$tmp=getMeteo ("2018-03-27","aprem");
#echo "======$tmp=======";
?>
