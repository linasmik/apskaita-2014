<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['formData']))
{
	parse_str($_POST['formData'],$data); 
	$core->load('members');
	
	// Patikriname ar yra visi duomenys
	if(isset($data['memberLastname'],$data['memberName'],$data['memberBirthdayYear'],$data['memberBirthdayMonth'],$data['memberBirthdayDay'],$data['memberPhone']))
	{
		// Sukuriame nauja nari
		$json['status'] = $core->members->create($data);
	}
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>