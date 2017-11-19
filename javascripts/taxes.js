$(document).ready(function()
{
	taxesFromToInit(true);
	taxesInit('thisMonth');

	function taxesFromToInit()
	{
		var year = '';
		var month = '';
		var day = '';
		var day2 = '';
		var d = new Date();

		var thisMonth = d.getMonth()+1;
		var thisYear = d.getFullYear();

		for(var i=thisYear;i>=thisYear-4;i--)
		{
			var selected = '';
			if(thisYear == i)
			{
				selected = ' selected="selected"';
			}

			year = year + "<option value='"+i+"'"+selected+">"+i+"</option>";
		}

		for(var i=12;i>=1;i--)
		{
			var selected = '';
			zeroi = i;
			if(thisMonth == i)
			{
				selected = ' selected="selected"';
			}
			if(zeroi < 10)
			{
				zeroi = "0"+i;
			}
			month = month + "<option value='"+i+"'"+selected+">"+zeroi+"</option>";
		}

		for(var i=31;i>=1;i--)
		{
			zeroi = i;
			var selected = '';
			if(i == 1)
			{
				selected = ' selected="selected"';
			}
			if(zeroi < 10)
			{
				zeroi = "0"+i;
			}

			day = day + "<option value='"+i+"'"+selected+">"+zeroi+"</option>";
		}

		for(var i=31;i>=1;i--)
		{
			zeroi = i;
			var selected = '';
			if(i == 31)
			{
				selected = ' selected="selected"';
			}
			if(zeroi < 10)
			{
				zeroi = "0"+i;
			}

			day2 = day2 + "<option value='"+i+"'"+selected+">"+zeroi+"</option>";
		}
		var content = 'Nuo <select name="fromYear">'+year+'</select> - <select name="fromMonth">'+month+'</select> - <select name="fromDay">'+day+'</select> iki <select name="toYear">'+year+'</select> - <select name="toMonth">'+month+'</select> - <select name="toDay">'+day2+'</select>';
		$("#taxesFromTo").html(content);
	}

	function taxesInit(formdata)
	{
		$.ajax({
			url: $(this).webroot()+'ajax_data/getTaxes.php',
			type: 'POST',
			async: false, 
			data: {formData: formdata},
			success: function(data)
			{
				if(data.taxes != false)
				{
				    
					var count = 0;
					var taxesSum = 0;
					var content = '<table id="taxTable"><tr><th class="taxCodeTh"></th><th class="taxPayerTh"></th><th class="taxDescriptionTh"></th><th class="taxSumTh"></th><th class="taxDateTh"></th><th class="taxPrintTh"></th></tr>';
					$.each(data.taxes, function(key, val) {
						if((count%2)==0)
						{
							taxtr = 'taxTrZebra';
						}
						else
						{
							taxtr = 'taxTr';
						}
						
						count++;
						var code = 25000 + parseInt(val.id);
						taxesSum += parseInt(val.taxes);
						var months = new Array("sausio","vasario","kovo","balandžio","gegužės","birželio","liepos","rugpjūčio","rugsėjo","spalio","lapkričio","gruodžio");
						month = months[val.month];
						content += '<tr class="'+taxtr+'"><td>'+code+'</td><td>'+val.name+' '+val.lastname+'</td><td>Narystės mokestis už '+val.year+ ' metų '+month+' mėnesį</td><td>'+val.taxes+' LTL</td><td>'+val.Date+'</td><td><div class="printTax" id="print'+val.id+'"></div></td></tr>';
					});
					
					content += '</table>';

					$("#taxes_table_rows").html(content);
					$("#taxesCount").html(count);
					$("#taxesTotal").html(taxesSum);
				}
				else
				{
					$("#taxesCount").html(0);
					$("#taxesTotal").html(0);
					$("#taxes_table_rows").html('<div class="emptyTaxes">Įrašų apie mokėtus mokesčius pasirinktame laiko tarpe nėra</div>');
				}
			}
		});
	}

	$(document).on("click",".printTax",function(){
		var id = $(this).attr("id").substring(5);
		$.ajax({
			url: $(this).webroot()+'ajax_data/printTax.php',
			type: 'POST',
			async: false, 
			data: {taxid: id},
			success: function(data)
			{
				var months = new Array("sausio","vasario","kovo","balandžio","gegužės","birželio","liepos","rugpjūčio","rugsėjo","spalio","lapkričio","gruodžio");
				month = months[data.info[0].month];
				var originalContents = document.body.innerHTML;
				var code = 25000 + parseInt(data.info[0].id);
				var printContents = '<div class="printTop">';
				var withoutPvm = parseInt(data.info[0].taxes)-((parseInt(data.info[0].taxes)*parseInt(data.pvm))/(100+parseInt(data.pvm))).toFixed(2);
				var pvmCount = ((parseInt(data.info[0].taxes)*parseInt(data.pvm))/(100+parseInt(data.pvm))).toFixed(2);
				printContents += '<div class="printCompanyName"><b>'+data.company_name+'</b></div>';
				printContents += '<div class="printCompanyCode">Įmonės kodas: <b>'+data.company_code+'</b></div>';
				printContents += '<div class="printCompanyCode">Adresas: <b>'+data.company_address+'</b></div>';
				printContents += '<div class="printTitle">Sąskaita už suteiktas paslaugas Nr. '+code+'</div>';
				printContents += '<table class="printTable">';
				printContents += '<tr><th>Mokėtojas</th><th>Mokėjimo paskirtis</th><th>Mokėjimo laikas</th><th>Mokestis</th></tr>';
				printContents += '<tr><td>'+data.info[0].name+' '+data.info[0].lastname+'</td><td>Narystės mokestis už '+data.info[0].year+ ' metų '+month+' mėnesį</td><td>'+data.info[0].Date+'</td><td>'+withoutPvm+' LTL</td></tr>';
				printContents += '<tr><td class="hideTd"></td><td class="hideTd"></td><td><b>PVM '+data.pvm+'% mokestis</b></td><td>'+pvmCount+' LTL</td></tr>';
				printContents += '<tr><td class="hideTd"></td><td class="hideTd"></td><td><b>Kaina su PVM</b></td><td>'+data.info[0].taxes+' LTL</td></tr>';
				printContents += '</table>';
				printContents += '</div>';
				
				document.body.innerHTML = printContents;
				window.print();
				document.body.innerHTML = originalContents;
			}
		});
	});

	$(document).on("submit","#taxesSearch",function(){

		taxesInit($(this).serialize());
		return false;
	});
});