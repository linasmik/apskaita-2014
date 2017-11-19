<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

// Template nustatymai
$core->temp->load('login');
$core->temp->append_title('Prisijungimo langas');
$core->temp->load_css('login');
$core->temp->load_js('login');

?>
