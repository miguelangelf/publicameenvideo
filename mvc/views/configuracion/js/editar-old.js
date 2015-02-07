// JavaScript Document

var exp_html = null;

var listJobTemplate = '<div class="media curriculums" id="[idJob]" style="display: none;">' +
					  	'<a class="pull-left" href="#">' +
					    	'<div style="background: #006b78; border-radius: 50px; width: 64px; height: 64px;"><h5 style="line-height: 45pt; text-align: center; color: #fff;">[abr]</h5></div>' +
					  	'</a>' +
					  	'<div class="media-body">' +
					  		'<span class="click-element">[Editar]</span>' +
					  		'<i class="fa fa-times close-element click-element" onclick="removeWork(this);"></i>' +
					    	'<h4 class="media-heading">[Job] en [Company]</h4>' +
					    	'<p style="color: #aaa;">([startDate] - [endDate]), [Ubicacion]</p>' +
					    	'[Description]' +
					  	'</div>' +
					  '</div>';
var curriculumTemplate = '<div class="media curriculums">' +
					  '<a class="pull-left disabled" href="#" id="disabled" style="padding: 10px;" onclick="addCurriculum(event, this);"><div class="add-curriculum"><i class="fa fa-plus btn-curriculum"></i></div></a>' +
	  				  '<i class="fa fa-download edit-element click-element" id="noFile" onclick="downloadCurriculum(this);" style="display: none;"></i>' +
					  '<div class="media-body">' +
					  	'<form id="curriculumForm" method ="post" enctype="multipart/form-data">' +
						  	'<input type="file" name="curriculumFile" style ="visibility: hidden;" onchange="changeFile(this);" />' +
						  	'<input type="text" id="new" name="descriptionFile" class="form-control edit-curriculum" onblur="updateDescription(this);" onkeyup="enableUpload(this);" placeholder="[Escribe el nombre de tu curr&iacute;culum]">' +
							'<span class="error-label">Mensaje de error</span>' +
							'<span class="loader"><img src="[themelocal]images/gear.gif" width="32px" height="32px" /></span>' +
						'</form>' +
					  '</div>' +
					'</div>';

setCatalog = function( $catalog, $element, $selected ){	
	$element.html('');
	$.each($catalog, function(index, value){
		var $option = $('<option/>');
		$option.text(value.name);
		$option.val(value.id);
		(value.id == $selected) ? $option.attr('selected', true) : false;
		$element.append($option);	
	});
	$element.removeAttr('disabled');	
}

initCatalog = function( $element, $label ){
	$element.html('');
	var $option = $('<option/>');
	$option.text($label);
	$option.val('');
	$element.append($option);
	$element.attr('disabled', 'disabled');	
}

getCatalog = function( $cp, $id_delegation, $id_colony ){
	$.ajax({
		url: "/web/registro/getColonies",
		type:"POST",
		dataType:"json",
		data:{
			cp: $cp
		}
	}).done(function(data){
		if(data.status == 1){
			setCatalog(data.catalog.colonies, $('#colonies'), $id_colony);
			setCatalog(data.catalog.delegation, $('#delegations'), $id_delegation);		
		}else{
			console.log(data.msg);
		}
	});
}

setProfesionalExperience = function (id) {
	
	$(".work-container").remove();
	$("#candidateProfileForm").hide();
	$("#slider-curriculum").hide();

	$.ajax({
		url: "/candidato/configuracion/experience"
	}).done(function(data){

		var html = data;
		$("#slider-experience").append(html);
		$("#slider-experience").show();
		$("#profesionalExperience :input").prop("disabled", true);
		exp_html = html;

		selectWorks(id);

	});
}

addWorkExperience = function(element) {
	var parent = $(element).parent();
	var form = $(element).siblings();

	$("#slider-experience").append(parent.prop('outerHTML'));
	//$(form).css('border', 'none');
	$(form).removeClass('disabled').addClass('no-disabled');
	$(form).find(':input:disabled').prop('disabled',false);
	$(parent).css("border","none");
	$(element).remove();
}

hideEndDate = function(element) {

	var form = $(element).parent().parent().parent().parent();

	if($(element).is(':checked')){
		$(form).next().slideUp("fast");
	}
	else {
		$(form).next().slideDown("fast");
	}
}

