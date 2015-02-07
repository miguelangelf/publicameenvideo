// JavaScript Document


var searching = false;

var months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

formatDate = function(date) {
	var fechaString = "N/A";
	if(date != null) {
		var dateTime = date.split(" ");
	
		var fecha = dateTime[0].split("-");
		var tiempo = dateTime[1].split(":");
		var dayTime = (tiempo[0] > 12) ? "PM" : "AM";

		fechaString = fecha[2] + " " +  months[parseInt(fecha[1]) - 1] + ", " + fecha[0] + " a las " + tiempo[0] + ":" + tiempo[1] + " " + dayTime;
	}

	return fechaString;
}

showMessageBody = function(e, element, data) {
	if(e) {
		e.preventDefault();
	}

	if(data == null) return false;
	$(".list-group > a").each(function(i, item) {
		$(item).removeClass("active-list");
	});

	$(element).removeClass("unread").addClass("active-list");

	var name = data.envia.split(" ");
	var abv = "";
	if(name.length > 1) {
		abv = name[0].charAt(0) + name[1].charAt(0);
	} else {
		abv = name[0].charAt(0);
	}

	var header = $("#message").find(".message-general-data");
	var body = $("#message > .panel-body").children();

	$(header).children().eq(0).html(data.envia);
	$(header).children().eq(1).html("Para: " + data.recibe);
	$(header).children().eq(2).html("<b>" + data.subject + "</b>");

	$("#message > .panel-heading").children().eq(1).children().html(formatDate(data.date));
	$("#message > .panel-heading").children().eq(2).find('h4').html(abv);
	$(body).eq(0).html(data.body);
	$(body).eq(1).html("Powered By Superchamba");

};

