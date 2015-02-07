// JavaScript Document

Vacancies = function(){
	
	var $page = 1;
	var $isLoading = false;
	
	this.setPage = function(){
		$page ++;		
	};
	
	this.unsetPage = function(){
		$page = 1;
		$('#vacancies-list').html('');
	};
	
	this.get = function( $id_company ){				
		var $parent = this;		
		if( $isLoading == false){
			var $i = $('<i>');
			$i.addClass("fa fa-refresh fa-spin");
			$('#vacancies-list').append($i);
			$isLoading = true;			
			$.ajax({
				url:"/web/bienvenido/listado_ofertas",
				type:"POST",
				data:{
					page: $page,
					searchBy: $('#searchBy').val()
				}
			}).done(function(data){
                            $('#vacanciesTotal').remove();	
                            $('#vacancies-list i.fa-refresh').remove();		
                            $('#vacancies-list').append(data);
                            var obj = jQuery.parseJSON($('#jsongroups').val());
                            $parent.setGrouping(obj);
                            $('#total').html($('#vacanciesTotal').val());
                            $('[data-toggle="tooltip"]').tooltip();
                            $parent.setPage();
                            $isLoading = false;				
			});		
		}
	};
        
        this.setGrouping = function(obj){
            var MAX_ELEMENTS = 5;
            var counter = 0;
            
            // Industry
            $("#group-industry").html("");
            $("#group-industry-hidden").html("");
            counter = 0;
            $.each(obj.industry, function(label, value) {
                if(counter < MAX_ELEMENTS){
                    $("#group-industry").append("<span class='attribute-group' onclick=\"addFilter('industry','"+label+"')\">"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }else{
                    $("#group-industry-hidden").append("<span class='attribute-group' onclick=\"addFilter('industry','"+label+"')\">"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }
                counter += 1;
            });
            if (counter-1 < MAX_ELEMENTS){
                $("#toggleIndustry").hide();
            }else{
                $("#toggleIndustry").show();
            }
            
            // Function
            $("#group-function").html("");
            $("#group-function-hidden").html("");
            counter = 0;
            $.each(obj.function, function(label, value) {
                if(counter < MAX_ELEMENTS){
                    $("#group-function").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }else{
                    $("#group-function-hidden").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }
                counter += 1;
            });
            if (counter-1 < MAX_ELEMENTS){
                $("#toggleFunction").hide();
            }else{
                $("#toggleFunction").show();
            }
            
            // State
            $("#group-state").html("");
            $("#group-state-hidden").html("");
            counter = 0;
            $.each(obj.state, function(label, value) {
                if(counter < MAX_ELEMENTS){
                    $("#group-state").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }else{
                    $("#group-state-hidden").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }
                counter += 1;
            });
            if (counter-1 < MAX_ELEMENTS){
                $("#toggleState").hide();
            }else{
                $("#toggleState").show();
            }
            
            // City
            $("#group-city").html("");
            $("#group-city-hidden").html("");
            counter = 0;
            $.each(obj.city, function(label, value) {
                if(counter < MAX_ELEMENTS){
                    $("#group-city").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }else{
                    $("#group-city-hidden").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }
                counter += 1;
            });
            if (counter-1 < MAX_ELEMENTS){
                $("#toggleCity").hide();
            }else{
                $("#toggleCity").show();
            }
            
            // Published
            $("#group-published").html("");
            $("#group-published-hidden").html("");
            counter = 0;
            $.each(obj.published, function(label, value) {
                if(counter < MAX_ELEMENTS){
                    $("#group-published").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }else{
                    $("#group-published-hidden").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }
                counter += 1;
            });
            if (counter-1 < MAX_ELEMENTS){
                $("#togglePublished").hide();
            }else{
                $("#togglePublished").show();
            }
            
             // Salary
            $("#group-salary").html("");
            $("#group-salary-hidden").html("");
            counter = 0;
            $.each(obj.salary, function(label, value) {
                if(counter < MAX_ELEMENTS){
                    $("#group-salary").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }else{
                    $("#group-salary-hidden").append("<span class='attribute-group'>"+label+"</span> <span class='attribute-group-value'>("+value+")</span> <br>");
                }
                counter += 1;
            });
            if (counter-1 < MAX_ELEMENTS){
                $("#toggleSalary").hide();
            }else{
                $("#toggleSalary").show();
            }
        };

};

function showElements(type){    

    switch(type){
        case "industry":
            $("#group-industry-hidden").slideToggle();
            var obj = document.getElementById("toggleIndustry");
            if(obj.getAttribute("open") === "NO"){
                $("#toggleIndustry").html("<i class='fa fa-caret-square-o-up'></i> Ver menos");
                obj.setAttribute("open","YES");
            }else{
                $("#toggleIndustry").html("<i class='fa fa-caret-square-o-down'></i> Ver mas");
                obj.setAttribute("open","NO");
                 $('html, body').animate({
                     scrollTop:$("#group-industry").parent().position().top
                 }, 'slow');
            }
            break;
        case "function":
            $("#group-function-hidden").slideToggle();
            var obj = document.getElementById("toggleFunction");
            if(obj.getAttribute("open") === "NO"){
                $("#toggleFunction").html("<i class='fa fa-caret-square-o-up'></i> Ver menos");
                obj.setAttribute("open","YES");
            }else{
                $("#toggleFunction").html("<i class='fa fa-caret-square-o-down'></i> Ver mas");
                obj.setAttribute("open","NO");
                 $('html, body').animate({
                     scrollTop:$("#group-function").parent().position().top 
                 }, 'slow');
            }
            break;
        case "state":
            $("#group-state-hidden").slideToggle();
            var obj = document.getElementById("toggleState");
            if(obj.getAttribute("open") === "NO"){
                $("#toggleState").html("<i class='fa fa-caret-square-o-up'></i> Ver menos");
                obj.setAttribute("open","YES");
            }else{
                $("#toggleState").html("<i class='fa fa-caret-square-o-down'></i> Ver mas");
                obj.setAttribute("open","NO");
                 $('html, body').animate({
                     scrollTop:$("#group-state").parent().position().top 
                 }, 'slow');
            }
            break;
        case "city":
            $("#group-city-hidden").slideToggle();
            var obj = document.getElementById("toggleCity");
            if(obj.getAttribute("open") === "NO"){
                $("#toggleCity").html("<i class='fa fa-caret-square-o-up'></i> Ver menos");
                obj.setAttribute("open","YES");
            }else{
                $("#toggleCity").html("<i class='fa fa-caret-square-o-down'></i> Ver mas");
                obj.setAttribute("open","NO");
                 $('html, body').animate({
                     scrollTop:$("#group-city").parent().position().top 
                 }, 'slow');
            }
            break;
        case "published":
            $("#group-published-hidden").slideToggle();
            var obj = document.getElementById("togglePublished");
            if(obj.getAttribute("open") === "NO"){
                $("#togglePublished").html("<i class='fa fa-caret-square-o-up'></i> Ver menos");
                obj.setAttribute("open","YES");
            }else{
                $("#togglePublished").html("<i class='fa fa-caret-square-o-down'></i> Ver mas");
                obj.setAttribute("open","NO");
                 $('html, body').animate({
                     scrollTop:$("#group-published").parent().position().top 
                 }, 'slow');
            }
            break;
        case "salary":
            $("#group-salary-hidden").slideToggle();
            var obj = document.getElementById("toggleSalary");
            if(obj.getAttribute("open") === "NO"){
                $("#toggleSalary").html("<i class='fa fa-caret-square-o-up'></i> Ver menos");
                obj.setAttribute("open","YES");
            }else{
                $("#toggleSalary").html("<i class='fa fa-caret-square-o-down'></i> Ver mas");
                obj.setAttribute("open","NO");
                 $('html, body').animate({
                     scrollTop:$("#group-salary").parent().position().top 
                 }, 'slow');
            }
            break;
    }
}

var current_filters = [];

function addFilter(type,value){
    var _html = "<div class='filtertag'><div class='row'><div class='col-md-9'>"+value+"</div><div class='col-md-1'><i class='fa fa-times filterclose'></i></div></dvi></div>";
    $("#filtersTags").append(_html);
    $('html, body').animate({
        scrollTop:$("#filtersTags").parent().position().top
    }, 'slow');
    //filterTags
}

function removeFilter(type,value){
    
}

$(document).ready(function(e) {
	
	var $vacancies = new Vacancies();
	$vacancies.get();
	   
    $("#ofertas_link").parent().prev().removeClass("open");
    $("#ofertas_link").parent().addClass("open");
	$(window).on('scroll', function() {
		if($(window).scrollTop() + $(window).height() == $(document).height()) {
			var $inPage = $('#vacancies-list .vacancy').length;
			var $total = $('#total').html();
			var $totalH = $('#vacanciesTotal').val();
			if( $totalH == $total && $total > $inPage){				
				$vacancies.get( );				
			}		
	   }
	});
	
});