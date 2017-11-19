<?php

error_reporting(E_ALL);
//error_reporting(0);

date_default_timezone_set('Europe/Vilnius');

// Pagrindinis katalogas
define('WEB_INIT',str_replace("\\", "/", realpath(dirname(__FILE__))).'/');

// Katalogai
define('DIR_SYSTEM',	WEB_INIT.'system/');		// Sistemos funkcionalumo katalogas
define('DIR_LIBRARIES',	DIR_SYSTEM.'libraries/');	// Objekati, klases
define('DIR_CONFIGS',	DIR_SYSTEM.'configs/');		// Konfiguracija
define('DIR_HELPERS',	DIR_SYSTEM.'helpers/');		// Pagalbines funkcijos
define('DIR_HTML',	DIR_SYSTEM.'html/');		// Svetaines html sablonai
define('DIR_PAGES',	DIR_SYSTEM.'pages/');		// Svetaines puslapiai
define('DIR_LOGS',	DIR_SYSTEM.'logs/');		// Logai
define('DIR_CACHE',	DIR_SYSTEM.'cache/');		// MySQL sukesuoti duomenys

// php pletinys
define('EXT','.php');

// ajax identifikatorius
define('IS_AJAX',
	isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
);

// Ikeliam pagalbines funkcijas
require_once DIR_HELPERS.'func'.EXT;

// Ikeliam branduoli
require_once DIR_SYSTEM.'core'.EXT;

$core = new Core;

?>