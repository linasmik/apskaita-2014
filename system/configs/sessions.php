<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

// Sesijos nustatymai
$config = array(
	'sess_cookie_name'		=> 'web_sess_id',
	'sess_expiration'		=> 7200,
	'cookie_path'			=> '/',
	'cookie_domain'			=> '',
	'sess_time_to_update'		=> 300,
);

?>