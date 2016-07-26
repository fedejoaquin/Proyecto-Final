var map;
var geocoder;
var marcas = [];
var ciudad_actual = " ,Bahía Blanca, Buenos Aires, Argentina";

var map_conf = {
        center: {lat: -38.717, lng: -62.265},
        zoom: 12
    };
      
var mapa = {
    
    map : function(){ return map; },
    geocoder : function(){ return geocoder; },
    ciudad_actual : function(){ return ciudad_actual },
    
    init : function(){
        //Inicializamos las variables que manejan el mapa y el geolocalizador
        map =  new google.maps.Map(document.getElementById('div_mapa'), map_conf);
        geocoder = new google.maps.Geocoder();
        mapa.marcas.agregar('Origen', null);
        mapa.marcas.agregar('Destino', null);
    },
    
    marcas : {
        agregar : function(nombre, position){
            var m = new google.maps.Marker({map: mapa.map(), title: nombre, position: position});
            marcas[nombre] = m;
        },
        
        quitar : function(nombre){
            if (marcas[nombre] !== undefined){
                marcas[nombre] = undefined;
            }
        },
        
        cambiar : function(nombre, position){
            if (marcas[nombre] !== undefined){
                marcas[nombre].setPosition(position);
            }
        },
        
        visibilidad : function(nombre, visible){
            if (marcas[nombre] !== undefined){
                if (visible){
                    marcas[nombre].setMap(mapa.map());
                }else{
                    marcas[nombre].setMap(null);
                }
            }
        },
        
        latitud : function(nombre){
            if (marcas[nombre] !== undefined){
                return marcas[nombre].position.lat();
            }
        },
        
        longitud : function(nombre){
            if (marcas[nombre] !== undefined){
                return marcas[nombre].position.lng();
            }
        }
    }    
    
    
}; //FIN MAPA

function _(){
    
    var map_conf = {
        center: {lat: -38.717, lng: -62.265},
        zoom: 12
    };
    
    //Definimos la ciudad donde estamos trabajando
    var ciudad_actual = " ,Bahía Blanca, Buenos Aires, Argentina";
    
    //Inicializamos las variables que manejan el map y el geolocalizador
    mapa =  new google.maps.Map(document.getElementById('mapa_validacion'), map_conf);
    geocoder = new google.maps.Geocoder();
    
    //Creamos las coordenadas y marcas que usará el usuario
    var pos_origen, pos_destino;
    var marca_origen = new google.maps.Marker({map: mapa, title: 'Origen'}); 
    var marca_destino = new google.maps.Marker({map: mapa, title: 'Destino'});
   
    //Indicamos la acción a seguir cuando se clicke sobre validar
    //var btn_validar = document.getElementById('btn_validar');
    //btn_validar.addEventListener('click', validar, false);
    
    //Función validar: valida los valores de origen y destino del nuevo pedido, y agrega las marcas
    //correspondientes en el mapa en el caso de corresponder.
    function validar(){
        var origen = $("#origen_viaje").val();
        var destino = $("#destino_viaje").val();
        var margen_adicional = $("#hora_max").val();
        
        if (origen.length >= 3){
            if (destino.length >= 3){
                if (margen_adicional !== null){
                    
                    //Agregamos información correspondiente a la ciudad actual
                    origen += ciudad_actual;
                    destino += ciudad_actual;
                    
                    //Localizamos el origen indicado.
                    geocoder.geocode( { 'address': origen }, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            pos_origen = results[0].geometry.location;
                            marca_origen.setPosition(pos_origen);
                        }else{
                            Materialize.toast("Error al localizar la dirección origen.", 3500, 'toast-error');
                        }
                    });
                    
                    //Localizamos el destino indicado.
                    geocoder.geocode( { 'address': destino }, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            pos_destino = results[0].geometry.location;
                            marca_destino.setPosition(pos_destino);
                        }else{
                            Materialize.toast("Error al localizar la dirección destino.", 3500, 'toast-error');
                        }
                    });
                    
                }else{
                    Materialize.toast("Debe ingresar un margen de tiempo.",3500,'toast-error');
                }
            }else{
                Materialize.toast("Debe ingresar un destino válido.",3500,'toast-error');
            }
        }else{
           Materialize.toast("Debe ingresar un origen válido.",3500,'toast-error'); 
        }
    }
}