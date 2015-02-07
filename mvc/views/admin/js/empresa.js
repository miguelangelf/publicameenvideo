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

Company = function(){
	
	this.form = function( $id ){
		
		var $id_company = ( $id == undefined ) ? '' : $id;
		
		$.ajax({
			url:"/web/admin/form_company",
			type:"POST",
			data:{
				id_company: $id_company
			}
		}).done(function(data){
			$('#form_company').html(data);
			$cp = $('#zip_code').val();
			$('#tax_regime').val($('#tax_regime_id').val());			
			$('#gender').val($('#gender_id').val());
			$('#sectorId').val($('#id_sector').val());
			if($('#id_sector').val() > 0){
				getCatalogSubSectors();
			}
			
			if($cp.length == 5){
				getCatalog( $cp, $('#id_delegation').val(), $('#id_colony').val() );	
			}
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
		});
		
	}
	
	this.get = function( $url, $search, $status ){
		
		$url = ($url == undefined || $url == null) ? "/web/admin/list_companies/page/1" :$url;
		
		systemStartWorks();
		$.ajax({
			url: $url,
			type:"POST",
			data:{
				_search: $search,
				_status: $status	
			}
		}).done(function(data){
			systemEndWorks();
			$('#busquedas-results').html(data);
			$('.company-status-switch').bootstrapSwitch();
		});
		
	}
	
	this.save = function(){		
		if($('#companyForm').valid()){
			
			var $parent = this;
			
			systemStartWorks();
			$.ajax({
				url:"/web/admin/saveCompany",
				type:"POST",
				dataType:"json",
				data: $('#companyForm').serialize()
			}).done(function(data){
				$('#form_company').find('div.alert').remove();
				var $msg = $('<div/>');
				$msg.addClass('alert');
				$msg.html(data.msg);			
				if(data.status == 1){				
					$msg.addClass('alert-success');		
					$('#form_company').html($msg);		
					$parent.get();
				}else{
					systemEndWorks();
					$msg.addClass('alert-danger');						
					$('#form_company').append($msg);	
				}				
			});
			
		}				
	}
	
	
	this.disable = function( $id_company ){
		
		var $parent = this;
		
		systemStartWorks();
		$.ajax({
			url: "/web/admin/disableCompany",
			dataType:"json",
			type:"POST",
			data:{
				id_company: $id_company
			}
		}).done(function(data){
			$('#form_company').find('div.alert').remove();
			var $msg = $('<div/>');
			$msg.addClass('alert');
			$msg.html(data.msg);			
			if(data.status == 1){				
				$msg.addClass('alert-success');		
				$('#form_company').html($msg);		
				$parent.get();
			}else{
				systemEndWorks();
				$msg.addClass('alert-danger');						
				$('#form_company').append($msg);	
			}	
		});

	}
	
	this.setStatus = function( $id_company, $status ){
		
		systemStartWorks();
		$.ajax({
			url:"/web/admin/statusCompany",
			data:{
				id_company: $id_company,
				status: ($status) ? 1 : 0
			},
			type:"POST",
			dataType:"json"
		}).done(function(data){
			if(data.status == 1){
				systemEndWorks();
			}
		});
	}


	this.getUsers = function( $id_company ){
		systemStartWorks();
		$.ajax({
			url:"/web/admin/companyUsers",
			data:{
				id_company: $id_company				
			},
			type:"POST"			
		}).done(function(data){
			$('#form_company').html(data);
			systemEndWorks();	
		});
	}
	
	
	this.settingsForm = function( $id_company ){
		systemStartWorks();
		$.ajax({
			url:"/web/admin/companySettingsForm",
			type:"POST",
			data:{
				id_company: $id_company	
			}
		}).done(function(data){
			systemEndWorks();
			$('#form_company').html(data);
		});		
	}
	
	this.saveCompanySettings = function(){
		systemStartWorks();
		$.ajax({
			url:"/web/admin/saveCompanySettings",
			type:"POST",
			data:$('#companySettingsForm').serialize(),
			dataType:"json"
		}).done(function(data){			
			$('#form_company').find('div.alert').remove();
			var $msg = $('<div/>');
			$msg.addClass('alert');
			$msg.html(data.msg);			
			if(data.status == 1){				
				$msg.addClass('alert-success');		
				$('#form_company').html($msg);						
			}else{				
				$msg.addClass('alert-danger');						
				$('#form_company').append($msg);	
			}
			systemEndWorks();
		});	
	}
	
}

var $company;

$(document).ready(function(e) {
	/*
	var $companiesSearch = '';
	
	$( "#companiesSearch" ).autocomplete({
		source: "/web/admin/findCompany",
		minLength: 2,
		select: function( event, ui ) {			
			console.log(ui);
			$companiesSearch = ui.item.label;			
		},
		close: function( event, ui ){
			$( "#companiesSearch" ).val($companiesSearch);	
			$companiesSearch = '';
		}		
	});
	*/
	
	var timer;
	var $name;
	var $zip_code = '';		
	
	$company = new Company();
	$company.get();
	
	$( "#companiesSearch" ).on('keyup', function () {
		$name = $("#companiesSearch").val();
		clearTimeout(timer);
		if($name.length >= 2){		
			timer = setTimeout(function(){
				$company.get( null, $name);	
			}, 800 );
		}
		if($name.length == 0){
			$company.get();
		}
	});
	
	$('#busquedas-results').on('click', '.panel-footer a', function(event){
		$company.get($(this).attr('href'), $name, $('#selectCompanyStatus').val());
		event.preventDefault();
	});
	
	$('#newCompany').on('click', function(){
		$company.form();	
	});
	
	$('.panel-body').on('click', '.row-empresa', function(){
		$company.form( $(this).attr('company-id'));
	});
	
	$('.panel-body').on('click', '.delete-button', function(){
		$('#modalDelete').modal('show');
		$('#modalDelete').attr('id-company', $(this).attr('id-company'));
	});
	
	$('.panel-body').on('click', '.register-button', function(){
		$company.save();
	});
	
	$('.panel-body').on('click', '.update-button', function(){
		$company.save();
	});
	
	$('#form_company').on('keyup', '#zip_code', function(){		
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
	
	$('#btnDelete').on('click', function(){
		$company.disable( $('#modalDelete').attr('id-company'));	
	});
	
	$('#selectCompanyStatus').on('change', function(){
		$company.get(null, $name, $('#selectCompanyStatus').val());
	});
	
	$('#busquedas-results').on('switchChange.bootstrapSwitch', '.company-status-switch', function(event, state){
		$company.setStatus( $(this).attr('company-id'), state);
	});
	
	$('#form_company').on('change', '#sectorId', function(){
		getCatalogSubSectors();	
	});

	$('.panel-body').on('click', '.view-company-users', function(event) {
		$company.getUsers($(this).attr('company-id'));
	});
	
	$('.panel-body').on('click', '.edit-company-settings', function(event) {
		$company.settingsForm( $(this).attr('company-id') )
		console.log($(this).attr('company-id'));
	});
	
	$('.panel-body').on('click', '#saveCompanySettings', function(){
		$company.saveCompanySettings();	
	});
		
});

