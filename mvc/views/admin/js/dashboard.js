var url;
var data;
var categoria;
var miinbox;
var buscador;
var auxpage;
var maxpage;
function SendData()
{

    $.post(url, data, function (response) {
        $("#content-panel").html(response);
        refreshnum();
       // alert(response);
    });
    
    
    
}

function refreshnum()
{
    var mypagenumber=auxpage;
    mypagenumber++;
    $('.numpage').html("Página "+mypagenumber)
    
    if(mypagenumber==1)
    {
        $('.masicono').hide();
        
    }
    else
    {
        $('.masicono').show();
        
    }
    
}



var Communicator = function () {

    this.load = function (page) {

        buscador="";
        switch (page) {
            case "usuarios":
                $(".menuitem").attr('class', 'menuitem');
                $("#usuarios").attr('class', 'menuitem active');
                auxpage=0;
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
                auxpage=0;
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
                auxpage=0;
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
           switch(numberpage)
        {
            case "menos": auxpage-=1; break;
            case "mas": auxpage+=1; break;
            
        }
        
        
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
        auxpage=0;
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
        auxpage=0;
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


      google.load("visualization", "1", {packages:["geochart"]});
      google.setOnLoadCallback(drawRegionsMap);

      function drawRegionsMap() {

        var data = google.visualization.arrayToDataTable([
          ['Country', 'Popularity'],
          ['Germany', 0],
          ['United States', 0],
          ['Brazil', 0],
          ['Canada', 0],
          ['Mexico',700],
          ['France', 0],
          ['RU', 0]
        ]);

        var options = {};

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
      }