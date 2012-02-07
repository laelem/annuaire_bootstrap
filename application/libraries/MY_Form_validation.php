<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Surcharge de fonctions :
 * 
 * Fonction alpha : utilisation d'une fonction php pour autoriser les accents
 * 
 */
class MY_Form_validation extends CI_Form_validation {

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		return ctype_alpha(utf8_decode($str));
	}

}

