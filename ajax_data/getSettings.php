<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['getSettings']))
{
	$json['companyName'] = $core->dbcfg('company_name');
	$json['companyAddress'] = $core->dbcfg('company_address');
	$json['companyCode'] = $core->dbcfg('company_code');
	$json['pvm'] = $core->dbcfg('pvm');
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>