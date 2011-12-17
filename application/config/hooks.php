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

$hook['display_override'] = array(
	'class' => 'Document_Renderer',
	'function' => 'render',
	'filename' => 'Document_renderer.php',
	'filepath' => 'hooks/document'
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */