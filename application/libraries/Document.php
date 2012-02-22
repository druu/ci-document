<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Document Template Engine
 *
 * A lightweight, marker-based template engine for CodeIgniter
 * 
 * <pre>
 * Special/Reserved markers:
 * 
 * Marker:            Used for:
 * _TPL_HEAD            Meta-Tags and CSS-Files in the &lt;head&lt;-section
 * _TPL_TITLE           Compiled page title
 * _TPL_MSG             Sytem Messages / Notifications
 * _TPL_SCRIPTS         JS files to be loaded
 * _TPL_DEBUG           Micro debug listing
 * _TPL_PROFILE         CI's Profiler output
 * _TPL_OB_CONTENTS     General output buffer
 * </pre>
 * 
 * @todo  Implement: 	System messages
 * @todo  Implement: 	General output buffering and inject to _TPL_OB_CONTENTS
 * @todo  Implement: 	Method chaining
 * @todo  Implement: 	Micro debug log
 * 
 * @todo  Handle error pages
 * @todo  Handle CI's Profiler output
 * 
 * @todo  Possibly: Store failed injections, and re-inject on_before_clean
 * 
 * @todo  Implement HTTP header handling OR
 * @todo  Possibly: Hook into CI's Output and Caching
 * 
 * @todo  Polish source & docs
 * 
 * @package		ci-document
 * @author 		_druu (david.wosnitza)
 * @copyright 	Copyright (c) 2011 // David Wosnitza
 * @license 	http://www.opensource.org/licenses/MIT MIT 
 * @version 	1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Document Template Engine Library
 *
 * This class is a lightweight, marker-based template engine
 * 
 *  
 * !!! Make sure to use them in your template !!!
 * 
 * @author 			_druu (david.wosnitza)
 * @copyright 		Copyright (c) 2011 // David Wosnitza
 * @license 		http://www.opensource.org/licenses/MIT MIT
 * @package			ci-document
 * @subpackage		Libraries
 */
class Document {
	
	/**
	* List of CSS files to load
	* 
	* @var array
	* @access protected
	*/
	protected $_css             = array(
		'screen'     => array(),
		'tty'        => array(),
		'tv'         => array(),
		'projection' => array(),
		'handheld'   => array(),
		'print'      => array(),
		'braille'    => array(),
		'aural'      => array(),
		'all'        => array()
	);
	/**
	* Associative array of Meta-Tags to add
	* 
	* @var array
	* @access protected
	*/
	protected $_meta            = array(
		'name'       => array(),
		'http-equiv' => array(),
		'property'   => array()
	);
	/**
	* List of JS files to load
	* 
	* @var array
	* @access protected
	*/
	protected $_scripts         = array(
		'body' => array(),
		'head' => array()
	);
	/**
	* List of link tags
	* 
	* @var array
	* @access protected
	*/
	protected $_links         = array();
	/**
	* Associative array of paths to use
	* 
	* @var array
	* @access protected
	*/
	protected $_paths           = array(
		'base' => '',
		'partials' => '',
		'statics' => '',
		'css' => '',
		'scripts' => '',
		'images' => ''
	);
	/**
	* CodeIgniter's super object
	* 
	* @var CI_Controller
	* @access protected
	*/
	protected $_CI              = null;
	/**
	* Basic output buffer
	* 
	* @var string
	* @access protected
	*/
	protected $_buffer          = '';
	/**
	* Page title Prefix
	* 
	* @var string
	* @access protected
	*/
	protected $_title_prefix    = '';
	/**
	* Page title suffix
	* 
	* @var string
	* @access protected
	*/
	protected $_title_suffix    = '';
	/**
	* Page title part separator
	* 
	* @var string
	* @access protected
	*/
	protected $_title_separator = '';
	/**
	 * Plain page title
	 * @var string
	 * @access protected
	 */
	protected $_title           = '';
	/**
	 * Sub-directory CI is stored in
	 * @var string
	 * @access protected
	 */
	 protected $_subdir = '';
	 /**
	  * Associative array of injections
	  * @type Array
	  */
	 protected $_injections = array();
	 /**
	  * Helper array to preserve injection order
	  * @type Array
	  */
	 protected $_injection_log = array();
	 /**
	  * Do we parse exec_vars?
	  * @type bool
	  */
	 protected $_parse_exec_vars = false;

// ------------------------------------------------------------------------

/**
 * TODOS:
 * 
 * @todo  Implement: 	Marker for setting page title from static files
 * @todo  Implement: 	System messages
 * @todo  Implement: 	General output buffering and inject to _TPL_OB_CONTENTS
 * @todo  Implement: 	Micro debug log
 * 
 * @todo  Handle error pages
 * @todo  Handle CI's Profiler output
 * 
 * @todo  Implement HTTP header handling OR
 * @todo  Possibly: Hook into CI's Output and Caching
 * 
 * @todo  Polish source & docs
 */

	
// ------------------------------------------------------------------------

