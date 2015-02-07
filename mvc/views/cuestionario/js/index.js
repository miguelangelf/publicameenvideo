// JavaScript Document

setCatalog = function( $catalog, $element ){	
	$element.html('');
	$.each($catalog, function(index, value){
		var $option = $('<option/>');
		$option.text(value.name);
		$option.val(value.id);
		$element.append($option);	
	});
	$element.removeAttr('disabled');
	$element.focus();
}

initCatalog = function( $element, $label ){
	$element.html('');
	var $option = $('<option/>');
	$option.text($label);
	$option.val('');
	$element.append($option);
	$element.attr('disabled', 'disabled');	
}

$(document).ready(function(e) {
	
	var $zip_code = '';
	
	$('#zip_code').on('keyup', function(){		
		var $cp = $(this).val();                
		if($cp.length == 5){
                    $('#error-cp').hide();
			initCatalog($('#colonies'), 'Colonia');
			initCatalog($('#delegations'), 'Delegación');
			
                        $.ajax({
                                url: "/web/registro/getColonies",
                                type:"POST",
                                dataType:"json",
                                data:{
                                        cp: $cp
                                }
                        }).done(function(data){
                                console.log(data);
                                if(data.status == 1){
                                        setCatalog(data.catalog.colonies, $('#colonies'));
                                        setCatalog(data.catalog.delegation, $('#delegations'));		
                                }else{
                                        console.log(data.msg);
                                        $('#zip_code').val('');
                                        $('#error-cp').show().html(data.msg);
                                }
                        });
																	
		}
	
	});
	
	$('#questionary').validate({
		rules:{
			"candidate[address]":{
				required: true
			},
			"candidate[zip_code]":{
				required: true,
				minlength: 5
			},
			"candidate[curp]":{
				required: true,
				minlength: 18
			}
		},
		errorPlacement: function(error, element){
			return true;
		},
		highlight: function(element, errorClass, validClass) {
			$(element).addClass('error-validation');
			$(element).focus();
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).removeClass('error-validation');
		}
	});			
	
	$('#btnQuestionary').on('click', function(){
		$('#msgErrorRegisterCandidate').hide();                
		if($('#questionary').valid()){
			systemStartWorks();
                        $('#btnQuestionary').addClass('disabled');
			$.ajax({
				url:"/candidato/registro/cuestionario",
				data:$('#questionary').serialize()+"&candidate[birthdate]="+$('#year').val()+"-"+$('#month').val()+"-"+$('#day').val()+"&candidate[between_streets]="+$('#streat_1').val()+" y "+$('#streat_2').val(),
				dataType:"json",
				type:"POST"
			}).done(function(data){				
				if(data.status == 1){
                                    window.location = '/candidato/calendario/citas';
				}else{
                                    systemEndWorks();
                                    $('#btnQuestionary').removeClass('disabled');
                                    $('#msgErrorRegisterCandidate').html(data.msg);
                                    $('#msgErrorRegisterCandidate').show();
                                }
			});
		}
	});
	
	$('#curp').on('keyup', function(){                
		var $curp = $(this).val();
		$(this).val($curp.toUpperCase());
		var $validRFC = $curp.match('^[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])');						                
		if( $validRFC ){
                        $('#error-curp').hide();
                        $('#error-age').hide();
			var $year = parseInt($curp.substr(4, 2));
			var $month = $curp.substr(6, 2);
			var $day = $curp.substr(8, 2);										
			var $date = new Date();
                        var $year4 = $date.getFullYear();
			var $currentYear = $year4.toString().substr(2,2); 
			var $fullYear = $year > $currentYear ? ($year+1900) : (2000+$year);			
                        var $age = $year4-$fullYear;
                        
                        if($age < 18 || $age>30){
                            $('#curp').val('');
                            $('#error-age').show();
                        }else{
                            $('#year').val($fullYear);
                            $('#month').val($month);			
                            $('#day').val($day);
                        }
                        
			var $validCURP = $curp.match('^[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[HM]{1}(AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{1}[0-9]{1}$');			
			if( $validCURP == null && $curp.length == 18){																
				$('#curp').val($curp.substr(0, 10));
                                $('#error-curp').show();
			}
		}else{
			if($curp.length >= 10){
				$('#curp').val('');
                                $('#error-curp').show();
                                $('#error-age').hide();
			}
		}

	});	
	
	$('.number').on('keyup', function(){
		if(isNaN($(this).val())){
			$(this).val('');	
		}
	});	
	
	
});