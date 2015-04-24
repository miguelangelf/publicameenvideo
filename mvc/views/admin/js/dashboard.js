var url;
var data;
var urlaux;


//SOLICITAR PAGINA------------------

var pagename;
var maxitems;
var searchdata;
var category;
var actualpage;

var file;




//ELIMINAR-BLOQUEAR-APROBAR
var listofitems = [];
var action;
var origin;



function createdatapage()
{
    data = {
        pagename: pagename,
        maxitems: maxitems,
        search: searchdata,
        category: category,
        actualpage: actualpage

    };

}

function createdatamanage()
{
    data = {
        elements: JSON.stringify(listofitems),
        action: action

    };
}


function GetChecked()
{
    listofitems.length = 0;
    $('.modallist').empty();
    $('.modalsend').show();

    var vacio = true;

    $('.checkbox').each(function (index, value) {
        if ($(this).is(':checked')) {
            var theid = $(this).attr("mainid");
            listofitems.push(theid);

            var info = $(this).val();


            $('.modallist').append(info);
            $('.modallist').append("<br>");
            vacio = false;

        } else {
        }
    });



    if (vacio)
    {
        $('.modallist').append("No hay elementos seleccionados");
        $('.modalsend').hide();

    }
    else
    {


    }
}



function SetPageNumber()
{
    var mypagenumber = actualpage;
    mypagenumber++;
    $('.numpage').html("Página " + mypagenumber);


}


function JustSend()
{
    $.post(urlaux, data, function (response) {
        createdatapage();
        SendData();
    });
}

function SendData()
{

    $.post(url, data, function (response) {
        $("#content-panel").html(response);
        SetPageNumber();
        Notify.refresh();
        $("#buscador").focus();
        $("#buscador").val(searchdata);

    });

}

//CORRECTO




var Notifications = function () {
    // this.no_users;

    this.refresh = function () {
        var parent = this;
        $.post("/site/admin/notificaciones/usuarios", null, function (response) {
            var jsonResponse = $.parseJSON(response);
            parent.no_users = jsonResponse.no_users;
            parent.no_unapproved = jsonResponse.no_unapproved;
            parent.no_companies = jsonResponse.no_companies;
            $("#user-notifications-number").html(parent.no_users);
            $("#video-notifications-number").html(parent.no_unapproved);
            $("#companies-notifications-number").html(parent.no_companies);
        });

    };
};

var Communicator = function () {

    this.load = function (page) {

        searchdata = "";
        category = 0;
        actualpage = 0;
        maxitems = 0;


        switch (page) {
            case "usuarios":
                $(".menuitem").attr('class', 'menuitem');
                $("#usuarios").attr('class', 'menuitem active');
                url = "/site/admin/usuarios";
                pagename = 'Usuarios';
                createdatapage();
                SendData();
                break;


            case "empresas":
                $(".menuitem").attr('class', 'menuitem');
                $("#empresas").attr('class', 'menuitem active');
                url = "/site/admin/empresas";
                pagename = 'Empresas';
                createdatapage();
                SendData();
                break;

            case "videos":
                $(".menuitem").attr('class', 'menuitem');
                $("#videos").attr('class', 'menuitem active');
                url = "/site/admin/videos";
                pagename = 'Videos';
                createdatapage();
                SendData();
                break;
        }
    };




    this.changepage = function (numberpage)
    {

        switch (numberpage)
        {
            case "menos":
                actualpage -= 1;
                break;
            case "mas":
                actualpage += 1;
                break;

        }

        createdatapage();
        SendData();

    };




    this.changecategory = function (categoria)
    {
        category = categoria;
        actualpage = 0;
        searchdata = "";
        createdatapage();
        SendData();

    };

    this.search = function (valor)
    {
        actualpage = 0;
        searchdata = valor.value;
        createdatapage();
        SendData();

    };

};

var CRUD = function ()
{

    this.makechange = function (page, option)
    {
        //1 Elimina
        //2 Bloquea
        action = option;


        if (option == 1)
        {

            $('#myModaldelete').modal('hide');


        }
        else if (option == 2)
        {
            $('#myModalBlock').modal('hide');

        }


        switch (page)
        {

            case 1:

                urlaux = "/site/admin/checkusuarios";

                break;

            case 2:
                urlaux = "/site/admin/checkempresas";

                break;

            case 3:
                urlaux = "/site/admin/checkvideos";


                break;
        }
        action = option;
        createdatamanage();
        JustSend();
    };
};

var ItsFIle = function ()
{

    this.sub = function ()
    {
        
        var lefile=$("#fileToUpload").valueOf();
        var rfile=lefile.files[0];
        alert(rfile.name);


    };

};



var Comm;
var Change;
var Files;
var Notify;

$(document).ready(function () {
    console.log("Loaded");
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });
    
    
    Comm = new Communicator();
    Notify = new Notifications();
    Change = new CRUD();
    Files = new ItsFIle();
  
    Comm.load('usuarios');
    Notify.refresh();
    search = "";
});









function redirect()
{
    document.getElementById('my_form').target = 'my_iframe';
    document.getElementById('my_form').submit();
    

}



function SubirFile()
{
    
    var selectedFile = $('#input').get(0).files[0];
    var numFiles = files.selectedFile;
    echo(numFiles);
}