candidates = function( $options ){
	
	this.content = $options.conteiner;
	this.url = $options.url;	
	
	closeTabsForms = function( $type, $content ){	
		var $tab_id;
		var $name_id;
		var $id;		
		$('#tab ul.tabpanel_mover li').each( function(index, element) {
			$name_id = $(this).attr('id');
			$id = $name_id.search( $type );
			if( $id != -1){
				$tab_id = $content.getTabPosision($name_id);	
				$content.kill($tab_id);	
			}
			
        });
	}
	
	this.get = function($id_tab, $url, search, filters){										
		
		var $content = this.content;
		$url = ($url == undefined) ? this.url : $url;							
		var _search = (search === undefined || search === null)  ? "" : search;							
		var _filters = (filters === undefined || filters === null)  ? "[]" : filters;							
		
		systemStartWorks();
		$.ajax({
			url: $url,
			type:"POST",
			data:{
				type: $id_tab,
                                search: _search,
                                filters: _filters
			}				
		}).done(function(data){
			systemEndWorks();
																					
			$content.setContent($id_tab, data);

			if($id_tab == 'talentDatabase'){
				$('.circliful').each(function(index, el) {
					$(this).circliful();	
				});
				console.log($id_tab);
			}

			if($id_tab == "messagesCompany") {
				$("#messages").find(".list-group").children().eq(0).click();
			}
							
			$('.hire_selected').hide();
			$('.test_selected').hide();
			$('.dismiss_selected').hide();
			$('.discard_selected').hide();
			$('.nohire_selected').hide();
                        if(searching){
                            $("#search-icon-input").removeClass("fa-gear fa-spin");
                            $('#search-icon-input').addClass("fa-search");
                            searching = false;
                        }
                        
                        
		});
			
	}		
	
	
	this.modalConfirmAction = function( $options ){							
		var parent = this;					
		var $candidates = $options.modal.attr('id-candidates').split(',');	
				
		$.ajax({
			url: $options.url,
			type:"POST",
			dataType:"json",
			data:{
				candidates: $candidates,
				data: $options.data
			}
		}).done(function(data){			
			$options.msg_content.show();
			if(data.status == 1){
				$options.msg_content.addClass('alert-success').removeClass('alert-danger');								
				if($options.upadate_hire){
					parent.get('hiredCandidates');									
				}
				if($options.upadate_td){
					parent.get('talentDatabase');									
				}
				if($options.upadate_interview){
					parent.get('inviteCandidates');									
				}
				if($options.upadate_test){
					parent.get('testCandidates');									
				}
				window.setTimeout(function(){
					$options.modal.modal('hide');
				},2000);
			}else{
				$options.msg_content.addClass('alert-danger').removeClass('alert-success');
			}
			$options.msg_content.html(data.msg);												
		});
			
	}
	
	this.hire = function(){
		
		var parent = this;		
		parent.modalConfirmAction({
			modal: $('#modalHire'),
			msg_content: $('#msgHire'),
			url:"/empresa/candidatos/hire",
			upadate_hire: true,
			upadate_td: true,
			upadate_interview: true,
			upadate_test: true
		});
	
	}
	
	this.test = function(){
		
		var parent = this;		
		parent.modalConfirmAction({
			modal: $('#modalTest'),
			msg_content: $('#msgTest'),
			url:"/empresa/candidatos/test",
			upadate_hire: false,
			upadate_td: true,
			upadate_interview: true,
			upadate_test: true
		});
	
	}
	
	this.dismiss = function(){
		
		var parent = this;		
		parent.modalConfirmAction({
			modal: $('#modalDismiss'),
			msg_content: $('#msgDismiss'),
			url:"/empresa/candidatos/dismiss",
			upadate_hire: true,
			upadate_td: false,
			upadate_interview: false,
			upadate_test: false,
			data: $('#reasonsToDismiss').serializeArray()
		});
		
	}
	
	
	this.discard = function(){
		
		var parent = this;		
		parent.modalConfirmAction({
			modal: $('#modalDiscard'),
			msg_content: $('#msgDiscard'),
			url:"/empresa/candidatos/discard",
			upadate_hire: false,
			upadate_td: true,
			upadate_interview: true,
			upadate_test: true
		});
							
	}
	
	this.contact = function(tabpanel, $candidate_id, $candidate_name){
		
		systemStartWorks();
		$.ajax({
			url:"/empresa/candidatos/citar/"+$candidate_id,
			type:"GET",
			cache:false
		}).done(function( data ){
			closeTabsForms("contact", tabpanel);
			tabpanel.addTab({
				id:"contact_"+$candidate_id,
				title:"Programar Entrevista: "+$candidate_name,
				html:data,
				closable: true				
			});						
			
			$('#contactForm').validate({
				rules:{
					"contact[Place]":{
                		required: true
            		},
					"contact[Date]":{
						required: true
					},
					"contact[Hour]":{
						required: true
					},
					"contact[Minute]":{
						required: true
					},
					"contact[Message]":{
						required: true
					},
					"contact[subject]":{
						required: true
					},
					"contact[interviewer]":{
						required: true
					}
				}
			});
			
			$('#datePicker').datepicker({
				minDate: 0,
				dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
				monthNames: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
				dateFormat: "dd MM yy"
			});
            
			systemEndWorks();
		
		});			
	}
	
	this.details = function( tabpanel, $candidate_id, $candidate_name ){
		systemStartWorks();
		$.ajax({
			url: '/empresa/candidatos/candidato',
			type:"POST",
			data:{
				candidate_id: $candidate_id
			}				
		}).done(function(data){
			systemEndWorks();
			closeTabsForms("candidate", tabpanel);			
			tabpanel.addTab({
				id:"candidate_"+$candidate_id,
				title:$candidate_name ,
				html:data,
				closable: true				
			});				
		});	
	};


	this.deleteDocumentVacancy = function( $fid ){
		systemStartWorks();
		$.ajax({
			url: '/empresa/vacancies/deleteDocumentVacancy',
			type: 'POST',
			dataType: 'json',
			data: {
				fid: $fid
			}
		})
		.done(function( data ) {			
			systemEndWorks();
			if( data.status == 1){
				$('#fileuploadBtn').show();
				$('#documentsActions').remove();
			}
		});
		
	}
	
	this.getMap = function(){				
			
			if($('#zip_code').val() != '' && $('#street').val() != '' && $('#number').val() != ''){
				
				$('#errorNoMap').hide();
				
				var $url = 'http://maps.googleapis.com/maps/api/geocode/json?address='+$('#street').val()+' '+encodeURIComponent($('#number').val())+', '+$('#id_city option:selected').text()+', '+$('#id_state option:selected').text()+'&components=postal_code:'+$('#zip_code').val()+'|country:'+$('#id_country option:selected').val();	
				console.log( $url );
				$.ajax({
					url: $url,
					type: "GET",
					dataType:"json"					
				}).done(function( data ){
					if(data.results[0] != undefined){
						//console.log( data.results[0].geometry.location );
						var $lat = data.results[0].geometry.location.lat;
						var $lng = data.results[0].geometry.location.lng;
						var myLatlng = new google.maps.LatLng( $lat, $lng );
						var mapOptions = {
						  zoom: 15,
						  center: myLatlng
						}
						var map = new google.maps.Map(document.getElementById("google_map"), mapOptions);
						
						// Place a draggable marker on the map
						var marker = new google.maps.Marker({
							position: myLatlng,
							map: map,
							draggable:true,
							title:"Selecciona la ubicacion de la vacante"
						});
						
						$('#vacancy_lat').val( $lat );
						$('#vacancy_lng').val( $lng );
						google.maps.event.addListener(marker, 'dragend', function(){					
							$('#vacancy_lat').val( marker.getPosition().lat() );
							$('#vacancy_lng').val( marker.getPosition().lng() );
						});
						
						$('#sectionConfirmMarker').show();
						$('#marker_ok').prop('checked', false);
					}
					
				});
			}
				
	}
	
	this.vacancyView = function( $vacancy_id, $vacancy, $revised ){
		
		closeTabsForms("vacancy_view", tabpanel);
		systemStartWorks();
		$.ajax({
			url:"/empresa/vacancies/datail",
			type:"POST",
			data:{
				vacancy_id: $vacancy_id,
				revised: $revised
			}
		}).done(function( data ){
			tabpanel.addTab({
				id:"vacancy_view_"+$vacancy_id,
				title:$vacancy ,
				html: data,
				closable: true				
			});
			
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
			
		});
	}


	this.vacancyManager = function( $vacancy_id, $vacancy ){
		systemStartWorks();

		var parent = this;

		$vacancy_id = ($vacancy_id == undefined) ? 'new' : $vacancy_id;
		$vacancy = ($vacancy == undefined) ? 'Nueva vacante' : $vacancy;
		$.ajax({
			url: '/empresa/vacancies/manager',
			type:"POST",
			data:{
				vacancy_id: $vacancy_id
			}				
		}).done(function(data){
			systemEndWorks();			
			closeTabsForms("vacancy_edit", tabpanel);			
			tabpanel.addTab({
				id:"vacancy_edit_"+$vacancy_id,
				title:$vacancy ,
				html:data,
				closable: true				
			});

			$("#id_country").select2();
			$("#id_state").select2();
			$("#id_city").select2();

			$("#txtEditor").Editor({
				'togglescreen': false,
				'block_quote': false,
				'print': false,
				'source': false
			});
			
			$("#txtEditor").Editor("setText", $('#job_description').val());

			var languages = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,			  			  				
				local: [],
				remote: {
					url: "/empresa/vacancies/languages/%QUERY",
					ajax:{						
						type: "POST"						
					},
					filter: function(list) {
				      	return $.map(list, function(value) {
				        	return { text: value }; 
				    	});
				    }
				}
			});

			var industries = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,			  			  				
				local: [],
				remote: {
					url: "/empresa/vacancies/industries/%QUERY",
					ajax:{						
						type: "POST"						
					},
					filter: function(list) {
				      	return $.map(list, function(value) {
				        	return { text: value }; 
				    	});
				    }
				}
			});

			var functions = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,			  			  				
				local: [],
				remote: {
					url: "/empresa/vacancies/functions/%QUERY",
					ajax:{						
						type: "POST"						
					},
					filter: function(list) {
				      	return $.map(list, function(value) {
				        	return { text: value }; 
				    	});
				    }
				}
			});
			
			languages.initialize();
			industries.initialize();
			functions.initialize();

			$("#languages").tagsinput({
				typeaheadjs: {
					name: 'languages',
					displayKey: 'text',
					valueKey: 'text',					
					source: languages.ttAdapter()
				}
			});

			$("#industries").tagsinput({				
				typeaheadjs: {
					name: 'industries',
					displayKey: 'text',
					valueKey: 'text',				
					source: industries.ttAdapter()
				}
			});

			$("#functions").tagsinput({
				typeaheadjs: {
					name: 'functions',
					displayKey: 'text',
					valueKey: 'text',					
					source: functions.ttAdapter()
				}
			});
			
			$('#attachment').on('change', function(){
				console.log( $(this).parent().find('span.file_name').html( ' ' + $(this).val() ) );
			});

		    $('#vacancyForm').validate({
		    	rules:{
		    		'vacancy[position]':{
		    			required:true
		    		},
		    		'vacancy[job_description]':{
		    			required:true
		    		},
		    		'vacancy[id_city]':{
		    			required:true
		    		},
		    		'vacancy[id_state]':{
		    			required:true
		    		},
		    		'vacancy[industries][]':{
		    			required:true
		    		},
		    		'vacancy[zip_code]':{
		    			required:true
		    		},
					'vacancy[street]':{
		    			required:true
		    		},
					'vacancy[number]':{
		    			required:true
		    		},
		    		'vacancy[salary]':{
		    			required:true,
		    			number: true,
						min:1
		    		},
					'vacancy[marker_ok]':{
						required:true,
					},
					'files[]': {						
						extension: "jpeg|jpg|gif|png|doc|docx|pdf"
					}
		    	},
		    	messages: {
					'files[]': "Permitidos: *.pdf, *.doc, *.docx, *.jpg, *.png, *.gif, *.bmp, *.gif",
					'vacancy[salary]':{
						min: "Requerido"
					}
		    	},
		    	errorPlacement: function(error, element){		    		
		    		$(element).addClass('error-validation');
		    		var $label = $(element).parent().find('label');
		    		$label.find('span.alert').remove();
		    		$label.append('<span class="alert alert-danger">'+$(error).html()+'</span>');
					return true;
				},
				highlight: function(element, errorClass, validClass) {					
					$(element).focus();
				},
				unhighlight: function(element, errorClass, validClass) {
					$(element).removeClass('error-validation');
					$(element).parent().find('label').find('span.alert').remove();
				}
		    });
			
			if( !isNaN($('#vacancy_lat').val()) && !isNaN($('#vacancy_lng').val()) && $('#vacancy_lat').val() != 0 && $('#vacancy_lng').val() != 0){				
				var $lat = $('#vacancy_lat').val();
				var $lng = $('#vacancy_lng').val();
				var myLatlng = new google.maps.LatLng( $lat, $lng );
				var mapOptions = {
				  zoom: 15,
				  center: myLatlng
				}
				var map = new google.maps.Map(document.getElementById("google_map"), mapOptions);
				
				// Place a draggable marker on the map
				var marker = new google.maps.Marker({
					position: myLatlng,
					map: map,
					draggable:true,
					title:"Selecciona la ubicacion de la vacante"
				});
				
				google.maps.event.addListener(marker, 'dragend', function(){					
					$('#vacancy_lat').val( marker.getPosition().lat() );
					$('#vacancy_lng').val( marker.getPosition().lng() );
				});
					
			}
						
		});	
	};


	this.vacancySave = function(){		
		var parent = this;
		$('#job_description').val( $("#txtEditor").Editor("getText") );

		if($('#vacancyForm').valid()){
			$('#errorNoMap').hide();		
			$('#vacancyForm').ajaxForm({				
				dataType: 'json',
				success: function(data){
					systemEndWorks();
					if(data.status == 1){
						console.log(data)
						closeTabsForms("vacancy", tabpanel);
						parent.get('vacanciesManager', '/empresa/vacancies/index/page/1');	
					}		
				}
			}).submit();
		}else{
			if( $('#marker_ok').valid() == false ){
				$('#errorNoMap').show();
			}
		}

	}

	
	this.sendMsg = function( $candidate_id ){
		
		var parent = this;		
		$('#contactForm div.alert').hide();		
		if($('#contactForm').valid()){
			
			systemStartWorks();
			$.ajax({
				url:"/empresa/candidatos/citar/"+$candidate_id,
				type:"POST",
				dataType:"json",
				data:$('#contactForm').serialize()
			}).done(function(data){
				systemEndWorks();
				$('#contactForm div.alert').show();
				if(data.status == 1){
					$('#contactForm div.alert').addClass('alert-success').removeClass('alert-danger');											
					window.setTimeout(function(){						
						tabpanel.kill('contact_'+$candidate_id );
						$('#inviteCandidates').trigger('click');						
						parent.get('interviewsCandidates', '/empresa/agendar');
					},2000);
				}else{
					$('#contactForm div.alert').addClass('alert-danger').removeClass('alert-success');
				}
				$('#contactForm div.alert').html(data.msg);								
					
			});
		
		}
			
	};
	
	this.getSelected = function( $section ){
		$candidates = new Array();
		$section.find('table input[type="checkbox"]').each(function(index, element) {
            if($(this).prop('checked')){
				$candidates.push($(this).attr('candidate-id'));				
			}
        });			
		return $candidates;
	}
	
	this.sendProfile = function( $email, $id_candidate ){
		console.log($email+'-'+$id_candidate);				
		$.ajax({
			url:"/web/mails/info_candidato",
			type:"POST",
			dataType:"json",
			data:{
				email: $email, 
				id_candidate: $id_candidate
			}
		}).done(function(data){
			$('#msgSendProfile').show();
			$('#msgSendProfile').html(data.msg);
			$('#msgSendProfile').removeClass('alert-success').removeClass('alert-danger');	
			if(data.status == 1){
				$('#msgSendProfile').addClass('alert-success');
				window.setTimeout(function(){
						$('#email_info').modal('hide');
					},
					2000
				);
			}else{
				$('#msgSendProfile').addClass('alert-danger');	
			}
			window.setTimeout(function(){
					$('#msgSendProfile').hide();
				},
				2000
			);
			console.log(data);
		});
	}
	
	this.showProfilingGraph = function( $element ){
		
                $('#modalProfile').modal('show');
                
		var $ProfilingGraph = $('<div/>');
		$ProfilingGraph.attr('id', 'ProfilingGraph');
		/*
		$element.popover({
			title:'Gráfica de Perfilamiento <i class="fa fa-times fa-fw" id="closePopover"></i>',
			html: true,
			placement: 'bottom',
			trigger: 'manual',
			content: $ProfilingGraph
		});
		*/		
		$ProfilingGraph.highcharts({
            chart: {
                type: 'line',
                inverted: true,
                backgroundColor: 'transparent'
            },
            title: {
                text: ''                
            },
            xAxis: [{
                gridLineWidth: 0,
                offset:12,
                categories: [
                    'Pensamiento Práctico',
                    'Integridad Alta',
                    'Respeto a Normas Bajo',
                    'Baja tendencia al Robo',
                    'Deshonesto',
                    'Espontáneo',
                    'Sumiso',
                    'Emocionalmente Inestable',
                    'Serio',
                    'Confiado',
                    'Exigente',
                    'Orientado al Servicio',
                    'Pacífico',
                    'Introvertido',
                    'Flexible',
                    'Independiente'
                ],
                reversed: true,
                labels: {
                    formatter: function () {
                        //return this.value + 'km';
                        return this.value;
                    }
                },
                maxPadding: 60,
                showLastLabel: true
            },{
                linkedTo: 0,
                offset:12,
                categories: [
                    'Pensamiento Estructurado',
                    'Integridad Baja',
                    'Respeto a Normas Alto',
                    'Alta tendencia al Robo',
                    'Honesto',
                    'Diplomático',
                    'Dominante',
                    'Emocionalmente Estable',
                    'Impetuoso',
                    'Suspicaz',
                    'Comprensivo',
                    'Orientado a la Ganancia',
                    'Enérgico',
                    'Extrovertido',
                    'Metódico',
                    'Dependiente'
                ],
                reversed: true,
                labels: {
                    formatter: function () {
                        //return this.value + 'km';
                        return this.value;
                    }
                },
                opposite: true,
                maxPadding: 60,
                showLastLabel: true
            }],
            yAxis:        
            {
                gridLineWidth: 0,
                tickInterval: 1,
                title:{enabled: false},
                min: 1,
                max:10,
                labels: {
                    formatter: function () {
                        //return this.value + '°';
                        return this.value;
                    }
                },
             opposite:true
            },
            legend: {
                enabled: false
            },
            tooltip: {
                enabled:false,
                headerFormat: '<b>{series.name}</b><br/>',
                pointFormat: '{point.x} Competencia: {point.y} Evaluacion'
            },
            plotOptions: {
                spline: {
                    marker: {
                        enable: false
                    }
                }
            },
            series: [{
                lineWidth:4,
                color:'orange',
                data: $.parseJSON($element.attr('data-profiling-graph'))
            }],
            credits:{enabled:false},
            exporting: {
              enabled: false
             }
        });
        
        $("#modalProfilebody").html($ProfilingGraph);
				
					
	};
	
	
	
	this.noHire = function(){
		var parent = this;		
		parent.modalConfirmAction({
			modal: $('#modalNoHire'),
			msg_content: $('#msgNoHire'),
			url:"/empresa/candidatos/noHire",
			upadate_hire: false,
			upadate_td: false,
			upadate_interview: false,
			upadate_test: true		
		});	
	}


	this.getInterviewsDates = function( $uid, $element ){
		$.ajax({
			url: '/empresa/candidatos/getInterviewsDates',
			type: 'POST',
			dataType: 'json',
			data: {id_candidate: $uid },
		})
		.done(function( data ) {

			var $interviews = "";
			$.each(data.interviews, function(index, val) {
				$interviews = $interviews + val.date_time +'<br>';	
			});

			$element.tooltip({
				title:'<strong>Fecha de entrevista</strong>:<br>'+$interviews, 
				placement: 'bottom',
				html:true
			});

			$element.tooltip('show');

		});
		
	}


	this.publishVacancy = function( $id_vacancy, $status ){

		$.ajax({
			url: '/empresa/vacancies/publish',
			type: 'POST',
			dataType: 'json',
			data: {
				id_vacancy: $id_vacancy,
				status: $status
			}
		}).done(function( data ) {
			console.log( data );
		});
		
	}
	
	
	this.companyTeamTab = function( tabpanel, $url ){
		systemStartWorks();
		$.ajax({
			url: $url,
			type:"POST"
		}).done(function( data ){
			tabpanel.addTab({
				id:"company_team",
				title:"Agregar reclutadores a tu equipo",
				html: data,
				closable: true				
			});
			systemEndWorks();	
		});
		
		
	}
	
};

