<?php

class Hooks_post_controller_constructor {
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
