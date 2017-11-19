$(document).ready(function()
{
	// Prisijungimo formos veiksmai
	$(document).on("submit","#loginForm",function(){
		
		// Kintamasis kuris uzfiksuoja jei yra klaidu formoje
		var error = false;

		// Patikriname ar ivestas prisijungimo vardas
		if($(".loginInputUser").val() == "")
		{
			error = true;
			$(".loginErrorUser").fadeIn();
		}
		else
		{
			$(".loginErrorUser").fadeOut();
		}
		
		// Patikriname ar ivestas slaptazodis
		if($(".loginInputPass").val() == "")
		{
			error = true;
			$(".loginErrorPass").fadeIn();
		}
		else
		{
			$(".loginErrorPass").fadeOut();
		}

		// Jei klaidu nera, bandome prisijungti
		if(error == false)
		{
			$.ajax({
				url: $(this).webroot()+'ajax_data/login.php',
				type: 'POST',
				async: false, 
				data: {username: $(".loginInputUser").val(),password:$(".loginInputPass").val()},
				success: function(data){
					// Duomenys teisingi
					if(data.status == 1)
					{
						$(".loginErrorWrong").fadeOut();
						//Perkraunam puslapi
						location.reload();
					}
					// Prisijungti nepavyko
					else
					{
						error = true;
						$(".loginErrorWrong").fadeIn();
					}
				}  
			});
		}

		if(error)
		{
			return false;
		}
	});
});