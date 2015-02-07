/* Custom Scripts */
systemStartWorks = function(){
	$('#systemWorks').modal({
		keyboard: false,
		backdrop: 'static'
	});
}

systemEndWorks = function(){
	$('#systemWorks').modal('hide');
}


Placeholder = function(){
    
    setLabel = function( $element ){
        var $label = $('<placeholder/>');
        var $label_css = 'display: block !important; margin-top: 3px !important; font-size:12px !important; color: #666 !important;';
        $label.html($element.attr('placeholder')+':').attr('style', $label_css);
        $element.before($label).attr('style', 'margin-top: 0 !important;');
    };
    
    findElements = function(){
        $('input[placeholder]').each(function(index, value){
            setLabel($(this));
        });
        $('textarea[placeholder]').each(function(index, value){
            setLabel($(this));
        });
    };
    
    this.init = function(){        
        var isInputSupported = 'placeholder' in document.createElement('input');
        var isTextareaSupported = 'placeholder' in document.createElement('textarea');
        
        if(!isInputSupported || !isTextareaSupported){
            findElements();
        }
    };
    
};



$(document).ready(function () {
    
        var $placeholder = new Placeholder();
        $placeholder.init();
    
	$.validator.messages.required = "Requerido";
	$.validator.messages.email = "Por favor introduce un correo válido";
	$.validator.messages.date = "Por favor introduce una fecha válida";	
	$.validator.messages.number = "Por favor introduce solo n&uacute;meros";
	$.validator.defaults.errorPlacement = function(error, element){
		return true;
	}
	$.validator.defaults.highlight = function(element, errorClass, validClass) {
		$(element).addClass('error-validation');
		$(element).focus();
	}
	$.validator.defaults.unhighlight = function(element, errorClass, validClass) {
		$(element).removeClass('error-validation');
	}
	// Start Header Animation
	$(window).scroll(function () {
	if ($(document).scrollTop() < 45) {
            // grande
            $('header').removeClass("small");
            $('header').removeClass('fix');
            
            $('#search-bar').removeClass("mid_search_small");
            $('#search-bar').addClass("mid_search");
            
            $('#fh').removeClass("fixheight_small");
            $('#fh').addClass("fixheight");
            
            $('#nav-bar-element').removeClass("navbar-brand_small");
            $('#nav-bar-element').addClass("navbar-brand");
            $('#img-logo').addClass("logo");
            $('#img-logo').removeClass("logo_small");
            
            $("#map-section").css("top",116);
            
        } else {
            // chico
            $('header').addClass("small");
            $('header').addClass('fix');
            $('#search-bar').removeClass("mid_search");
            $('#search-bar').addClass("mid_search_small");
            $('#fh').removeClass("fixheight");
            $('#fh').addClass("fixheight_small");
            $('#nav-bar-element').removeClass("navbar-brand");
            $('#nav-bar-element').addClass("navbar-brand_small");
            $('#img-logo').removeClass("logo");
            $('#img-logo').addClass("logo_small");
            
            $("#map-section").css("top",90);
	}
	});
	// Start Parallax script
	$('#serviceArea').parallax("50%", 0.03);
	// Start ToolTip
	$('[data-toggle=tooltip]').tooltip()
	// Start PoPover
	$('[data-toggle=popover]').popover();
	
	
	$('#companyTeam').on('click', function(event){
		if(typeof tabpanel == 'undefined'){
			console.log('companyTeam');
			systemStartWorks();
			document.location = '/empresa/candidatos/todos#equipo'
		}
		event.preventDefault();	
	});
	
});	