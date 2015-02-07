// JavaScript Document

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

getCatalogSubSectors = function(){
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
				setCatalog(data.catalog, $('#subSectorId'), $('#id_subsector').val());	
			}else{
				console.log(data.msg);
			}
		});	
}

managerCompany = function( $params ){
	
	this.form = $params.form;
	this.save = $params.save;
	this.user = $params.saveCompanyUser;
	
	this.getForm = function( $section ){
		
		$form = this.form;
		systemStartWorks();
		
		$.ajax({
			url: $form.url,
			type:"POST",
			data: {
				section: $section
			}
		}).done(function(data){
			$form.target.html(data);
			$form.init();			
			systemEndWorks();
		});		
		
	}
	
	
	this.saveForm = function(){		
	
		var $save = this.save;
	
		if($($save.target).valid()){						
			
			systemStartWorks();
			$.ajax({
				url: $save.url,
				type:"POST",
				dataType:"json",
				data: $save.data()
			}).done(function(data){
				$save.done( data );
			});
			
		}				
	}
	
	this.saveCompanyUser = function(){
		
		var $save = this.user;				
		if($($save.target).valid()){
			systemStartWorks();			
			$.ajax({
				url: $save.url,
				type:"POST",
				dataType:"json",
				data: $save.data()
			}).done(function(data){
				$save.done( data );
			});
		}
		
	}
	
}


timeSlots = function(){

	this.managerForm =function(){
		$.ajax({
			url: '/web/admin/generalTimeSlots',
			type: 'POST'			
		})
		.done(function(data) {
			$('#slider-wrapper').html(data);
		});	
		
	}


	this.manageSlot = function( $action, $data ){

		var $parent = this;

		systemStartWorks();
		$.ajax({
			url: '/web/admin/manageSlot',
			type: 'POST',
			dataType: 'json',
			data: $data+'&slot[action]='+$action
		})
		.done(function(data) {
			console.log(data);
			$parent.managerForm();
			systemEndWorks();
		});
		
	}

}

