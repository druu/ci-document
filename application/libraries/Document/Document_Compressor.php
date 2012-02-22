<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Document Template Engine
 *
 * A lightweight, marker-based template engine for CodeIgniter
 * 
 * 
 */
class Document_Compressor {
	
	/**
	 * Regexp matching compressible 6-character RGB hex strings (e.g. #FF0000, compressible to #F00)
	 * @var $regex_rbhhex_compress
	 */
	protected $_regex_rbghex_compress = '~#([A-F0-9])\1([A-F0-9])\2([A-F0-9])\3(?!=#)~Ui';

	/**
	 * Regexp replacement expression for compressible 6-character RGB hex strings, see above
	 * @var $regex_rbghex_replace
	 */
	protected $_regex_rbghex_replace  = '#\1\2\3';

	/**
	 * Collector Array for CSS Files. key = filename, value = content
	 * @var array
	 */
	protected $_css_collector = array();

	public function __construct() {
		
	}


	/**
	 * Naive implementation of a CSS minifier
	 * @param <string> $css CSS String
	 * @return <string> Minified CSS String
	 */
	private function _minify($css) {
		$minified = preg_replace(
			array(
				'~/\*[\s\S]+?\*/~',					// block comments -- not context-sensitive
				'~[\r\n\t]~',						// CR, LF, TAB
				'~\s{2,}~',							// multiple spaces
				'~\s*?([\,\;\:\{\}])\s*~',			// spaces around meta characters
				'~;\}~',							// trailing in-block semicolon
				$this->_regex_rbghex_compress
			),
			array(
				'',
				'',
				' ',								// one space
				'\1',								// the metacharacter itself
				'}',
				$this->_regex_rbghex_replace
			),
			$css
		);
		
		return $minified;
	}

}