filters = function( $candidates ){
    
    // Static attributes
    this.controller = "/module-engine/filters/";
    this.loaded     = false;
    this.currentFilters = [];   
    // Current state
    this.currentFilter = {    	
        edad_mayor: { value: "" },
        edad_menor: { value: "" },
        genero:     { value: -1},
        delegacion: { value: 0 },
        licencia:   { value: -1 },
        estudios:   { value: 0 },        
        hijos:      { value: -1 },
        estado:     { value: 0 },
        turno:      { value: 0 },
        perfil1:    { value: 0 }, // Administrativo
        perfil2:    { value: 0 }, // Comercial
        perfil3:    { value: 0 }, // Operativo
        perfil4:    { value: 0 },  // Servicio al cliente	
        concluidos: { value: -1 }
    };


    (getIds = function( $parent ){    	
    	$.ajax({
    		url: $parent.controller +'getIds',
    		type: 'POST',
    		dataType: 'json'    		
    	})
    	.done(function( data ) {
    		$.each($parent.currentFilter, function(index, val) {
    			$parent.currentFilter[index].id = data[index];	 
    		});    		
    	});
    	
    })(this);
    
    this.listener = function(attribute,obj){     	   	
        var id = $("#select-filter-input").val();
        var parent = this;
        switch(attribute){
            case "edad_mayor": 
                var value = $(obj).val();
                parent.currentFilter.edad_mayor.value = value;
                break;
            case "edad_menor": 
                var value = $(obj).val();
                parent.currentFilter.edad_menor.value = value;
                break;
            case "genero": 
                var value = $(obj).val();
                parent.currentFilter.genero.value = value;
                break;
            case "delegacion":
                var value = $(obj).val();
                parent.currentFilter.delegacion.value = value;
                break;
            case "licencia":
                var value = $(obj).val();                               
                parent.currentFilter.licencia.value = value;
                break;
            case "estudios":
                var value = $(obj).val();                
                parent.currentFilter.estudios.value = value;
                break;
            case "concluidos":
                var value = $(obj).val();                
                parent.currentFilter.concluidos.value = value;
                break;
            case "hijos":
                var value = $(obj).val();
                parent.currentFilter.hijos.value = value;
                break;
            case "estado":
                var value = $(obj).val();
                parent.currentFilter.estado.value = value;
                break;
            case "turno":
                var value = $(obj).val();
                parent.currentFilter.turno.value = value;
                break;
            case "perfil1":  // Administrativo
                var value = $(obj).is(":checked");
                if(value){
                    parent.currentFilter.perfil1.value = 1;
                }else{
                    parent.currentFilter.perfil1.value = 0;
                }
                break;
            case "perfil2":  // Comercial
                var value = $(obj).is(":checked");
                if(value){
                    parent.currentFilter.perfil2.value = 1;
                }else{
                    parent.currentFilter.perfil2.value = 0;
                }
                break;
            case "perfil3":  // Operativo
                var value = $(obj).is(":checked");
                if(value){
                    parent.currentFilter.perfil3.value = 1;
                }else{
                    parent.currentFilter.perfil3.value = 0;
                }
                break;
            case "perfil4":  // Servicio al cliente
                var value = $(obj).is(":checked");
                if(value){
                    parent.currentFilter.perfil4.value = 1;
                }else{
                    parent.currentFilter.perfil4.value = 0;
                }
                break;
        }
        if(this.loaded){
            $("#select-filter-input").val("0");
            this.loaded = false;
        }
    };
    
    this.load = function(){
        var parent  = this;
        var request = "getallnames";
        $.post(this.controller+request).done(function(response){
            $('#select-filter-input').find('option').remove();
            $('#select-filter-input').append($("<option></option>").attr("value",0).text("Selecciona una búsqueda")); 
            var resp = jQuery.parseJSON(response);
            resp.forEach(function(element){
                $('#select-filter-input').append($("<option></option>").attr("value",element.id).text(element.name));                 
            });
            var _url = window.location.pathname;
            var elements = _url.split('/');
            if(elements.length == 5){
                var id = elements[4];
                if(id!=""){
                    _filtros.loadPassedFilter(id);
                }
            }
                       
        });
    };
    
    this.apply = function(){
        var save = $("#check-save").is(":checked");
        if(save){
            var name = $("#name-filter").val();
            if(name === ""){
                alert("El nombre del filtro no puede estar vacío.");
            }else{
                // Saving filter
                this.refreshFilterList();
                var parent = this;
                var request = "/module-engine/filters/add";

                var data = {
                    name: name,
                    entity: "Candidatos",
                    filters: this.currentFilters,
                    mask:"",
                    owner:$("#bus_owner_id").val()
                };
                $.post(request,data).done(function(response){
                    var resp = jQuery.parseJSON(response);
                    if(resp.RESPONSE === "OK"){
                        $('#select-filter-input').append($("<option></option>").attr("value",resp.ID).text(name)); 
                        $('#select-filter-input').val(resp.ID);
                        var $url = '/empresa/candidatos/filtered/page/1';
                        var search = $("#candidate-search").val();
                        $tab = tabpanel.getActiveTab();
                        filters = parent.currentFilter;
                        $candidates.get($tab.id, $url, search, filters);
                        $("#check-save").trigger("click");
                    }            
                });
            }
        }else{
            var $url = '/empresa/candidatos/filtered/page/1';
            var search = $("#candidate-search").val();
            $tab = tabpanel.getActiveTab();
            filters = this.currentFilter;
            $candidates.get($tab.id, $url, search, filters);
        }
        switchFilterWindow();
    };
    
    this.applyPassed = function(){
        var $url = '/empresa/candidatos/filtered/page/1';
        var search = $("#candidate-search").val();
        $tab = tabpanel.getActiveTab();
        filters = this.currentFilter;
        $candidates.get($tab.id, $url, search, filters);
    };
    
    this.refresh = function(){
        var $url = '/empresa/candidatos/filtered/page/1';
        var search = $("#candidate-search").val();
        $tab = tabpanel.getActiveTab();
        filters = this.currentFilter;
        $candidates.get($tab.id, $url, search, filters);
    };
    
    
    this.loadPassedFilter = function(id){
       $('#select-filter-input').val(id);
       _filtros.loadSelectedFilter();
       window.setTimeout(this.checkPendingRequest, 550);       
    };
    
    this.checkPendingRequest = function(){
        if($.active === 0){
           _filtros.applyPassed();
       }else{
           window.setTimeout(this.checkPendingRequest, 550);
       }
    };
        
    
    this.loadSelectedFilter = function(){       
       var id = $("#select-filter-input").val();
       if(id==="0")return false;
       var parent = this;
       
       // Clear inputs
        $("#lic_default").trigger("click");
        $("#est_default").val("0");
        $("#est_default").trigger("change");
        $("#del_default").val("0");
        $("#del_default").trigger("change");
        $("#gen_default").trigger("click");
        $("#_mayor_a").val("");
        $("#_mayor_a").trigger("change");
        $("#_menor_a").val("");
        $("#_menor_a").trigger("change");
        $("#hij_default").val("-1");
        $("#hij_default").trigger("change");
        $("#estado_default").val("0");
        $("#estado_default").trigger("change");
        $("#turno_default").val("0");
        $("#turno_default").trigger("change");
        $("#check-perfil4").prop('checked', false);
        $("#check-perfil4").trigger("change");
        $("#check-perfil3").prop('checked', false);
        $("#check-perfil3").trigger("change");
        $("#check-perfil2").prop('checked', false);
        $("#check-perfil2").trigger("change");
        $("#check-perfil1").prop('checked', false);
        $("#check-perfil1").trigger("change");
        
        $("#select-filter-input").val(id);
            
       $.post("/module-engine/filters/entity/"+id,function(response){           
    
          var resp = jQuery.parseJSON(response);
          var filters_ = jQuery.parseJSON(resp.json_structure);
          
          if(filters_ == null){
             return false;
          }
          
          filters_.forEach(function(element){

                switch(element.id){
                    // edad mayor
                    case parent.currentFilter.edad_mayor.id: 
                        $("#_mayor_a").val(element.values.v1); 
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.edad_mayor.value = element.values.v1;
                        break;
                    // edad menor
                    case parent.currentFilter.edad_menor.id: 
                        $("#_menor_a").val(element.values.v1);
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.edad_menor.value = element.values.v1;
                        break;
                    // genero
                    case parent.currentFilter.genero.id:
                        $('input[name="genero-input"][value="'+element.values.v1+'"]').trigger("click");
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.genero.value = element.values.v1;
                        break;
                    // delegacion
                    case parent.currentFilter.delegacion.id: 
                        $("#del_default").val(element.values.v1); 
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.delegacion.value = element.values.v1;
                        break;
                    // licencia
                    case parent.currentFilter.licencia.id:
                        $('input[name="licencia-input"][value="'+element.values.v1+'"]').trigger("click");
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.licencia.value = element.values.v1;
                        break;
                    // estudios
                    case parent.currentFilter.estudios.id: 
                        $("#est_default").val(element.values.v1); 
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.estudios.value = element.values.v1;
                        break;
                    // concluidos
                    case parent.currentFilter.concluidos.id:
                        $('input[name="concluidos-input"][value="'+element.values.v1+'"]').trigger("click");
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.concluidos.value = element.values.v1;
                        break;
                    // hijos
                    case parent.currentFilter.hijos.id: 
                        $("#hij_default").val(element.values.v1); 
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.hijos.value = element.values.v1;
                        break;
                    // estado civil
                    case parent.currentFilter.estado.id: 
                        $("#estado_default").val(element.values.v1); 
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.estado.value = element.values.v1;
                        break;
                    // turno deseado
                    case parent.currentFilter.turno.id: 
                        $("#turno_default").val(element.values.v1); 
                        //_filters.currentFilters.push(element);
                        parent.currentFilter.turno.value = element.values.v1;
                        break;
                    // perfil administrativo
                    case parent.currentFilter.perfil1.id:
                        if(element.values.v1 === "1" || element.values.v1 === 1){
                            $("#check-perfil1").prop('checked', true);
                        }else{
                            $("#check-perfil1").prop('checked', false);
                        }
                        parent.currentFilter.perfil1.value = element.values.v1;
                        break;
                    // perfil comercial
                    case parent.currentFilter.perfil2.id:
                        if(element.values.v1 === "1" || element.values.v1 === 1){
                            $("#check-perfil2").prop('checked', true);
                        }else{
                            $("#check-perfil2").prop('checked', false);
                        }
                        parent.currentFilter.perfil2.value = element.values.v1;
                        break;
                    // perfil operativo
                    case parent.currentFilter.perfil3.id:
                        if(element.values.v1 === "1" || element.values.v1 === 1){
                            $("#check-perfil3").prop('checked', true);
                        }else{
                            $("#check-perfil3").prop('checked', false);
                        }
                        parent.currentFilter.perfil3.value = element.values.v1;
                        break;
                    // perfil servicio al cliente
                    case parent.currentFilter.perfil4.id:
                        if(element.values.v1 === "1" || element.values.v1 === 1){
                            $("#check-perfil4").prop('checked', true);
                        }else{
                            $("#check-perfil4").prop('checked', false);
                        }
                        parent.currentFilter.perfil4.value = element.values.v1;
                        break;                    
                }
          });
          parent.loaded = true;
       });
       
       this.refreshFilterList();
    };
    
    this.refreshFilterList = function(){
        this.currentFilters = [];
        
        // Edad mayor
        if(this.currentFilter.edad_mayor.value!=""){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.edad_mayor.id,values:{v1:this.currentFilter.edad_mayor.value}};
            this.currentFilters.push(_filter);
        }
        
        // Edad menor
        if(this.currentFilter.edad_menor.value!=""){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.edad_menor.id,values:{v1:this.currentFilter.edad_menor.value}};
            this.currentFilters.push(_filter);
        }
        
        // genero
        if(this.currentFilter.genero.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.genero.id,values:{v1:this.currentFilter.genero.value}};
            this.currentFilters.push(_filter);
        }
        
        // delegacion
        if(this.currentFilter.delegacion.value!=0){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.delegacion.id,values:{v1:this.currentFilter.delegacion.value}};
            this.currentFilters.push(_filter);
        }
        
        // licencia
        if(this.currentFilter.licencia.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.licencia.id,values:{v1:this.currentFilter.licencia.value}};
            this.currentFilters.push(_filter);
        }
        
        // estudios
        if(this.currentFilter.estudios.value!=0){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.estudios.id,values:{v1:this.currentFilter.estudios.value}};
            this.currentFilters.push(_filter);
        }

        // concluidos
        if(this.currentFilter.concluidos.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.concluidos.id,values:{v1:this.currentFilter.concluidos.value}};
            this.currentFilters.push(_filter);
        }
        
        // hijos
        if(this.currentFilter.hijos.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.hijos.id,values:{v1:this.currentFilter.hijos.value}};
            this.currentFilters.push(_filter);
        }
        
        // estado civil
        if(this.currentFilter.estado.value!=0){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.estado.id,values:{v1:this.currentFilter.estado.value}};
            this.currentFilters.push(_filter);
        }
        
        // turno deseado
        if(this.currentFilter.turno.value!=0){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.turno.id,values:{v1:this.currentFilter.turno.value}};
            this.currentFilters.push(_filter);
        }
        
        // Perfil Administrativo
        if(this.currentFilter.perfil1.value === 1 || this.currentFilter.perfil1.value === "1"){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.perfil1.id,values:{v1:this.currentFilter.perfil1.value}};
            this.currentFilters.push(_filter);
        }
        
        // Perfil Comercial
        if(this.currentFilter.perfil2.value === 1 || this.currentFilter.perfil2.value === "1"){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.perfil2.id,values:{v1:this.currentFilter.perfil2.value}};
            this.currentFilters.push(_filter);
        }
        
        // Perfil Operativo
        if(this.currentFilter.perfil3.value === 1 || this.currentFilter.perfil3.value === "1"){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.perfil3.id,values:{v1:this.currentFilter.perfil3.value}};
            this.currentFilters.push(_filter);
        }
        
        // Perfil Servicio al cliente
        if(this.currentFilter.perfil4.value === 1 || this.currentFilter.perfil4.value === "1"){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.perfil4.id,values:{v1:this.currentFilter.perfil4.value}};
            this.currentFilters.push(_filter);
        }
    };
        
    this.load();
};
	
