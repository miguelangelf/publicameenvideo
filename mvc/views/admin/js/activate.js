// JavaScript Document
$(document).ready(function(e) {
	
	$.validator.messages.equalTo = "Las contraseñas no coinsiden";
	/*$.validator.defaults.errorPlacement = function(error, element){
		return true;
	}
	$.validator.defaults.highlight = function(element, errorClass, validClass) {
		$(element).addClass('error-validation');
		$(element).focus();
	}
	$.validator.defaults.unhighlight = function(element, errorClass, validClass) {
		$(element).removeClass('error-validation');
	}*/
	
	$('#changePass').validate({
		rules:{
			"user[pass]":{
				required: true
			},
			"user[confirm_pass]":{				
				equalTo: "#userPassword"
			},
		}	
	});
	
	$('#createPassBtn').on('click', function(){
		if($('#changePass').valid()){
			$('#changePass').submit();
		}	
	});
});