formatDate = function(month, year) {
	var date = null;

	switch(month) {
		case '01':
			date = "Enero " + year;
			break;
		case '02':
			date = "Febrero " + year;
			break;
		case '03':
			date = "Marzo " + year;
			break;
		case '04':
			date = "Abril " + year;
			break;
		case '05':
			date = "Mayo " + year;
			break;
		case '06':
			date = "Junio " + year;
			break;
		case '07':
			date = "Julio " + year;
			break;
		case '08':
			date = "Agosto " + year;
			break;
		case '09':
			date = "Septiembre " + year;
			break;
		case '10':
			date = "Octubre " + year;
			break;
		case '11':
			date = "Noviembre " + year;
			break;
		case '12':
			date = "Diciembre " + year;
			break;
	}

	return date;
}

saveWork = function(event, element) {
	if(event) {
		event.preventDefault();
	}

	var form = $(element).parent().parent().parent();
	var jobObject = {};

	$(form).serializeArray().map(function(x){ jobObject[x.name] = x.value; });

	var $btn = $(element).button('loading');

	console.log(jobObject);
	$.ajax({
		url: "/candidato/configuracion/saveWork",
		type:"POST",
		dataType:"json",
		data: {job : jobObject}
	}).done(function(data){
	
		setTimeout(function() {

			if(data !== "Sin Datos") {

				$(form.parent()).slideUp("slow", function() {

					//currentTemplate = listJobTemplate.replace("[abr]", abr).replace("[startDate]", startDate).replace("[endDate]", endDate).replace("[Ubicacion]", data.location).replace("[Description]", data.description).replace("[Job]", data.position).replace("[Company]", data.company).replace("[idJob]", data.id);
					$(".list-jobs").append(formatJobTemplate(data));

					$("#" + data.id).slideDown("slow");
					form.parent().remove();
				});

			}

			$btn.button('reset');

		}, 2000);
	});

}

formatJobTemplate = function(job) {

	var startDate = (job.startDate != null) ? formatDate(job.startDate.split('-')[0],job.startDate.split('-')[1])  : formatDate(job.startMonth, job.startYear);
	var jobPosition = job.position.split(" ");
	var description = (job.description != null) ? job.description : job.job_description;
	var endDate = null;

	var abr = null;

	if(jobPosition.length > 1) abr = jobPosition[0].substr(0,1) + jobPosition[1].substr(0,1);
	else abr = jobPosition[0].substr(0,1);

	if(job.endMonth != null) {
		if(job.endMonth != null && job.endYear != null) var endDate = formatDate(job.endMonth, job.endYear);
		else var endDate = "Actual";
	} else {
		if(job.current == 1) endDate = "Actual";
		else endDate = formatDate(job.endDate.split('-')[0], job.endDate.split('-')[1]);
	}

	currentTemplate = listJobTemplate.replace("[abr]", abr.toUpperCase()).replace("[startDate]", startDate).replace("[endDate]", endDate).replace("[Ubicacion]", job.location).replace("[Description]", description).replace("[Job]", job.position).replace("[Company]", job.company).replace("[idJob]", job.id);

	return currentTemplate;
}

removeWork = function(element) {
	var listElement = $(element).parent().parent();

	$.ajax({
		url: "/candidato/configuracion/deleteWork",
		type:"POST",
		dataType:"json",
		data: { id : $(listElement).attr("id") }
	}).done(function(data){
		if(data == 1) {
			$(listElement).slideUp("slow", function() {
				$(listElement).remove();
			});
		}
	});
}

editWork = function(element) {
	var listElement = $(element).parent().parent();

	$.ajax({
		url: "/candidato/configuracion/editWork",
		type:"POST",
		dataType:"json",
		data: { id : $(listElement).attr("id") }
	}).done(function(data){
		console.log(data);
	});
}

selectWorks = function(id) {
	var html = "";
	$.ajax({
		url: "/candidato/configuracion/getWork",
		dataType:"json"
	}).done(function(data){
		for (var i = 0; i < data.length; i++) {
			html += formatJobTemplate(data[i]).replace("display: none;", "");
		};

		$(".list-jobs").html(html);
	});

}

viewCurriculum = function() {
	$("#candidateProfileForm").hide();
	$("#slider-experience").hide();
	
	$.ajax({
		url: "/candidato/configuracion/curriculum"
	}).done(function(data){
		$("#slider-curriculum").html(data);
		$("#slider-curriculum").show();
		curriculumTemplate = curriculumTemplate.replace("[themelocal]", themeLocal);
	});
}

