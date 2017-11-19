<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['getMembers'],$_POST['page'],$_POST['search']))
{
	$page = (int)$_POST['page']*15;
	$core->load('members');
	$members = $core->members->getMembers($page,15,$_POST['search']);
	$json['members'] = $members;
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>