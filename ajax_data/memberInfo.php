<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['memberId']))
{
	$core->load('members');
	$taxes = $core->members->getTaxes((int)$_POST['memberId']);
	$member = $core->members->getMember((int)$_POST['memberId']);
	$json['member'] = $member;
	$json['taxes'] = $taxes;
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>