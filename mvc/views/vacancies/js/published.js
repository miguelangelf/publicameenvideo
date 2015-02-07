// JavaScript Document
var password = '';

loginUser = function( $candidate, $vacancies ){
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
					}else{
						$vacancies.unsetPage();
						$vacancies.get( $('#CompanyId').val() );	
					}
				}
				console.log(response);
			}
		});
	}
}

systemStartWorks = function(){
	$('#systemWorks').modal({
		keyboard: false,
		backdrop: 'static'
	});
}

systemEndWorks = function(){
	$('#systemWorks').modal('toggle');
}

Vacancies = function(){
	
	var $page = 1;
	var $isLoading = false;
	
	this.setPage = function(){
		$page ++;		
	}
	
	this.unsetPage = function(){
		$page = 1;
		$('#vacancies-list').html('');
	}
	
	this.get = function( $id_company ){				
		var $parent = this;		
		if( $isLoading == false){
			var $i = $('<i>');
			$i.addClass("fa fa-refresh fa-spin");
			$('#vacancies-list').append($i);
			$isLoading = true;			
			$.ajax({
				url:"/empresa/vacancies/published_list",
				type:"POST",
				data:{
					page: $page,
					id_company: $id_company
				}
			}).done(function(data){
				$('#vacanciesTotal').remove();	
				$('#vacancies-list i.fa-refresh').remove();		
				$('#vacancies-list').append(data);
				if($('#CandidateNameHidden').val() != undefined){
					$('#CandidateName').html($('#CandidateNameHidden').val());
					$('#loggedUser').show();
					$('#CandidateNameHidden').remove();
				}else{
					if($('#CandidateName').html() != ''){
						$('#logout').trigger('click');
					}					
				}				
				$('#total').html($('#vacanciesTotal').val());
				$parent.setPage();
				$isLoading = false;				
			});		
		}
	}
	
	
	this.getById = function( $vacancy_id ){
		systemStartWorks();
		$.ajax({
			url:"/empresa/vacancies/datail",
			type: "POST",
			data:{
				vacancy_id: $vacancy_id,
				for_candidates: 'true'		
			}
		}).done(function(data){
			$('#vacancyDetail').html( data ).attr('vacancy-id', $vacancy_id);
			$('#vacancyDetail').show();
			$('.return').show();
			$('#vacancies').hide();
			$('.img-logo').css('position', 'unset');
			
			if( $('#vacancy_lat_detail').val() != "0" && $('#vacancy_lng_detail').val() != "0"){
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
			
			systemEndWorks();
			$(window).scrollTop(0);
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
				vacancy_id: $vacancy_id
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
	
	var $vacancies = new Vacancies();
	var $candidate = new Candidate();
	var $id_company = $('#CompanyId').val();	
	
	$vacancies.get( $id_company );
	
	$('#id_country').select2();
	$('#id_state').select2();
	$('#id_city').select2();
	
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
		url: '/web/registro',
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
	
	$(window).on('scroll', function() {
		if($(window).scrollTop() + $(window).height() == $(document).height()) {
			var $inPage = $('#vacancies-list .vacancy').length;
			var $total = $('#total').html();
			var $totalH = $('#vacanciesTotal').val();
			if( $totalH == $total && $total > $inPage){				
				$vacancies.get( $id_company );				
			}		
	   }
	});
	
	$('#vacancies-list').on('click', '.vacancy', function(){
		$vacancies.getById( $(this).attr('vacancy-id') );
	});
	
	$('.return').on('click', function(){
		$('.img-logo').css('position', 'fixed');
		$('.return').hide();
		$('#vacancyDetail').hide().removeAttr('vacancy-id').html('');
		$('#vacancies').show();			
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
	
	$('#createAccount').on('click', function(){
		$('#responseRegister').hide();	
		if($('#CandidateRegisterForm').valid()){			
			$('#CandidateRegisterForm').submit();			
		}
	});
	
	$("#btnLogin").on('click', function() {
        loginUser( $candidate, $vacancies );        
    });
	
	$('#UserEmail').on('keypress', function(event){
        if(event.keyCode == 13){
			loginUser( $candidate, $vacancies );	
		}
    });
	
	$('#UserPassword').on('keypress', function(event){
        if(event.keyCode == 13){
			loginUser( $candidate, $vacancies );	
		}
    });	
	
	$('#vacancyDetail').on('click', '.btnApply', function(){		
		$candidate.applyTo( $('#vacancyDetail').attr('vacancy-id') );
	});
	
	
	$('#logout').on('click', function(){
		systemStartWorks();	
		$.ajax({ 
			url:"/system/logout.php" 
		}).done(function(){
			systemEndWorks();
			$('#btnApply').show();		
			$('#loggedUser').hide();
			$("#btnLogin").removeAttr('disabled');
			$('#responseRegisterSuccess').hide();
			$('#CandidateRegisterForm').show();									
			$('#responseRegisterFail').hide();
			$('#CandidateName').html('')			
			$vacancies.unsetPage();
			$vacancies.get( $('#CompanyId').val() );
		});					
	});
	
});
