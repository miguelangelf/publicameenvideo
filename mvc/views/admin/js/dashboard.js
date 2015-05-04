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


var path;


//ELIMINAR-BLOQUEAR-APROBAR
var listofitems = [];
var action;
var origin;

var modaloption;



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
        // createdatapage();
        Notify.refresh();
        //SendData();
        hidelist();
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

function hidelist()
{
    for (var i = 0; i < listofitems.length; i++)
    {

        var nameelement = "#tr" + listofitems[i];

        $(nameelement).remove();

    }


}


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


    this.preparemodal = function (picked)
    {

        GetChecked();

        modaloption = picked;


        switch (pagename)
        {

            case "Usuarios":
                //  alert("usuarios");

                switch (modaloption)
                {
                    case 1:
                        $('#modallabel').html("Eliminar Usuario");
                        $('#modalmessage').html("¿Esta seguro que desa ELIMINAR los siguientes usuarios?");
                        break;
                    case 2:
                        $('#modallabel').html("Eliminar Usuario");
                        $('#modalmessage').html("¿Esta seguro que desa eliminar este usuario?");
                        break;
                    case 3:
                        $('#modallabel').html("Suspender Usuario");
                        $('#modalmessage').html("¿Esta seguro que desa suspender este usuario?");
                        break;


                }
                break;

            case "Empresas":
                // alert("empresas"); 
                switch (modaloption)
                {
                    case 1:
                        // alert("jodertio");
                        $('#modallabel').html("Eliminar Empresa");
                        $('#modalmessage').html("¿Esta seguro que desea ELIMINAR las siguientes empresas?");
                        break;
                    case 2:
                        $('#modallabel').html("Bloquear Empresa");
                        $('#modalmessage').html("¿Esta seguro que desa bloquear esta empresa?");
                        break;
                    case 3:
                        $('#modallabel').html("Eliminar Empresa");
                        $('#modalmessage').html("¿Esta seguro que desa eliminar esta empresa?");
                        break;


                }
                break;

            case "Videos":
                // alert("empresas"); 
                switch (modaloption)
                {
                    case 1:
                        //  alert("entro a eliminar video");
                        $('#modallabel').html("Eliminar VID");
                        $('#modalmessage').html("¿Esta seguro que desea ELIMINAR los siguientes videos?");
                        break;
                    case 2:
                        $('#modallabel').html("Bloquear VID");
                        $('#modalmessage').html("¿Esta seguro que desa bloquear esta empresa?");
                        break;
                    case 3:
                        $('#modallabel').html("Eliminar VID que desa eliminar esta empresa?");
                        break;


                }
                break;


        }


        $('#genericmodal').modal('show');



    };

    this.shvideo = function (id, src)
    {

        path = src;
        path += "como.mp4";

        //alert(path);

        $('#ModalVid').modal('show');

        // $("#videospace").attr("src", path)


    };

    this.upload = function ()
    {
        $('#upvideo').modal('show');

    };

    this.makechange = function ()
    {
        //1 Elimina
        //2 Bloquea
        action = modaloption;

        $('#genericmodal').modal('hide');



        switch (pagename)
        {

            case "Usuarios":
                urlaux = "/site/admin/delusuarios";
                break;

            case "Empresas":
                urlaux = "/site/admin/delempresas";
                break;

            case "Videos":
                urlaux = "/site/admin/delvideos";
                break;
        }
        // hidelist();
        createdatamanage();
        JustSend();
    };


    this.insertuser = function ()
    {


        var namemodal = "#inuser";
        $(namemodal).modal('show');
        $("#mybanner").hide();


        var pagetosend = "/site/admin/getitemstoinsertuser";

        var datatosend = {
            table: "status"
        };


        $.post(pagetosend, datatosend, function (response) {
           $(".emptyform").html(response);
        });




    };



    this.insertcompany = function ()
    {
        var namemodal = "#incompany";
        $(namemodal).modal('show');

        $("#mybanner").hide();


        var pagetosend = "/site/admin/getitemstoinsertcompany";

        var datatosend = {
            table: "status"
        };


        $.post(pagetosend, datatosend, function (response) {
            $(".emptyform").html(response);
        });




    };


    this.insertvideo = function ()
    {

        var namemodal = "#invideo";
        $(namemodal).modal('show');
        $("#mybanner").hide();


        var pagetosend = "/site/admin/getitemstoinsertvideo";

        var datatosend = {
            table: "status"
        };


        $.post(pagetosend, datatosend, function (response) {
            $(".emptyform").html(response);
        });




    };


};






var Comm;
var Change;
var Notify;

$(document).ready(function () {
    console.log("Loaded");
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });


    Comm = new Communicator();
    Notify = new Notifications();
    Change = new CRUD();

    Comm.load('usuarios');
    Notify.refresh();
    search = "";




});




/*jslint unparam: true */
/*global window, $ */





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

function showvideo(id, dir)
{
    //alert("WOLOLO");
    path = dir;
    path += "como.mp4";

    alert(path);

    $('#ModalVid').modal('show');
    $('#videospace').attr('src', path);

}


function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}
;

function returntop()
{
    $('#inuser').animate({
       // scrollTop: ($('#fcnombre').offset().top)
    }, 500);


}


