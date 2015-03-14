var Communicator = function(){
    
    this.load = function(page){
        switch(page){
            case "usuarios":
                $("#title-panel").html("Panel de Usuarios");
                //content-panel
                var url = "/site/admin/usuarios";
                data = {
                    inbox:55
                };
                $.post(url,data,function(response){
                    $("#content-panel").html(response);
                });
                break;
            
        }
    };
};

var Comm;

$(document).ready(function () {
    console.log("Loaded");
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });
    Comm = new Communicator();
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

