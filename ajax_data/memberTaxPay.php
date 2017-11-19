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
	if(isset($data['memberId'],$data['taxYear'],$data['taxMonth'],$data['tax']))
	{
		// Sumokame mokescius
		$json['status'] = $core->members->taxPay($data['memberId'],$data['taxYear'],$data['taxMonth'],$data['tax']);
		$json['id'] = $data['memberId'];
		$json['thisMonthPay'] = false;
		if((($data['taxMonth']+1) == date('n')) AND (($data['taxYear']) == date('Y')))
		{
			$json['thisMonthPay'] = true;
		}
	}
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>