changeFile = function(element) {
	var file_data = $(element).prop('files')[0];
	var file_button = $(element).parent().parent().siblings();
	var form_data = null;
	$(element).siblings().eq(1).hide();
	if (typeof file_data !== 'undefined') {
		$(element).siblings().eq(2).show();
		if($(file_button).eq(1).attr("id") !== "noFile") {
			form_data = new FormData();                  
			form_data.append('files[]', file_data);
			form_data.append('description', "");
			form_data.append('id', $(element).siblings().eq(0).attr("id"));
			$.ajax({
		        url: '/candidato/configuracion/curriculum',
	            dataType: 'json',
	            cache: false,
	            contentType: false,
	            processData: false,
	            data: form_data,                        
	            type: 'post',
	            success: function(data) {
	            	if(data[0] != null) {
	            		var errors = Object.getOwnPropertyNames(data[0].error).sort();
	            		if(errors[0] === "accept_file_types") {
	            			$(element).siblings().eq(1).html("Error: archivos permitidos: *.pdf, *.doc, *.docx");
	            			setTimeout(function() {
								$(element).siblings().eq(1).show();
							}, 1500);
	            		}
	            	} else {
	            		$(file_button.eq(1)).attr("id", data);
	            	}
	            },
	            error: function (jqXHR, status, err) {
					$(element).siblings().eq(1).html("Verifica el tamaño del archivo(maximo 2MB)");
					setTimeout(function() {
						$(element).siblings().eq(1).show();
					}, 1500);
				},
	            complete: function() {
	            	setTimeout(function() {
						$(element).siblings().eq(2).hide();
					}, 1000);
	            }
		    });
		} else {
			form_data = new FormData();                  
			form_data.append('files[]', file_data);
			form_data.append('description', $(element).siblings().eq(0).val());
			$.ajax({
		        url: '/candidato/configuracion/curriculum',
	            dataType: 'json',
	            cache: false,
	            contentType: false,
	            processData: false,
	            data: form_data,                        
	            type: 'post',
	            success: function(data) {
	            	if(data[0] != null) {
	            		var errors = Object.getOwnPropertyNames(data[0].error).sort();
	            		if(errors[0] === "accept_file_types") {
	            			$(element).siblings().eq(1).html("Error: archivos permitidos: *.pdf, *.doc, *.docx");
	            			setTimeout(function() {
								$(element).siblings().eq(1).show();
							}, 1500);
	            		}
						// $(element).siblings().eq(1).html(data[0].error);
						// $(element).siblings().eq(1).show();
	            	} else {
	            		$(file_button.eq(0)).children().css("background","#006b78");
						$(file_button.eq(0)).children().children().removeClass("fa fa-plus").addClass("fa fa-pencil").css("color", "#fff");
						$(".list-curriculums").append(curriculumTemplate);
						$(file_button.eq(1)).show();
						$(file_button.eq(1)).attr("id", data.file_name);
						$(element).siblings().eq(0).attr("id", data.id);
	            	}
	            },
	            error: function (jqXHR, status, err) {
					$(element).siblings().eq(1).html("Verifica el tamaño del archivo(maximo 2MB)");
					setTimeout(function() {
						$(element).siblings().eq(1).show();
					}, 1500);
				},
				complete: function() {
					setTimeout(function() {
						$(element).siblings().eq(2).hide();
					}, 1000);
	            }
		    });
		}
	}
}

addCurriculum = function(e, element) {
	e.preventDefault();
	var one = false;
	if($(element).attr("id") != "disabled") {
		var contenedor = $(element).siblings(".media-body").eq(0);
		$(contenedor).children().children().eq(0).click();
	}
}

downloadCurriculum = function(element) {
	window.open("/candidato/configuracion/viewCurriculum/" + $(element).attr("id"));
}

enableUpload = function(element) {
	var contenedor = $(element).parent().parent().siblings().eq(0);
	$(contenedor).removeClass("disabled");
	$(contenedor).attr("id", "enable");
}

updateDescription = function(element) {

	if($(element).attr("id") !== "new") {

		var form_data = new FormData();
		form_data.append("description", $(element).val());
		form_data.append("file_name", "");
		form_data.append("id", $(element).attr("id"));
		$(element).siblings().eq(2).show();

		$.ajax({
	        url: '/candidato/configuracion/editDescription',
	        dataType: 'json',
	        cache: false,
	        contentType: false,
	        processData: false,
	        data: form_data,                        
	        type: 'post',
	        success: function(data) {
	        	console.log(data);
	        },
	        complete: function() {
	        	setTimeout(function() {
					$(element).siblings().eq(2).hide();
				}, 1000);
	        }
	    });
	}
}

