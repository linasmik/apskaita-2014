<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['taxid']))
{
	$core->load('members');
	$json['info'] = $core->members->getTaxById((int)$_POST['taxid']);
	$json['pvm'] = $core->dbcfg('pvm');
	$json['company_code'] = $core->dbcfg('company_code');
	$json['company_address'] = $core->dbcfg('company_address');
	$json['company_name'] = $core->dbcfg('company_name');
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>