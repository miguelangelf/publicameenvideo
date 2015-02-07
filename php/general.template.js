var geocoder;
var map;
var x = document.getElementById('geolocation');
var iconBase = _THEME-DOMAIN_ + 'images/';
var jsonBase = _CORE-DOMAIN_ + 'web/jobmarkers/';
var infoWindow;
var markers = [];
//var _markers = [];
var currentJob = 0;
var markerCluster

function formatMoney(n, c, d, t){
var c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };


var icons = {
    current: {
        icon: iconBase + 'current.gif'
    },
    job: {
        icon: iconBase + 'marker.png'
    },
    selected: {
        icon: iconBase + 'uggi_job.png'
    }
};

var styles = [{"stylers": [{"saturation": -100}]}, {"featureType": "water", "elementType": "geometry.fill", "stylers": [{"color": "#0099dd"}]}, {"elementType": "labels", "stylers": [{"visibility": "off"}]}, {"featureType": "poi.park", "elementType": "geometry.fill", "stylers": [{"color": "#aadd55"}]}, {"featureType": "road.highway", "elementType": "labels", "stylers": [{"visibility": "on"}]}, {"featureType": "road.arterial", "elementType": "labels.text", "stylers": [{"visibility": "on"}]}, {"featureType": "road.local", "elementType": "labels.text", "stylers": [{"visibility": "on"}]}, {}];

function initialize_map() {
    var myOptions = {
        zoom: 4,
        mapTypeControl: false,
        //mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
        navigationControl: true,
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    map.setOptions({styles: styles});
    
    markerCluster = new MarkerClusterer(map);
    //alert(markerCluster.getStyles());
    var clusterStyles = [
        {
          textColor: 'white',
          url: iconBase+'m1.png',
          height: 52,
          width: 53
        },
       {
          textColor: 'white',
          url: iconBase+'m2.png',
          height: 55,
          width: 56
        },
       {
          textColor: 'white',
          url: iconBase+'m3.png',
          height: 66,
          width: 65
        },
       {
          textColor: 'white',
          url: iconBase+'m4.png',
          height: 78,
          width: 77
        }
      ];
    markerCluster.setStyles(clusterStyles);
    addMarkers("json");
    
}

function addMarkers(jsonFile) {

    $.getJSON(jsonBase+jsonFile, function(data) { 
        $.each( data.points, function(i, value) {
            var position = new google.maps.LatLng(parseFloat(value.lat), parseFloat(value.lon));
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                icon: icons["job"].icon,
                title: value.title
            });

            //var jobElement = '<div class="side-job-element" id="id'+value.id+'"><span class="position-side-list" onclick="gotoPoint('+value.id+');"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;'+value.title+'</span><br/><span class="orange">'+value.salary+'</span><br/><span class="company">'+value.company+'</span><br/><p class="white">' + value.description + '</p></div><hr class="hrside-list"/>';
            var jobElement = '<div class="side-job-element" id="id'+value.id+'"><span class="position-side-list" onclick="gotoPoint('+value.id+');"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;'+value.title+'</span><br/><span class="orange">$ '+formatMoney(value.salary,2, '.', ',')+'</span><br/><span class="company">'+value.company+'</span><br/></div><hr class="hrside-list"/>';
            
            $(".side-list").append(jobElement);
            infoWindow = new google.maps.InfoWindow(), marker, i;
            
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    map.panTo(marker.position);
                    //var _html = '<a class="position-title" href="#">' + value.title + '</a><br><span class="orange">' + value.salary + '</span><p style="margin-bottom:3px;">' + value.description + '</p><a class="orange" href="' + _CORE-DOMAIN_ + 'web/bienvenido/detalle_oferta">Ver m&aacute;s</a>';
					var $id_vacancy = (value.source == 'E') ? value.hash : value.id_source;
                    var _html = '<a class="position-title" href="#">' + value.title + '</a><br><span class="orange">$ ' + formatMoney(value.salary,2, '.', ',') + '</span><p style="margin-bottom:3px;"><i class="fa fa-building"></i> '+value.company+'</p><a class="orange" href="' + _CORE-DOMAIN_ + 'web/bienvenido/detalle_oferta/'+$id_vacancy+'">Ver m&aacute;s</a>';
                    infoWindow.setContent(_html);
                    infoWindow.open(map, marker);                    
                    addSelected(value.id,true);
                };
            })(marker, i));
            
            //_markers.push(marker);
            markers["id"+value.id] = {"marker":marker,"data":value};
            
            markerCluster.addMarker(marker);
        });
        
        //var mcOptions = {gridSize: 50, maxZoom: 15};
        //var markerCluster = new MarkerClusterer(map, _markers,mcOptions);
        
    });
}

var previous = 0;

