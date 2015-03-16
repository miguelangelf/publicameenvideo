var url;
var data;
var categoria;
var miinbox;
var buscador;
var auxpage;
function SendData()
{

    $.post(url, data, function (response) {
        $("#content-panel").html(response);
    });
}




var Communicator = function () {

    this.load = function (page) {

        buscador="";
        switch (page) {
            case "usuarios":
                $(".menuitem").attr('class', 'menuitem');
                $("#usuarios").attr('class', 'menuitem active');
                auxpage='0';
                url = "/site/admin/usuarios";
                miinbox = 'Usuarios';
                categoria = auxpage;
                data = {
                    inbox: miinbox,
                    next: auxpage,
                    cat: categoria,
                     message:buscador
                };
                SendData();
                break;
            case "empresas":
                $(".menuitem").attr('class', 'menuitem');
                $("#empresas").attr('class', 'menuitem active');
                url = "/site/admin/empresas";
                miinbox = 'Empresas';
                auxpage='0';
                categoria = '0';
                data = {
                    inbox: miinbox,
                    next: auxpage,
                    cat: categoria,
                     message:buscador
                };
                SendData();
                break;
            case "videos":
                $(".menuitem").attr('class', 'menuitem');
                $("#videos").attr('class', 'menuitem active');
                url = "/site/admin/videos";
                miinbox = 'Videos';
                auxpage='0';
                categoria = '0';
                data = {
                    inbox: miinbox,
                    next: auxpage,
                    cat: categoria,
                     message:buscador
                };
                SendData();
                break;
        }
    };


    this.changepage = function (numberpage)
    {
       auxpage=numberpage;
        data = {
            inbox: miinbox,
            next: auxpage,
            cat: categoria,
            message:buscador
        };
        SendData();

    };

    this.changecategory = function (category)
    {
        categoria = category;
        auxpage='0';
        data = {
            inbox: miinbox,
            next: auxpage,
            cat: categoria,
            message:buscador
        };
        SendData();

    };
    
    this.search =function (valor)
    {
        buscador=valor.value;
        data = {
            inbox: miinbox,
            next: auxpage,
            cat: categoria,
            message:buscador
        };
        SendData();
        
    };

};
var Comm;
$(document).ready(function () {
    console.log("Loaded");
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });
    Comm = new Communicator();
    Comm.load('usuarios');
    buscador="";


});

