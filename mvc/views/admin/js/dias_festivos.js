// JavaScript Document

holiDays = function( $params ){
	
	this.offices = $params.offices;
	
	this.getOffices = function(){
		
		$offices = this.offices;
		
		$.ajax({
			url: $offices.url,
			type:"POST",
			data: $offices.data
		}).done(function(data){
			$offices.target.html(data);
		});
	}
	
}

$(document).ready(function(e) {
	
	var $holiDays = new holiDays({
		offices:{
			url:"/web/admin/officeHoliDays",
			target: $('#list_offices'),
			data: $('#holidaysForm').serialize()
		}		
	});
	
	$('#showTimeSlots').on('change', function(){
		var $checked = $(this).prop('checked');
		$('#time_slots input[type="checkbox"]').each(function(index, element) {
				$(this).prop('checked', $checked);
		});					
	});
	
	$('#time_slots input[type="checkbox"]').on('click', function(){
		if(!$(this).prop('checked')){
			$('#showTimeSlots').prop('checked', false);
		}
	});
	
	$('#getHoliDays').on('click', function(){
		//$holiDays.getOffices();	
	});
	
});