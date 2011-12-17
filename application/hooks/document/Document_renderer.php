<?php
/**
 * Document template engine hook
 *
 * This class is used for our display_override hook
 * 
 * @author 			_druu (david.wosnitza)
 * @copyright 		Copyright (c) 2011 // David Wosnitza
 * @license 		http://www.opensource.org/licenses/MIT MIT
 * @package			ci-document
 * @subpackage		Hooks
 * @category		document
 */
 class Document_Renderer {
	static function render() {
		$CI =& get_instance();
		$CI->document->prepare_render();
		$CI->document->clean();
		echo $CI->document->get_contents();
	}
}

/* End of file Document_Renderer.php */
/* Location: ./application/hooks/document/Document_Renderer.php */