<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die();

// Duomenu bazes nustatymai
$config['webdb'] = array(
	'hostname'	=> 'localhost',
	'database'	=> 'apskaita',
	'username'	=> 'root',
	'password'	=> '',
	'char_set'	=> 'utf8',
	'dbcollat'	=> 'utf8_general_ci',
	'save_queries'	=> true,
	'cachedir'	=> DIR_CACHE
);

// Duomenu bazes lenteles
define('TABLE_SESSIONS',	'sessions');	// Sesijos
define('TABLE_CONFIGS',		'configs');	// Konfiguracija
define('TABLE_USERS',		'users');	// Vartotoju lentute
define('TABLE_USERS_LOGS',	'users_logs');	// Vartotoju logai
define('TABLE_MEMBERS',		'members');	// Nariu lentele
define('TABLE_TAXES',		'taxes');	// Mokesciu lentele

?>