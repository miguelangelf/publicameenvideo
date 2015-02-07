// JavaScript Document

var $optionsFileUpload = {
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
			_document_name: $('#nameNewDocument').val(), 
			_id_candidate: $("#slider-content").attr('id-candidate'), 
			_onDone: function(){
				$('#nameNewDocument').val('');	
				$('.fileinput-button').addClass('disabled')			
				getDocuments( 
					$("#slider-content").attr('id-candidate'), 
					function( data ){
						$('#listDocuments').html(data);
					}
				);
			}
		});
	}
};



enableExam = function(){
	
	var $all_cocuments = 0;
	
	$('#slider-wrapper .document-check').each(function(index, element) {
		$all_cocuments += $(this).attr('checked') ? 0 : 1;
		var $row = $(this).parents('tr.document-control');	
		if($(this).attr('checked')){
			if(!$row.find('i.checkinactivo').length){
				$row.find('span.fileinput-button').addClass('disabled');
			}
		}else{
			$row.find('span.fileinput-button').addClass('disabled');	
		}			
	});
	
	var $modal = ($all_cocuments == 0) ? 'modal' : '';		
	$('#btnexamen').attr('data-toggle', $modal);
	
	if($modal == ''){
		$('#btnexamen').addClass('disabled').removeClass('enabled');		
	}else{
		$('#btnexamen').addClass('enabled').removeClass('disabled');	
	}
}

Document = function(){		
	
	this.register = function($id_candidate, $type, $action, $element){		
		
		systemStartWorks();
		$.ajax({
			url:"/gobierno/agendar/registerDocument",
			type:"POST",
			dataType:"json",
			data:{
				"document[id_candidate]": $id_candidate,
				"document[type]": $type,
				"document[action]": $action
			}
		}).done(function(data){
			
			systemEndWorks();			
			//console.log(data);
			
			if(data.status == 1){
				
				var $row = $element.parents('tr.document-control');															
				
				if(!$element.attr('checked')){
					$row.attr('id-document', data.id);
					$element.addClass('fa-check-square-o').removeClass('fa-square-o');
					$row.find('span.fileinput-button').removeClass('disabled');					
				}else{
					$row.attr('id-document', '');
					$element.addClass('fa-square-o').removeClass('fa-check-square-o');										
					$row.find('i.examplecheck').removeClass('examplecheck').addClass('checkinactivo');					
					$row.find('div.btnver').removeClass('enabled').removeClass('download').addClass('disabled');	
					$row.find('span.fileinput-button').addClass('disabled');				
				}
					
				$element.attr('checked', $action);				
				enableExam();							
				
			}
			
		});
		
	}
	
}