var tabpanel;
var _filtros;

var $_candidates;

$(document).ready(function(e) {

	var $notesManager = new candidateNotes({
		_target: '#candidate-tabs #notes-full-profile #txt',
		_content: '#candidate-tabs #notes-full-profile #container_notes',
		_get:{
			_url: '/empresa/candidatos/getNotes',
			_onDone: function( data ){
				console.log( 'load-notes' );
			}
		},
		_save:{
			_url: '/empresa/candidatos/addNotes',
			_onDone: function( data ){
				console.log( data );
			}
		}
	});

	var $optionsFileUploadFP = {
		url: '/web/agendar/uploadDocuments',
		dataType: 'json',	
		acceptFileTypes: /(\.|\/)(pdf|doc|docx|jpeg|jpg|png|gif|bmp)$/i,	
		maxFileSize: 2000000, // 2 MB			
		processalways:	function (e, data) {
			if(data.files.error){
				var $message;							
				switch(data.files[data.index].error){
					case 'File type not allowed':
						$message = 'Los tipos de archivos permitidos son: *.pdf, *.doc, *.docx, *.jpg, *.png, *.gif, *.bmp';
					break;
					case 'File is too large':
						$message = 'El tamaño del archivo debe ser menor a 2MB';
					break;
				}							
				
				$(e.target).parent().popover({content:$message, placement: 'left', title: 'Error'});
				$(e.target).parent().popover('show');
				window.setTimeout(function(){
						$(e.target).parent().popover('destroy');
					},
					3000
				);	
			}else{
				systemStartWorks();
			}
		},		
		done: function (e, data) {					
			uploadResponse({
				_data: data.result,
				_element:  $(e.target), 
				_document_name: $('#candidate-tabs #nameNewDocument').val(), 
				_id_candidate: $("#candidate-tabs").attr('candidate-id'), 
				_onDone: function(){
					$('#candidate-tabs #nameNewDocument').val('');	
					$('#candidate-tabs .fileinput-button').addClass('disabled')			
					getDocuments( 
						$("#candidate-tabs").attr('candidate-id'), 
						function( data ){
							$('#candidate-tabs #listDocuments').html(data);
						}
					);
				}
			});
		}
	};

	$('#tab').on('mouseover', '.show-interviews-tooltip', function(){		
		if($(this).attr('loaded-tooltip') == undefined){
			$(this).attr('loaded-tooltip', true);
			$candidates.getInterviewsDates( $(this).attr('candidate-id'),  $(this));
		}
	});
	
	$('#tab').on('mouseover', '.rec-picture', function(){		
		if($(this).attr('loaded-tooltip') == undefined){
			$(this).attr('loaded-tooltip', true);			
			$(this).tooltip('show');
		}
	});

	$('#tab').on('click', '.publish-vacancy-btn', function(){
		console.log($(this).attr('vacancy-id'));
		console.log($(this).parent().find('input[type="checkbox"]').prop('checked'));
		var $id_vacancy =  $(this).attr('vacancy-id');
		var $status = $(this).parent().find('input[type="checkbox"]').prop('checked');

		$candidates.publishVacancy( $id_vacancy, $status );
	});
	
	$('#tab').on('click', '#closePopover', function(){
		$('.popover').remove();
	});
	
    $("#fired-date").datepicker({
        dateFormat : 'dd/mm/yy',
        defaultDate: new Date()
    });
    var today = $.datepicker.formatDate('dd/mm/yy', new Date());
    $("#fired-date").val(today);
    
	var $candidates;    	
	
	tabpanel = new TabPanel({  
        renderTo:'tab',  
        width:'100%',        
		items : [
			{
				id:"vacanciesManager",
				title:"Vacantes",
				html:'',
				closable: false				
			},
			{
				id:"interviewsCandidates",
				title:"Agendar",
				html:'',
				closable: false				
			},
			{
				id:"hiredCandidates",
				title:"Contratados",
				html:'',
				closable: false				
			},
			{
				id:"talentDatabase",
				title:"Buscar Candidatos",
				html:'',
				closable: false				
			},
			{
				id:"messagesCompany",
				title:"Mensajes",
				html:'',
				closable: false				
			}
		]          
    });			
	
	$candidates = new candidates({
		conteiner:tabpanel,
		url: '/empresa/candidatos/filtered/page/1'
	});

	$_candidates = $candidates;
	
	if(location.hash == '#equipo'){
		$candidates.companyTeamTab( tabpanel, $('#companyTeam').attr('href') );
		location.hash = '';
	}
	
        var _url = window.location.pathname;
        var elements = _url.split('/');
        if(elements.length != 5){
            $candidates.get('talentDatabase');
        }else{
            var id = elements[4];
            if(id===""){
                $candidates.get('talentDatabase');
            }
        }				            
	
	$candidates.get('vacanciesManager', '/empresa/vacancies/index/page/1');
	$candidates.get('hiredCandidates');
	$candidates.get('interviewsCandidates', '/empresa/agendar');
	$candidates.get('messagesCompany', '/candidato/cuenta/messages_company');
	
	$('#tab').on('click', '.pagination a', function(event){
		var $url = $(this).attr('href');
		$tab = tabpanel.getActiveTab();
                var search = $("#candidate-search").val();
                console.log(_filtros);
		$candidates.get($tab.id, $url, search, _filtros.currentFilter);
		event.preventDefault();
	});
	
	$('#tab').on('click', '.details', function(event){
		$candidates.details(tabpanel, $(this).attr('candidate-id'), $(this).attr('candidate-name'));		
	});
	
	$('#tab').on('click', '.hire', function(event){
		$('#modalHire').modal();
		$('#msgHire').hide();
		$('#modalHire').attr('id-candidates', $(this).attr('candidate-id'));		
		event.preventDefault();
	});
	
	$('#tab').on('click', '.nohire', function(event){
		$('#modalNoHire').modal();
		$('#msgNoHire').hide();
		$('#modalNoHire').attr('id-candidates', $(this).attr('candidate-id'));		
		event.preventDefault();
	});
	
	$('#tab').on('click', '.test', function(event){
		$('#modalTest').modal();
		$('#msgTest').hide();
		$('#modalTest').attr('id-candidates', $(this).attr('candidate-id'));		
		event.preventDefault();
	});
	
	
	
	$('#tab').on('click', '.discard', function(event){
		$('#modalDiscard').modal();
		$('#msgDiscard').hide();
		$('#modalDiscard').attr('id-candidates', $(this).attr('candidate-id'));
		event.preventDefault();
	});
	
	$('#tab').on('click', '.contact', function(event){		
		$candidates.contact(tabpanel, $(this).attr('candidate-id'), $(this).attr('candidate-name'));		
		event.preventDefault();
	});
	
	$('#tab').on('click', '#sendMsg', function(event){		
		$candidates.sendMsg($(this).attr('candidate-id') );
		event.preventDefault();
	})
	
	$('#tab').on('click', '.check_all', function(){		
		var $status = $(this).prop('checked');		
		var $section = $(this).parents('section.content');
					
		$($section).find('input[type="checkbox"]').each(function(index, element) {
            if($(this).prop('checked', $status));			
        });
		if($status){
			$($section).find('.hire_selected').show();
			$($section).find('.nohire_selected').show();
			$($section).find('.test_selected').show();
			$($section).find('.discard_selected').show();
			$($section).find('.dismiss_selected').show();
		}else{
			$($section).find('.hire_selected').hide();
			$($section).find('.nohire_selected').hide();
			$($section).find('.test_selected').hide();
			$($section).find('.discard_selected').hide();
			$($section).find('.dismiss_selected').hide();
		}
	});
	
	$('#tab').on('click', 'table input[type="checkbox"]', function(){
		var $section = $(this).parents('section.content');
		$($section).find('.check_all').prop('checked', false);
		$checked_candidates = $candidates.getSelected( $($section) );	
		if($checked_candidates.length > 0){
			$($section).find('.hire_selected').show();
			$($section).find('.discard_selected').show();
			$($section).find('.test_selected').show();
			$($section).find('.dismiss_selected').show();
		}else{
			$($section).find('.hire_selected').hide();
			$($section).find('.test_selected').hide();
			$($section).find('.discard_selected').hide();
			$($section).find('.dismiss_selected').hide();			
		}
	});
	
	$('#tab').on('click', '.discard_selected', function(event){
		$('#modalDiscard').modal();
		$('#msgDiscard').hide();		
		var $section = $(this).parents('section.content');
		$checked_candidates = $candidates.getSelected( $($section) );		
		$('#modalDiscard').attr('id-candidates', $checked_candidates);		
		event.preventDefault();
	});
	
	$('#tab').on('click', '.hire_selected', function(event){
		$('#modalHire').modal();
		$('#msgHire').hide();
		var $section = $(this).parents('section.content');
		$checked_candidates = $candidates.getSelected( $($section) );		
		$('#modalHire').attr('id-candidates', $checked_candidates);		
		event.preventDefault();
	});
	
	$('#tab').on('click', '.nohire_selected', function(event){
		$('#modalNoHire').modal();
		$('#msgNoHire').hide();
		var $section = $(this).parents('section.content');
		$checked_candidates = $candidates.getSelected( $($section) );		
		$('#modalNoHire').attr('id-candidates', $checked_candidates);		
		event.preventDefault();
	});
	
	$('#tab').on('click', '.test_selected', function(event){
		$('#modalTest').modal();
		$('#msgTest').hide();
		var $section = $(this).parents('section.content');
		$checked_candidates = $candidates.getSelected( $($section) );		
		$('#modalTest').attr('id-candidates', $checked_candidates);		
		event.preventDefault();
	});	
			
	$('#btnHire').on('click', function(){
		$candidates.hire();				
	});
	
	$('#btnTest').on('click', function(){
		$candidates.test();				
	});
	
	$('#btnDiscard').on('click', function(){		
		$candidates.discard();
	});
	
	$('#tab').on('click', '.dismiss_selected', function(event){
		$('#modalDismiss').modal();		
		$('#msgDismiss').hide();
		var $section = $(this).parents('section.content');
		$checked_candidates = $candidates.getSelected( $($section) );		
		$('#modalDismiss').attr('id-candidates', $checked_candidates);		
		event.preventDefault();
	});
	
	$('#tab').on('click', '.dismiss', function(event){
		$('#modalDismiss').modal();
		$('#msgDismiss').hide();
		$('#modalDismiss').attr('id-candidates', $(this).attr('candidate-id'));
		event.preventDefault();
	});
	
	$('#btnDismiss').on('click', function(){		
		$candidates.dismiss();
	});
	
	
	$('#btnNoHire').on('click', function(){
		$candidates.noHire();
	});
	
	
	$('#tab').on('click', '.no-action', function(event){
		$('#modalNoAction').modal();		
		event.preventDefault();
	});
	
	$('.panel-heading').on('keyup', '.input-filter-number', function(){
		if(isNaN($(this).val())){
			$(this).val('');	
		}
	});	
	
        var timer;

        $("#candidate-search").keyup(function () {
            clearTimeout(timer);
            timer = setTimeout(function(){
                var $url = '/empresa/candidatos/filtered/page/1';
                var search = $("#candidate-search").val();
				$tab = tabpanel.getActiveTab();
                searching = true;
                $('#search-icon-input').removeClass("fa-search");
                $("#search-icon-input").addClass("fa-gear fa-spin");
				if( $tab.id == 'vacanciesManager'){
					$url = '/empresa/vacancies/index/page/1';
				}
				$candidates.get($tab.id, $url, search);
            }, 800 );
        });         
        
        $("#check-save").change(function(){
            var value = $("#check-save").is(":checked");
            if(value){
                $(".check-inside-filter span").html("Guardar");
                $("#name-filter").show();
            }else{
                $(".check-inside-filter span").html("Guardar filtro");
                $("#name-filter").hide();
            }
        });
        
        _filtros = new filters( $candidates );
		
	var $searchBy = new Array();
	var $titleTab = new Array();	
	
	$('#candidate-search').on('keyup', function(){
		$tab = tabpanel.getActiveTab();
		$searchBy[$tab.id] = $(this).val();
	});			
	
	
	var $activeTab = 'talentDatabase';
	(activeTab = function(){
		$tab = tabpanel.getActiveTab();
		if($tab.id != $activeTab){
			var $txt = $searchBy[$tab.id] == undefined ? '' : $searchBy[$tab.id];
			var $title = tabpanel.getTitle($tab.id)
			$('#candidate-search').val( $txt );
			$('.panel-heading .header-title div').html( $title );
			$('.panel-heading div.tab-title').html( $title );
			if($tab.id == 'talentDatabase' || $tab.id == 'inviteCandidates' || $tab.id == 'hiredCandidates' || $tab.id == 'testCandidates' || $tab.id == 'vacanciesManager'){
				$('.panel-heading .filter-controller').show();
				$('.panel-heading .header-title').hide();				
				if($tab.id == 'talentDatabase'){
					$('#candidate-filters').removeClass('disabled');	
				}else{
					$('#candidate-filters').addClass('disabled');	
				}
			}else{						
				$('.panel-heading .filter-controller').hide();
				$('.panel-heading .header-title').show();
			}
			if($tab.id == 'interviewsCandidates'){
				$('.panel-heading .header-title div').html( 'Entrevistas' );
			}
			
			$activeTab = $tab.id;
		}	
		window.setTimeout(activeTab,250);
	})();
	
	$('#tab').on('click', '.sendProfile', function(){	
		$("#sendProfileForm").validate({
			rules:{
				'email-from':{
					required: true,
					email: true			
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
		if($('#sendProfileForm').valid()){
			$candidates.sendProfile($('#emailFrom').val(), $('#profileFromEmail').attr('id-candidate'));
		}
		
	});
	
	$('#tab').on('click', '.profiling_graph', function(){
		$candidates.showProfilingGraph($(this));
	});


	$('#tab').on('click', '#candidate-tabs #notesTab', function(event){	
		$notesManager.get( $('#candidate-tabs').attr('candidate-id') );
	});

	$('#tab').on('click', '#candidate-tabs #addNote', function(event){	
		$notesManager.add( $('#candidate-tabs').attr('candidate-id') );
	});

	$('#tab').on('click', '#candidate-tabs #documentsTab', function(event){
		$('#candidate-tabs .fileupload').fileupload($optionsFileUploadFP).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');		
		event.preventDefault();
		getDocuments( 
			$('#candidate-tabs').attr('candidate-id'), 
			function( data ){
				$('#candidate-tabs #listDocuments').html(data);
			}
		);
	});

	$('#tab').on('click', '#candidate-tabs .deleteDocument', function(event){	
		deleteDocument(
			$(this).attr('id-document'),		
			function( data ){
				getDocuments( 
					$('#candidate-tabs').attr('candidate-id'), 
					function( data ){
						$('#candidate-tabs #listDocuments').html(data);
					}
				);
			}
		);
	});

	$('#tab').on('keyup', '#candidate-tabs #nameNewDocument', function(event) {
		$(this).val() == '' ? $('#candidate-tabs .fileinput-button').addClass('disabled') : $('#candidate-tabs .fileinput-button').removeClass('disabled');
	});


	$('#tab').on('click', '#newVacancy', function(){
		$candidates.vacancyManager();		
	});

	$('#tab').on('click', '#saveVacancy', function(){		
		$candidates.vacancySave();
	});

	$('#tab').on('click', '#vacanciesTable .vacancy-btn', function(){
		var $vacancy_id = $(this).attr('vacancy-id');
		var $name = 'Editar '+$(this).attr('vacancy-name'); 
		$candidates.vacancyManager( $vacancy_id, $name );		
	});
	
	$('#tab').on('click', '#vacanciesTable .position-name', function(){
		var $revisedStatus = $(this).parents('tr').find('td.revised-status span');
		var $revised = $revisedStatus.hasClass('vacancy-no-revised');
		if( $revised ){		
			$revisedStatus.removeClass('vacancy-no-revised').addClass('vacancy-revised');
		}
		var $vacancy_id = $(this).attr('vacancy-id');
		var $name = 'Detalles de '+$(this).attr('vacancy-name'); 
		$candidates.vacancyView( $vacancy_id, $name, $revised );		
	});	


	$('#tab').on('click', '#vacancyForm .delete-document', function(){
		console.log($(this).attr('id-document'));
		$candidates.deleteDocumentVacancy( $(this).attr('id-document') );
	});


	$('#tab').on('change', '#vacancyForm #id_country', function(){
		$('#id_state').attr('disabled', true);
		$('#id_state').html('');
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
		

	});
	
	$('#tab').on('click', '#vacancyForm #updateGoogleMap', function(){									
		$('#zip_code').valid();
		$('#street').valid();
		$('#number').valid();
		$candidates.getMap();
	});


	$('#tab').on('change', '#vacancyForm #id_state', function(){
		$('#id_city').attr('disabled', true);
		$('#id_city').html('');
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
		

	});
	
	$('#tab').on('change', '#marker_ok', function(){
		console.log( $(this).prop('checked') );
	});
	
	$('#companyTeam').on('click', function(event){
		$(this).parent().prev().removeClass("open");
		$candidates.companyTeamTab( tabpanel, $(this).attr('href') )
		event.preventDefault();
	});
	
	
	$('#tab').on('click', '#addUser', function(){
		systemStartWorks();
		closeTabsForms("team_manager", tabpanel);
		$.ajax({
			url: $(this).attr('data-url'),
			type:"POST"
		}).done(function( data ){
			tabpanel.addTab({
				id:"team_manager",
				title:"Nuevo reclutador",
				html: data,
				closable: true				
			});	
			
			$('#teamManager').validate({
				rules:{
					"user[name]":{
                		required: true
            		},
					"user[last_name]":{
						required: true
					},
					"user[email]":{
						required: true,
						email:true
					}					
				},
		    	errorPlacement: function(error, element){		    		
		    		$(element).addClass('error-validation');
		    		var $label = $(element).parent().find('label');
		    		$label.find('span.alert').remove();
		    		$label.append('<span class="alert alert-danger">'+$(error).html()+'</span>');
					return true;
				},
				highlight: function(element, errorClass, validClass) {					
					$(element).focus();
				},
				unhighlight: function(element, errorClass, validClass) {
					$(element).removeClass('error-validation');
					$(element).parent().find('label').find('span.alert').remove();
				}
			});
			
			systemEndWorks();
					
		});
				
	});
	
	$('#tab').on('click', '#saveUser', function(){
		$('#response-saveUser').hide();
		if($('#teamManager').valid()){
			systemStartWorks();			
			$.ajax({
				url: $(this).attr('data-url'),
				type:"POST",
				data: $('#teamManager').serialize(),
				dataType:"json"
			}).done(function( data ){
				if(data.status == 1){
					closeTabsForms("team_manager", tabpanel);
					$candidates.get("company_team", "/empresa/equipo/index/page/1");
				}else{
					$('#response-saveUser').show().html( data.msg );
					systemEndWorks();
				}							
			});			
		}
	});
	
	
	$('#tab').on('click', '.delete-user', function(){
		
		var $uid = $(this).attr('data-uid');
		systemStartWorks();
		$.ajax({
			url:"/empresa/vacancies/getByUser",
			type:"POST",			
			data:{
				uid: $uid
			}
		}).done(function(data){			
			$('#delete-user').attr('data-uid', $uid );
			$('#delete-user .modal-body').html(data);			
			systemEndWorks();
			$('#delete-user').modal('show');
		});				
		
	});
	
	$('#tab').on('click', '#btnDeleteUser', function(){
		systemStartWorks();
		$.ajax({
			url:"/empresa/equipo/disableUser",
			type:"POST",
			data: $('#deleteUserForm').serialize(),
			dataType:"json"
		}).done(function(data){
			if(data.status == 1){				
				$candidates.get("company_team", "/empresa/equipo/index/page/1");
				$('#delete-user').modal('hide');
				systemEndWorks();
			}	
		});		
	});		
	
});


var filterwindow = "CLOSE";

function switchFilterWindow(){
    if(filterwindow === "OPEN"){
        $('#filter-container').hide();
        $('#icon-filtrar').addClass("fa-filter");
        $('#icon-filtrar').removeClass("fa-sort-desc fa-lg");
        filterwindow = "CLOSE";
    }else{
        $('#filter-container').show();
        $('#icon-filtrar').removeClass("fa-filter");
        $('#icon-filtrar').addClass("fa-sort-desc fa-lg");
        filterwindow = "OPEN";
    }
}

function deltag(attribute){
    switch(attribute){
        case "licencia": 
            $("#lic_default").trigger("click");
            $(".tag-licencia").remove();
            _filtros.refresh();
            break;
        case "estudios": 
            $("#est_default").val("0");
            $("#est_default").trigger("change");
            $(".tag-estudios").remove();
            _filtros.refresh();
            break;
        case "concluidos":             
            $("#con_default").trigger("click");
            $(".tag-concluidos").remove();
            _filtros.refresh();
            break;
        case "delegacion":
            $("#del_default").val("0");
            $("#del_default").trigger("change");
            $(".tag-delegacion").remove();
            _filtros.refresh();
            break;
        case "genero": 
            $("#gen_default").trigger("click");
            $(".tag-genero").remove();
            _filtros.refresh();
            break;   
        case "mayor_a":
            $("#_mayor_a").val("");
            $("#_mayor_a").trigger("change");
            $(".tag-edad1").remove();
            _filtros.refresh();
            break;
        case "menor_a":
            $("#_menor_a").val("");
            $("#_menor_a").trigger("change");
            $(".tag-edad2").remove();
            _filtros.refresh();
            break;
        case "hijos":
            $("#hij_default").val("-1");
            $("#hij_default").trigger("change");
            $(".tag-hijos").remove();
            _filtros.refresh();
            break;
        case "estado":
            $("#estado_default").val("0");
            $("#estado_default").trigger("change");
            $(".tag-estado").remove();
            _filtros.refresh();
            break;
        case "turno":
            $("#turno_default").val("0");
            $("#turno_default").trigger("change");
            $(".tag-turno").remove();
            _filtros.refresh();
            break;
        case "perfil1": // Administrativo
            $("#check-perfil1").attr('checked', false);
            $("#check-perfil1").trigger("change");
            $(".tag-perfil1").remove();
            _filtros.refresh();
            break;
        case "perfil2": // Comercial
            $("#check-perfil2").attr('checked', false);
            $("#check-perfil2").trigger("change");
            $(".tag-perfil2").remove();
            _filtros.refresh();
            break;
        case "perfil3": // Operativo
            $("#check-perfil3").attr('checked', false);
            $("#check-perfil3").trigger("change");
            $(".tag-perfil3").remove();
            _filtros.refresh();
            break;
        case "perfil4": // Servicio al cliente
            $("#check-perfil4").attr('checked', false);
            $("#check-perfil4").trigger("change");
            $(".tag-perfil4").remove();
            _filtros.refresh();
            break;
    }   
}

function delAll(){
	$("#candidate-search").val('');	
	$("#candidate-search").trigger('keyup');
    $("#lic_default").trigger("click");
    $(".tag-licencia").remove();
    $("#con_default").trigger("click");
    $(".tag-concluidos").remove();
    $("#est_default").val("0");
    $("#est_default").trigger("change");
    $(".tag-estudios").remove();
    $("#del_default").val("0");
    $("#del_default").trigger("change");
    $(".tag-delegacion").remove();
    $("#gen_default").trigger("click");
    $(".tag-genero").remove();
    $("#_mayor_a").val("");
    $("#_mayor_a").trigger("change");
    $(".tag-edad1").remove();
    $("#_menor_a").val("");
    $("#_menor_a").trigger("change");
    $(".tag-edad2").remove();
    $("#hij_default").val("-1");
    $("#hij_default").trigger("change");
    $(".tag-hijos").remove();
    $("#estado_default").val("0");
    $("#estado_default").trigger("change");
    $(".tag-estado").remove();
    $("#turno_default").val("0");
    $("#turno_default").trigger("change");
    $(".tag-turno").remove();
    $("#check-perfil4").prop('checked', false);
    $("#check-perfil4").trigger("change");
    $(".tag-perfil4").remove();
    $("#check-perfil3").prop('checked', false);
    $("#check-perfil3").trigger("change");
    $(".tag-perfil3").remove();
    $("#check-perfil2").prop('checked', false);
    $("#check-perfil2").trigger("change");
    $(".tag-perfil2").remove();
    $("#check-perfil1").prop('checked', false);
    $("#check-perfil1").trigger("change");
    $(".tag-perfil1").remove();
    _filtros.refresh();
}


candidateNotes = function( $params ){

	this.params = $params;

	this.get = function( $id_candidate ){

		var $params = this.params;

		$.ajax({
			url: $params._get._url,
			type: 'POST',
			data: {
				id_candidate: $id_candidate
			}
		})
		.done(function( data ) {
			$params._get._onDone( data );
			$($params._content).html(data);
		});

	}

	this.add = function( $id_candidate ){

		var $params = this.params;
		var $parent = this;
		var $txt = $($params._target).val();

		if($txt != ''){

			systemStartWorks();
			$.ajax({
				url: $params._save._url,
				type: 'POST',
				dataType: 'json',			
				data: {
					txt: $txt,
					id_candidate: $id_candidate
				}
			})
			.done(function( data ) {
				$params._save._onDone( data );
				if( data.status == 1 ){
					$($params._target).val('');
					$parent.get( $id_candidate );
					systemEndWorks();				
				}
			});		
		}

	}

}


getDocuments = function( $id_candidate, $onDone ){

	$.ajax({
		url: '/empresa/candidatos/getDocuments',
		type: 'POST',		
		data: {id_candidate: $id_candidate},
	})
	.done(function( data ) {
		$onDone( data );
	});
	

}


deleteDocument = function( $id, $onDone ){
	$.ajax({
		url: '/empresa/agendar/deleteDocumentCompany',
		type: 'POST',		
		data: {
			id: $id
		},
	})
	.done(function( data ) {
		$onDone( data );
	});
}

uploadResponse = function( $params ){
		
	if($params._data[0].error == undefined){	
		
		$.ajax({
			url:"/gobierno/agendar/setFidDocument",
			type:"POST",
			dataType:"json",
			data:{
				fid: $params._data[0].fid,
				document_name: $params._document_name,
				id_candidate: $params._id_candidate
			}
		}).done(function(data){
			systemEndWorks();
			if(data.status){
				$params._onDone();
			}else{
				$params._element.parent().popover({content:'Ha ocurido un error favor de intentarlo nuevamente', placement: 'left', title: 'Error'});
				$params._element.parent().popover('show');
				window.setTimeout(function(){
						$params._element.parent().popover('destroy');
					},
					3000
				);
			}			
		});
	}else{
		systemEndWorks();
		$.each($data[0].error, function(index, value){
			$params._element.parent().popover({content:value, placement: 'left', title: 'Error'});	
		});		
		$params._element.parent().popover('show');
		window.setTimeout(function(){
				$params._element.parent().popover('destroy');
			},
			3000
		);
		
	}
	
		
}
