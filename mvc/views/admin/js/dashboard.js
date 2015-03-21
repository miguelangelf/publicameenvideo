var url;
var data;
var categoria;
var miinbox;
var buscador;
var auxpage;
var maxpage;

function CreateData()
{
    data = {
                    inbox: miinbox,
                    next: auxpage,
                    cat: categoria,
                     message:buscador
                };
    
    
}

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

function DeleteUsers()
{
    $('.listtodelete').empty();
     $('.okdelete').show();
    var vacio=true;
    
    $('.eliminar').each(function(index,value){
        
        if($(this).is(':checked')) { 
           
            var info=$(this).val();
            $('.listtodelete').append(info);
            $('.listtodelete').append("<br>");
            vacio=false;
            
        } else {    
        } 
        
        
        
    });
    
    if(vacio)
    {
        $('.listtodelete').append("No hay elementos a borrar");
        $('.okdelete').hide();
        
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
                CreateData();
                SendData();
                break;
            case "empresas":
                $(".menuitem").attr('class', 'menuitem');
                $("#empresas").attr('class', 'menuitem active');
                url = "/site/admin/empresas";
                miinbox = 'Empresas';
                auxpage=0;
                categoria = '0';
                CreateData();
                SendData();
                break;
            case "videos":
                $(".menuitem").attr('class', 'menuitem');
                $("#videos").attr('class', 'menuitem active');
                url = "/site/admin/videos";
                miinbox = 'Videos';
                auxpage=0;
                categoria = '0';
                CreateData();
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
        
        CreateData();
        SendData();

    };

    this.changecategory = function (category)
    {
        categoria = category;
        auxpage=0;
        CreateData();
        SendData();

    };
    
    this.search =function (valor)
    {
        auxpage=0;
        buscador=valor.value;
        CreateData();
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

/*
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
      
      */