Notification = function(){
	
	register = function($id_notification, $id_candidate, success, error ){
		systemStartWorks();
		$.ajax({
			url:"/gobierno/agendar/notification",
			type:"POST",
			dataType:"json",			
			data:{
				id_notification: $id_notification,
				id_candidate: $id_candidate,
				selected_date: $('#selectedDate').val()
			}
		}).done(function(data){
			systemEndWorks();			
			if(data.status == 1){
				(success != undefined) ? success( data ) : console.log(data);
			}else{
				(error != undefined) ? error( data ) : console.log(data);
			}
		});	
	}
	
	this.initExam = function( $id_candidate ){
		if($id_candidate > 0){
			register(
				3,  
				$id_candidate, 
				function( data ){
					$('#btnexamen').addClass('disabled').addClass('withExam').html('Candidato realizando pruebas').attr('id', 'withExam');
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "success", "<i class='fa fa-check fa-lg'></i>&nbsp;&nbsp;Operación exitosa");
					setTimeout(function() {
						//  let's now erase the notification & clode the modal
						$(".candidate-modal-notification").html("");
						$('#candidateAccepted2').modal('hide');
					}, 2000);					
				},
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "danger", "<i class='fa fa-times fa-lg'></i>&nbsp;&nbsp;Ha ocurido un error");
				}
			);
		}else{
			return false;
		}
	}
	
	this.noShow = function( $id_candidate ){
		if($id_candidate > 0){
			register(
				1,  
				$id_candidate, 
				function( data ){					
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "success", "<i class='fa fa-check fa-lg'></i>&nbsp;&nbsp;Operación exitosa");
					var $slot = $('div[id-candidate="'+$id_candidate+'"].schedule-wrapper');
					if($slot.length > 0){
						$slot.addClass('disabled');	
						$slot.find('div.btn.opciones').addClass('disabled');
						togglePanel('#panelRight');
					}
					setTimeout(function() {
						//  let's now erase the notification & clode the modal
						$(".candidate-modal-notification").html("");
						$('#noShow').modal('hide');
					}, 2000);	
				},
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "danger", "<i class='fa fa-times fa-lg'></i>&nbsp;&nbsp;Ha ocurido un error");
				}
			);
		}else{
			return false;
		}
	}
	
	this.candidateIdentity = function( $id_candidate, officeType ){
		if($id_candidate > 0){
			var $status = 7;
			register(
				$status,  
				$id_candidate, 
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "success", "<i class='fa fa-check fa-lg'></i>&nbsp;&nbsp;Operación exitosa");
					setTimeout(function() {
						//  let's now erase the notification & clode the modal
						$(".candidate-modal-notification").html("");
						$('#candidateIdentity').modal('hide');
						$('#candidateIdentityBtn').addClass('disabled');
					}, 2000);	
				},
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "danger", "<i class='fa fa-times fa-lg'></i>&nbsp;&nbsp;Ha ocurido un error");
				}
			);
		}else{
			return false;
		}
	}
	
	this.examUncomplete = function( $id_candidate ){
		if($id_candidate > 0){
			register(
				5,  
				$id_candidate, 
				function( data ){
					$(".candidate-modal-notification").html('');
showNotification(".candidate-modal-notification", "success", "<i class='fa fa-check fa-lg'></i>&nbsp;&nbsp;Operación exitosa");
					var $slot = $('div[id-candidate="'+$id_candidate+'"].schedule-wrapper');
					if($slot.length > 0){
						$slot.addClass('disabled');	
						$slot.find('div.btn.opciones').addClass('disabled');
						togglePanel('#panelRight');
					}
					setTimeout(function() {
						//  let's now erase the notification & clode the modal
						$(".candidate-modal-notification").html("");
						$('#examUncomplete').modal('hide');
					}, 2000);	
				},
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "danger", "<i class='fa fa-times fa-lg'></i>&nbsp;&nbsp;Ha ocurido un error");
				}
			);
		}else{
			return false;
		}
	}
	
	this.newMeeting = function( $id_candidate ){
		if($id_candidate > 0){
			register(
				4,  
				$id_candidate, 
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "success", "<i class='fa fa-check fa-lg'></i>&nbsp;&nbsp;Operación exitosa");
					var $slot = $('div[id-candidate="'+$id_candidate+'"].schedule-wrapper');
					if($slot.length > 0){
						$slot.addClass('disabled');	
						$slot.find('div.btn.opciones').addClass('disabled');
						togglePanel('#panelRight');
					}
					setTimeout(function() {
						//  let's now erase the notification & clode the modal
						$(".candidate-modal-notification").html("");
						$('#newMeeting').modal('hide');
					}, 2000);	
				},
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "danger", "<i class='fa fa-times fa-lg'></i>&nbsp;&nbsp;Ha ocurido un error");
				}
			);
		}else{
			return false;
		}
	}
	
	this.docsUncomplete = function( $id_candidate ){
		if($id_candidate > 0){
			register(
				2,  
				$id_candidate, 
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "success", "<i class='fa fa-check fa-lg'></i>&nbsp;&nbsp;Operación exitosa");
					var $slot = $('div[id-candidate="'+$id_candidate+'"].schedule-wrapper');
					if($slot.length > 0){
						$slot.addClass('disabled');	
						$slot.find('div.btn.opciones').addClass('disabled');
						togglePanel('#panelRight');
					}
					setTimeout(function() {
						//  let's now erase the notification & clode the modal
						$(".candidate-modal-notification").html("");
						$('#docsUncomplete').modal('hide');
					}, 2000);	
				},
				function( data ){
					$(".candidate-modal-notification").html('');
					showNotification(".candidate-modal-notification", "danger", "<i class='fa fa-times fa-lg'></i>&nbsp;&nbsp;Ha ocurido un error");
				}
			);
		}else{
			return false;
		}
	}
		
}

