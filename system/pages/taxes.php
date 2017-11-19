<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

// Template nustatymai
$core->temp->load('taxes',true);
$core->temp->append_title('Mokesčiai');
$core->temp->set_menu_active('taxes');
$core->temp->load_css('taxes');
$core->temp->load_js('taxes');

?>