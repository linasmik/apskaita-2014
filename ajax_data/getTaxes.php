<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['formData']))
{
	if($_POST['formData'] == 'thisMonth')
	{
		$data = array();
		$data['fromYear'] = $data['toYear'] = date('Y');
		$data['fromMonth'] = $data['toMonth'] = date('n');
		$data['fromDay'] = 1;
		$data['toDay'] = 31;
	}
	else
	{
		parse_str($_POST['formData'],$data);
	}
	
	$core->load('members');
	$json['taxes'] = $core->members->showTaxes($data);
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>