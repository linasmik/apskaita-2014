$(document).ready(function()
{
	var newMemberOpen = false;
	var memberCreateSubmit = false;
	var memEditEvent = false;
	var page = 0;

	membersInit();
	
	function membersInit(created,search)
	{
		if(created == true)
		{
			page = 0;
		}

		if(typeof search === 'undefined')
		{
			search = '';
		}

		$.ajax({
			url: $(this).webroot()+'ajax_data/members.php',
			type: 'POST',
			async: false, 
			data: {getMembers: true, page:page, search:search},
			success: function(data)
			{
				var content = '';
				if(page == 0)
				{
				    content = "<table id='members_table' border='1'><tr><th style='width:160px'>Vardas</th><th style='width:160px'>Pavardė</th><th style='width:90px'>Gimimo data</th><th style='width:153px'>Telefono numeris</th><th style='width:100px'>Narys nuo</th><th style='width:100px'>Mokesčiai</th><th style='width:110px'>Veiksmai</th></tr>"; 
				}
				    
				if(data.members == false)
				{
					content = content + "<tr><td colspan='7' class='emptymembers'>Narių sąrašas tuščias.</td></tr>";
				}
				else
				{
					var i = 0;
					var d = new Date();
					var month = d.getMonth()+1;
					var year = d.getFullYear();
					$.each(data.members, function(key, val) {
						if((i%2)==0)
						{
							memtr = 'memTrZebra';
						}
						else
						{
							memtr = 'memTr';
						}
						
						if(i == 0)
						{
							if(created == true)
							{
								memtr = 'memTrCreated';
							}
						}
			
						var taxes = '<td class="taxesUnpaid" id="taxesInfo'+val.id+'">Nesumokėta</td>';
			
						if((parseInt(val.year) == year) && (parseInt(val.month) == month) && (val.taxes > 0))
						{
							taxes = '<td class="taxesPaid" id="taxesInfo'+val.id+'">Sumokėta</td>';
						}
						
						var tableRow = "<tr class='"+memtr+"' id='memTrId"+val.id+"'><td>"+val.name+"</td><td>"+val.lastname+"</td><td>"+val.birthday+"</td><td>"+val.telephone+"</td><td>"+val.Date+"</td>"+taxes+"<td><div class='editUser' id='edit"+val.id+"'></div><div class='removeUser' id='remove"+val.id+"'></div><div class='cb'></div></td></tr><tr id='editpanel"+val.id+"' class='editContent'></tr>";
						if(search != ""){tableRow = doHighlight(tableRow,search);}
						content = content + tableRow;
						i++;
					});
				}

				if(page == 0)
				{
					content = content + "</table>"
					if(i == 15)
					{
						page = 1;
						content = content + "<div class='showMore'>Rodyti daugiau</div>";
					}
					$("#members_table_content").html(content);
					if(created == true)
					{
						$(".memTrCreated").css({background:"#49a134",color:"#fff"});
						setTimeout(function(){$(".memTrCreated").css({background:"#6fc55b",color:"#fff"});setTimeout(function(){$(".memTrCreated").css({background:"#49a134",color:"#fff"});$(".memTrCreated").css({background:"#dde0ec",color:"#000"})},500);},500);
					}
				}
				else
				{
					$("#members_table").append(content);
					if(i == 15)
					{
						page++;
					}
					else
					{
						$(".showMore").hide();
					}
				}
			} 
		});
	}
	
	// Naujo nario sukurimas
	$(document).on("click","#member_new",function(){
		if(newMemberOpen == true)
		{
			return false;
		}
		
		newMemberOpen = true;
		var year = '';
		var month = '';
		var day = '';
		for(var i=2010;i>=1950;i--)
		{
			year = year + "<option value='"+i+"'>"+i+"</option>";
		}
		for(var i=12;i>=1;i--)
		{
			month = month + "<option value='"+i+"'>"+i+"</option>";
		}
		for(var i=31;i>=1;i--)
		{
			day = day + "<option value='"+i+"'>"+i+"</option>";
		}
		var content = "<form action='' method='post' id='createMemberForm'><div id='new_member_container'>Vardas <input type='text' name='memberName' id='memberNameInput' /> Pavardė <input type='text' name='memberLastname' id='memberLastnameInput' /> Gimimo data <select name='memberBirthdayYear'>"+year+"</select> - <select name='memberBirthdayMonth'>"+month+"</select> - <select name='memberBirthdayDay'>"+day+"</select> Tel. Numeris <input type='text' name='memberPhone' id='memberPhoneInput' /> <input type='submit' value='Sukurti' /> <input type='reset' value='Atšaukti' id='closeCreateMember' /></div></form>";
		$("#new_member_content").html(content).slideDown(500);
	});

	$(document).on("click","#closeCreateMember",function(){
		$("#new_member_content").slideUp(500);
		newMemberOpen = false;
	});

	$(document).on("click",".removeUser",function(){
		var id = $(this).attr("id").substr(6);
		
		var oldContent = $(this).parent().html();
		$(this).parent().html("<div class='removeQuestion'>Trinti?</div><div class='removeYes' id='memRemIdYes"+id+"'>TAIP</div><div class='removeNo'>NE<div class='oldActionContent'>"+oldContent+"</div><div class='cb'></div>");
	});
	
	$(document).on("click",".removeNo",function(){
		var oldContent = $(this).children().html();
		$(this).parent().html(oldContent);
	});

	$(document).on("click",".removeYes",function(){
		var id = $(this).attr('id').substring(11);
		$(this).parent().parent().html("<td colspan='7' class='removedMember'>Narys panaikintas</td>");
		$.ajax({
			url: $(this).webroot()+'ajax_data/memberRemove.php',
			type: 'POST',
			async: false, 
			data: {memberId: id},
			success: function(data)
			{
			    
			}
		});
	});

	$(document).on("click",".editUser",function(){
		var id = $(this).attr("id").substr(4);

		$("#remove"+id).fadeOut();
		$("#edit"+id).fadeOut();
		
		showMemberEditPanel(id);
	});

	function showMemberEditPanel(id,noslide)
	{
		$.ajax({
			url: $(this).webroot()+'ajax_data/memberInfo.php',
			type: 'POST',
			async: false, 
			data: {memberId: id},
			success: function(data)
			{
				var d = new Date();
				var year = d.getFullYear();
				var month = d.getMonth();
				var taxYearOptions;
				var totalTaxes = 0;
				for(var i=year;i>=year-2;i--)
				{
					taxYearOptions += "<option value='"+i+"'>"+i+"</option>";
				}

				var months = new Array("Sausis","Vasaris","Kovas","Balandis","Gegužė","Birželis","Liepa","Rugpjūtis","Rugsėjis","Spalis","Lapkritis","Gruodis");
				var taxMonthOptions;

				for (var i=0; i < months.length;++i)
				{
					var selected = '';
					if(month == i)
					{
						selected = ' selected="selected"';
					}
					taxMonthOptions += "<option value='"+i+"'"+selected+">"+months[i]+"</option>";
				}

				var content = '<div class="memberTaxesContainer"><form action="" method="post" class="member_taxes_form"><input type="hidden" name="memberId" value="'+id+'"><div class="memEditLabel">Narystės mokesčiai</div><div class="memTaxContent">';
				
				content += '<div class="taxInputLine"><select name="taxYear">'+taxYearOptions+'</select><select name="taxMonth">'+taxMonthOptions+'</select> Suma+PVM <input type="text" name="tax" class="taxInput" /> LTL <input type="submit" value="Sumokėti" class="taxSubmit" /></div>';
				content += '<div class="taxesTableContainer">';
				
				if(data.taxes != false)
				{
					content += '<table class="taxesTable"><tr><th>Metai</th><th>Mėnuo</th><th>Mokestis</th></tr>';
					$.each(data.taxes, function(key, val) {
						totalTaxes += parseInt(val.taxes);
						content += '<tr><td>'+val.year+'</td><td>'+months[(parseInt(val.month)-1)]+'</td><td>'+val.taxes+' LTL</td></tr>';
						
					});
					content += '</table>';
				}
				else
				{
					content += "<div class='emptyTaxTable'>Nėra įrašų apie mokėtus mokesčius</div>";
				}

				content += "</div><div class='totalTaxLine'><div class='fl'>Viso sumokėta:</div><div class='totalTaxesSum'>"+totalTaxes+" LTL</div><div class='cb'></div></div></div></form></div>";
				content += "<div class='memEditFormContainer'><form action='' method='post' class='member_edit_form'><input type='hidden' name='memberId' class='memberId' value='"+id+"'>";
				content += "<div class='memEditInputTicket'>Vardas <input type='text' value='"+data.member[0].name+"' class='memEditInput' name='name' /></div>";
				content += "<div class='memEditInputTicket'>Tel. Nr <input type='text' value='"+data.member[0].telephone+"' class='memEditInput' name='telephone' /></div>";
				content += "<div class='memEditInputTicket'>Pavardė <input type='text' value='"+data.member[0].lastname+"' class='memEditInput' name='lastname' /></div>";

				var year = '';
				var month = '';
				var day = '';
				
				var memYear = data.member[0].birthday.substring(0,4);
				for(var i=2010;i>=1950;i--)
				{
					var selected = '';
					if(memYear == i)
					{
						selected = ' selected="selected"';
					}
					year = year + "<option value='"+i+"'"+selected+">"+i+"</option>";
				}
				var memMonth = data.member[0].birthday.substring(5,7);
				if(parseInt(memMonth) < 10)
				{
					memMonth = memMonth.substring(1,2);
				}
				for(var i=12;i>=1;i--)
				{
					var selected = '';
					zeroi = i;
					if(memMonth == i)
					{
						selected = ' selected="selected"';
					}
					if(zeroi < 10)
					{
						zeroi = "0"+i;
					}
					month = month + "<option value='"+i+"'"+selected+">"+zeroi+"</option>";
				}
				var memDay = data.member[0].birthday.substring(8,10);
				if(parseInt(memDay) < 10)
				{
					memDay = memDay.substring(1,2);
				}
				for(var i=31;i>=1;i--)
				{
					var selected = '';
					zeroi = i;
					if(memDay == i)
					{
						selected = ' selected="selected"';
					}
					if(zeroi < 10)
					{
						zeroi = "0"+i;
					}
					day = day + "<option value='"+i+"'"+selected+">"+zeroi+"</option>";
				}
				
				content += "<div class='memEditInputTicket'>Gimimo data <select name='memberBirthdayYear'>"+year+"</select> - <select name='memberBirthdayMonth'>"+month+"</select> - <select name='memberBirthdayDay'>"+day+"</select></div>";
				content += "<div class='memEditInputTicketLong'>Adresas <input type='text' value='"+data.member[0].address+"' class='memEditInputAddress' name='address' /></div>"
				content += "<div class='memEditInputTicketLong'><div class='fl' style='width:80px;text-align:center;'>Papildoma<br />Informacija</div><div class='fl'><textarea name='otherInfo' class='memEditOtherInfo'>"+data.member[0].otherInfo+"</textarea></div></div>";
				content += "<div class='cb'></div>";
				content += "<div class='memEditSubmitContent'><div class='fr'><input type='submit' value='Atnaujinti' class='memEditSubmit' id='memEditSub"+id+"' /></div><div id='hideMemId"+id+"' class='hideEdit'>Uždaryti</div><div class='cb'></div></div>";
				content += "</form></div>";
				content +="<div class='cb'></div>";
				
				$("#editpanel"+id).html("<td colspan='7'><div class='memberEditContent'>"+content+"</div></td>");
				if(noslide == true)
				{
					$("#editpanel"+id).show();
					$("#editpanel"+id+" td:first-child > div").show();
				}
				else
				{
					$("#editpanel"+id).show(0,function(){$("#editpanel"+id+" td:first-child > div").slideDown();});
				}
			}
		});
	}

	$(document).on("click",".hideEdit",function(){
		var id = $(this).attr("id").substring(9);
		$("#editpanel"+id+" td:first-child > div").slideUp(function(){$("#editpanel"+id).hide()});
		$("#remove"+id).fadeIn();
		$("#edit"+id).fadeIn();
	});

	$(document).on("submit",".member_taxes_form",function(){

		$.ajax({
			url: $(this).webroot()+'ajax_data/memberTaxPay.php',
			type: 'POST',
			async: false, 
			data: {formData: $(this).serialize()},
			success: function(data)
			{
				showMemberEditPanel(data.id,true);
				if(data.thisMonthPay == true)
				{
					$("#taxesInfo"+data.id).removeClass("taxesUnpaid").addClass("taxesPaid").html("Sumokėta");
				}
			}
		});
		
		return false;
	});

	$(document).on("submit",".member_edit_form",function(){
		var id = $(this).children('.memberId').val();
		if(memEditEvent)
		{
			return false;
		}

		memEditEvent = true;

		$("#memEditSub"+id).val("Atnaujinama..").css({background:'#298729'});

		setTimeout(function(){
		    $("#memEditSub"+id).val("Atnaujinti").css({background:'#ffdf04'});
		    memEditEvent = false;
		},500);

		$.ajax({
			url: $(this).webroot()+'ajax_data/memberEdit.php',
			type: 'POST',
			async: false, 
			data: {formData: $(this).serialize()},
			success: function(data)
			{
				if(data.newInfo != false)
				{
					$("#memTrId"+data.newInfo.memberId+" td:first-child").html(data.newInfo.name);
					$("#memTrId"+data.newInfo.memberId+" td:first-child + td").html(data.newInfo.lastname);
					$("#memTrId"+data.newInfo.memberId+" td:first-child + td + td").html(data.newInfo.birthday);
					$("#memTrId"+data.newInfo.memberId+" td:first-child + td + td + td").html(data.newInfo.telephone);
				}
			}
		});
		
		return false;
	});

	$(document).on("click",".showMore",function(){
		if($(".member_search_input").val() != "Paieška..")
		{
			membersInit(false,$(".member_search_input").val());
		}
		else
		{
			membersInit();
		}
	});

	$(document).on("submit","#memberSearch",function(){
		page = 0;
		membersInit(false,$(".member_search_input").val());
		return false;
	});

	$(document).on("keyup",".member_search_input",function(){
		page = 0;
		membersInit(false,$(".member_search_input").val());
	});

	$(document).on("submit","#createMemberForm",function(){
		if(memberCreateSubmit == true)
		{
			return false;
		}

		var error = false;
		memberCreateSubmit = true;

		// Patikriname ar ivestas vardas
		if($("#memberNameInput").val() == "")
		{
			error = true;
			$("#memberNameInput").css({background:"#f58d8d"});
		}
		else
		{
			$("#memberNameInput").css({background:"#fff"});
		}

		// Patikriname ar ivesta pavarde
		if($("#memberLastnameInput").val() == "")
		{
			error = true;
			$("#memberLastnameInput").css({background:"#f58d8d"});
		}
		else
		{
			$("#memberLastnameInput").css({background:"#fff"});
		}

		// Patikriname ar ivestas telefono numeris
		if($("#memberPhoneInput").val() == "")
		{
			error = true;
			$("#memberPhoneInput").css({background:"#f58d8d"});
		}
		else
		{
			// Patikriname ar tai skaicius
			if(!isNumeric($("#memberPhoneInput").val()))
			{
				error = true;
				$("#memberPhoneInput").css({background:"#f58d8d"});
			}
			else
			{
				$("#memberPhoneInput").css({background:"#fff"});
			}
		}

		// Jei klaidu nera, sukuriame nauja nari
		if(error == false)
		{
			$.ajax({
				url: $(this).webroot()+'ajax_data/createMember.php',
				type: 'POST',
				async: false, 
				data: {formData: $(this).serialize()},
				success: function(data)
				{
					if(data.status == true)
					{
						$("#new_member_content").slideUp(500);
						newMemberOpen = false;
						membersInit(true);
					}
				}
			});
		}

		memberCreateSubmit = false;
		return false;
	});

	function isNumeric(n)
	{
		return !isNaN(parseFloat(n)) && isFinite(n);
	}

	function doHighlight(text, search) 
	{
		startTag = "<font style='color:blue; background-color:#ffff00;'>";
		endTag = "</font>";
		
		var newText = "";
		var i = -1;
		var lbSearchTerm = search.toLowerCase();
		var lbBodyText = text.toLowerCase();

		while (text.length > 0)
		{
			i = lbBodyText.indexOf(lbSearchTerm, i+1);
			if (i < 0)
			{
				newText += text;
				text = "";
			}
			else
			{
				if (text.lastIndexOf(">", i) >= text.lastIndexOf("<", i))
				{
					if (lbBodyText.lastIndexOf("/script>", i) >= lbBodyText.lastIndexOf("<script", i))
					{
						newText += text.substring(0, i) + startTag + text.substr(i, search.length) + endTag;
						text = text.substr(i + search.length);
						lbBodyText = text.toLowerCase();
						i = -1;
					}
				}
			}
		}

		return newText;
	}
});