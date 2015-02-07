var file_data = null;
var currentImage = null;
var currentPosition = null;
var cropper;
var currentPicture = null;
var currentSize = null;

var months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

var options =
{
    thumbBox: '.thumbBox',
    spinner: '.spinner',
    imgSrc: 'no-avatar.png'
};

var imageTypes =  ["png", "jpeg", "bmp", "gif", "jpg"];

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

showMessageBody = function(e, element, data, id) {
	if(e) {
		e.preventDefault();
	}

	// Actualizamos la bandera //
	if(data == null) return false;

	if(!$(element).attr("data-toggle")) {
		if(parseInt($(element).attr("data-read")) <= 0) {
			systemStartWorks();
			$.ajax({
		        url: '/candidato/cuenta/messages_company',
		        dataType: 'json',
		        data: { id: id },                        
		        type: 'post',
		        success: function(data) {
		        	var unreadMessages = 0;

		 			$(".list-group").children().each(function() {
						if($(this).hasClass("unread")) unreadMessages += 1;
					});

					$(".badge").html(unreadMessages);
					$(element).attr("data-read", 1);
					systemEndWorks();
		        }
		    });
		}

		$(".list-group > a").each(function(i, item) {
			$(item).removeClass("active-list");
		});
		$(element).removeClass("unread").addClass("active-list");
	}

	
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

changeBackground = function(element) {

	if (typeof element.files[0] !== "undefined") {

		var type = element.files[0].type.split('/');

		if(imageTypes.indexOf(type[1]) != -1 && element.files[0].size <= 2000000) {

			file_data = $(element).prop('files')[0];
			$("#editProfile").hide();
			$("#saveBackground").show();
			$("#cancelBackground").show();

			var reader = new FileReader();
			currentImage = $(".bck-profile").css("background-image");
			currentPosition = $(".bck-profile").css("background-position");

			$(".bck-profile").css("background-position", "0px 0px");

		    reader.onload = function (e) {
		        $(".bck-profile").css("background-image","url(" + e.target.result + ")");
		        $("#backPosition").val($(".bck-profile").css("background-position"));
		        $(".bck-profile").css("cursor","move");
		        $(".bck-profile").backgroundDraggable({ 
		        	bound: false,
		        	done: function() {
					    backgroundPosition = $(".bck-profile").css('background-position');
					    $("#backPosition").val(backgroundPosition);
					}
		        });
		    }

		    reader.readAsDataURL(file_data);
		} else {
			var $msg = $('<div class="col-md-12"></div>');
			$msg.addClass('alert');
			$msg.css("width","100%");
			$msg.css("border-radius","0px");
			$msg.html("Error: archivo muy grande ( > 2MB) o tipo de archivo no aceptado (jpg, jpeg, bmp, gif, png)");
			$msg.addClass('alert-danger');

			$("#headerCandidate").append($msg);
			setTimeout(function(){
				$($msg).slideUp("slow", function() {
					$($msg).remove();
				});
			}, 5000);
		}
	} else {
		console.log("Aqui tambien erorricilo");
	}
}

profilePicChange = function(element) {
	if (typeof element.files[0] !== "undefined") {

		var type = element.files[0].type.split('/');
		
		if(imageTypes.indexOf(type[1]) != -1 && element.files[0].size <= 2000000) {
		    var reader = new FileReader();
		    reader.onload = function(e) {
		        options.imgSrc = e.target.result;
		        cropper = $('.imageBox').cropbox(options);
		        $('#modalCrop').modal('show');
		    }
		    reader.readAsDataURL(element.files[0]);
		    this.files = [];
		} else {
			var $msg = $('<div class="col-md-12"></div>');
			$msg.addClass('alert');
			$msg.css("width","100%");
			$msg.css("border-radius","0px");
			$msg.html("Error: archivo muy grande ( > 2MB) o tipo de archivo no aceptado (jpg, jpeg, bmp, gif, png)");
			$msg.addClass('alert-danger');

			$("#headerCandidate").append($msg);
			setTimeout(function(){
				$($msg).slideUp("slow", function() {
					$($msg).remove();
				});
			}, 5000);
		}
	}
}

navigatetoSettings = function(e) {
	if(e) {
		e.preventDefault();
	}

	window.location.href = "/candidato/configuracion/editar";
}

setEmbed = function(e, data, element, type) {
	if(e) {
		e.preventDefault();
	}

	$("#curriculum").find("a").removeClass("active-list");
	$(element).addClass("active-list");

	if(type == "docx" || type == "doc") {
		$("#dataPreview").hide();
		$("#display > img").remove();
		$("#display").append('<div class="no-word"><i class="fa fa-download click-element" onclick="downloadCurriculum(' + $(element).attr("id") + ');"></i></div>');
	} else if(type != "pdf") {
		$("#dataPreview").hide();
		$("#display > div").remove();
		$("#display").append("<img src='" + data + "' style='margin-top: -20px;' width='550px' />");
	} else if(type == "pdf") {
		var objeto = $("#dataPreview").clone();
		$(objeto).attr("src", data);
		$("#dataPreview").replaceWith($(objeto));
		$("#display > img").remove();
		$("#display > div").remove();

		$("#dataPreview").show();
	}
}

downloadCurriculum = function(name) {
	window.open("/candidato/configuracion/viewCurriculum/" + name);
}

$(document).ready(function() {
	var unreadMessages = 0;

	$("#perfil_link").parent().addClass("open");
	$("#perfil_link").parent().next().removeClass("open");

	$(".list-group").children().each(function() {
		if($(this).hasClass("unread")) unreadMessages += 1;
	});

	$("#curriculum").find(".list-group-item").eq(0).addClass("active-list");
	$("#curriculum").find(".list-group-item").eq(0).click();

	$(".badge").html(unreadMessages);

	$("#messages").find(".list-group").children().eq(0).click();
	$("#messages").find(".list-group").children().eq(0).removeAttr("data-toggle");

	$("#editBackground").click(function() {
		$("#profilePicture").click();
	});

	$("#editProfile").click(function() {
		window.location.href='/candidato/configuracion/editar';
	});

	$("#saveBackground").click(function(e) {
		var $btn = $(this).button('loading');
		$("#cancelBackground").hide();
		if(e) {
			e.preventDefault();
		}

		if(file_data != null) {
			var form_data = new FormData();                  
			form_data.append('files[]', file_data);
			form_data.append('backposition', $("#backPosition").val());

			$.ajax({
		        url: '/empresa/candidatos/profileImageUpload',
	            dataType: 'json',
	            cache: false,
	            contentType: false,
	            processData: false,
	            data: form_data,                        
	            type: 'post',
	            success: function(data) {
	     			setTimeout(function() {
						$("#saveBackground").hide();
						$("#editProfile").show();
						$(".bck-profile").css("cursor","default");
						$(".bck-profile").css("background-image", "url(/empresa/candidatos/showAvatar/" + data.file_name + "/background)");
						$(".bck-profile").css("background-position", data.position);
						$('.bck-profile').backgroundDraggable('disable');
						$btn.button('reset');
						console.log(data);
					}, 2000);
	            }
	        });
		}
	});

	$("#uploadProfilePic").click(function(e) {
		if(e) {
			e.preventDefault();
		}

		$("#picForm")[0].reset();
		$("#file-avatar").click();

		$('#btnZoomIn').off('click');
		$('#btnZoomOut').off('click');

		$('#btnZoomIn').on('click', function(){
			cropper.zoomIn();
		});
		$('#btnZoomOut').on('click', function(){
			cropper.zoomOut();
		});

		$("#btnCrop").click(function() {
	    	var img = cropper.getBlob();
	    	var form_data = new FormData();

	    	form_data.append('files[]', img, "new_profile." + img.type.split('/')[1]);

	    	$.ajax({
		        url: '/empresa/candidatos/avatarUpload',
	            dataType: 'json',
	            cache: false,
	            contentType: false,
	            processData: false,
	            data: form_data,                     
	            type: 'post',
	            success: function(data) {
	            	$(".img_profile").css("background", "url(/empresa/candidatos/showAvatar/" + data + "/profile)");
	            	$(".img_profile > h1 ").remove();
	            	$('#modalCrop').modal('hide');
					cropper.stopDrag();
	            }
	        });

	    });

	});

	$("#cancelBackground").click(function(e) {
		if(e) {
			e.preventDefault();
		}

		$("#saveBackground").hide();
		$("#cancelBackground").hide();
		$("#editProfile").show();
		$(".bck-profile").css("cursor","default");
		$('.bck-profile').backgroundDraggable('disable');

		$('.bck-profile').css("background-image", currentImage);
		$('.bck-profile').css("background-position", currentPosition);
	});

	$("#sendMessageBtn").click(function() {

		var messageObject = {};
		$("#sendMessageForm").serializeArray().map(function(x){ 
			messageObject[x.name] = x.value;
		});

		$.ajax({
	        url: '/empresa/candidatos/sendMessage',
            dataType: 'json',
            data: { message: messageObject },                        
            type: 'post',
            success: function(data) {
     			console.log(data);
     			$("#modalMessage").modal('hide');
     			$("#sendMessageForm")[0].reset();
            }
        });

	});

});