	/**
	 * Constructor
	 * 
	 * The constructor can be passed an array of config values
	 * 
	 * @param array 	$document 	Template-configuration array
	 * @access public
	 */
	public function __construct($document) 
	{
		$this->_ci =& get_instance();

		$this->_subdir = substr(str_replace($_SERVER['DOCUMENT_ROOT'],'', FCPATH), 0, -1);

		$template_name = isset($document['template']) && strlen($document['template']) > 0 ? $document['template'] : 'default';

		// Let's see if we have a custom template config override situation here
		if ( file_exists(FCPATH.'/templates/'.$template_name.'/document.php'))
		{
			// Let's get those configs merged yayyyy
			include (FCPATH.'/templates/'.$template_name.'/document.php');
			if (is_array($config))
			{
				// Merge and release some memory
				$document = array_replace_recursive($document, $config);
				unset($config);
				// And just to be sure reset the template name
				$template_name = isset($document['template']) && strlen($document['template']) > 0 ? $document['template'] : 'default'; 
			}
		}
		// Apply config overrides;
		// Relative paths for in-html use
		$this->_paths['css']      = $this->_subdir.
									(
										isset($document['paths']['css']) ? 
										$document['paths']['css'] : 
										'/templates/'.$template_name.'/css'
									);

		$this->_paths['scripts']  = $this->_subdir.
									(
										isset($document['paths']['scripts']) ? 
										$document['paths']['scripts'] : 
										'/templates/'.$template_name.'/js'
									);

		$this->_paths['images']   = $this->_subdir.
									(
										isset($document['paths']['images']) ? 
										$document['paths']['images'] : 
										"/assets/img"
									);
	
		// Absolute paths for content generation
		$this->_paths['base']     = FCPATH.
									(
										isset($document['paths']['base']) ? 
										$document['paths']['base'] : 
										'templates/'.$template_name.'/base'
									);

		$this->_paths['partials'] = FCPATH.
									(
										isset($document['paths']['partials']) ? 
										$document['paths']['partials'] : 
										'templates/'.$template_name.'/partials'
									);

		$this->_paths['statics']  = FCPATH.
									(
										isset($document['paths']['statics']) ? 
										$document['paths']['statics'] : 
										'templates/'.$template_name.'/statics'
									);
		
		if(isset($document['title']) && is_array($document['title']))
		{
			$this->_title_prefix    = isset($document['title']['prefix'])    ? $document['title']['prefix']    : '';
			$this->_title_suffix    = isset($document['title']['suffix'])    ? $document['title']['suffix']    : '';
			$this->_title_separator = isset($document['title']['separator']) ? $document['title']['separator'] : '';
		}

		// Initialize 
		$this->_initialize($document);

	}

	/**
	 * Shorthand add-function
	 * 
	 * Note: THIS IS VERY EXPERIMENTAL
	 * 
	 * @param  mixed 	$args    A string or an Array of data...
	 * @param  string 	$special The optional parameter to all specific add functions
	 * @return Document
	 */
	public function add ( $args, $special = null) 
	{
		$is_string = is_string($args);
		$is_array  = is_array($args);
		$len       = count($args);

		if( ! $is_string && ! $is_array)
		{
			return $this;
		}

		if ( $is_string )
		{	
			if (substr($args, -3) === ".js") 
			{
				return $this->add_js($args, $special);
			}
			elseif (substr($args, -4) === ".css") 
			{
				return $this->add_css($args, $special);
			}
			else {
				return $this->add_meta($args, $special);
			}
		}
		elseif($is_array && $len === 1)
		{
			reset($args); 
			$m_attr_v = key($args);
			$c_attr_v = $args[$m_attr_v];
			if(in_array($special, array_keys($this->_meta)))
			{	
				return $this->add_meta($m_attr_v, $c_attr_v, $special);
			}
			else {
				return $this->add($c_attr_v, $m_attr_v);
			}
		}
		else 
		{
			foreach($args as $k => $v)
			{	
				// $k = $k ? $k : null;
				// $this->add($v, $k);
				
				if(!is_int($k))
				{	
					$k = $k ? $k : null;
					$this->add($v, $k);
				}
				else
				{
					$this->add($v, $special);
				}
			}
		}
		return $this;
	}


	

