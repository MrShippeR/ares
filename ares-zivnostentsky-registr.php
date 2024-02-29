<?php

// Main code for calling function and printing basic info of we are searching for.
$ico = '49531930'; // IČO náhodného živnostníka: Miroslav Novák, truhlář.
$ares_data = dp77GetAresDataZivnostentsky($ico);
print_r($ares_data);



function dp77GetAresDataZivnostentsky($ico) {
	// sources and used documentation:
	// https://www.garth.cz/ostatni/ares-ziskani-dat-pomoci-php/
	// https://www.rzp.cz/cgi-bin/aps_cacheWEB.sh?VSS_SERV=ZVWSBJVYP&OKRES=&CASTOBCE=&OBEC=&ULICE=&CDOM=&COR=&COZ=&ICO=49531930&OBCHJM=&OBCHJMATD=0&ROLES=P&JMENO=&PRIJMENI=&NAROZENI=&ROLE=&VYPIS=2&type=&PODLE=subjekt&IDICO=eff596fd4221d0e1fc68&HISTORIE=0
	// https://ares.gov.cz/swagger-ui/#/

	if ($ico == false or $ico == '')
      return false;
	
	$ares_find_url = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty-rzp/'.$ico;
	
	// https://stackoverflow.com/questions/15617512/get-json-object-from-url
	$json = file_get_contents($ares_find_url);
	$obj = json_decode($json);
	if (!is_object($obj))
		return false; // or you can use: die("Chyba. ARES nevrátil data v očekávaném formátu. Ověřte zadávané IČO.");
	
	if (property_exists($obj, 'kod'))
		return false; // die('Chyba ARES. '.$obj->popis);
	
	$records = $obj->zaznamy;
	$records = $records[0];
	$adress = $records->adresySubjektu;
	$adress = $adress[0];
	$person = $records->osobaPodnikatel;
	$activities = $records->zivnosti;
	
	$array_activities = array();
	foreach($activities as $activity) {
		if (property_exists($activity, 'predmetPodnikani'))
			$array_activities[] = $activity->predmetPodnikani;
	}
	$sorted_activity = '';
	foreach($array_activities as $activity) {
		// to eliminate responses citating paragraphs like: Podnikání podle paragrafu živnostentského zákona bla bla bla....
      if (strlen($activity) < 50)
			$sorted_activity = $activity;
	}
	
	$ares_data = array (
						'ico' 			=> $records->ico,
						'nazev_firmy' 	=> $records->obchodniJmeno,
						'adresa_firmy'	=> $adress->textovaAdresa,
						'druh_zivnosti' => $sorted_activity,
						'jmeno'			=> $person->jmeno,
						'prijmeni'		=> $person->prijmeni,
				);
	return $ares_data;
}

