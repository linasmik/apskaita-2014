$(document).ready(function()
{
	settingsInit();
	
	function settingsInit()
	{
		$.ajax({
			url: $(this).webroot()+'ajax_data/getSettings.php',
			type: 'POST',
			async: false, 
			data: {getSettings: true},
			success: function(data)
			{
				$('input[name=companyName]').val(data.companyName);
				$('input[name=companyAddress]').val(data.companyAddress);
				$('input[name=companyCode]').val(data.companyCode);
				$('input[name=pvm]').val(data.pvm);
			}
		});
	}
	
	$(document).on("submit","#passwordChangeForm",function(){
		var error = false;
		$(".errorMsg").fadeOut();
		$(".successMsg").fadeOut();
		$('input[name=realPassword]').removeClass("errorInput");
		$('input[name=newPassword]').removeClass("errorInput");
		$('input[name=newPasswordRepeat]').removeClass("errorInput");
		
		if($('input[name=realPassword]').val() == "")
		{
			$('input[name=realPassword]').addClass("errorInput");
			$("#realPassError").html('Įveskite dabartinį slaptažodį!');
			$("#realPassError").fadeIn();
			error = true;
		}
		
		if($('input[name=newPassword]').val() == "")
		{
			$('input[name=newPassword]').addClass("errorInput");
			$("#newPassError").html('Įveskite naują slaptažodį!');
			$("#newPassError").fadeIn();
			error = true;
		}
		
		if($('input[name=newPasswordRepeat]').val() == "")
		{
			$('input[name=newPasswordRepeat]').addClass("errorInput");
			$("#newPassRepeatError").html('Pakartokite naują slaptažodį!');
			$("#newPassRepeatError").fadeIn();
			error = true;
		}
		
		
		if(($('input[name=newPassword]').val() != "") && ($('input[name=newPasswordRepeat]').val() != "") && ($('input[name=newPasswordRepeat]').val() != $('input[name=newPassword]').val()))
		{
			$('input[name=newPassword]').addClass("errorInput");
			$('input[name=newPasswordRepeat]').addClass("errorInput");
			$("#newPassRepeatError").html('Slaptažodžiai nesutampa');
			$("#newPassRepeatError").fadeIn();
			error = true;
		}
		
		if(($('input[name=newPassword]').val() != "") && ($('input[name=newPasswordRepeat]').val() != "") && ($('input[name=newPasswordRepeat]').val() == $('input[name=newPassword]').val()) && ($('input[name=newPassword]').val().length <= 5))
		{
			$('input[name=newPassword]').addClass("errorInput");
			$('input[name=newPasswordRepeat]').addClass("errorInput");
			$("#newPassRepeatError").html('Trumpas slaptažodis. Mažiausiai 6 simboliai');
			$("#newPassRepeatError").fadeIn();
			error = true;
		}

		if(error == false)
		{
			$.ajax({
				url: $(this).webroot()+'ajax_data/changePassword.php',
				type: 'POST',
				async: false, 
				data: {formData: $(this).serialize()},
				success: function(data)
				{
					if(data.status == true)
					{
						$('input[name=realPassword]').val("");
						$('input[name=newPassword]').val("");
						$('input[name=newPasswordRepeat]').val("");
						$("#passwordChanged").html('Slaptažodis pakeistas');
						$("#passwordChanged").fadeIn();
					}
					else
					{
						$('input[name=realPassword]').addClass("errorInput");
						$("#realPassError").html('Neteisingas slaptažodis!');
						$("#realPassError").fadeIn();
					}
				}
			});
		}

		return false;
	});

	$(document).on("submit","#otherInfoForm",function(){

		$("#otherInfoChanged").fadeOut();
		$.ajax({
			url: $(this).webroot()+'ajax_data/changeSettings.php',
			type: 'POST',
			async: false, 
			data: {formData: $(this).serialize()},
			success: function(data)
			{
				$("#otherInfoChanged").html("Nustatymai pakeisti");
				$("#otherInfoChanged").fadeIn();
			}
		});

		return false;
	});
});