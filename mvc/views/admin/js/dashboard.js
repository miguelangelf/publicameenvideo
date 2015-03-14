$(document).ready(function () {
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });




});

function solicitud()
{
   
   $.ajax({
        url: "dashboard",
        type: "post",
        data: "yolo=nolose",
        success: function(){
            alert("success");
        },
        error:function(){
            alert("failure");
        }
    });
   
   
}

