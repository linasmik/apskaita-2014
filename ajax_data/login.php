<?php

// Ikeliam ajax failiuka darbui su uzklausom
require 'ajax_init.php';

// json duomenu masyvas
$json = array();

// Patikriname ar jis yra neprisijunges
if($core->user->isLogged())
{
    die();
}

// Patikriname ar nurodytas vartotojo vardas ir slaptazodis
if(isset($_POST['username'], $_POST['password']))
{
	// Bandome prisijungti
	$login = $core->user->login($_POST['username'], $_POST['password']);
	
	// Duodame atsakyma
	if($login == true)
	{
		// Prisijungimas pavyko
		$json['status'] = 1;
	}
	else
	{
		// Prisijungimas nepavyko
		$json['status'] = 0;
	}
}

// Uzkoduojam ir atvaizduojam atsakyma json formatu
echo json_encode($json);

?>