var currentPos = null;
var _options = [0,0,0,0];
var _flip = function(no){
    _options.forEach(function(element,index){
        if((_options[index] == 1) && (index != (no-1))){
            $("#Option-"+(index+1)).quickFlipper();
            _options[index] = 0;
        }
    });
    _options[no-1] = _options[no-1]===1?0:1;
    if(_options[no-1] === 1){
        currentPos = no;
    }else{
        currentPos = null;
    }
    $("#Option-"+no).quickFlipper();
};

$(document).ready(function(e) {
	
	var $notification = new Notification();	
	var $document = new Document();

	var $notesManager = new candidateNotes({
		_target: '#slider-wrapper #notes #txt',
		_content: '#slider-wrapper #notes #container_notes',
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
        
	$('#slider-wrapper').on('click', '.document-check', function(){										
		var $action = !$(this).attr('checked') ? true : false;		
		$document.register($("#slider-content").attr('id-candidate'), $(this).attr('doc-type'), $action, $(this) );				
		
	});
	
	$('#slider-wrapper').on('click', '.document-control .download', function(){	
		window.open($(this).attr('file-url'));		
	});
	
	$('#btn-candidateAccepted').on('click', function(){
		$notification.initExam($("#slider-content").attr('id-candidate'));
	});
	
	$('#slider-wrapper').on('click', '.no-show', function(){
		$('#btn-noShow').attr('id-candidate', $(this).attr('id-candidate'));	
	});
	
	$('#btn-noShow').on('click', function(){		
		$notification.noShow($(this).attr('id-candidate'));
	});
	
	$('#btn-candidateIdentity').on('click', function(){
		$notification.candidateIdentity($("#slider-content").attr('id-candidate'), $('#candidateIdentityBtn').attr('office-type') );
	});
	
	$('#btn-examUncomplete').on('click', function(){
		$notification.examUncomplete($("#slider-content").attr('id-candidate'));
	});
	
	$('#btn-newMeeting').on('click', function(){
		$notification.newMeeting($("#slider-content").attr('id-candidate'));
	});
	
	$('#btn-docsUncomplete').on('click', function(){
		$notification.docsUncomplete($("#slider-content").attr('id-candidate'));
	});

	$('#slider-wrapper').on('click', '#notesTab', function(event){	
		$notesManager.get( $("#slider-content").attr('id-candidate') );
	});	
	
	$('#slider-wrapper').on('click', '#addNote', function(event){	
		$notesManager.add( $("#slider-content").attr('id-candidate') );
	});


	$('#slider-wrapper').on('click', '#documentsTab', function(event){	
		getDocuments( 
			$("#slider-content").attr('id-candidate'), 
			function( data ){
				$('#listDocuments').html(data);
			}
		);
	});

	$('#slider-wrapper').on('click', '.deleteDocument', function(event){	
		deleteDocument(
			$(this).attr('id-document'),		
			function( data ){
				getDocuments( 
					$("#slider-content").attr('id-candidate'), 
					function( data ){
						$('#listDocuments').html(data);
					}
				);
			}
		);
	});

	$('#slider-wrapper').on('keyup', '#nameNewDocument', function(event) {
		$(this).val() == '' ? $('.fileinput-button').addClass('disabled') : $('.fileinput-button').removeClass('disabled');
	});

});