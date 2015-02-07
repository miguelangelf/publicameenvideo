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


Office = function(){

	this.form = function( $id ){

		var $id_office = ( $id == undefined ) ? '' : $id;
		systemStartWorks();
		$.ajax({
			url:"/web/admin/form_office",
			type:"POST",
			data:{
				id_office: $id_office
			}
		}).done(function(data){
			$('#form_office').html(data);
			$cp = $('#zip_code').val();
			if($cp.length == 5){
				getCatalog( $cp, $('#id_delegation').val(), $('#id_colony').val() );
			}
			$('#officeForm').validate({
				rules:{
					"office[name]":{
						required: true
					},
					"office[zip_code]":{
						required: true,
						number: true,
						minlength: 5
					}
				}
			});
			systemEndWorks();
		});

	}

	this.get = function( $url, $search ){

		$url = ($url == undefined) ? "/web/admin/list_offices/page/1" :$url;

		systemStartWorks();
		$.ajax({
			url: $url,
			type:"POST",
			data:{
				_search: $search
			}
		}).done(function(data){
			systemEndWorks();
			$('#busquedas-results').html(data);
		});

	}

	this.save = function(){

		if($('#officeForm').valid()){

			var parent = this;
			$.ajax({
				url:"/web/admin/saveOffice",
				type:"POST",
				dataType:"json",
				data:$('#officeForm').serialize()
			}).done(function(data){
				console.log(data);
				var $msg = $('<div/>');
				$msg.addClass('alert');
				$msg.html(data.msg);
				if(data.status == 1){
					$msg.addClass('alert-success');
					$('#form_office').html($msg);
					parent.get();
				}else{
					$msg.addClass('alert-danger');
					$('#form_office').append($msg);
				}
			});

		}

	}


	this.disable = function( $id_office ){

		var $parent = this;

		systemStartWorks();
		$.ajax({
			url: "/web/admin/disableOffice",
			dataType:"json",
			type:"POST",
			data:{
				id_office: $id_office
			}
		}).done(function(data){
			$('#form_office').find('div.alert').remove();
			var $msg = $('<div/>');
			$msg.addClass('alert');
			$msg.html(data.msg);
			if(data.status == 1){
				$msg.addClass('alert-success');
				$('#form_office').html($msg);
				$parent.get();
			}else{
				systemEndWorks();
				$msg.addClass('alert-danger');
				$('#form_office').append($msg);
			}
		});

	}


	this.managerTimeSlots = function( $id_office ){
		systemStartWorks();
		$.ajax({
			url:"/web/admin/managerTimeSlots",
			type:"POST",
			data:{
				id_office: $id_office
			}
		}).done(function(data){
			$('#form_office').html(data);
			systemEndWorks();
		});

	}


	this.getTimeSlots = function( $id_office ){
		$.ajax({
			url:"/web/admin/getTimeSlots",
			type: "POST",
			data:{
				id_office: $id_office
			}
		}).done(function(data){
			$('#listTimeSlots').html(data);
		});
	}


	this.timeSlots = function( $action ){

		systemStartWorks();
		$.ajax({
			url:"/web/admin/timeSlots",
			type:"POST",
			dataType:"json",
			data: $('#addTimesForm').serialize()
		}).done(function(data){
			var $msg = $('<div/>');
			$msg.addClass('alert');
			$msg.html(data.msg);
			if(data.status == 1){
				$msg.addClass('alert-success');
			}else{
				$msg.addClass('alert-danger');
			}

			$('#msgAddTimeSlots').html($msg);

			systemEndWorks();
			console.log(data);
		});

	}

	this.managerHoliDays = function( $id_office, $holiday ){
		systemStartWorks();
		$.ajax({
			url:"/web/admin/dias_festivos",
			type:"POST",
			data:{
				id_office: $id_office,
				holiday: $holiday
			}
		}).done(function(data){
			$('#form_office').html(data);
			$('#day').val($('#selected-day').val());
			$('#month').val($('#selected-month').val());
			$('#year').val($('#selected-year').val());
			if($('#selected-year').val() != ''){
				$('#getHoliDays').trigger('click');
			}
			systemEndWorks();
		});
	}


	this.getHoliDays = function( $id_office ){
		systemStartWorks();
		$.ajax({
			url:"/web/admin/getHoliDays",
			type:"POST",
			data:{
				id_office: $id_office
			}
		}).done(function(data){
			$('#form_office').html(data);
			systemEndWorks();
		});
	}

        this.getCPS = function( $id_office ){
            systemStartWorks();
            $.ajax({
                url:"/web/admin/getCPS",
                type:"POST",
                data:{
                    id_office: $id_office
                }
            }).done(function(data){
                $('#form_office').html(data);
                systemEndWorks();
            });

        }


        this.saveCPS = function(){
            systemStartWorks();
            $.ajax({
                url:"/web/admin/saveCPS",
                type:"POST",
                data:$('#office_cps').serialize(),
                dataType:'json'
            }).done(function(data){
                var $msg = $('<div/>');
                $msg.addClass('alert');
                $msg.html(data.msg);
                if(data.status == 1){
                    $msg.addClass('alert-success');
                }else{
                    $msg.addClass('alert-danger');
                }
                $('#msgSaveCPS').html($msg);
                systemEndWorks();
            });
        }

}