$(document).ready(function(e) {
	
	var $zip_code = '';	

	var $timeSlots = new timeSlots();
	
	var $managerCompany = new managerCompany({
		form:{
			url: "/empresa/admin/updateCompanyData",
			target: $('#slider-wrapper'),
			init: function(){
				
				if( $('.edit-company.pink').attr('section') == 'companyCareerSite' ){
					
					var options =
					{
						thumbBox: '.thumbBox',
						spinner: '.spinner',
						imgSrc: ''
					}
					var cropper = $('.imageBox').cropbox(options);
					$('#slider-wrapper #company_picture').on('change', function(){
						var reader = new FileReader();
						reader.onload = function(e) {
							options.imgSrc = e.target.result;
							cropper = $('.imageBox').cropbox(options);
						}
						reader.readAsDataURL(this.files[0]);
						this.files = [];						
					})
					$('#slider-wrapper #btnCrop').on('click', function(){
						var img = cropper.getDataURL();
						$('#company_picture_id').attr('src', img);
						$('#company_picture_base64').val(img);
						$('#cropModal').modal('hide');
					})
					$('#slider-wrapper #btnZoomIn').on('click', function(){
						cropper.zoomIn();
					})
					$('#slider-wrapper #btnZoomOut').on('click', function(){
						cropper.zoomOut();
					});
					
					$('#slider-wrapper #company_picture').fileupload({						
						autoUpload: false,
						acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
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
								$('#cropModal').modal('show');
							}							
						}
					}).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
					
				}
				
				if( $('.edit-company.pink').attr('section') == 'userProfile' ){
					
					$('#companyForm').validate({
						rules:{
							'User[name]':{
								required: true
							},
							'User[last_name]':{
								required: true
							}
						}
					});
					var options =
					{
						thumbBox: '.thumbBox',
						spinner: '.spinner',
						imgSrc: ''
					}
					var cropper = $('.imageBox').cropbox(options);
					$('#slider-wrapper #user_picture').on('change', function(){
						var reader = new FileReader();
						reader.onload = function(e) {
							options.imgSrc = e.target.result;
							cropper = $('.imageBox').cropbox(options);
						}
						reader.readAsDataURL(this.files[0]);
						this.files = [];						
					})
					$('#slider-wrapper #btnCrop').on('click', function(){
						var img = cropper.getDataURL();
						$('#user_picture_id').attr('src', img);
						$('#user_picture_base64').val(img);
						$('#myModal').modal('hide');
					})
					$('#slider-wrapper #btnZoomIn').on('click', function(){
						cropper.zoomIn();
					})
					$('#slider-wrapper #btnZoomOut').on('click', function(){
						cropper.zoomOut();
					});
					
					$('#slider-wrapper #user_picture').fileupload({
						url: '/web/admin/saveCompanyUser',
						dataType: 'json',
						autoUpload: false,
						acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
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
								$('#myModal').modal('show');
							}							
						}
					}).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
				}
				
				if($('#sectorId').length){
					$('#sectorId').val($('#id_sector').val());
					if($('#id_sector').val() > 0){
						getCatalogSubSectors();
					}
				}
				
				if($('#zip_code').length){
					$cp = $('#zip_code').val();
					if($cp.length == 5){
						getCatalog( $cp, $('#id_delegation').val(), $('#id_colony').val() );	
					}
				}
				
				$('#tax_regime').length ? $('#tax_regime').val($('#tax_regime_id').val()) : false;												
				$('#tax_regime').length ? $('#tax_regime').val($('#tax_regime_id').val()) : false;
				$('#gender').length ? $('#gender').val($('#gender_id').val()) : false;
				
				
				$('#companyForm').validate({
					rules:{
						"company[name]":{
							required: true
						},
						"company[zip_code]":{
							required: true,
							number: true,
							minlength: 5						
						}
					}
				});
												
			}
		},
		save:{
			url: "/web/admin/saveCompany",
			target: '#companyForm',
			data: function(){
				var $data = $('#companyForm').serialize();				
				return $data;
			},
			done: function( data ){
				$('#slider-wrapper').html();
				var $msg = $('<div/>');
				$msg.addClass('alert');
				$msg.html(data.msg);			
				if(data.status == 1){				
					$msg.addClass('alert-success');		
				}else{					
					$msg.addClass('alert-danger');							
				}
				$('#slider-wrapper').html($msg);
				systemEndWorks();
			}
		},
		saveCompanyUser:{
			url: "/web/admin/saveCompanyUser",
			target: '#companyForm',
			data: function(){
				var $data = $('#companyForm').serialize();				
				return $data;
			},
			done: function( data ){
				$('#slider-wrapper').find('div.alert').remove();
				console.log( data );
				var $msg = $('<div/>');
				$msg.addClass('alert');
				$msg.html(data.msg);			
				if(data.status == 1){				
					$msg.addClass('alert-success');		
				}else{					
					$msg.addClass('alert-danger');							
				}
				$('#slider-wrapper').append($msg);
				systemEndWorks();
			}
		}
	});
	
	$.validator.messages.equalTo = "Las contraseñas no coinsiden";
	
	$('#changePass').validate({
		rules:{
			"password[confirm_new]":{
				required: true,
				equalTo:"#newPassword"
			}
		}
	});
	
	$('#changePassBtn').on('click', function(){
		if($('#changePass').valid()){
			
			systemStartWorks();
			$.ajax({
				url:"",
				type:"POST",
				dataType:"json",
				data: $('#changePass').serialize()
			}).done(function(data){
				$('#msgUpdatePass').find('div.alert').remove();
				var $msg = $('<div/>');
				$msg.addClass('alert');
				$msg.html(data.msg);			
				if(data.status == 1){				
					$msg.addClass('alert-success');
				}else{				
					$msg.addClass('alert-danger');											
				}
				$('#msgUpdatePass').append($msg);
				systemEndWorks();
			});
		}
	});
	
	$('.change-pass').on('click', function(){
		systemStartWorks();	
	});
	
	$('.edit-company').on('click', function(){
		$('#conf-menu-wrapper h5').each(function(index, element) {
			$(element).removeClass('pink');
		});
		$(this).addClass('pink');
		$managerCompany.getForm( $(this).attr('section') );
	});
	
	$('#slider-wrapper').on('change', '#sectorId', function(){
		getCatalogSubSectors();	
	});
	
	$('#slider-wrapper').on('keyup', '#zip_code', function(){		
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
	
	$('#slider-wrapper').on('click', '#updateDataCompany', function(){
		$managerCompany.saveForm();
	});
	
	
	$('#slider-wrapper').on('keyup', '.number', function(){
		if(isNaN($(this).val())){
			$(this).val('');	
		}
	});


	$('.manager-time-slots').on('click', function(){
		$('#conf-menu-wrapper h5').each(function(index, element) {
			$(element).removeClass('pink');
		});
		$(this).addClass('pink');
		$timeSlots.managerForm();
	});

	$('#slider-wrapper').on('click', '#addTimeSlot', function(event) {
		$timeSlots.manageSlot('add', $('#timeSlotsForm').serialize());		
	});

	$('#slider-wrapper').on('click', '.deleteTimeSlot', function(event) {
		$timeSlots.manageSlot('delete', 'slot[id]='+$(this).attr('id-time-slot'));		
	});
	
	
	$('#slider-wrapper').on('click', '#saveCompanyUser', function(event){
		$managerCompany.saveCompanyUser();	
	});
	
});
