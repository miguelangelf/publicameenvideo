// JavaScript Document

var password = '';

loginUser = function( $candidate ){
	if($('#UserLoginForm').valid()){
		$('#failLogin').hide();
		$("#btnLogin").attr('disabled','disabled');
		var encryptedString = $.jCryption.encrypt('{"UserEmail":"'+$("#UserEmail").val()+'","UserPassword":"'+$("#UserPassword").val()+'"}', password);		
		$.ajax({
			url: "/web/acceso/login",
			dataType: "json",
			type: "POST",
			data: {
				_token: encryptedString,
				_candidate: $("#UserType").val()
			},
			success: function(response) {
				var $vacancy_id = $('#vacancyDetail').attr('vacancy-id');
				if(response.success == 0){
					$('#failLogin').show();
					$("#btnLogin").removeAttr('disabled');
				}else{					
					$('#applyModal').modal('hide');					
					$('#failLogin').hide();
					$('#btnApply').hide();		
					$('loggedUser').show();			
					if( $vacancy_id != undefined ){
						$candidate.applyTo( $('#vacancyDetail').attr('vacancy-id') );	
					}
				}
				console.log(response);
			}
		});
	}
}

Candidate = function(){
	
	this.applyTo =function( $vacancy_id ){
		systemStartWorks();
		$('.response-apply').hide();
		$.ajax({
			url:"/empresa/vacancies/applyTo",
			type:"POST",
			dataType:"json",
			data:{
				vacancy_id: $vacancy_id,
				external: $('#external').val()
			}
		}).done(function(data){
			console.log(data);
			if(data.success == 1){
				$('.btnApply').remove();
				$('.response-apply').html(data.msg).show().addClass('alert-success');
				$('#vacancies-list .vacancy[vacancy-id="'+$vacancy_id+'"] .applied-status').show();
			}
			if(data.success == 0){				
				$('.response-apply').html(data.msg).show().addClass('alert-danger');
			}
			if(data.success == -1){
				$('#applyModal').modal('show');
			}
			systemEndWorks();
		});
	}
	
}


