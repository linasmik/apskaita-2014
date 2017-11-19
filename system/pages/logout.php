<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

if($core->user->isLogged())
{
	$core->user->unregisterUserSession();
}

jump_to_main_page();
?>