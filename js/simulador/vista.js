var simulador_vista = {

    recorridos : {
        informar_finalizados : function(ids_viajes, ids_recursos){
            for(i=0; i<ids_viajes.length; i++){
                mapa.marcas.estaticas.eliminar("o"+ids_viajes[i], false);
                mapa.marcas.estaticas.eliminar("d"+ids_viajes[i], false);
                mapa.marcas.estaticas.eliminar("t"+ids_recursos[i], false);
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