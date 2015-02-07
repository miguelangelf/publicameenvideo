// JavaScript Document

Users = function( $params ){
	
	this.list = $params.list;
	this.form = $params.form;
	this.save = $params.save;
	this.catalog = $params.catalog;
	this.disable = $params.disable;
	
	this.get = function( $url, $search ){
		
		var $url = ($url == undefined || $url == null) ? this.list.url : $url;
		var $target = this.list.target;
		
		systemStartWorks();
		$.ajax({
			url: $url,
			type:"POST",
			data:{
				_search: $search	
			}
		}).done(function(data){
			systemEndWorks();
			$target.html(data);
		});
		
	}
	
	
	this.getForm = function( $id_user ){
		
		var $form = this.form;
		
		systemStartWorks();
		$.ajax({
			url:$form.url,
			type:"POST",
			data:{
				id_user:$id_user	
			}			
		}).done(function(data){
			systemEndWorks();
			$form.target.html(data);	
			$form.init();		
		});
	}
	
	
	this.saveForm = function(){
		
		var $save = this.save;
		var $form = this.form;		
		var $parent = this;		
				
		if( $($save.form).valid()){
			systemStartWorks();
			$.ajax({
				url:$save.url,
				data:$($save.form).serialize(),
				dataType:"json",
				type:"POST"
			}).done(function(data){
				$form.target.find('div.alert').remove();
				var $msg = $('<div/>');
				$msg.addClass('alert');
				$msg.html(data.msg);			
				if(data.status == 1){				
					$msg.addClass('alert-success');		
					$($save.form).html($msg);		
					$parent.get();
				}else{
					systemEndWorks();
					$msg.addClass('alert-danger');						
					$($save.form).append($msg);	
				}
			});
		}
		
	}
	
	
	this.getCatalog = function($labelTxt, $type, $selected){
		
		var $catalog = this.catalog;
		$($catalog.target).html('');
		
		$.ajax({
			url:$catalog.url,
			dataType:"json",
			type:"POST",
			data:{
				list_of: $type
			}
		}).done(function(data){
			if(data.length > 0){
				var $label = $('<label/>');
				$label.html($labelTxt);
				var $select = $('<select/>');
				$select.addClass('form-control required');
				$select.attr('name', 'user[assoc_id]');
				$.each( data, function(index, value){
					var $option = $('<option/>');
					$option.attr('value', value.id);
					$option.text(value.name);
					($selected == value.id) ? $option.attr('selected', true) : false;
					$select.append($option);
				});
				$($catalog.target).append($label);
				$($catalog.target).append($select);						
			}
		});
	}
	
	this.disableU = function( $uid ){
		
		var $disable = this.disable;
		var $form = this.form;
		var $parent = this;
		
		systemStartWorks();
		$.ajax({
			url: $disable.url,
			type:"POST",
			dataType:"json",
			data: $($disable.form).serialize()
		}).done(function(data){
			$form.target.find('div.alert').remove();
			var $msg = $('<div/>');
			$msg.addClass('alert');
			$msg.html(data.msg);			
			if(data.status == 1){				
				$msg.addClass('alert-success');		
				$form.target.html($msg);		
				$parent.get();
			}else{
				systemEndWorks();
				$msg.addClass('alert-danger');						
				$form.target.append($msg);	
			}			
		});
	}
}

$(document).ready(function(e) {
	
	var $name = '';
	var timer;
	
	var $users = new Users({
		list: {
			url:"/web/admin/list_users/page/1",
			target:$('#busquedas-results')
		},
		form:{
			url:"/web/admin/form_user",
			target:$('#form_section'),
			init: function(){				
				$('#userForm').validate({
					rules:{
						"user[email]":{
							required: true,
							email: true
						}
					}
				});
				
				$('#us_rid').val($('#us_rid_value').val());
				$('#us_rid').trigger('change');								
			}
		},
		save:{
			form: '#userForm',
			url: "/web/admin/saveUser"
		},
		catalog:{
			url: "/web/admin/catalog",
			target: '#list_for_rids'
		},
		disable:{
			form: '#userForm',
			url: "/web/admin/disableUser"
		}
	});
	
	$users.get();
	
	$('#busquedas-results').on('click', '.user', function(){
		$users.getForm( $(this).attr('id-user') );
	});
	
	$('#newUser').on('click', function(){
		$users.getForm();	
	});
	
	$('#busquedas-results').on('click', '.panel-footer a', function(event){
		$users.get($(this).attr('href'), $name);
		event.preventDefault();
	});
	
	$( "#usersSearch" ).on('keyup', function () {
		$name = $("#usersSearch").val();
		clearTimeout(timer);
		if($name.length >= 2){		
			timer = setTimeout(function(){
				$users.get( null, $name);	
			}, 800 );
		}
		if($name.length == 0){
			$users.get();
		}
	});
	
	$('.panel-body').on('click', '.delete-button', function(){
		$('#modalDelete').modal('show');
		$('#modalDelete').attr('id-candidate', $(this).attr('id-candidate'));
	});
	
	$('.panel-body').on('click', '.register-button', function(){
		$users.saveForm();
	});
	
	$('.panel-body').on('click', '.update-button', function(){
		$users.saveForm();
	});
	
	$('.panel-body').on('change', '#us_rid', function(){
		$users.getCatalog( $(this).find('option:selected').text(), $(this).val(), $('#catalog_id').val() );	
	});
	
	$('#btnDelete').on('click', function(){
		$users.disableU($('#modalDelete').attr('id-candidate'));
	});
	
	
});