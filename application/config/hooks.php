<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

// $hook['post_controller_constructor'][] = array(
	// 'class'    => 'Hooks_post_controller_constructor',
	// 'function' => 'navigateur_check',
	// 'filename' => 'hooks_post_controller_constructor.php',
	// 'filepath' => 'hooks',
// );

$hook['post_controller_constructor'][] = array(
	'class'    => 'Hooks_post_controller_constructor',
	'function' => 'start',
	'filename' => 'hooks_post_controller_constructor.php',
	'filepath' => 'hooks',
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */