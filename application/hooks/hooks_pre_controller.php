<?php

class Hooks_pre_controller {
    var $CI;

    function __construct() {
        $this->CI =& get_instance();
    }
    
    function navigateur_check() {

		if(preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])){ 
            $this->CI->load->view('navigateur_incompatible');
		}
    }
}
