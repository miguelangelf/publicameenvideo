$(document).ready(function() {
	
	$('#registerCandidateForm').validate({
		rules:{
			"User[name]":{
				required: true
			},
			"User[last_name]":{
				required: true
			},
			"User[email]":{
				required: true,
				email:true
			},
			"User[password]":{
				required: true
			},
			"User[confirm_password]":{				
				equalTo: "#userPassword"
			}
		}
	});

	$('#registerCandidate').on('click', function(event){

		$('#responseRegister').hide();

		if($('#registerCandidateForm').valid()){

			$.ajax({
				url: '/web/registro',
				type: 'POST',
				dataType: 'json',
				data: $('#registerCandidateForm').serialize(),
			})
			.done(function(data) {
				
				$('#responseRegister').show().removeClass('alert-danger').removeClass('alert-success');

				if(data.status == 0){
					$('#responseRegister').addClass('alert-danger');					
				}else{
					$('#responseRegister').addClass('alert-success');
				}

				$('#responseRegister').html(data.msg);
			});

		}

		event.preventDefault();
		
		
	});

});