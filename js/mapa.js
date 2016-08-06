var map;
var geocoder;
var service;
var marcas_estaticas = [];
var marcas_dinamicas = [];
var icons = [];
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
        
        var base = 'http://localhost/PF/img/marcas/';
        icons['Origen_Procesando'] = base + 'o_p.png';
        icons['Destino_Procesando'] = base + 'd_p.png';
        icons['Origen_Aceptado'] = base + 'o_a.png';
        icons['Destino_Aceptado'] = base + 'd_a.png';
        icons['Origen_Rechazado'] = base + 'o_r.png';
        icons['Destino_Rechazado'] = base + 'd_r.png';
        icons['Origen_A_despachar'] = base + 'o_ad.png';
        icons['Destino_A_despachar'] = base + 'd_ad.png';
        icons['Origen_Despachado'] = base + 'o_d.png';
        icons['Destino_Despachado'] = base + 'd_d.png';
        icons['Origen_Finalizado'] = base + 'o_f.png';
        icons['Destino_Finalizado'] = base + 'd_f.png';
        icons['Recurso'] = base + 'r.png';
        
        mapa.marcas.estaticas.agregar('Origen', null);
        mapa.marcas.estaticas.agregar('Destino', null);
    },
    
    marcas : {
        
        estaticas : {
            
            agregar : function(nombre, position){
                var m = new google.maps.Marker({map: mapa.map(), title: nombre, position: position});
                marcas_estaticas[nombre] = m;
            },
            
            cambiar : function(nombre, position){
                if (marcas_estaticas[nombre] !== undefined){
                    marcas_estaticas[nombre].setPosition(position);
                    marcas_estaticas[nombre].setMap(mapa.map());
                }
            },
    
            visibilidad : function(nombre, visible){
                if (marcas_estaticas[nombre] !== undefined){
                    if (visible){
                        marcas_estaticas[nombre].setMap(mapa.map());
                    }else{
                        marcas_estaticas[nombre].setMap(null);
                    }
                }
            },
            
            latitud : function(nombre){
                if (marcas_estaticas[nombre] !== undefined){
                    return marcas_estaticas[nombre].position.lat();
                }
            },

            longitud : function(nombre){
                if (marcas_estaticas[nombre] !== undefined){
                    return marcas_estaticas[nombre].position.lng();
                }
            }
        },

        dinamicas : {
            agregar : function(nombre, tipo, position){
                var m = new google.maps.Marker({map: mapa.map(), title: nombre, position: position, icon: icons[tipo]});
                marcas_dinamicas.push(m);
            },

            eliminar : function(){
                for(i=0; i<marcas_dinamicas.length; i++){
                    marcas_dinamicas[i].setMap(null);
                }   
                delete(marcas_dinamicas);
                marcas_dinamicas = [];
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