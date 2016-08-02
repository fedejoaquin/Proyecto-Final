var map;
var geocoder;
var service;
var marcas = [];
var ciudad_actual = " ,Bahía Blanca, Buenos Aires, Argentina";

var map_conf = {
        center: {lat: -38.717, lng: -62.265},
        zoom: 12
    };
      
var mapa = {
    
    map : function(){ return map; },
    geocoder : function(){ return geocoder; },
    service : function(){ return service; },
    ciudad_actual : function(){ return ciudad_actual },
    
    init : function(){
        //Inicializamos las variables que manejan el mapa y el geolocalizador
        map =  new google.maps.Map(document.getElementById('div_mapa'), map_conf);
        geocoder = new google.maps.Geocoder();
        service = new google.maps.DistanceMatrixService;
        
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
    },
    
    distancia : {
        calcular : function(lat_origen, long_origen, lat_destino, long_destino, callback){
            var origen = {lat: lat_origen, lng: long_origen };
            var destino = {lat: lat_destino, lng: long_destino };
            
            service.getDistanceMatrix({
                origins: [origen],
                destinations: [destino],
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC,
                avoidHighways: false,
                avoidTolls: false
            },function(response, status){
                var error, demora, distancia;
                if (status !== google.maps.DistanceMatrixStatus.OK) {
                    error = 'No se ha podido calcular la distancia y/o demora del pedido';
                }else{
                    var results = response.rows[0].elements;
                    demora = results[0].duration.value;
                    distancia = results[0].distance.value;
                }
                callback(error, demora, distancia);
            });
        }
    }
    
}; //FIN MAPA