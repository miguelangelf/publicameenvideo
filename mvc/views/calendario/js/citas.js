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



$(document).ready(function(){
    $('.box').quickFlip();
    
    $("#calendar-day-confirmation-btn").on("click",function(){
        $("#calendar-day-confirmation-btn").hide();
        $("#calendar-day-confirmation-btn").before("<span>Agendando..</span>");
        var value = $("#Option-"+currentPos+" .back-flip select").val();
        var data  = {valuedate:value,type:"CDD"};
        $.post("/candidato/scheduler/schedule",data,function(response){
            var _resp = jQuery.parseJSON(response);
            if(_resp.response=="OK"){                
                window.location = "/candidato/comprobante";
            }else{
                $("#calendar-day-confirmation-btn").show();
                $("#calendar-day-warning").show();
            }
        });
    });
    
    $("#calendar-day-modal").on("click",function(){
        $("#calendar-day-warning").hide();
        if(currentPos !== null){
            //$("#calendar-day-confirmation-btn").show();
            var date  = $("#Option-"+currentPos+" .back-flip .calendar-day-header").html();
            var value = $("#Option-"+currentPos+" .back-flip select").val();
            var time  = $("#Option-"+currentPos+" .back-flip select option:selected").attr("label-text");
            $("#calendar-day-final-schedule").html("Desea confirmar su cita para el día "+date+" a las "+time+" horas.");
        }else{
            $("#calendar-day-confirmation-btn").hide();
            $("#calendar-day-final-schedule").html("No tiene ningun día seleccionado, favor de seleccionar un día y un horario.");
        }
    });
});