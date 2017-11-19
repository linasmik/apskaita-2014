<?php

require 'init.php';

// Patikriname ar prisijunges
if($core->user->isLogged())
{
	// Prisijungusio vartotojo langas
	$pagename = 'members';
}
else
{
	$pagename = 'login';
}

// $_GET masyvo elementai
$get_data = array();

if(isset($_GET['get_data']))
{
	// Isiskaidom gautus duomenis
	$get_data = explode('/',strtolower($_GET['get_data']));

	// Kiek isviso elementu atskirtu pasviruoju bruksniu
	$count = count($get_data);

	// Suzinome paskutini elementa
	$end = 0;
	if($count > 1)
	{
		$end = $count-1;
	}

	// Jei paskutinis elementas tuscias, ji isvis panaikiname
	if($get_data[$end] == "")
	{
		unset($get_data[$end]);
	}

	// Nusetiname nebenaudojamus kintamuosius
	unset($count,$end);

	// Patikriname ar nurodytas puslapis
	if(isset($get_data[0]))
	{
		$pagename = $get_data[0];
	}
}

// Paleidziame sablono biblioteka
$core->load('template');

// Jei reikalingas tik puslapio pagrindinis contentas. Ji atvaizduojame jsonu
if(isset($_POST['showPageContentJSON'])){$core->temp->justContent();}

// Pradiniai sablono nustatymai
$core->temp->set_location($core->dbcfg('web_location'));
$core->temp->set_title("Klientų monitoringo sistema");
$core->temp->load_css('main');
$core->temp->load_js('jquery/jquery');
$core->temp->load_js('main');
$core->temp->load_meta(
	array(
		'author'	=> 'Linas Mikalauskas',
		'description'	=> 'Sporto klubo apskaitos sistema'
	)
);

// Prisijungusio vartotojo puslapiai
if($core->user->isLogged())
{
	$pages = array(
		'home'		=> true, // Pradinis puslapis
		'members'	=> true, // Klubo nariai
		'coaches'	=> true, // Klubo treneriai
		'settings'	=> true, // Programos nustatymai
		'login'		=> true, // Prisijungimo puslapis
		'logout'	=> true, // Atsijungimas
		'services'	=> true, // Paslaugos
		'taxes'		=> true, // Mokesciai
		'404'		=> true // Klaidos puslapis
	);
}
else
{
	$pages = array(
		'login' => true, // Prisijungimo langas
		'remindpassword' => true, // Slaptazodzio priminimo langas
		'404'		=> true // Klaidos puslapis
	);
}

// Jei puslapis egzistuoja
if(isset($pages[$pagename]))
{
	require_once DIR_PAGES.$pagename.EXT;
}
// Jei puslapis neegzistuoja, atidarome 404 klaidos puslapi
else
{
	jump_to_main_page('404');
}

// Nustatome utf8 koduote
header('Content-Type: text/html; charset=utf-8');

// Nustatome aktyvu meniu punkta
$core->temp->replace('<li id="menu_'.$core->temp->menu_active().'">','<li id="menu_'.$core->temp->menu_active().'" class="current">');

// Atvaizduojame puslapi
$core->temp->publish();

?>