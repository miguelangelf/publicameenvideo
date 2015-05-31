
var selectSpecificService = function ()
{
    var url;
    var id;

    this.send = function ()
    {
        var iddata = {
            id: this.id

        };
        var context = this;
        $.post(url, iddata, function (response) {
            context.loadinfo(response);
        });

    };

    this.loadinfo = function (response)
    {
        $(".containerinfo").html(response);

    }

    this.usuario = function (id)
    {
        url = "/site/admin/getmoreofuser";
        this.id = id;
        this.send();

    };

    this.empresa = function (id)
    {
        url = "/site/admin/getmoreofcompany";
        this.id = id;
        this.send();
    };

    this.video = function (id)
    {
        url = "/site/admin/getmoreofvideo";
        this.id = id;
        this.send();

    };

};

var insertService = function ()
{
    var url;
    var formdata;


    this.dictionary = function (response)
    {

        var context = this;
        //  alert(response);
        var criticalerror = "Error critico, La operación no logro terminarse exitosamente";
        var jsonResponse;


        try
        {
            jsonResponse = $.parseJSON(response);
        }
        catch (e)
        {
            alert(criticalerror);
            return false;
        }


        if (jsonResponse.length === 0)
        {
            alert(criticalerror);
            return false;
        }


        for (var i = 0; i < jsonResponse.length; i++)
        {

            switch (jsonResponse[i])
            {
                case "E1":
                    notify($(".femail").first(), "Este correo ya ha sido previamente registrado");
                    break;
                case "F1":
                    notify($(".ffile").first(), "Archivo no soportado o dañado");
                    break;
                case "OK":

                    return true;


                    break;
            }
        }

        return false;


    };

    this.send = function (url, formdata)
    {
        var context = this;
        $.post(url, formdata, function (response) {
            var r = context.dictionary(response);
            if (r)
            {
                context.refreshpage();
                $('.mastercontainer').modal('hide');
                
            }

        });

    };

    this.refreshpage = function ()
    {
        SelectAll.send();
        Notify.refresh();

    };


    this.insertusuarios = function () {

        url = "/site/admin/insertusarios";
        if (!standardvalidation())
            return false;
        formdata = startSubmit();
        this.send(url, formdata);
    };

    this.insertempresas = function () {
        url = "/site/admin/insertempresas";
        if (!standardvalidation())
            return false;
        formdata = startSubmit();
        this.send(url, formdata);
    };

    this.insertvideo = function () {
        url = "/site/admin/insertvideo";
        if (!standardvalidation())
            return false;
        formdata = startSubmit();
        this.send(url, formdata);
    };

    this.modaluser = function ()
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


    this.modalempresa = function ()
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


    this.modalvideo = function ()
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

var selectAllService = function ()
{
    var url;
    var pagename;
    var maxitems;
    var searchdata;
    var category;
    var actualpage;
    var submitdata;

    this.preparedata = function ()
    {
        this.submitdata = {
            pagename: this.pagename,
            maxitems: this.maxitems,
            search: this.searchdata,
            category: this.category,
            actualpage: this.actualpage

        };

    };




    this.send = function () {

        this.preparedata();
        var context = this;
        $.post(this.url, this.submitdata, function (response) {
            context.loadinfo(response);
        });
    };


    this.loadinfo = function (response)
    {
        $("#content-panel").html(response);

        $("#buscador").focus();
        $("#buscador").val(this.searchdata);
        Notify.refresh();
        this.updatepagenumber();



    };


    this.categoria = function (category)
    {
        this.category = category;
        this.actualpage = 0;
        this.searchdata = "";
        this.send();


    };

    this.updatepagenumber = function ()
    {
        $(".numpage").html(this.actualpage + 1);
    }


    this.changepage = function (option)
    {
        switch (option)
        {
            case "+":
                this.actualpage += 1;
                break;
            case "-":
                this.actualpage -= 1;
                break;

        }
        this.send();

    };


    this.usuarios = function ()
    {
        this.url = "/site/admin/usuarios";
        this.pagename = "Usuarios";
        this.searchdata = "";
        this.category = 0;
        this.actualpage = 0;
        this.maxitems = 20;
        $(".menuitem").removeClass('active');
        $("#usuarios").addClass('active');
        this.send();
    };

    this.empresas = function ()
    {
        this.pagename = "Empresas";
        this.url = "/site/admin/empresas";
        this.searchdata = "";
        this.category = 0;
        this.actualpage = 0;
        this.maxitems = 20;
        $(".menuitem").removeClass('active');
        $("#empresas").addClass('active');
        this.send();
    };

    this.videos = function ()
    {

        this.url = "/site/admin/videos";
        this.pagename = "Videos";
        this.searchdata = "";
        this.category = 0;
        this.actualpage = 0;
        this.maxitems = 20;
        $(".menuitem").removeClass('active');
        $("#videos").addClass('active');
        this.send();
    };

    this.search = function (valor)
    {
        this.actualpage = 0;
        this.searchdata = valor.value;
        this.send();

    };


};

var Notifications = function ()
{

    this.refresh = function () {
        var noti = this;

        $.post("/site/admin/notificaciones", null, function (response) {

            var jsonResponse = $.parseJSON(response);

            noti.no_users = jsonResponse.no_users;
            noti.no_unapproved = jsonResponse.no_unapproved;
            noti.no_companies = jsonResponse.no_companies;
            $("#user-notifications-number").html(noti.no_users);
            $("#video-notifications-number").html(noti.no_unapproved);
            $("#companies-notifications-number").html(noti.no_companies);
        });

    };


};

var ChangeService = function ()
{

    var url;
    var list;
    var option;

    this.initializelist = function ()
    {
        list = [];
    };

    this.send = function ()
    {
        var context = this;
        var data = {
            elements: JSON.stringify(list),
            action: this.option
        };

        $.post(this.url, data, function (response) {
            context.refreshpage();
            $('#genericmodal').modal('hide');

        });

    };

    this.refreshpage = function ()
    {
        SelectAll.send();
        Notify.refresh();

    };

    this.getchecked = function ()
    {

        list.length = 0;
        $('.modallist').empty();
        $('.modalsend').show();

        var vacio = true;

        $('.checkbox').each(function (index, value) {
            if ($(this).is(':checked')) {
                var theid = $(this).attr("mainid");
                list.push(theid);

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

    };

    this.usuario = function (option)
    {
        this.option = option;
        this.getchecked();
        $('#genericmodal').modal('show');
        this.url = "/site/admin/delusuarios";
        //Bloquear, eliminar, activar, etc... se relizan enviando la misma listas al mismo metodo
        //se diferencia mediante la variable option
        switch (option)
        {
            case 1:
                $('#modallabel').html("Eliminar Usuario");
                $('#modalmessage').html("¿Esta seguro que desa eliminar los siguientes usuarios?");

                break;
            case 2:
                $('#modallabel').html("*** Usuario");
                $('#modalmessage').html("¿Esta seguro que desa *** los siguientes usuarios?");

                break;
            case 3:
                $('#modallabel').html("*** Usuario");
                $('#modalmessage').html("¿Esta seguro que desa *** los siguientes usuarios?");

                break;
        }
    };

    this.empresa = function (option)
    {
        this.option = option;
        this.getchecked();
        $('#genericmodal').modal('show');

        this.url = "/site/admin/delempresas";
        switch (option)
        {
            case 1:
                // alert("jodertio");
                $('#modallabel').html("Eliminar Empresa");
                $('#modalmessage').html("¿Esta seguro que desea eliminar las siguientes empresas?");
                break;
            case 2:
                $('#modallabel').html("*** Empresa");
                $('#modalmessage').html("¿Esta seguro que desa bloquear *** las siguientes empresas?");
                break;
            case 3:
                $('#modallabel').html("*** Empresa");
                $('#modalmessage').html("¿Esta seguro que desa *** las siguientes empresas?");
                break;


        }

    };

    this.video = function (option)
    {
        this.option = option;
        this.getchecked();
        $('#genericmodal').modal('show');

        this.url = "/site/admin/delvideos";
        switch (option)
        {
            case 1:
                //  alert("entro a eliminar video");
                $('#modallabel').html("Eliminar Video");
                $('#modalmessage').html("¿Esta seguro que desea eliminar los siguientes videos?");
                break;
            case 2:
                $('#modallabel').html("*** video");
                $('#modalmessage').html("¿Esta seguro que desa *** los siguientes videos?");
                break;
            case 3:
                $('#modallabel').html("*** video");
                $('#modalmessage').html("¿Esta seguro que desa *** los siguientes videos?");
                break;


        }

    };








    this.makechange = function ()
    {
        this.send();
    };



};



var Notify;
var Insert;
var SelectSpecific;
var SelectAll;
var Change;

$(document).ready(function () {
    console.log("Loaded");
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });

    Notify = new Notifications();
    Change = new ChangeService();
    Change.initializelist();
    Insert = new insertService();
    SelectSpecific = new selectSpecificService();
    SelectAll = new selectAllService();
    SelectAll.usuarios();

});


