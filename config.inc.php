<?php

// $Id: //

/**
 * @file config.php
 *
 * Global configuration variables (may be added to by other modules).
 *
 */

global $config;

// Date timezone
date_default_timezone_set('UTC');


// Elastic--------------------------------------------------------------------------------

$config['use_elastic'] = true;

$config['elastic_options'] = array(
		'index' => 'elasticsearch/wikispecies',
		'protocol' => 'http',
		'host' => '35.204.73.93',
		'port' => 80,
		'user' => 'user',
		'password' => '7WbQZedlAvzQ'
		);

	
?>