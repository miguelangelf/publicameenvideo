filters = function(){
    
    // Static attributes
    this.controller = "/module-engine/filters/";
    
    // Current state
    this.currentFilter = {      
        edad_mayor: { value: "" },
        edad_menor: { value: "" },
        genero:     { value: -1},
        delegacion: { value: 0 },
        licencia:   { value: -1 },
        estudios:   { value: 0 },        
        hijos:      { value: -1 },
        estado:     { value: 0 },
        turno:      { value: 0 },
        perfil1:    { value: 0 }, // Administrativo
        perfil2:    { value: 0 }, // Comercial
        perfil3:    { value: 0 }, // Operativo
        perfil4:    { value: 0 },  // Servicio al cliente   
        concluidos: { value: -1 }
    };


    (getIds = function( $parent ){      
        $.ajax({
            url: $parent.controller +'getIds',
            type: 'POST',
            dataType: 'json'            
        })
        .done(function( data ) {
            $.each($parent.currentFilter, function(index, val) {
                $parent.currentFilter[index].id = data[index];   
            });         
        });
        
    })(this);
    
    this.listener = function(attribute,obj){
        var parent = this;
        switch(attribute){
            case "edad_mayor": 
                var value = $(obj).val();
                parent.currentFilter.edad_mayor.value = value;
                break;
            case "edad_menor": 
                var value = $(obj).val();
                parent.currentFilter.edad_menor.value = value;
                break;
            case "genero": 
                var value = $(obj).val();
                parent.currentFilter.genero.value = value;
                break;
            case "delegacion":
                var value = $(obj).val();
                parent.currentFilter.delegacion.value = value;
                break;
            case "licencia":
                var value = $(obj).val();
                parent.currentFilter.licencia.value = value;
                break;
            case "estudios":
                var value = $(obj).val();
                parent.currentFilter.estudios.value = value;
                break;
            case "concluidos":
                var value = $(obj).val();
                parent.currentFilter.concluidos.value = value;
                break;
            case "hijos":
                var value = $(obj).val();
                parent.currentFilter.hijos.value = value;
                break;
            case "estado":
                var value = $(obj).val();
                parent.currentFilter.estado.value = value;
                break;
            case "turno":
                var value = $(obj).val();
                parent.currentFilter.turno.value = value;
                break;
            case "perfil1":  // Administrativo
                var value = $(obj).is(":checked");
                if(value){
                    parent.currentFilter.perfil1.value = 1;
                }else{
                    parent.currentFilter.perfil1.value = 0;
                }
                break;
            case "perfil2":  // Comercial
                var value = $(obj).is(":checked");
                if(value){
                    parent.currentFilter.perfil2.value = 1;
                }else{
                    parent.currentFilter.perfil2.value = 0;
                }
                break;
            case "perfil3":  // Operativo
                var value = $(obj).is(":checked");
                if(value){
                    parent.currentFilter.perfil3.value = 1;
                }else{
                    parent.currentFilter.perfil3.value = 0;
                }
                break;
            case "perfil4":  // Servicio al cliente
                var value = $(obj).is(":checked");
                if(value){
                    parent.currentFilter.perfil4.value = 1;
                }else{
                    parent.currentFilter.perfil4.value = 0;
                }
                break;
        }
        
        this.refreshFilterList();
        
    };
    
    this.refreshFilterList = function(){
        _filters.currentFilters = [];
        
        // Edad mayor
        if(this.currentFilter.edad_mayor.value!=""){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.edad_mayor.id,values:{v1:this.currentFilter.edad_mayor.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // Edad menor
        if(this.currentFilter.edad_menor.value!=""){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.edad_menor.id,values:{v1:this.currentFilter.edad_menor.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // genero
        if(this.currentFilter.genero.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.genero.id,values:{v1:this.currentFilter.genero.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // delegacion
        if(this.currentFilter.delegacion.value!=0){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.delegacion.id,values:{v1:this.currentFilter.delegacion.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // delegacion
        if(this.currentFilter.licencia.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.licencia.id,values:{v1:this.currentFilter.licencia.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // estudios
        if(this.currentFilter.estudios.value!=0){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.estudios.id,values:{v1:this.currentFilter.estudios.value}};
            _filters.currentFilters.push(_filter);
        }

        // concluidos
        if(this.currentFilter.concluidos.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.concluidos.id,values:{v1:this.currentFilter.concluidos.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // hijos
        if(this.currentFilter.hijos.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.hijos.id,values:{v1:this.currentFilter.hijos.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // estado
        if(this.currentFilter.estado.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.estado.id,values:{v1:this.currentFilter.estado.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // turno
        if(this.currentFilter.turno.value!=-1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.turno.id,values:{v1:this.currentFilter.turno.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // Perfil Administrativo        
        if(this.currentFilter.perfil1.value === "1" || this.currentFilter.perfil1.value === 1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.perfil1.id,values:{v1:this.currentFilter.perfil1.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // Perfil Comercial
        if(this.currentFilter.perfil2.value === "1" || this.currentFilter.perfil2.value === 1){            
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.perfil2.id,values:{v1:this.currentFilter.perfil2.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // Perfil Operativo
        if(this.currentFilter.perfil3.value === "1" || this.currentFilter.perfil3.value === 1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.perfil3.id,values:{v1:this.currentFilter.perfil3.value}};
            _filters.currentFilters.push(_filter);
        }
        
        // Perfil Servicio al cliente
        if(this.currentFilter.perfil4.value === "1" || this.currentFilter.perfil4.value === 1){
            var uuid = Math.uuid(5, 10);
            var _filter = {uuid:uuid,id:this.currentFilter.perfil4.id,values:{v1:this.currentFilter.perfil4.value}};
            _filters.currentFilters.push(_filter);
        }        
    };
    
    this.loadFilters = function(jsonFilters){
        _filters.currentFilters = [];
        jsonFilters.forEach(function(element){
            
            switch(String(element.id)){
                // edad mayor
                case _filtros.currentFilter.edad_mayor.id: 
                    $("#_mayor_a").val(element.values.v1); 
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.edad_mayor.value = element.values.v1;
                    break;
                // edad menor
                case _filtros.currentFilter.edad_menor.id: 
                    $("#_menor_a").val(element.values.v1);
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.edad_menor.value = element.values.v1;
                    break;
                // genero
                case _filtros.currentFilter.genero.id:
                    $('input[name="genero-input"][value="'+element.values.v1+'"]').trigger("click");
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.genero.value = element.values.v1;
                    break;
                // delegacion
                case _filtros.currentFilter.delegacion.id: 
                    $("#del_default").val(element.values.v1); 
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.delegacion.value = element.values.v1;
                    break;
                // licencia
                case _filtros.currentFilter.licencia.id:
                    $('input[name="licencia-input"][value="'+element.values.v1+'"]').trigger("click");
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.licencia.value = element.values.v1;
                    break;
                // estudios
                case _filtros.currentFilter.estudios.id: 
                    $("#est_default").val(element.values.v1); 
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.estudios.value = element.values.v1;
                    break;
                // concluidos
                case _filtros.currentFilter.concluidos.id:
                    $('input[name="concluidos-input"][value="'+element.values.v1+'"]').trigger("click");
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.concluidos.value = element.values.v1;
                    break;
                // hijos
                case _filtros.currentFilter.hijos.id: 
                    $("#hij_default").val(element.values.v1); 
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.hijos.value = element.values.v1;
                    break;
                // estado
                case _filtros.currentFilter.estado.id: 
                    $("#estado_default").val(element.values.v1); 
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.estado.value = element.values.v1;
                    break;
                // turno
                case _filtros.currentFilter.turno.id: 
                    $("#turno_default").val(element.values.v1); 
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.turno.value = element.values.v1;
                    break;
                // perfil administrativo
                case _filtros.currentFilter.perfil1.id:
                    if(element.values.v1 === "1" || element.values.v1 === 1){
                        $("#check-perfil1").prop('checked', true);
                    }else{
                        $("#check-perfil1").prop('checked', false);
                    }
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.perfil1.value = element.values.v1;
                    break;
                // perfil comercial
                case _filtros.currentFilter.perfil2.id:
                    if(element.values.v1 === "1" || element.values.v1 === 1){
                        $("#check-perfil2").prop('checked', true);
                    }else{
                        $("#check-perfil2").prop('checked', false);
                    }
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.perfil2.value = element.values.v1;
                    break;
                // perfil operativo
                case _filtros.currentFilter.perfil3.id:
                    if(element.values.v1 === "1" || element.values.v1 === 1){
                        $("#check-perfil3").prop('checked', true);
                    }else{
                        $("#check-perfil3").prop('checked', false);
                    }
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.perfil3.value = element.values.v1;
                    break;
                // perfil servicio al cliente
                case _filtros.currentFilter.perfil4.id:
                    if(element.values.v1 === "1" || element.values.v1 === 1){
                        $("#check-perfil4").prop('checked', true);
                    }else{
                        $("#check-perfil4").prop('checked', false);
                    }
                    _filters.currentFilters.push(element);
                    _filtros.currentFilter.perfil4.value = element.values.v1;
                    break;                
            }
        });
    };
};

var _citiesCatalog  = [];
var _studiesCatalog = [];

$(document).ready(function() {

    $.post("busquedas/getcities",function(response){
        var resp = jQuery.parseJSON(response);
        resp.forEach(function(element){
            _citiesCatalog.push(element);            
        });
    });
    
    $.post("busquedas/getstudies",function(response){
        var resp = jQuery.parseJSON(response);
        resp.forEach(function(element){
            var studie = { value:element.id, name:element.name };
            _studiesCatalog.push(studie); 
        });
    });
	
	$('.busquedas-panel').on('keyup', '.input-filter-number', function(){
		if(isNaN($(this).val())){
			$(this).val('');	
		}
	});	
    
});

$.ready()

_filtros = new filters();