	/**
	 * Add CSS File
	 * 
	 * Adds a CSS-File to the template
	 * 
	 * @param string 	$file 	Example: '/path/to/file.css'
	 * @param string 	$media 	Defines the media type.
	 * @return void
	 * @access public
	 */
	public function add_css($file, $media="screen") 
	{	
		$media = is_null($media) ? "screen" : $media;

		if (
			$file[0] !== '/' &&
			! preg_match('~^http[s]?\:\/\/~i', $file)
		) 
		{
			$file = $this->_paths['css'].'/'.$file;
		}
		if (array_key_exists($media, $this->_css)) 
		{
			array_push($this->_css[$media], $file);
		}
		return $this;
	}

	/**
	 * Adds link tags to the documents head
	 * 
	 * 
	 * @param array $args Associative Array: array('attribute_name'=>'attribute_value')
	 * @return Document
	 */
	public function add_link($args) 
	{
		
		if(is_array($args)) 
		{	
			$link = "<link ";
			foreach($args as $k => $v)
			{
				$link .= $k.'="'.$v.'" ';
			}
			$link .= '/>';
			array_push($this->_links, $link);

		}
		return $this;
	}

	/**
	 * Add JS File
	 * 
	 * Adds a JS-File to the template
	 * 
	 * @param string 	$file 	Example: '/path/to/file.js'
	 * @param string 	$pos 	Defines where the script tag will be set. Possible values: head, body
	 * @return void
	 * @access public
	 */
	public function add_js($file, $pos = 'body') 
	{
		$pos = is_null($pos) ? "body" : $pos;
		if (
			$file[0] !== '/' &&
			! preg_match('~^http[s]?\:\/\/~i', $file)
		) 
		{
			$file = $this->_paths['scripts'].'/'.$file;
		}
		if (array_key_exists($pos, $this->_scripts)) 
		{
			array_push($this->_scripts[$pos], $file);
		}
		return $this;
	}

	/**
	 * Add Meta Tag
	 * 
	 * Adds a Meta-Tag to the template
	 * 
	 * @param string 	$name    	Meta-Tag 'name'-attribute value
	 * @param string 	$content 	Meta-Tag 'content'-attribute vakue
	 * @param string 	$main_attr 	Defines the main attribute. Possible values: name, http-equiv, property 
	 * @return void
	 * @access public
	 */
	public function add_meta($name, $content, $main_attr = 'n') 
	{	
		if (array_key_exists($main_attr, $this->_meta)) 
		{
			$this->_meta[$main_attr][$name] = $content;
		}
		return $this;
	}

	/**
	 * Get Content
	 * 
	 * Returns the current output buffer
	 * 
	 * @return string
	 * @access public
	 */
	public function get_contents() 
	{
		return $this->_buffer;
	}

	/**
	 * Inject
	 * 
	 * Replaces the given marker with the given injection
	 * 
	 * @param  string 	$marker		The marker to be replaced
	 * @param  string 	$injection 	A string containing the marker replacement
	 * @return void
	 * @access public
	 */
	public function inject($marker, $injection, $append_to_last = false) 
	{	
		$marker = strtoupper($marker);
		// Make sure we can collect dem injections
		if (!array_key_exists($marker, $this->_injections) OR !is_array($this->_injections[$marker])) 
		{
			$this->_injections[$marker] = array();
		}

		$injection_count = count($this->_injections[$marker]);
		// Appending the last injection on the given Marker?
		if ($append_to_last && isset($this->_injections[$marker][$injection_count-1])) 
		{
			$this->_injections[$marker][$injection_count-1] .= $injection;
		}
		else 
		{
			array_push($this->_injections[$marker], $injection);
			// We aren't appending, so write a log entry for later rendering
			array_push($this->_injection_log, array($marker, $injection_count));
		}

		return $this;
	}

	/**
	 * Inject static content file 
	 * 
	 * Replaces the given marker with the content of the given 
	 * static content file
	 * 
	 * @param  string 	$marker 	The marker to be replaced
	 * @param  string 	$static 	Name of the static content's file
	 * @return void
	 * @access public
	 */
	public function inject_static($marker, $static, $append_to_last = false) 
	{
		$injection = $this->_load('statics',$static);
		$this->inject($marker, $injection, $append_to_last);
		return $this;
	}