function addSelected(id,animation){
    $("#id"+currentJob).css("border-right","5px solid transparent");
    
    $("#id"+id).css("border-right","5px solid orange");
    
    if (typeof markers["id"+currentJob] !== 'undefined' ){
        markers["id"+currentJob].marker.setIcon(icons["job"].icon);
        markers["id"+id].marker.setIcon(icons["selected"].icon);
    }
    
    if(animation){
        var target = $("#id"+id).position().top;
        var scroll = $('.side-list').scrollTop();
        var fix = -10;
        var final = target + scroll + fix;
        $('.side-list').animate({
            scrollTop: final
        }, 300);
    }
    currentJob = id;
}

function gotoPoint(id){
    var element = markers["id"+id];
    var lat = parseFloat(element.data.lat);
    var lng = parseFloat(element.data.lon);
    var latlng = new google.maps.LatLng(lat, lng);
    map.setZoom(18);
    map.panTo(latlng);
    //var _html = '<a class="position-title" href="#">' + element.data.title + '</a><br><span class="orange">' + element.data.salary + '</span><p style="margin-bottom:3px;">' + element.data.description + '</p><a class="orange" href="' + _CORE-DOMAIN_ + 'web/bienvenido/detalle_oferta">Ver m&aacute;s</a>';
	var $id_vacancy = (element.data.source == 'E') ? element.data.hash : element.data.id_source;
    var _html = '<a class="position-title" href="#">' + element.data.title + '</a><br><span class="orange">$ ' + formatMoney(element.data.salary,2, '.', ',') + '</span><p style="margin-bottom:3px;"><i class="fa fa-building"></i> '+element.data.company+'</p><a class="orange" href="' + _CORE-DOMAIN_ + 'web/bienvenido/detalle_oferta/'+$id_vacancy+'">Ver m&aacute;s</a>';
    infoWindow.setContent(_html);
    infoWindow.open(map, element.marker);
    //element.marker.setIcon(icons["selected"].icon);
    addSelected(element.data.id,false);
}


function initialize() {
    if (navigator.geolocation) {
        x.innerHTML = "Determinando tu ubicación...";
        geocoder = new google.maps.Geocoder();
        navigator.geolocation.getCurrentPosition(show_position, showMapError, function () {
            x.innerHTML = "No se encontró tu ubicación";
        }, {enableHighAccuracy: true});
    } else {
        x.innerHTML = "Funcionalidad no disponible";
    }
}

function show_position(p)
{
    document.getElementById('geolocation').innerHTML = p.coords.latitude + "," + p.coords.longitude;
    var pos = new google.maps.LatLng(p.coords.latitude, p.coords.longitude);
    map.setCenter(pos);
    map.setZoom(16);

    var infowindow = new google.maps.InfoWindow({
        content: "<strong>SI</strong>"
    });

    var marker = new google.maps.Marker({
        position: pos,
        map: map,
        icon: icons["current"].icon,
        optimized: false,
        title: "Estás aqui"
    });

    google.maps.event.addListener(marker, 'click', function () {
        infowindow.open(map, marker);
    });
    var lat = parseFloat(p.coords.latitude);
    var lng = parseFloat(p.coords.longitude);
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function (results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                infowindow.setContent(results[1].formatted_address);
                $('#geolocation').text(results[1].formatted_address);
            } else {
                alert('No se encontró tu ubicación');
            }
        } else {
            alert('Funcionalidad no disponible. ' + status);
        }
    });
}

function showMapError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            x.innerHTML = "Quieres compartir tu ubicación? [info dialog here!]";
            //  here let's call the function to show only the button for the general search
            break;
        case error.POSITION_UNAVAILABLE:
            x.innerHTML = "No se encontró tu ubicación.";
            break;
        case error.TIMEOUT:
            x.innerHTML = "No se encontró tu ubicación.";
            break;
        case error.UNKNOWN_ERROR:
            x.innerHTML = "No se encontró tu ubicación.";
            break;
    }
}

var openbar = true;
function barToggle(){
    if(openbar){
        // close
        $('.conthome').hide('slide', {direction: 'up'}, 400, function(){
            $('.conthome-down').show();
            $('.side-list').show('slide', {direction: 'left'}, 200);
            $(".totals").show('slide', {direction: 'right'}, 200);
        });
        openbar = false;
    }else{
        // open
        $(".totals").hide('slide', {direction: 'right'}, 200);
        $('.side-list').hide('slide', {direction: 'left'}, 200,function(){
            $('.conthome-down').hide();
            $('.conthome').show('slide', {direction: 'up'}, 400);
        });
        openbar = true;
    }
}
$('#map-section').on('click', function (e) {
    var allow = e.target.parentNode.parentNode.parentNode.className;
    if (allow === "map-area-click"){
        if(openbar){
            barToggle();
        }
    }
});

$(".control-panel-button").on("click",function(e){
    barToggle();
});
$(".control-panel-button-down").on("click",function(e){
    barToggle();
});