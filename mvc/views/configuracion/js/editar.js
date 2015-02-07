
var exp_html = null;
var dataTemplate = null;

var listJobTemplate = '<div class="media curriculums" id="[idJob]" style="display: none;">' +
					  	'<a class="pull-left" href="#">' +
					    	'<div class="job-template tbriefcase"><h5 class="glyphicon glyphicon-briefcase"></h5></div>' +
					  	'</a>' +
					  	'<div class="media-body">' +
					  		'<span class="click-element" onclick="editJob(event, [idJob]);">[Editar]</span>' +
					  		'<i class="fa fa-times close-element click-element" onclick="removeWork(this);"></i>' +
					    	'<h4 class="media-heading">[Job] en [Company]</h4>' +
					    	'<p class="date-template">([startDate] - [endDate]) - [Ubicacion]</p>' +
					    	'[Description]' +
					  	'</div>' +
					  '</div>';
var curriculumTemplate = '<div class="media curriculums">' +
					  '<a class="pull-left disabled" href="#" id="disabled" style="padding: 10px;" onclick="addCurriculum(event, this);"><div class="add-curriculum"><i class="fa fa-upload btn-curriculum"></i></div></a>' +
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
var listEduTemplate = '<div class="media curriculums" id="[idSchool]" style="display: none;">' +
							'<a class="pull-left" href="#">' +
								'<div class="job-template">' +
									'<h5 class="fa fa-graduation-cap"></h5>' +
								'</div>' +
							'</a>' +
							'<div class="media-body">' +
								'<span class="click-element" onclick="editSchool(event, [idSchool])">[Editar]</span>' +
				  					'<i class="fa fa-times close-element click-element" onclick="removeSchool(this);"></i>' +
				   					'<h4 class="media-heading">[Degree]</h4>' +
				   					'<h6 style="font-weight: 500; color: #777;">[School]</h6>' +
				   					'<p class="date-template">([startYear] - [endYear])</p>' +
							'</div>' +
						'</div>';

var languageTemplate = '<div class="col-md-12">' +
							'<form class="form-inline" id="languagesForm">' +
								'<div class="form-group">' +
									'<label>Idioma:</label>' +
									'<select id="languageName" name="languageName" onchange="skillUp(this);">' +
										'<option value="">Selecciona</option>' +
									'</select>' +
								'</div>' +
								'<div class="form-group disabled">' +
									'<label for="languageLevel">Nivel:</label>' +
									'<select class="form-control" name="languageLevel" disabled id="languageLevel" data-function>' +
									  '<option>Principiante</option>' +
									  '<option>Competencia básica limitada</option>' +
									  '<option>Competencia intermedia</option>' +
									  '<option>Nivel avanzado</option>' +
									  '<option>Lengua materna o bilingüe</option>' +
									'</select>' +
								'</div>' +
								'<span class="disabled">' +
									'<button type="submit" class="btn btn-default btn-circle btn-lg fa fa-plus" onclick="addSL(event, this, 2);"></button>' +
								'</span>' +
							'</form>' +
						'</div>';

setProfesionalExperience = function (id) {
	
	// $(".work-container").remove();
	// $("#candidateProfileForm").hide();
	$("#slider-curriculum").hide();

	$.ajax({
		url: "/candidato/configuracion/experience"
	}).done(function(data){

		var html = data;
		$("#slider-experience").append(html);
		$("#slider-experience").show();
		// $("#profesionalExperience :input").prop("disabled", true);
		exp_html = html;

		$("#search-select").select2();
		$("#search-select-city").select2();

		selectWorks(id);

		for (var i = 2015; i >= 1900; i--) {
			$('.work-container').find('#endYear').append("<option value='" + i + "'>" + i + "</option>");
			$('.work-container').find('#startYear').append("<option value='" + i + "'>" + i + "</option>");	
		}

	});
}

// addWorkExperience = function(element) {
// 	var parent = $(element).parent();
// 	var form = $(element).siblings();

