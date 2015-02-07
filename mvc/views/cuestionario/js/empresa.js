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
			initCatalog($('#colonies'), 'Colonia');
			initCatalog($('#delegations'), 'Delegación');
			if($zip_code != $cp){
				$zip_code = $cp;
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
					}
				});
			}														
		}
	
	});
	
	$('#questionary').validate({
		rules:{
			"company[address]":{
				required: true
			},
			"company[zip_code]":{
				required: true,
				minlength: 5
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
			
		if($('#questionary').valid()){
			systemStartWorks();
			$.ajax({
				url:"/candidato/registro/createCompany",
				data:$('#questionary').serialize()+"&company[between_streets]="+$('#streat_1').val()+" y "+$('#streat_2').val()+'&company[rfc]='+$('#rfc_segment_1').val()+$('#rfc_segment_2').val()+$('#rfc_segment_3').val(),
				dataType:"json",
				type:"POST"
			}).done(function(data){
				systemEndWorks();
				if(data.status == 1){
					window.location = '/web/cuestionario/registro_exitoso';
				}
			});
		}
	});
	
	
	$('#sectorId').on('change', function(){
		initCatalog($('#subSectorId'), 'Selecciona el subsector');
		
		$.ajax({
			url:"/web/registro/getSubSector",
			type:"POST",
			dataType:"json",
			data:{
				id_sector: $('#sectorId').val()
			}
		}).done(function(data){
			if(data.status == 1){
				setCatalog(data.catalog, $('#subSectorId'));	
			}else{
				console.log(data.msg);
			}
		});
	});
	
	$('.number').on('keyup', function(){
		if(isNaN($(this).val())){
			$(this).val('');	
		}
	});	
	
});