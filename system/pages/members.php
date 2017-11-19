<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

// Template nustatymai
$core->temp->load('members',true);
$core->temp->append_title('Klientai');
$core->temp->set_menu_active('members');
$core->temp->load_css('members');
$core->temp->load_js('members');

?>