$(document).ready(function(e) {
	
	$.validator.messages.required = "Requerido";
	$.validator.messages.email = "Correo no válido";
	$.validator.messages.date = "Por favor introduce una fecha válida";	
	$.validator.messages.number = "Por favor introduce solo n&uacute;meros";
	$.validator.messages.equalTo = "No es igual"
	$.validator.defaults.errorPlacement = function(error, element){		    		
		$(element).addClass('error-validation');
		var $label = $(element).parent().find('label');
		$label.find('span.alert').remove();
		$label.append('<span class="alert alert-danger">'+$(error).html()+'</span>');
		return true;
	}
	$.validator.defaults.highlight = function(element, errorClass, validClass) {
		$(element).focus();
	}
	$.validator.defaults.unhighlight = function(element, errorClass, validClass) {
		$(element).removeClass('error-validation');
		$(element).parent().find('label').find('span.alert').remove();
	}
	
	var $candidate = new Candidate();
	
	$('#id_country').select2();
	$('#id_state').select2();
	$('#id_city').select2();
	
	if( $('#vacancy_lat_detail').val() != 0 && $('#vacancy_lng_detail').val() != 0){
		var $lat = $('#vacancy_lat_detail').val();
		var $lng = $('#vacancy_lng_detail').val();
		var myLatlng = new google.maps.LatLng( $lat, $lng );
		var mapOptions = {
		  zoom: 15,
		  center: myLatlng
		}
		var map = new google.maps.Map(document.getElementById("google_map_detail"), mapOptions);
		
		// Place a draggable marker on the map
		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			draggable:false,
			title:"Ubicación de la vacante"
		});
	}
	
	$('.btnApply').on('click', function(){		
		$candidate.applyTo( $('#vacancyDetail').attr('vacancy-id') );
	});
	
	$('#id_country').on('change', function(){
		
		$('#id_state').attr('disabled', true);
		var $empty = $('#id_state option[value=""]');		
		$('#id_state').html('');
		$('#id_state').append($empty);
		if($(this).val() != ''){
			$("#id_state").select2('destroy');
			$.ajax({
				url: '/empresa/vacancies/states',
				type: 'POST',
				dataType: 'json',
				data: {
					id_country: $(this).val()
				}
			})
			.done(function( data ) {			
				$.each( data, function(index, val) {
					var $option = $('<option/>');
					$option.text(val);
					$option.attr('value', index);
					$('#id_state').append($option);		 
				});			
				$('#id_state').attr('disabled', false);
				$("#id_state").select2();
			});
		}
		
	});
	
	
	$('#id_state').on('change', function(){
	
		$('#id_city').attr('disabled', true);
		var $empty = $('#id_city option[value=""]');
		$('#id_city').html('');
		$('#id_city').append($empty);
		if( $('#id_country').val() != '' && $('#id_state').val() != ''){
			$("#id_city").select2('destroy');
			$.ajax({
				url: '/empresa/vacancies/cities',
				type: 'POST',
				dataType: 'json',
				data: {
					id_state: $('#id_state option:selected').text(),
					id_country: $('#id_country').val()
				}
			})
			.done(function( data ) {
				$.each( data, function(index, val) {
					var $option = $('<option/>');
					$option.text(val);
					$option.attr('value', index);
					$('#id_city').append($option);		 
				});			
				$('#id_city').attr('disabled', false);
				$("#id_city").select2();
			});
		}
	
	});
	
	
	$('#CandidateRegisterForm').validate({
		rules:{
			'User[name]':{
				required:true
			},
			'User[last_name]':{
				required:true
			},
			'User[email]':{
				required:true,
				email:true
			},
			"User[confirm_email]":{				
				equalTo: "#candidate_email"
			},
			'User[id_country]':{
				required:true
			},
			'User[id_state]':{
				required:true
			},
			'User[id_city]':{
				required:true
			},
			'User[street]':{
				required:true
			},
			'User[number]':{
				required:true
			},
			'User[zip_code]':{
				required:true
			},
			'files[]':{
				required:true,
				extension: "jpeg|jpg|gif|png|doc|docx|pdf"
			},
			"input-capcha-capcha":{
				required: true
			}
		},
		messages: {
			'files[]': {
				extension: "Permitidos: *.pdf, *.doc, *.docx, *.jpg, *.png, *.gif, *.bmp, *.gif"			
			}
		}
	});
	
	
	$('#UserLoginForm').validate({
        rules:{
            "user[email]":{
                email: true,
                required: true
            },
            "user[password]":{
                required: true
            }
        }
    });
	
	$('#CandidateRegisterForm').ajaxForm({
		url: '/web/registro/index/vacancy/'+$('#vacancyDetail').attr('vacancy-id'),
		type: 'POST',
		dataType: 'json',
		beforeSend: function(){
			systemStartWorks();	
		},				
		success: function(data) {
			systemEndWorks();	
			$('#responseRegisterFail').hide();									
			if(data.status == 1){
				$('#responseRegisterSuccess').show();
				$('#CandidateRegisterForm').hide();									
			}else{
				$('#responseRegisterFail').show();
			}
			
		}
	});
	
	$('#createAccount').on('click', function(){
		$('#responseRegister').hide();	
		if($('#CandidateRegisterForm').valid()){			
			$('#CandidateRegisterForm').submit();			
		}
	});
	
	$("#btnLogin").on('click', function() {
        loginUser( $candidate );        
    });
	
	$('#UserEmail').on('keypress', function(event){
        if(event.keyCode == 13){
			loginUser( $candidate );	
		}
    });
	
	$('#UserPassword').on('keypress', function(event){
        if(event.keyCode == 13){
			loginUser( $candidate );	
		}
    });
	
});