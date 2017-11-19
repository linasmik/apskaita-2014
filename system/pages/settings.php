<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

// Template nustatymai
$core->temp->load('settings',true);
$core->temp->append_title('Nustatymai');
$core->temp->set_menu_active('settings');
$core->temp->load_css('settings');
$core->temp->load_js('settings');

?>