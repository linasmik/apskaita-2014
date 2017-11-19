<?php

// ikeliam pagrindini faila
require '../init.php';

// Ar reikalinga patikrinti kad prisijunges
if(isset($checkonline))
{
	if(!$core->user->isLogged())
	{
		die('please login');
	}
}

// Patikrinam ar kreipiasi ajax
if(!IS_AJAX)
{
	if(!isset($fakeajax))
	{
		die();
	}
}

// Nurodom json koduote
if(!isset($noheader))
{
	header("Content-type: application/json; charset=utf-8");
}

?>