	/**
	 * Inject a template-partial
	 * 
	 * Replaces the given marker with the content of the given 
	 * template-partial
	 * 
	 * @param  string 	$marker 	The marker to be replaced
	 * @param  string 	$partial 	Name of the partial's file
	 * @return void
	 * @access public
	 */
	public function inject_partial($marker, $partial, $append_to_last = false) 
	{
		$injection = $this->_load('partials',$partial);
		$this->inject($marker, $injection, $append_to_last);		
		return $this;
	}

	/**
	 * Inject CI view file
	 * 
	 * Replaces the given marker with a CI-View file
	 * 
	 * @param  string 	$marker 	The marker to be replaced
	 * @param  string 	$file   	Name of the view's file
	 * @param  array  	$data   	An associative array of data to be 
	 *                          	extracted for use in the view.
	 * @return void
	 * @access public
	 */
	public function inject_view($marker, $file, $data = null, $append_to_last = false) 
	{
		$view = $this->_ci->load->view($file, $data, true);
		$this->inject($marker, $view, $append_to_last);
		return $this;
	}

	/**
	 * Initialize 
	 * 
	 * Load every file given by the config array, prepopulate 
	 * Meta-Tag, CSS and JS collections and apply init-time injections
	 * 
	 * @param  array 	$document Config-array passed by the {@link __construct() constructor }
	 * @return void
	 * @access protected
	 */
	protected function _initialize($document) 
	{
		// Reset Buffer
		$this->_buffer = '';

		// Compile base template markup
		if (isset($document['base'])) 
		{
			if (is_array($document['base'])) 
			{
				foreach ($document['base'] as $name ) 
				{
					$this->_buffer .= $this->_load('base', $name);
				}
			}
			else 
			{
				$this->_buffer .= $this->_load('base', $document['base']);
			}
		}
		
		// Do we have partials in the config? If so: INJECT!
		if (isset($document['inject']) && is_array($document['inject'])) 
		{
			foreach ($document['inject'] as $marker => $name ) 
			{
				$this->inject_partial(strtoupper($marker), $name);
			}
		}
		
		// Any string injections configurated? If so: Pump 'em in!
		if (isset($document['inject_string']) && is_array($document['inject_string'])) 
		{
			foreach ($document['inject_string'] as $marker => $name ) 
			{
				$this->inject(strtoupper($marker), $name);
			}
		}

		// Get them links
		if (isset($document['links']) && is_array($document['links'])) 
		{
			foreach($document['links'] as $link) 
			{
				$this->add_link($link);
			}
		}

		// Collect CSS files
		if (isset($document['css']) && is_array($document['css'])) 
		{
			foreach($document['css'] as $group => $items) 
			{
				foreach($items as $css)
				{
					$this->add_css($css, $group);
				}
			}
		}

		// Collect JS files
		if (isset($document['scripts']) && is_array($document['scripts'])) 
		{
			foreach($document['scripts'] as $group => $items) 
			{
				foreach($items as $js)
				{
					$this->add_js($js, $group);
				}
			}
		}

		// Gimme those Meta-Tags
		if (isset($document['meta']) && is_array($document['meta'])) 
		{
			foreach($document['meta'] as $group => $items) 
			{	
				foreach($items as $k => $v)
				{
					$this->add_meta($k,$v,$group);
				}
			}
		}

		// play: startup.wav
	}

	/**
	 * Set page title
	 * 
	 * Sets the page title with giving the option to directly append to the current 
	 * page title, or appending the title separator first and concatenating the title to append
	 * 
	 * @param string $title          The page title without pre- & suffix
	 * @param bool   $append         Do you want to append to the {@link $_title current title}? Default: false
	 * @param bool   $with_separator Do you want to append, using the {@link $_title_separator title separator}? Default: true
	 */
	public function set_title($title, $append = false, $with_separator = true) 
	{
		
		if ($append) 
		{
			if ($with_separator) 
			{
				$this->_title .= ' '.$this->_title_separator.' '.$title;
			}
			else 
			{
				$this->_title .= $title;
			}
		}
		else 
		{
			$this->_title = $title;
		}
		return $this;
	}

	/**
	 * Get page title
	 * 
	 * @return string
	 */
	public function get_title() 
	{
		return $this->_title;
	}

	public function parse_exec_vars($val = true) 
	{
		$this->_parse_exec_vars = is_bool($val) ? $val : TRUE;
		return $this;
	}

