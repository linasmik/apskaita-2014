<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['formData']))
{
	parse_str($_POST['formData'],$data);
	$json['status'] = $core->user->changePassword($data['realPassword'],$data['newPassword']);
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>