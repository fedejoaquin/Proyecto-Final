var simulador_vista = {

    recorridos : {
        informar_finalizados : function(ids_viajes){
            for(i=0; i<ids_viajes.length; i++){
                mapa.marcas.estaticas.visibilidad("o"+ids_viajes[i], false);
                mapa.marcas.estaticas.visibilidad("d"+ids_viajes[i], false);
                mapa.marcas.estaticas.visibilidad("t"+ids_viajes[i], false);
                mapa.direcciones.eliminar(ids_viajes[i]);
            }
        }
    }, //FIN RECORRIDOS

    fecha : {
        init: function(fecha){
            $('#fecha_sistema').text(fecha);
        },
        
        actualizar_notificar : function(fecha){
            $('#fecha_sistema').text(fecha);
        }
        
    } //FIN FECHA

}//FIN SIMULADOR_VISTA   