function validateformuser()
{
    var unombre = $("#funombre").val();
    var uapellido = $("#fuapellido").val();
    var ucon = $("#fucon").val();
    var ucon2 = $("#fucon2").val();
    var uemail = $("#fuemail").val();
    var usexo = $("#fusexo").val();
    var ucumple = $("#birthpicker").val();
    var uexpiration = $("#expirationpicker").val();
    var urol = $("#furol").val();
    var uplan = $("#fuplan").val();
    var ustatus = $("#fustatus").val();
    var photo = $("#photoname").val();



    $("#bannermessage").empty();

    if (unombre == null || unombre.length == 0)
    {
        $("#bannermessage").html("Nombre vacio <br/>");
        $("#mybanner").show();
        returntop();

        $('#funombre').focus();
        return false;
    }

    if (uapellido == null || uapellido.length == 0)
    {
        $("#bannermessage").html("Apellido vacio <br/>");
        $("#mybanner").show();

        returntop();
        $('#fuapellido').focus();
        return false;
    }
    if (ucon == null || ucon.length == 0)
    {
        $("#bannermessage").html("Contraseña Vacia <br/>");
        $("#mybanner").show();

        returntop();
        $('#fucon').focus();
        return false;
    }
    if (ucon2 != ucon)
    {
        $("#bannermessage").html("Las contraseñas no coinciden <br/>");
        $("#mybanner").show();

        returntop();
        $('#fucon2').focus();
        return false;
    }

    if (uemail == null || uemail.length == 0)
    {
        $("#bannermessage").html("Email vacia <br/>");
        $("#mybanner").show();

        returntop();
        $('#fuemail').focus();
        return false;
    }
    else
    {
        if (!isValidEmailAddress(uemail)) {
            $("#bannermessage").html("NO ES EMAIL <br/>");
            $("#mybanner").show();

            returntop();
            $('#fuemail').focus();
            return false;
        }
        ;

    }
    if (ucumple == null || ucumple.length == 0)
    {
        $("#bannermessage").html("Fecha de Cumpleaños Vacia <br/>");
        $("#mybanner").show();

        $('#birthpicker').focus();
        returntop();
        return false;
    }
    if (uexpiration == null || uexpiration.length == 0)
    {
        $("#bannermessage").html("Fecha de Expiracion vacia <br/>");
        $("#mybanner").show();

        $('#expirationpicker').focus();
        returntop();
        return false;
    }

    if (photo == null || photo.length == 0)
    {
        $("#bannermessage").html("Seleccione una Foto <br/>");
        $("#mybanner").show();


        returntop();
        return false;
    }



    $("#bannermessage").html("Everythings looks ok <br/>");
    var pic = $("#photoname").val();

    var infouser = {
        gender: usexo,
        birthdate: ucumple,
        name: unombre,
        lastname: uapellido,
        email: uemail,
        role: urol,
        plan: uplan,
        expiration: uexpiration,
        status: ustatus,
        password: ucon,
        photo: pic

    };

    $.post("/site/admin/insertusarios", infouser, function (response) {
        $('#inuser').modal('hide');

    });




}





function validateformcompany()
{
    var fnombre = $("#fcnombre").val();
    var fcon = $("#fccon").val();
    var fcon2 = $("#fccon2").val();
    var femail = $("#fcemail").val();
    var frfc = $("#fcrfc").val();
    var fdireccion = $("#fcdireccion").val();
    var fdescripcion = $("#fcdescripcion").val();
    var ftelefono = $("#fctelefono").val();
    var photo = $("#photoname").val();



    $("#bannermessage").empty();

    if (fnombre == null || fnombre.length == 0)
    {
        $("#bannermessage").html("Nombre vacio <br/>");
        $("#mybanner").show();
        returntop();

        $('#fcnombre').focus();
        return false;
    }

    
    if (fcon == null || fcon.length == 0)
    {
        $("#bannermessage").html("Contraseña Vacia <br/>");
        $("#mybanner").show();

        returntop();
        $('#fccon').focus();
        return false;
    }
    if (fcon != fcon2)
    {
        $("#bannermessage").html("Las contraseñas no coinciden <br/>");
        $("#mybanner").show();

        returntop();
        $('#fccon2').focus();
        return false;
    }
    
    
    if (frfc == null || frfc.length == 0)
    {
        $("#bannermessage").html("RFC vacio <br/>");
        $("#mybanner").show();

        returntop();
        $('#fcrfc').focus();
        return false;
    }
    
    
    
    if (fdireccion == null || fdireccion.length == 0)
    {
        $("#bannermessage").html("Direccion vacia <br/>");
        $("#mybanner").show();

        returntop();
        $('#fcdireccion').focus();
        return false;
    }
    
    
    if (fdescripcion == null || fdescripcion.length == 0)
    {
        $("#bannermessage").html("Descripcion vacia <br/>");
        $("#mybanner").show();

        returntop();
        $('#fcdescripcion').focus();
        return false;
    }
    
    
    
    
    
    if (ftelefono == null || ftelefono.length == 0)
    {
        $("#bannermessage").html("Telefono vacio <br/>");
        $("#mybanner").show();

        returntop();
        $('#fctelefono').focus();
        return false;
    }
    
    
    
    
    
    

    if (femail == null || femail.length == 0)
    {
        $("#bannermessage").html("Email vacia <br/>");
        $("#mybanner").show();

        returntop();
        $('#fcemail').focus();
        return false;
    }
    else
    {
        if (!isValidEmailAddress(femail)) {
            $("#bannermessage").html("NO ES EMAIL <br/>");
            $("#mybanner").show();

            returntop();
            $('#fuemail').focus();
            return false;
        }
        ;

    }
    
    

    if (photo == null || photo.length == 0)
    {
        $("#bannermessage").html("Seleccione una Foto <br/>");
        $("#mybanner").show();


        returntop();
        return false;
    }



    $("#bannermessage").html("Everythings looks ok <br/>");
    var pic = $("#photoname").val();

    var infouser = {
        name: fnombre,
        rfc: frfc,
        address: fdireccion,
        descripcion: fdescripcion,
        phone: ftelefono,
        email: femail,
        password: fcon,
        photo:pic
        

    };

    $.post("/site/admin/insertempresas", infouser, function (response) {
        $('#incompany').modal('hide');

    });




}