// 	// $("#slider-experience > .fakeWork-container").append(parent.prop('outerHTML'));
// 	//$(parent.prop('outerHTML')).insertBefore("#addJobBtn");
// 	//$(form).css('border', 'none');
// 	$(form).removeClass('disabled').addClass('no-disabled');
// 	$(form).find(':input:disabled').prop('disabled',false);
// 	$(parent).css("border","none");
// 	$(element).remove();
// 	//$("#addJobBtn").show("slow");
// }

addEducation = function(element) {
	var parent = $(element).parent();
	var form = $(element).siblings();

	$("#slider-education > .fake-container").prepend(parent.prop('outerHTML'));
	//$(form).css('border', 'none');
	$(form).removeClass('disabled').addClass('no-disabled');
	$(form).find(':input:disabled').prop('disabled',false);
	$(parent).css("border","none");
	$(element).remove();
}

hideEndDate = function(element) {

	var form = $(element).parents().eq(3);

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

saveEducation = function(event, element) {
	if(event) {
		event.preventDefault();
	}

	var form = $(element).parent().parent().parent();
	var schoolObject = {};
	var emptyIn = [];

	$(form).serializeArray().map(function(x){ 
		if(x.value == "") emptyIn.push(x.name);
		else schoolObject[x.name] = x.value; 
	});

	var $btn = $(element).button('loading');

	if(emptyIn.length <= 0) {
		$.ajax({
			url: "/candidato/configuracion/addSchools",
			type:"POST",
			dataType:"json",
			data: {school : schoolObject}
		}).done(function(data){
			setTimeout(function() {
				if(!data.editar) {
					$(form).slideUp("slow", function() {
						var eduTemplate = listEduTemplate.replace("[Degree]", data.degree).replace("[School]", data.name).replace("[startYear]", data.startYear).replace("[endYear]", data.endYear).replace("[idSchool]", data.id).replace("[idSchool]", data.id);
						
						$(eduTemplate).insertBefore("#addEduBtn");
						$("#" + data.id).slideDown("slow", function() {
							$("#addEduBtn").slideDown();
							$(form)[0].reset();
						});
						//currentTemplate = listJobTemplate.replace("[abr]", abr).replace("[startDate]", startDate).replace("[endDate]", endDate).replace("[Ubicacion]", data.location).replace("[Description]", data.description).replace("[Job]", data.position).replace("[Company]", data.company).replace("[idJob]", data.id);
						// $(".list-jobs").append(formatJobTemplate(data));

						// $("#" + data.id).slideDown("slow");
					});
				} else {
					$(form).slideUp("slow", function() {
						var eduTemplate = listEduTemplate.replace("[Degree]", data.degree).replace("[School]", data.name).replace("[startYear]", data.startYear).replace("[endYear]", data.endYear).replace("[idSchool]", data.id_school).replace("[idSchool]", data.id_school);
						$("#" + data.id_school).replaceWith(eduTemplate);
						$("#" + data.id_school).slideDown("slow");
						$(form)[0].reset();
					});
				}

				$btn.button('reset');

			}, 2000);
		});
	} else {
		$("[name=" + emptyIn[0] + "]").focus();
		for (var i = 0; i < emptyIn.length; i++) {
			$("[name=" + emptyIn[i] + "]").addClass("input-error");
			$("[name=" + emptyIn[i] + "]").keyup(function(e) {
				if($(this).val() != "") $(this).removeClass("input-error");
				else $(this).addClass("input-error");
			})
		}
		$btn.button('reset');
	}

}

saveWork = function(event, element) {
	if(event) {
		event.preventDefault();
	}

	var form = $(element).parent().parent().parent();
	var jobObject = {};
	var emptyIn = [];

	var $btn = $(element).button('loading');

	$(form).serializeArray().map(function(x){ 
		if(x.value == "") emptyIn.push(x.name);
		else jobObject[x.name] = x.value; 
	});

	if ($("#current").is(':checked')) {
		var index = emptyIn.indexOf("endMonth");
		$("[name=endMonth]").removeClass("input-error");
		$("[name=endYear]").removeClass("input-error");
		if(index > -1) emptyIn.splice(index, 1);

		var index2 = emptyIn.indexOf("endYear");
		if(index2 > -1) emptyIn.splice(index2, 1);
	};

	if(emptyIn.length <= 0) {
		$.ajax({
			url: "/candidato/configuracion/saveWork",
			type:"POST",
			dataType:"json",
			data: {job : jobObject}
		}).done(function(data){

			console.log(data);
			setTimeout(function() {

				if(!data.editado) {

					$(form).slideUp("slow", function() {

						$(formatJobTemplate(data)).insertBefore("#addJobBtn");

						$("#endDate").show();
						$("#" + data.id).slideDown("slow", function() {
							$("#addJobBtn").slideDown();
							$(form)[0].reset();
							$(form).find('#search-select').select2("val","MX");
							$(form).find('#search-select').change();
						});
					});

				} else {
					$(form).slideUp("slow", function() {

						$("#endDate").show();
						data.id = data.id_job;
						$("#" + data.id_job).replaceWith(formatJobTemplate(data));
						$("#" + data.id_job).slideDown("slow", function() {
							$(form)[0].reset();
							$(form).find('#search-select').select2("val","MX");
							$(form).find('#search-select').change();
						});
					});
				}

				$btn.button('reset');

			}, 2000);
		})
	} else {
		$("[name=" + emptyIn[0] + "]").focus();
		for (var i = 0; i < emptyIn.length; i++) {
			$("[name=" + emptyIn[i] + "]").addClass("input-error");

			if($("[name=" + emptyIn[i] + "]").prop('type') != "select-one") {
				$("[name=" + emptyIn[i] + "]").keyup(function(e) {
					if($(this).val() != "") $(this).removeClass("input-error");
					else $(this).addClass("input-error");
				})
			} else {
				$("[name=" + emptyIn[i] + "]").change(function(e) {
					if($(this).val() != "") $(this).removeClass("input-error");
					else $(this).addClass("input-error");
				})
			}
		}
		$btn.button('reset');
	}
}

formatJobTemplate = function(job) {

	var startDate = (job.startDate != null) ? formatDate(job.startDate.split('-')[0],job.startDate.split('-')[1])  : formatDate(job.startMonth, job.startYear);
	var jobPosition = job.position.split(" ");
	var description = (job.description != null) ? job.description : job.job_description;
	var endDate = null;

	if(job.endMonth != null) {
		if(job.endMonth != null && job.endYear != null) var endDate = formatDate(job.endMonth, job.endYear);
		else var endDate = "Actual";
	} else {
		if(job.current == 1) endDate = "Actual";
		else endDate = formatDate(job.endDate.split('-')[0], job.endDate.split('-')[1]);
	}

	currentTemplate = listJobTemplate.replace("[startDate]", startDate).replace("[endDate]", endDate).replace("[Ubicacion]", job.location).replace("[Description]", description).replace("[Job]", job.position).replace("[Company]", job.company).replace("[idJob]", job.id).replace("[idJob]", job.id);

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
			if($("#profesionalExperience").attr("data-type") == "editar") {
				$("#endDate").show();
				$("#profesionalExperience").slideUp("slow", function() {
					$("#profesionalExperience")[0].reset();
					$("#profesionalExperience").find('#search-select').select2("val","MX");
					$("#profesionalExperience").find('#search-select').change();
				});
			}
			$(listElement).slideUp("slow", function() {
				$(listElement).remove();
			});
		}
	});
}

