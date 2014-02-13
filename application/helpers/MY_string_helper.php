<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('print_rh'))
{
	function print_rh($str)
	{
		echo '<pre>';
		print_r($str);
		echo '</pre>';
	}
}

// Récupération de la liste des pays via un fichier xml
if ( ! function_exists('liste_pays'))
{
	function liste_pays()
	{
		$pays_xml = simplexml_load_file(APPPATH.'data/pays.xml');
		$tab_pays = array(0 => '');
		$i = 0;
		while($pays_xml->country[$i]){
			$tab_pays[] = $pays_xml->country[$i]->name_country_uppercase;
			++$i;
		}
		setlocale(LC_ALL, "fr_FR.UTF-8");
		sort($tab_pays, SORT_LOCALE_STRING);
		
		return $tab_pays;
	}
}