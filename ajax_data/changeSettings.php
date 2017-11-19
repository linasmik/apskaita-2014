<?php

$checkonline = true;

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

if(isset($_POST['formData']))
{
	parse_str($_POST['formData'],$data);
	$company_name = $core->db->escape_str($data['companyName']);
	$company_code = $core->db->escape_str($data['companyCode']);
	$company_address = $core->db->escape_str($data['companyAddress']);
	$pvm = (int)$data['pvm'];
	
	$sqls = array(
	    0 => 'UPDATE '.TABLE_CONFIGS.' SET config_value = "'.$company_name.'" WHERE config_name = "company_name" LIMIT 1',
	    1 => 'UPDATE '.TABLE_CONFIGS.' SET config_value = "'.$company_code.'" WHERE config_name = "company_code" LIMIT 1',
	    2 => 'UPDATE '.TABLE_CONFIGS.' SET config_value = "'.$company_address.'" WHERE config_name = "company_address" LIMIT 1',
	    3 => 'UPDATE '.TABLE_CONFIGS.' SET config_value = "'.$pvm.'" WHERE config_name = "pvm" LIMIT 1'
	);
	
	foreach($sqls as $key => $sql)
	{
		$core->db->simple_query($sql);
	}
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>