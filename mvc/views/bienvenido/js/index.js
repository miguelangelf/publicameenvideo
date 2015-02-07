$(document).ready(function() {
    /*$('.contsearch').blurjs({
	source: '#topimage',
	radius: 7,
	overlay: 'rgba(255,255,255,0.4)'
    });*/
    // Start Main Content Slider
    $('#mainslider').nivoSlider();
    $('#logo').flexslider({
        animation: "slide",
        touch: true,
        initDelay: 0, //{NEW} Integer: Set an initialization delay, in milliseconds
        controlNav: false, //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
        prevText: "<span class='fa fa-angle-left'></span>",
        nextText: "<span class='fa fa-angle-right'></span>",
        itemWidth: 190,
        itemMargin: 45,
        minItems: 1,
        maxItems: 5,
        move: 1,
        slideshowSpeed: 2300    // Time delay between animatios
    });
	
	$('#searchForm').validate({
		rules:{
			'search-by':{
				required:true
			}
		}
	});
	
	$('#searchByWord').on('click', function(event){
		if($('#searchForm').valid()){			
			systemStartWorks();			
			$('#searchForm').submit();			
		}		
		event.preventDefault();
	});
	
	$('#searchBy').on('keypress', function(event){
        if(event.keyCode == 13){
			if($('#searchForm').valid()){			
				systemStartWorks();			
				$('#searchForm').submit();			
			}	
			event.preventDefault();
		}		
    });
	
	/*
	$('#searchBy').on('focus', function(){
		if($('#locationSearch').val() == ''){
			$("#locationSearch").select2("open");
			$(this).removeAttr('placeholder');
		}
	});
	
	$('#locationSearch').select2({		
		placeholder: "¿Donde buscas?",
		formatInputTooShort:"Escribe al menos una letra para buscar",		
		formatSearching: "Buscando ...",
		formatSelectionTooBig:"Ya has elejido ciudad",
		maximumSelectionSize: 1,
		tags: true,		
		ajax: {
			url: "/empresa/vacancies/cities",
			dataType: 'json',
			type:"POST",
			quietMillis: 250,
			data: function (term, page) {
				return {
					city: term
				};
			},
			results: function (data, page) {
				return { 
					results: data 
				};
			},
			cache: true
		},		
		formatResult: function(data){
			$option = ( data.state != '' ) ? '<div>'+data.city+', '+data.state+'</div>' : '<div>'+data.city+'</div>';
			return $option;
		}, 
		formatSelection: function(data){
			return data.city;
		}
	}).on("select2-selecting", function( e ){
		window.setTimeout(function(){
				$('#searchBy').focus().attr('placeholder', '¿Que buscas?');		
			},
			50
		);		
	}).on("select2-focus", function() { 
		$('#searchBy').removeAttr('placeholder'); 
	});
	
	*/
		 
	
});