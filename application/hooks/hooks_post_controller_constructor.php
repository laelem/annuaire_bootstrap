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
	
	function start() {

		if($this->CI->layout){
			// $this->CI->layout->ajouter_css('jquery-ui/ui-lightness/jquery-ui-1.8.17.custom');
			$this->CI->layout->ajouter_css('bootstrap.min');
			$this->CI->layout->ajouter_css('bootstrap-responsive.min');
			$this->CI->layout->ajouter_css('style');
			$this->CI->layout->ajouter_css('chosen/chosen/chosen');
			$this->CI->layout->ajouter_js('jquery/jquery-1.7.1.min');
			// $this->CI->layout->ajouter_js('jquery-ui/jquery-ui-1.8.17.custom.min');
			$this->CI->layout->ajouter_js('bootstrap.min');
			$this->CI->layout->ajouter_js('chosen/chosen/chosen.jquery.min');
			$this->CI->layout->ajouter_js('main');
		}
    }
}
