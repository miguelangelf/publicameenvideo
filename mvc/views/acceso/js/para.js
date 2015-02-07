$(function() {
	
	$('.privacy_check').on('click', function(){
		if($(this).attr('checked')){
			$(this).attr('checked', false);
			$(this).addClass('fa-square-o').removeClass('fa-check-square-o');
			$('.btnRegister').addClass('disabled');
		}else{
			$(this).attr('checked', true);
			$(this).addClass('fa-check-square-o').removeClass('fa-square-o');
			$('.btnRegister').removeClass('disabled');
		}
	});
	
	$('#companyFormCapcha').validate({
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
			},
			"User[confirm_mail]":{				
				equalTo: "#userMail"
			},
			"input-capcha-capcha":{
				required: true
			}
		},
		messages:{
			"User[confirm_password]":{				
				equalTo: "Las contraseñas no son iguales"
			},
			"User[confirm_mail]":{				
				equalTo: "Los emails no son iguales"
			}		
		}
	});
	
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
			"User[curp]":{
				required: true
			},
			"User[confirm_password]":{				
				equalTo: "#userPassword"
			},
			"User[confirm_mail]":{				
				equalTo: "#userMail"
			},
			"input-capcha-capcha":{
				required: true
			}
		},
		messages:{
			"User[confirm_password]":{				
				equalTo: "Las contraseñas no son iguales"
			},
			"User[confirm_mail]":{				
				equalTo: "Los emails no son iguales"
			}		
		}
	});
	
	$('.btnRegister.company').on('click', function(){
		if($('#companyFormCapcha').valid()){
			systemStartWorks();
			$.ajax({
				url:"/web/registro/createUserCompany",
				type:"POST",
				dataType:"json",				
				data:$('#companyFormCapcha').serialize()
			}).done(function(data){
				console.log(data);												
				$('#responseRegister').show().removeClass('alert-danger').removeClass('alert-success');

				if(data.status == 0){
					$('#responseRegister').addClass('alert-danger');					
				}else{
					$('#responseRegister').addClass('alert-success');
				}

				$('#responseRegister').html(data.msg);
				systemEndWorks();
			});
		}
	});
	
	$('.btnRegister.candidate').on('click', function(event){
		$('#responseRegister').hide();		
		if($('#registerCandidateForm').valid()){
			systemStartWorks();
			$.ajax({
				url: '/web/registro',
				type: 'POST',
				dataType: 'json',
				data: $('#registerCandidateForm').serialize(),
			})
			.done(function(data) {
				systemEndWorks();
				
				$('#responseRegister').show().removeClass('alert-danger').removeClass('alert-success');

				if(data.status == 0){
					$('#responseRegister').addClass('alert-danger');					
				}else{
					$('#responseRegister').addClass('alert-success');
				}

				$('#responseRegister').html(data.msg);
			});

		}	
	});
    
	$('#btnReset').on('click', function(event){
		$('#msgResetPassword').hide();		
		if($('#ResetPasswordForm').valid()){
			systemStartWorks();
			$.ajax({
				url: '/web/acceso/resetPassword',
				type: 'POST',
				dataType: 'json',
				data: $('#ResetPasswordForm').serialize(),
			})
			.done(function(data) {
				systemEndWorks();
				
				$('#msgResetPassword').show().removeClass('alert-danger').removeClass('alert-success');

				if(data.status == 0){
					$('#msgResetPassword').addClass('alert-danger');
					window.setTimeout(function(){
						$('#msgResetPassword').hide('slow');	
					},5000);					
				}else{
					$('#msgResetPassword').addClass('alert-success');
					window.setTimeout(function(){
						$('#resetpwd').modal('hide');
						$('#msgResetPassword').hide();
					},3000);					
				}

				$('#msgResetPassword').html(data.msg);
								
			});

		}	
	});
    
    $('#UserLoginForm').validate({
        rules:{
            "data[User][email]":{
                email: true,
                required: true
            },
            "data[User][password]":{
                required: true
            },
            "data[User][curp]":{
                required: true
            }
        }
    });
    
    $('#ResetPasswordForm').validate({
        rules:{
            "data[User][resetpassword]":{
                email: true,
                required: true
            }
        }
    });
    
    //var password = $.jCryption.encrypt("", "");                                                                
    var password = '';
    /*
    $.jCryption.authenticate(
        password, 
        "/system/welcome/start.php?LKM56SG36GDK", 
        "/system/welcome/continue.php?FSJI5739SDF", 
        function(AESKey) {
            $("#btnLogin, #UserEmail, #UserPassword").removeAttr('disabled');                      
        }, 
        function() {
            // Authentication failed
            console.log('Authentication failed');
        }
    );*/

    $("#btnLogin").on('click', function() {
        loginUser();        
    });
    
    $('#UserEmail').on('keypress', function(event){
        if(event.keyCode == 13){
			loginUser();	
		}
    });
	
	$('#UserPassword').on('keypress', function(event){
        if(event.keyCode == 13){
			loginUser();	
		}
    });
    
    
    loginUser = function(){
        if($('#UserLoginForm').valid()){
            $('#failLogin').hide();
            $("#btnLogin").attr('disabled','disabled');
            var encryptedString = $.jCryption.encrypt('{"UserEmail":"'+$("#UserEmail").val()+'","UserPassword":"'+$("#UserPassword").val()+'"}', password);
            $.ajax({
                url: "/web/acceso/login",
                dataType: "json",
                type: "POST",
                data: {
                    _token: encryptedString,
                    _candidate: $("#UserType").val()
                },
                success: function(response) {
                    if(response.success == 0){
                        $('#failLogin').show();
                        $("#btnLogin").removeAttr('disabled');
                    }else{
                        $('#failLogin').hide();
                        window.location = response.afterLogin;
                    }
                }
            });
        }
    }


});