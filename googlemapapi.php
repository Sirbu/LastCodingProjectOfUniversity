#!/bin/php -q
<?php
function calculDistance ($depart,$destination) {

$ch = curl_init();

curl_setopt($ch,CURLOPT_URL,"https://maps.googleapis.com/maps/api/distancematrix/json?origins=$depart&destinations=$destination&key=AIzaSyA5TmSOwCKpxqyYid46sPvhsk0eqtkcOdM");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

//Execute the request.
$data = curl_exec($ch);

//Close the cURL handle.
curl_close($ch);
#echo $data;
//Print the data out onto the page.
$rep=json_decode($data,true);
#echo "+++++++++++++\n";

$elmnt = $rep['rows'][0]['elements'][0];
#	echo "------------";
#	echo $elmnt ['elements']['distance']['text'];
	#var_dump ($elmnt);
#	echo "------------\n";
	#oint_r ($elmnt);
	echo "la distance:";
	#echo $elmnt['distance']['text'];
	echo "\nla durée:"; 
	#echo $elmnt['duration']['text'];
	echo "\n";
	if (!strcmp ($elmnt['status'], "NOT_FOUND" )){
		return $elmnt['status'];
	}else {
		$tmp = explode (" ",$elmnt['distance']['text']);
		return $tmp[0];
		
	}
	
}
#$reponse=calculDistance ('Toulouse','Biarritz');
#echo "This is the response : " . $reponse;
?>
