<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['formData']))
{
	$core->load('members');
	$json['newInfo'] = $core->members->edit($_POST['formData']);
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>