holiDays = function( $params ){

	this.offices = $params.offices;
	this.officesSave = $params.officesSave;

	this.getTimes = function(){

		$offices = this.offices;
		systemStartWorks();
		$.ajax({
			url: $offices.url,
			type:"POST",
			data: $($offices.data).serialize(),
			dataType:"json"
		}).done(function(data){
			$offices.target(data);
		});
	}


	this.saveTimes = function(){

		$officesSave = this.officesSave;
		systemStartWorks();
		$.ajax({
			url: $officesSave.url,
			type:"POST",
			data: $($officesSave.data).serialize(),
			dataType:"json"
		}).done(function(data){
			var $msg = $('<div/>');
			$msg.addClass('alert');
			$msg.html(data.msg);
			if(data.status == 1){
				$msg.addClass('alert-success');
			}else{
				$msg.addClass('alert-danger');
			}

			$($officesSave.msg).html($msg);

			systemEndWorks();
		});

	}

}

$(document).ready(function(e) {

	var $office;
	var $zip_code = '';
	var $name = '';
	var timer;

	$office = new Office();
	$office.get();

	$('#newOffice').on('click', function(){
		console.log('newOffice');
		$office.form();
	});

	$('.panel-body').on('click', '.row-enlace', function(){
		console.log($(this).attr('office-id'));
		$office.form( $(this).attr('office-id') );
	});

	$('.panel-body').on('click', '.delete-button', function(){
		$('#modalDelete').modal();
		$('#modalDelete').attr('office-id', $(this).attr('office-id'));
	});

	$('.panel-body').on('click', '.register-button', function(){
		$office.save();
	});

	$('.panel-body').on('click', '.update', function(){
		$office.save();
	});

	$('.panel-body').on('click', '.pagination-list-offices a', function(event){
		$office.get( $(this).attr('href'), $name );
		event.preventDefault();
	});

	$('#form_office').on('keyup', '#zip_code', function(){
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

	$( "#bus_search" ).on('keyup', function () {
		$name = $("#bus_search").val();
		clearTimeout(timer);
		if($name.length >= 2){
			timer = setTimeout(function(){
				$office.get( null, $name);
			}, 800 );
		}
		if($name.length == 0){
			$office.get();
		}
	});


	$('#btnDelete').on('click', function(){
		$office.disable( $('#modalDelete').attr('office-id'));
	});


	$('.panel-body').on('click', '.set-time-slots', function(){
		$office.managerTimeSlots($(this).attr('office-id'));
		console.log($(this).attr('office-id'));
	});


	$('#form_office').on('click', '#addTimes', function(){
		$office.timeSlots('add');
	});

	$('#form_office').on('click', '#deleteTimes', function(){
		$office.timeSlots('delete');
	});

	$('#form_office').on('change', '#timeSlots', function(){
		var $checked = $(this).prop('checked');
		$('#listTimeSlots input[type="checkbox"]').each(function(index, element) {
			$(this).prop('checked', $checked);
		});
	});

	$('#form_office').on('click', '#listTimeSlots input[type="checkbox"]', function(){
		if(!$(this).prop('checked')){
			$('#timeSlots').prop('checked', false);
		}
	});

	$('.panel-body').on('click', '.set-holiday', function(){
		$office.managerHoliDays($(this).attr('office-id'));
	});


	$('.panel-body').on('click', '.get-holidays', function(){
		$office.getHoliDays($(this).attr('office-id'));
	});


        $('.panel-body').on('click', '.set-cps', function(){
            $office.getCPS($(this).attr('office-id'));
	});

	$('#form_office').on('change', '#showTimeSlots', function(){
		var $checked = $(this).prop('checked');
		$('#time_slots input[type="checkbox"]').each(function(index, element) {
			$(this).prop('checked', $checked);
		});
	});
        
        
        $('#form_office').on('change', '#allCPS', function(){
		var $checked = $(this).prop('checked');
		$('#list_cps input[type="checkbox"]').each(function(index, element) {
			$(this).prop('checked', $checked);
		});
	});
        
        
        

	var $holiDays = new holiDays({
		offices:{
			url:"/web/admin/officeHoliDays",
			target: function(data){
				$('#time_slots input[type="checkbox"]').each(function(index, element) {
					var $checked = data[$(this).val()] != undefined ? true : false;
					$(this).prop('checked', $checked);
				});
				systemEndWorks();
			},
			data: '#holidaysForm'
		},
		officesSave:{
			url:"/web/admin/saveHoliDays",
			data: '#holidaysForm',
			msg: "#holidaysMsg"
		}
	});


	$('#form_office').on('click', '#getHoliDays', function(){

		var $txt = '';
		$("#holidaysMsg").html('');

		if($('#day').val() == '' || $('#month').val() == '' || $('#year').val() == '' ){
			$txt = 'Selecciona una fecha valida';
		}

		if(isNaN($('#id_office').val())){
			$txt = 'Selecciona un enlace';
		}

		if($txt != ''){
			var $msg = $('<div/>');
			$msg.addClass('alert');
			$msg.html($txt);
			$msg.addClass('alert-danger');
			$("#holidaysMsg").html($msg);
			return false;
		}

		$holiDays.getTimes();

	});


	$('#form_office').on('click', '#saveHoliDays', function(){
		$("#holidaysMsg").html('');
		if($('#day').val() != '' && $('#month').val() != '' && $('#year').val() != ''){
			$holiDays.saveTimes();
		}else{
			var $msg = $('<div/>');
			$msg.addClass('alert');
			$msg.html('Selecciona una fecha valida');
			$msg.addClass('alert-danger');
			$("#holidaysMsg").html($msg);
		}
	});

	$('#form_office').on('click', '.holiday-datail', function(){
		$office.managerHoliDays($(this).attr('office-id'), $(this).attr('holiday'));
	});


        $('#form_office').on('click', '#save_office_cps', function(){
		$office.saveCPS();
	});


});