$(document).ready(function(e) {

	(parseCURP = function(){
		var $curp = $('#curp').val();
		$('#curp').val($curp.toUpperCase());
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
	})();
	
	$zip_code = '';
	$cp = $('#zip_code').val();
	if($cp.length == 5){
		getCatalog( $cp, $('#id_delegation').val(), $('#id_colony').val() );	
	}
	
	$('#marital_status').val($('#id_marital_status').val());
	$('#number_sons').val($('#id_number_sons').val());
	$('#gender').val($('#gender_id').val());
	$('#live_with').val($('#id_live_with').val());
	$('#degree').val($('#id_degree').val());
	$('#degree_status').val($('#id_degree_status').val());
	$('#proof_of_studies').val($('#id_proof_of_studies').val());
	$('#currently_studying').val($('#id_currently_studying').val());
	$('#languages').val($('#id_languages').val());
	$('#area').val($('#id_area').val());
	$('#relocation_availavility').val($('#id_relocation_availavility').val());
	$('#working_time').val($('#id_working_time').val());	
	$('#working_time_change').val($('#id_working_time_change').val());	
	$('#driving_license').val($('#id_driving_license').val());	
	$('#job_type').val($('#id_job_type').val());	

	$('#zip_code').on('keyup', function(){		
		var $cp = $(this).val();		
		if($cp.length == 5){
			initCatalog($('#colonies'), 'Colonia');
			initCatalog($('#delegations'), 'Delegación');
			if($zip_code != $cp){
				$zip_code = $cp;
				getCatalog( $cp );
			}														
		}
	
	});
	
	$('#candidateProfileForm').validate({
		rules:{
			'candidate[phone_number]':{
				number: true
			},
			"candidate[curp]":{
				required: true,
				minlength: 18
			},
			"candidate[zip_code]":{
				required: true,
				number: true,
				minlength: 5
			}
		}
	});
	
	$('#updateCandidateProfile').on('click', function(){
		if($('#candidateProfileForm').valid()){
			systemStartWorks();
			$.ajax({
				url:"",
				type:"POST",
				dataType:"json",
				data:$('#candidateProfileForm').serialize()+"&candidate[birthdate]="+$('#year').val()+"-"+$('#month').val()+"-"+$('#day').val()
			}).done(function(data){
				$('#msgUpdateCandidate').find('div.alert').remove();
				var $msg = $('<div/>');
				$msg.addClass('alert');
				$msg.html(data.msg);			
				if(data.status == 1){				
					$msg.addClass('alert-success');
				}else{				
					$msg.addClass('alert-danger');											
				}
				$('#msgUpdateCandidate').append($msg);
				systemEndWorks();
			});
		}
		
	});
	
	$('.number').on('keyup',  function(){
		if(isNaN($(this).val())){
			$(this).val('');	
		}
	});
	
	$('#curp').on('keyup', function(){		
		parseCURP();	
	});
	
	$('#conf-menu-wrapper h5').on('click', function(){				
		
		$("#slider-experience").hide();
		$("#slider-curriculum").hide();
		$("#candidateProfileForm").show();

		if($('#candidateProfileForm').valid() && $(this).attr('section-name') != undefined){		
			var $active = $(this);
			$('#conf-menu-wrapper h5').each(function(index, element) {
				$(this).removeClass('pink');
				var $section = '#'+$(this).attr('section-name');
				$($section).hide();
			});
			
			$active.addClass('pink');
			$section = '#'+$active.attr('section-name');
			$($section).show();
		}
	});
		
	$("#experienceBtn").click(function() {
		$('#conf-menu-wrapper h5').each(function(index, element) {
			$(this).removeClass('pink');
			var $section = '#'+$(this).attr('section-name');
			$($section).hide();
		});
		$(this).addClass('pink');
		setProfesionalExperience(1);
	});

	$("#experienceFormBtn").click(function(e) {

		if(e) {
			e.preventDefault();
		}

		console.log("Enviamos form");

	});

	$("#uploadCurriculum").click(function() {

		$('#conf-menu-wrapper h5').each(function(index, element) {
			$(this).removeClass('pink');
			var $section = '#'+$(this).attr('section-name');
			$($section).hide();
		});
		$(this).addClass('pink');
		viewCurriculum();
	});

	if($("#mainArea").hasClass("exp")) {
		$("#experienceBtn").trigger("click");
	}

});