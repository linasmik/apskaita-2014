<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

// Template nustatymai
$core->temp->load('services',true);
$core->temp->append_title('Paslaugos');
$core->temp->set_menu_active('services');
$core->temp->load_css('services');
$core->temp->load_js('services');

?>