	/**
	 * Prepare rendering
	 * 
	 * Compile Meta-Tag, CSS and JS collections and inject into template
	 * 
	 * @return void
	 * @access public
	 */
	public function prepare_render() 
	{
		$head = array();
		foreach ($this->_meta as $group => $items)
		{
			foreach($items as $k => $v) 
			{
				array_push($head, '<meta '.$group.'="'.$k.'" content="'.$v.'" />');
			}
		}
		
		foreach ($this->_links as $link)
		{
			array_push($head, $link);
		}

		foreach ($this->_css as $group => $items)
		{
			foreach($items as $file) 
			{
				array_push($head, '<link rel="stylesheet" href="'.$file.'" media="'.$group.'"/>');
			}
		}

		foreach ($this->_scripts['head'] as $js)
		{
			array_push($head, '<script src="'.$js.'" ></script>');
		}

		
		$head_out = implode("\n", $head);



		$scripts = array();
		foreach ($this->_scripts['body'] as $js)
		{
			array_push($scripts, '<script src="'.$js.'" ></script>');
		}

		$scripts_out = implode("\n", $scripts);

		$title_out = $this->_compile_title();


		// Inject internal markers
		$this->_buffer = str_replace('</body>','#@#_TPL_SCRIPTS#@#</body>', $this->_buffer);
		
		$this->inject('_tpl_title', $title_out);
		$this->inject('_tpl_head', $head_out);
		$this->inject('_tpl_scripts', $scripts_out);

		$this->_perform_injections();
			
	}

	/**
	 * Perform injections
	 * 
	 * Alright, let's do the serious stuff. Go through the injection log and perform our beloved injections.
	 * Additionally replace CI's exev vars.
	 * 
	 * @return void
	 */
	private function _perform_injections() 
	{	
		global $BM;

		$ins = array();

		foreach ($this->_injection_log as $inject)
		{
			$ins["#@#".$inject[0]."#@#"] = $this->_injections[$inject[0]][$inject[1]];
		}	

		$this->_buffer= str_replace(array_keys($ins), $ins, $this->_buffer);


		// This bit is copied from CI's Output class
		// see: https://github.com/EllisLab/CodeIgniter/blob/develop/system/core/Output.php#L372
		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');
		
		if ($this->_parse_exec_vars === TRUE)
		{
			$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';

			$this->_buffer = str_replace('{elapsed_time}', $elapsed, $this->_buffer);
			$this->_buffer = str_replace('{memory_usage}', $memory, $this->_buffer);
		}

	}


	/**
	 * File Exists
	 * 
	 * Checks whether a file exists within our template folder
	 * 
	 * @param  string $file     The filename
	 * @param  string $path_key One of the $_path array keys (base / partials / statics)
	 * @return bool
	 */
	public function exists($file, $path_key)
	{	
		return is_file($this->_paths[$path_key].'/_'.$file.'.php');
	}

	/**
	 * Compile and return the page title
	 * 
	 * This function simply concatenates the page title related strings
	 * Note: The title separator is wrapped in spaces
	 * 
	 * @return string
	 */
	protected function _compile_title() 
	{
		$title_out = '';
		$this->_title_prefix    = trim($this->_title_prefix);
		$this->_title_suffix    = trim($this->_title_suffix);
		$this->_title_separator = trim($this->_title_separator);
		$this->_title           = trim($this->_title);
		

		if (strlen($this->_title_prefix)>0) 
		{
			$title_out .= $this->_title_prefix.( strlen($this->_title) ? ' '.$this->_title_separator.' ' : '');
		}
		$title_out .= $this->_title;
		
		if (strlen($this->_title_suffix)>0) 
		{
			$title_out .= ' '.$this->_title_separator.' '.$this->_title_suffix;
		}
		return $title_out;
	}

	/**
	 * CLEAN ALL THE THINGS!!!
	 * 
	 * Remove all unused markers from output buffer
	 * 
	 * @link http://s3.amazonaws.com/kym-assets/photos/images/original/000/140/938/responsibility12(alternate).png?1318992465
	 * @return void
	 * @access public
	 */
	public function clean() 
	{
		$this->_buffer = preg_replace('~#@#.*?#@#~', '', $this->_buffer);
	}

	/**
	 * Load
	 * 
	 * Wrapper/Helper function to load a file from the template folder
	 * 
	 * @param  string 	$path 	Key of the path's array 
	 * @param  string 	$name 	Name of file to be loaded. Without undescore prefix and .php suffix
	 * @return void
	 * @access protected
	 */
	protected function _load ($path, $name) 
	{
		return $this->_ci->load->file($this->_paths[$path]."/_$name.php", true);
	}


}

/* End of file Document.php */
/* Location: ./application/libraries/Document.php */