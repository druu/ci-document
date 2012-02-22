<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------------
| Document Template Engine
|--------------------------------------------------------------------------------
|
| This file will contain the settings needed to use the Document Template Engine.
|
| For complete instructions please GTFO ;)
| Just kidding. Content tba.
|
|--------------------------------------------------------------------------------
| EXPLANATION OF VARIABLES
|--------------------------------------------------------------------------------
|
| tba.
|
*/

// Template folder
$config['template']                   = 'default';

// Base Template files
$config['base']                       = 'index';

// Partials that will be injected on every page
//$config['inject']['partial']        = 'partial';

// Other injections performed on every page
$config['inject_string']['copyright'] = 'CI-DOCUMENT &copy; '.date('Y');

// Page Title
$config['title']['prefix'] = 'ci-document';
$config['title']['suffix'] = 'codeigniter';
$config['title']['separator'] = '|';


// Path overrides
//$config['paths']['css']               = '/templates/default/css';
//$config['paths']['scripts']           = '/ci-document/templates/default/js';


// Pre-populate <head> section
// 1st key: meta tags
// 2nd key: main attribute: name/http-equiv/property
// 3rd key: value of main attribute
//   value: value of content attribute
$config['meta']['name']['author']             = 'david.wosnitza';
$config['meta']['name']['description']        = 'Mothereffing Template Engine! ci-document template base';
$config['meta']['name']['viewport']           = 'width=device-width,initial-scale=1';

// This is how you add general link tags
$config['links'][]	                          = array('rel'=>'favicon', 'src'=>'/favicon.ico');                            

// 1st key: stylesheet links
// 2nd key: media-attribute value
$config['css']['screen'][]                    = 'bootstrap.min.css';
$config['css']['screen'][]                    = 'bootstrap-responsive.min.css';
$config['css']['screen'][]                    = 'style.css';

// JS Files needed on every page
// 1st key: script files
// 2nd key: position: head or body
$config['scripts']['head'][]                  = 'modernizr.min.js';
$config['scripts']['body'][]                  = 'jquery.min.js';
$config['scripts']['body'][]                  = 'bootstrap.min.js';
