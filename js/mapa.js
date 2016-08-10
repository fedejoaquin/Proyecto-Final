var map, geocoder, service, direction;
var marcas_estaticas = [];
var marcas_dinamicas = [];
var caminos = [];
var icons = [];
var colores = [];
var indice_colores = 0;
var ciudad_actual = " ,Bahía Blanca, Buenos Aires, Argentina";

var map_conf = {
        center: {lat: -38.717, lng: -62.265},
        zoom: 12
    };
      
var mapa = {
    
    map : function(){ return map; },
    geocoder : function(){ return geocoder; },
    service : function(){ return service; },
    direction : function(){ return direction; },
    ciudad_actual : function(){ return ciudad_actual },
    
    init : function(){
        //Inicializamos las variables que manejan el mapa y el geolocalizador
        map =  new google.maps.Map(document.getElementById('div_mapa'), map_conf);
        geocoder = new google.maps.Geocoder();
        service = new google.maps.DistanceMatrixService;
        direction = new google.maps.DirectionsService();
        
        mapa.iconos.init();
        mapa.colores.init();
        
        mapa.marcas.estaticas.agregar('Origen', null);
        mapa.marcas.estaticas.agregar('Destino', null);
    },
    
    marcas : {
        
        estaticas : {
            
            agregar : function(nombre, position){
                var m = new google.maps.Marker({map: mapa.map(), title: nombre, position: position});
                marcas_estaticas[nombre] = m;
            },
            
            eliminar : function(nombre){
                if (marcas_estaticas[nombre] !== undefined){
                    marcas_estaticas[nombre].setPosition(null);
                    marcas_estaticas[nombre].setMap(null);
                    delete(marcas_estaticas[nombre]);
                }
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
                var m = new google.maps.Marker({map: mapa.map(), title: nombre, position: position, icon: mapa.iconos.get(tipo)});
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
    },
    
    direcciones : {
        
        dibujar : function (nombre, posOrigen, posIntermedia, posDestino, callback){
            var request = {
                origin: posOrigen,
                waypoints: [{location: posIntermedia, stopover: false}],
                destination: posDestino,
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC
            };

            direction.route(request, function (response, status){
                if (status === google.maps.DirectionsStatus.OK){
                    var path = new google.maps.Polyline({
                        path: response.routes[0].overview_path,
                        geodesic: true,
                        strokeColor: mapa.colores.get(),
                        strokeOpacity: 1.0,
                        strokeWeight: 3
                    });

                    path.setMap(mapa.map());
                    caminos[nombre] = path;
                    
                    callback(response);
                }
            });
        },
        
        eliminar : function(nombre){
            if (caminos[nombre] !== undefined){
                caminos[nombre].setMap(null);
                delete(caminos[nombre]);
            }
        }
    },
    
    iconos : {
        init : function(){
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
        },

        get : function(tipo){
            return icons[tipo];
        }
    },
    
    colores : {
        init : function(){
            colores.push('#FF0000');
            colores.push('#00FF09');
            colores.push('#7700FF');
            colores.push('#FFFF00');
            colores.push('#FF9A00');
            colores.push('#FF00F3');
            colores.push('#00EFFF');
            colores.push('#646464');
            colores.push('#538E92');
            colores.push('#6B9253');
            colores.push('#5D5392');
            colores.push('#925362');
            colores.push('#8F9044');
            colores.push('#5B9E9B');
            colores.push('#C098B9');
        },
        
        get : function(){
            var color = colores[indice_colores];
            indice_colores = (indice_colores + 1) % colores.length;
            return color;
        }
    }
    
}; //FIN MAPA

