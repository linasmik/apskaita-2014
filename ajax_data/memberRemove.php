<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['memberId']))
{
	$core->load('members');
	$json['status'] = $core->members->remove((int)$_POST['memberId']);
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>