removeSchool = function(element) {
	var listElement = $(element).parent().parent();

	$.ajax({
		url: "/candidato/configuracion/deleteSchool",
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

// editWork = function(element) {
// 	var listElement = $(element).parent().parent();

// 	$.ajax({
// 		url: "/candidato/configuracion/editWork",
// 		type:"POST",
// 		dataType:"json",
// 		data: { id : $(listElement).attr("id") }
// 	}).done(function(data){
// 		console.log(data);
// 	});
// }

selectWorks = function(id) {
	var html = "";
	$.ajax({
		url: "/candidato/configuracion/getWork",
		dataType:"json"
	}).done(function(data){
		for (var i = 0; i < data.length; i++) {
			html += formatJobTemplate(data[i]).replace("display: none;", "");
		};

		$(".list-jobs").prepend(html);
	});

}

getSchools = function() {
	var html = "";
	$.ajax({
		url: "/candidato/configuracion/getSchools",
		dataType:"json"
	}).done(function(data){
		for (var i = 0; i < data.length; i++) {
			var eduTemplate = listEduTemplate.replace("[Degree]", data[i].degree).replace("[School]", data[i].name).replace("[startYear]", data[i].startYear).replace("[endYear]", data[i].endYear).replace("[idSchool]", data[i].id).replace("display: none;", "").replace("[idSchool]", data[i].id);
			$(".list-edus").prepend(eduTemplate);
		};
	});
}

viewCurriculum = function() {
	$.ajax({
		url: "/candidato/configuracion/curriculum"
	}).done(function(data){
		$("#slider-curriculum").html(data);
		$("#slider-curriculum").show();
		curriculumTemplate = curriculumTemplate.replace("[themelocal]", themeLocal);

		$(".list-curriculums > .media > .media-body > form").each(function(i, item){
			var input = $(item).children().eq(1);
			$(input).keypress(function(e) {
				if(e.keyCode == 13) {
					e.preventDefault();
					if($(this).attr("id") != "new") $(input).blur();
				}
			});
		});
	});
}

changeFile = function(element) {
	var file_data = $(element).prop('files')[0];
	var file_button = $(element).parent().parent().siblings();
	var tipos = {'pdf':'fa fa-file-pdf-o', 'doc':'fa fa-file-word-o', 'docx':'fa fa-file-word-o', 'jpg':'fa fa-file-image-o', 'jpeg':'fa fa-file-image-o', 'png':'fa fa-file-image-o', 'gif':'fa fa-file-image-o', 'bmp':'fa fa-file-image-o'};
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
	            			$(element).siblings().eq(1).html("Error: archivos permitidos: *.pdf, *.doc, *.docx, *.(jpg,png,gif,bmp)");
	            			setTimeout(function() {
								$(element).siblings().eq(1).show();
							}, 1500);
	            		}
	            	} else {
	            		$(file_button).eq(2).attr("id", data.file);
	            		$(file_button).eq(0).children().children().removeClass().addClass(tipos[data.extension]).addClass("btn-curriculum");
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
			console.log("Saludos");
			$.ajax({
		        url: '/candidato/configuracion/curriculum',
	            dataType: 'json',
	            cache: false,
	            contentType: false,
	            processData: false,
	            data: form_data,                        
	            type: 'post',
	            success: function(data) {
	            	console.log(data);
	            	if(data[0] != null) {
	            		var errors = Object.getOwnPropertyNames(data[0].error).sort();
	            		if(errors[0] === "accept_file_types") {
	            			$(element).siblings().eq(1).html("Error: archivos permitidos: *.pdf, *.doc, *.docx, *.(jpg,png,gif,bmp)");
	            			setTimeout(function() {
								$(element).siblings().eq(1).show();
							}, 1500);
	            		}
						// $(element).siblings().eq(1).html(data[0].error);
						// $(element).siblings().eq(1).show();
	            	} else {
	            		var media = $(element).parents().eq(2);
	            		$(file_button.eq(0)).children().css("background","#ff7900");
						$(file_button.eq(0)).children().children().removeClass("fa fa-upload").addClass(tipos[data.extension]).css("color", "#fff");
						$(".list-curriculums").append(curriculumTemplate);
						$(file_button.eq(1)).show();
						$('<i class="fa fa-close edit-element click-element" onclick="deleteCurriculum(event, this);" style="color: #333;"></i>').insertBefore($(file_button).eq(1));
						$(file_button.eq(1)).attr("id", data.file_name);
						$(element).siblings().eq(0).attr("id", data.id);
	            	}
	            },
	            error: function (jqXHR, status, err) {
	            	console.log(err);
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

deleteCurriculum = function(e, element) {
	if(e) {
		e.preventDefault();
	}

	var $name = $(element).siblings().eq(1).attr("id");
	var $id = $(element).siblings().eq(2).find("input[type=text]").attr("id");

	$.ajax({
        url: "/candidato/configuracion/deleteCurriculum",
		type:"POST",
		dataType:"json",
		data: {id : $id, name : $name },
		success: function(data) {
			if(data == 1) {
				$(element).parent().slideUp("slow", function() {
					$(this).remove();
				});
			}
		}
    });
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

skillUp = function(element) {
	if($(element).val() == "") {
		$(element).parent().siblings().removeClass("no-disabled").addClass("disabled");
		$(element).parent().siblings().eq(0).children().prop("disabled", true);
		$(element).parent().siblings().eq(1).children().removeClass("click-element");
	} else {
		$(element).parent().siblings().removeClass("disabled").addClass("no-disabled");
		console.log();
		$(element).parent().siblings().eq(0).children().prop("disabled", false);
		$(element).parent().siblings().eq(1).children().addClass("click-element");
	}
}

getAllSL = function() {
	$.ajax({
		url: "/candidato/configuracion/skills"
	}).done(function(data){
		$("#languages").children().eq(0).children().append(data);
	});

	$.ajax({
		url: "/candidato/configuracion/languages"
	}).done(function(data){
		$("#languages").children().eq(1).children().append(data);
		console.log(theme_path);
		$.getJSON( theme_path + "data/languages.json", function( data ) {
			console.log(data);
			dataTemplate = data;
			$("#languagesForm select[name='languageName']").each(function(i, objeto){
				var html = "";
				$.each(data.languages, function (i, item) {
					if($(objeto).attr("data-value") == item) {
						$(objeto).append('<option value="' + item + '" selected >' + item + '</option>');
					} else {
						$(objeto).append('<option value="' + item + '">' + item + '</option>');
					}
				});
				$(objeto).select2();
			});
			
		});
	});
}

addSL = function(e, element, type) {
	if(e) {
		e.preventDefault();
	}

	var template = $(element).parent().parent().parent().prop("outerHTML");

	if(type == 1 && $(element).parent().attr("class") != "disabled") {
		var skillObject = {};
		var form = $(element).parent().parent(); 
		$(form).serializeArray().map(function(x){ skillObject[x.name] = x.value; });
		$.ajax({
	        url: "/candidato/configuracion/saveSL",
			type:"POST",
			dataType:"json",
			data: {skill : skillObject},
			success: function(data) {
				var select = $(element).parent().siblings().eq(1).children().eq(1);
				var input = $(element).parent().siblings().eq(0).children().eq(1);
				template = template.replace("no-disabled", "disabled").replace("no-disabled", "disabled");
				$(select).change(function(e){
					updateDataSL(e, $(this), 1);
				});
				$(input).blur(function(e){
					updateDataSL(e, $(this), 1);
				});
				$(form).attr("data-id", data);
				$("#slider-skills").append(template);
				$(element).parent().html("<button class='btn btn-default btn-circle btn-lg click-element btn-theme fa fa-close' onclick='removeSL(event, this, 1);'></button>");
			}
	    });
	} else if(type == 2 && $(element).parent().attr("class") != "disabled") {
		var languageObject = {};
		var form = $(element).parent().parent();
		$(element).parent().parent().serializeArray().map(function(x){ languageObject[x.name] = x.value; });

		$.ajax({
	        url: "/candidato/configuracion/saveSL",
			type:"POST",
			dataType:"json",
			data: {language : languageObject},
			success: function(data) {
				var select = $(element).parent().siblings().eq(1).children().eq(1);
				var selectName = $(element).parent().siblings().eq(0).children().eq(1);
				languageTemplate = languageTemplate.replace("no-disabled", "disabled").replace("no-disabled", "disabled");
				$(select).change(function(e){
					updateDataSL(e, $(this), 2);
				});
				$(selectName).change(function(e){
					updateDataSL(e, $(this), 2);
				});
				$(form).attr("data-id", data);
				$("#slider-languages").append(languageTemplate);

				var size = $("#slider-languages").find("select").length;
				var currentSelect = $("#slider-languages").find("select").eq(size - 2);
				for (var i = 0; i < dataTemplate.languages.length; i++) {
					$(currentSelect).append('<option value="' + dataTemplate.languages[i] + '">' + dataTemplate.languages[i] + '</option>');
				};
				console.log(currentSelect);
				$(currentSelect).select2();
				$(element).parent().html("<button class='btn btn-default btn-circle btn-lg click-element btn-theme fa fa-close' onclick='removeSL(event, this, 2);'></button>");
			}
	    });
	}
}

updateDataSL = function(e, element, type) {
	if(e) {
		e.preventDefault();
	}

	
	if(type == 1) {
		var skillObject = {};
		$(element).parent().parent().serializeArray().map(function(x){ skillObject[x.name] = x.value; });

		$.ajax({
	        url: "/candidato/configuracion/saveSL",
			type:"POST",
			dataType:"json",
			data: {skill : skillObject, id : $(element).parent().parent().attr("data-id")},
			success: function(data) {
				console.log(data);
			}
	    });
	} else if(type == 2) {
		var languageObject = {};
		$(element).parent().parent().serializeArray().map(function(x){ languageObject[x.name] = x.value; });
		
		$.ajax({
	        url: "/candidato/configuracion/saveSL",
			type:"POST",
			dataType:"json",
			data: {language : languageObject, id : $(element).parent().parent().attr("data-id")},
			success: function(data) {
				console.log(data);
			}
	    });
	}
}

removeSL = function(e, element, type) {
	if(e) {
		e.preventDefault();
	}

	var row = $(element).parents().eq(2);

	if(type == 1) {
		var skillObject = {};
		$(element).parent().parent().serializeArray().map(function(x){ skillObject[x.name] = x.value; });
		console.log(skillObject);
		$.ajax({
	        url: "/candidato/configuracion/removeSL",
			type:"POST",
			dataType:"json",
			data: {skill : skillObject},
			success: function(data) {
				$("#slider-skills > div").each(function(){
					var id = $(this).children().attr("data-id");
					if(id > data) {
						$(this).children().attr("data-id", id - 1);
					}
				});
				$(row).remove();
			}
	    });
	} else if(type == 2) {
		var languageObject = {};
		$(element).parent().parent().serializeArray().map(function(x){ languageObject[x.name] = x.value; });
		$.ajax({
	        url: "/candidato/configuracion/removeSL",
			type:"POST",
			dataType:"json",
			data: {language : languageObject},
			success: function(data) {
				$("#slider-languages > div").each(function(){
					var id = $(this).children().attr("data-id");
					if(id > data) {
						$(this).children().attr("data-id", id - 1);
					}
				});
				$(row).remove();
			}
	    });
	}
}

changeCountry = function(e, element) {
	if(e) {
		e.preventDefault();
	}

	var state = $(element).parents().eq(1).next().find("select");
	var city = $(element).parents().eq(1).next().next().find("select");
	console.log(city);
	$.ajax({
		url:"/candidato/configuracion/states",
		type:"POST",
		dataType:"json",
		data: { id_country : $(element).val() }
	}).done(function(data){
		
		var options = "<option value=''>Selecciona</option>";

		$.each(data, function(i, item) {
			options += "<option value='" + i + "'>" + item + "</option>";
		});

		$(state).parent().find('span').eq(0).html("Selecciona");
		$(state).html(options);
		$(city).html("<option value=''>Selecciona</option>");
		$(city).select2('val', '');
	})
}

changeCountryforCity = function(e, element) {
	if(e) {
		e.preventDefault();
	}

	var country = $(element).parents().eq(1).next().find("select");
	$.ajax({
		url:"/candidato/configuracion/cityByCountry",
		type:"POST",
		dataType:"json",
		data: { id_country : $(element).val() }
	}).done(function(data){

		var options = "";
		var firstOption = "";

		$.each(data, function(i, item) {
			if(i == 0) firstOption = item[Object.getOwnPropertyNames(item)[0]];
			options += "<option value='" + Object.getOwnPropertyNames(item)[0] + "'>" + item[Object.getOwnPropertyNames(item)[0]] + "</option>";
		});

		$(country).parent().find('span').eq(0).html(firstOption);
		$(country).html(options);
	})
}

changeState = function(e, element) {
	if(e) {
		e.preventDefault();
	}

	var country = $(element).parents().eq(1).prev().find("select");
	var state = $(element).find(":selected").text();
	var city = $(element).parents().eq(1).next().find("select");

	$.ajax({
		url:"/candidato/configuracion/cities",
		type:"POST",
		dataType:"json",
		data: { id_country : $(country).val(), id_state : state }
	}).done(function(data){
		
		var options = "<option value=''>Selecciona</option>";

		$.each(data, function(i, item) {
			options += "<option value='" + i + "'>" + item + "</option>";
		});

		$(city).parent().find('span').eq(0).html("Selecciona");
		$(city).html(options);
		
	})
}

editSchool = function(e, id) {
	if(e) {
		e.preventDefault();
	}
	console.log("Entramos");
	$("#addEduBtn").slideDown();
	$("#educationForm").attr("data-type", "editar");
	$("#educationForm").append("<input type='hidden' value='" + id + "' name='id_school' />");
	$("#educationForm").slideUp("slow", function() {
		$.ajax({
			url:"/candidato/configuracion/editSchool",
			type:"POST",
			dataType:"json",
			data: { id_school : id }
		}).done(function(data){
			console.log(data);
			$.each(data[0], function(i, item) {
				$("[name=" + i + "]").val(item);
			});

			$("#educationForm").slideDown("slow", function() {
				$("#educationForm :input").eq(0).focus();
			});
		});
	});
}

editJob = function(e, id) {
	if(e) {
		e.preventDefault();
	}

	$("#addJobBtn").slideDown();
	$("#profesionalExperience").attr("data-type", "editar");
	$("#profesionalExperience").slideUp("slow", function() {
		$.ajax({
			url:"/candidato/configuracion/editJob",
			type:"POST",
			dataType:"json",
			data: { id_job : id }
		}).done(function(data){
			$.each(data[0], function(i, item) {
				if(i == "id") $("#profesionalExperience").append("<input type='hidden' name='id_job' value='" + item + "' />");
				if(i == "startDate") {
					var dateData = item.split("-");
					$("[name=startMonth]").find("option[value='" + dateData[0] + "']").attr("selected", "selected");
					$("[name=startYear]").val(dateData[1]);
				} else if(i == "endDate") {
					var dateData = item.split("-");
					$("[name=endMonth]").find("option[value='" + dateData[0] + "']").attr("selected", "selected");
					$("[name=endYear]").val(dateData[1]);
				}
				else {
					if($("[name=" + i + "]").prop('type') == 'text') {
						$("[name=" + i + "]").val(item);
					} else if($("[name=" + i + "]").prop('type') == 'textarea') {
						$("[name=" + i + "]").val(item);
					} else if($("[name=" + i + "]").prop('type') == 'checkbox') {
						if(item > 0) {
							$("[name=" + i + "]").prop("checked", true);
							$("#endDate").slideUp("fast");
						}
					} else if($("[name=" + i + "]").prop('type') == 'select-one'){
						if(i != "senior_level") {
							if(i == "id_country") {
								$("[name=" + i + "]").select2("val", item);
								$("[name=" + i + "]").triggerHandler('change');
							} else {
								$("[name=" + i + "]").prop("disabled", true);
								setTimeout( function() {
									$("[name=" + i + "]").select2("val", item);
									$("[name=" + i + "]").prop("disabled", false);
								}, 1000);
							}
						}
						else $("[name=" + i + "]").find("option[value='" + item + "']").attr("selected", "selected");
					}
				}
			});
			$("#profesionalExperience").slideDown("slow", function() {
				$("#profesionalExperience :input").eq(0).focus();
			});
		});
	});
}

$(document).ready(function() {

	$('a[title]').tooltip();
	setProfesionalExperience(1);
	getSchools();
	viewCurriculum();
	getAllSL();

	var fecha = $("#birthdate").val().split('-');
	$('#day').val(fecha[2]);
	$('#month').val(fecha[1]);
	$('#year').val(fecha[0]);

	$('#candidateProfileForm').validate({
		rules:{
			'candidate[phone_number]':{
				number: true
			},
			"candidate[zip_code]":{
				required: true,
				number: true,
				minlength: 5
			}
		}
	});

	$('#updateCandidateProfile').on('click', function(e){
		
		if(e) {
			e.preventDefault();
		}

		var $btn = $(this).button('loading');

		if($('#candidateProfileForm').valid()){
			$.ajax({
				url:"",
				type:"POST",
				dataType:"json",
				data:$('#candidateProfileForm').serialize()+"&candidate[birthdate]="+$('#year').val()+"-"+$('#month').val()+"-"+$('#day').val()
			}).done(function(data){
				
				console.log(data);
				setTimeout(function() {

					var $msg = $('<div/>');
					$msg.addClass('alert');
					$msg.css("width","100%");
					$msg.css("display", "none");
					$msg.html(data.msg);

					if(data.status == 1){				
						$msg.addClass('alert-success');
					}else{				
						$msg.addClass('alert-danger');											
					}
					$('#msgUpdateInfo').append($msg);
					$($msg).slideDown("fast", function(){
						$btn.button('reset');
						setTimeout(function() {
							$(".alert").slideUp();
						}, 10000);
					});

				}, 2000);

			});
		}
		
	});

	$("#addJobBtn").click(function(e) {
		if(e) {
			e.preventDefault();
		}

		$(this).slideUp();
		$("#profesionalExperience").attr("data-type", "nuevo");
		$("[name=id_job]").remove();
		$("#profesionalExperience").slideUp("slow", function() {
			$("#profesionalExperience").find("input[type=text], textarea").val("");
			$("#profesionalExperience").find("select").prop("selectedIndex", 0);
			$("#profesionalExperience").find('#search-select').select2("val","MX");
			$("#profesionalExperience").find('#search-select').change();
			
			$("#profesionalExperience").slideDown("slow", function() {
				$("#profesionalExperience :input").eq(0).focus();
			});
		});
	});

	$("#addEduBtn").click(function(e) {
		if(e) {
			e.preventDefault();
		}

		$("[name=id_school]").remove();
		$(this).slideUp();
		$("#educationForm").slideDown("slow", function() {
			$("#educationForm :input").eq(0).focus();
		});
	});

	if($("#mainArea").hasClass("exp")) {
		$("#experienceBtn").trigger("click");
	}

	$.validator.messages.equalTo = "Las contraseñas no coinsiden";

	$('#changePassBtn').click(function(){
		if($('#changePass').valid()){
			
			systemStartWorks();
			$.ajax({
				url:"/candidato/configuracion",
				type:"POST",
				dataType:"json",
				data: $('#changePass').serialize()
			}).done(function(data){
				$('#msgUpdatePass').find('div.alert').remove();
				var $msg = $('<div/>');
				$msg.addClass('alert');
				$msg.css("width","100%");
				$msg.html(data.msg);
				if(data.status == 1){				
					$msg.addClass('alert-success');
				}else{				
					$msg.addClass('alert-danger');											
				}
				$("#changePass")[0].reset();
				$('#msgUpdatePass').append($msg);
				systemEndWorks();
			});
		}
	});

	$('.change-pass').click(function(){
		systemStartWorks();	
	});

	$("#selectCountry").select2();
	$("#selectState").select2();
	$("#selectCity").select2();

	$('#changePass').validate({
		rules:{
			"password[confirm_new]":{
				required: true,
				equalTo:"#newPassword"
			}
		}
	});

	for (var i = 2015; i >= 1900; i--) {
		$('.edu-container').find('#endYearSchool').append("<option value='" + i + "'>" + i + "</option>");
		$('.edu-container').find('#startYearSchool').append("<option value='" + i + "'>" + i + "</option>");	
	}

});