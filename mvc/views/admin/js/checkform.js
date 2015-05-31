






function nofeedback(container, icon)
{
    container.removeClass("has-error");
    container.removeClass("has-success");
    container.removeClass("has-warning");
    container.removeClass("focusable");
    icon.removeClass("glyphicon-remove");
    icon.removeClass("glyphicon-ok");
    icon.removeClass("glyphicon-warning-sign")
    icon.tooltip('destroy');
    $("#bannermessage").empty();
}

function notify(objeto, mensaje)
{

    var object = objeto;
    var id = object.attr("id");
    var val = object.val();
    var nombre = object.attr("name");

    var containerparent = getContainer(object);
    var nextglyphicon = getIcon(object);


    nofeedback(containerparent, nextglyphicon);
    showwarning(containerparent, nextglyphicon);

    var mymessage = mensaje;
    showMessage(nextglyphicon, mymessage);



}

function startSubmit()
{


    var forminfo = '{';
    var first = true;



    $(".ftosend").each(function () {
        var object = $(this);
        var id = object.attr("id");
        var val = object.val();
        var outsidename = object.attr("outsidename");
        var nombre = object.attr("name");
        if (!first)
        {
            forminfo += ',';

        }
        forminfo += '"' + outsidename + '":"' + val + '"';
        first = false;

    });

    forminfo += '}';


    var jsondata = JSON.parse(forminfo);

    //alert(JSON.stringify(jsondata));

    return jsondata;

}

function showMessage(toolt, message)
{


    toolt.attr("title", message);
    toolt.attr("data-placement", "left");
    toolt.tooltip('show');



}

function showok(container, icon)
{
    container.addClass("has-success");
    icon.addClass("glyphicon-ok");

}

function showerror(container, icon)
{
    container.addClass("has-error");
    container.addClass("focusable");
    icon.addClass("glyphicon-remove");

}

function showwarning(container, icon)
{
    container.addClass("has-warning");
    container.addClass("focusable");
    icon.addClass("glyphicon-warning-sign");
}

function getContainer(actual)
{
    return actual.parent("div");

}


function getIcon(actual)
{
    return actual.next("span");

}

function validatepasswords()
{
    // alert("validando password");
    var success = true;
    var object1 = $(".fpass1:first");
    var object2 = $(".fpass2:first");

    var val1 = object1.val();
    var val2 = object2.val();

    var containerparent2 = getContainer(object2);
    var nextglyphicon2 = getIcon(object2);


    nofeedback(containerparent2, nextglyphicon2);

    if (val1 !== val2)
    {
        var mymessage = "Las contraseñas no coinciden";
        showMessage(nextglyphicon2, mymessage);
        showerror(containerparent2, nextglyphicon2);
        success = false;



    }
    else
    {
        showok(containerparent2, nextglyphicon2);

    }
    return success;

}


function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}


function validateemail()
{
    var success = true;
    $(".femail").each(function () {
        // alert("revisando mail");
        var object = $(this);
        var id = object.attr("id");
        var val = object.val();
        var nombre = object.attr("name");

        var containerparent = getContainer(object);
        var nextglyphicon = getIcon(object);

        nofeedback(containerparent, nextglyphicon);

        var resp = isValidEmailAddress(val);

        if (!resp)
        {
            success = false;
            var mymessage = "Ingrese un E-mail valido";
            showMessage(nextglyphicon, mymessage);
            showerror(containerparent, nextglyphicon);



        }
        else
        {
            showok(containerparent, nextglyphicon);

        }




    });
    return success;
}

function standardvalidation()
{
    var passed = true;
    if (!validaterequired())
        passed = false;
    if (!validateemail())
        passed = false;
    if (!validatepasswords())
        passed = false;

    if(!passed)returntop();

    return passed;


}

function returntop()
{
    var firstcontainererror= $(".focusable").first();
    var firstinputerror=firstcontainererror.children("input");
    
    
    $('.mastercontainer').first().animate({    
        
        
         scrollTop: (firstinputerror.offset().top)
    }, 500,function(){
        firstinputerror.focus()
        
    });


}

function validaterequired()
{

    var success = true;
    $(".frequired").each(function () {

        var object = $(this);
        var id = object.attr("id");
        var val = object.val();
        var nombre = object.attr("name");

        var containerparent = getContainer(object);
        var nextglyphicon = getIcon(object);

        nofeedback(containerparent, nextglyphicon);


        if (val.length === 0 || val === null)
        {
            var mymessage = "El campo " + nombre + " no puede ir vacio";
            showMessage(nextglyphicon, mymessage);
            showerror(containerparent, nextglyphicon);
            success = false;
        }
        else
        {
            showok(containerparent, nextglyphicon);

        }

    });

    return success;
}