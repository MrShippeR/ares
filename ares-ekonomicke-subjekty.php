<?php


// Main code for calling function and printing basic info of we are searching for.
$ico = '49531930'; // IČO náhodného živnostníka: Miroslav Novák, truhlář.
$ares_data = dp77GetAresDataBasic($ico);
print_r($ares_data);


function dp77GetAresDataBasic($ico) {
	if ($ico == false or $ico == '')
      return false;
	
	$ares_find_url = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty-res/'.$ico;
	
	$json = file_get_contents($ares_find_url);
	$obj = json_decode($json);
	if (!is_object($obj))
		die("Chyba. ARES nevrátil data v očekávaném formátu. Ověřte zadávané IČO.");
	
	if (property_exists($obj, 'kod'))
		die('Chyba ARES. '.$obj->popis);	
	
	$records = $obj->zaznamy;
	$records = $records[0];
	$adress = $records->sidlo;

	$ares_data = array (
						'ico' 			=> $records->ico,
						'nazev_firmy' 	=> $records->obchodniJmeno,
						'adresa_firmy'	=> $adress->textovaAdresa,			
				);
	
	return $ares_data;
}