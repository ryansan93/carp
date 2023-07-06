
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Config File
 */

// Site Details
$config['connection'] = array(
	'default' => array(
		'driver'    => 'mysql',

		// NOTE : LIVE DATABASE
		// 'host'      => 'localhost',
		// 'database'  => 'gf_pos',
		// 'username'  => 'sa',
		// 'password'  => 'admin123',

		// NOTE : LOCAL DATABASE
		'host'      => 'localhost',
		'database'  => 'carp',
		'username'  => 'root',
		'password'  => '',

		'charset'   => 'utf8',
		'collation' => 'utf8_general_ci',
		'prefix'    => '',
	),
);
