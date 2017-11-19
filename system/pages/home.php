<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

// Template nustatymai
$core->temp->load('home',true);
$core->temp->append_title('Pradžia');
$core->temp->set_menu_active('home');
$core->temp->load_css('home');
$core->temp->load_js('home');

?>