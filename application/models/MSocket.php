<?php 
class MSocket extends CI_Model {
    
    /**
     * Notifica al servidor Prolog, el despacho efectivo de los viajes asociados a los recursos
     * cuyos ids pertenecen a $ids_viajes y $ids_recursos respectivamente.
     * Retorna true o false indicando operación exitosa o no.
     */
    public function indicar_despacho($ids_viajes, $ids_recursos){
        $asignaciones = array();
        
        //Por cada asociación id_viaje, id_recurso, genero una tupla asignado(id_recurso, id_viaje)
        for($i=0; $i<count($ids_viajes); $i++){
            $cadena = "asignacion(".$ids_recursos[$i]['id'].",$ids_viajes[$i])";
            array_push($asignaciones, $cadena);
        }
        //Genero un string con las tuplas asignado(viaje,recurso) separadas por el caracter ','.
        $elementos = implode(",", $asignaciones);
        
        //Indico el mensaje a enviar a prolog via socket.
        $toSend = "asignar([$elementos])\n";
        
        return ($this->enviar_mensaje($toSend));
    }
    
    /**
     * Notifica al servidor Prolog, la finalización de las tareas asociadas a los
     * recursos cuyos ids pertenecen a $ids_recursos.
     * Retorna true o false indicando operación exitosa o no.
     */
    public function indicar_finalizacion($ids_recursos){
        //Genero un string con los ids de recursos separados por el caracter ','.
        $elementos = implode(',', $ids_recursos);
        
        //Indico el mensaje a enviar a prolog via socket.
        $toSend = "finalizo([$elementos])\n";
        
        return ($this->enviar_mensaje($toSend));
    }
    
    /**
     * Envía el mensaje parametrizado en $toSend.
     * Retorna true o false indicando operación exitosa o no.
     */
    private function enviar_mensaje($toSend){
        //Creo la conexión vía socket y envío el mensaje.
	$ip = 'localhost';
	$port = 10000;

	//Creación de sockets tcp/ip
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false){ return false; }
        
        //Nos conectamos con el server socket de prolog.
        $result = socket_connect($socket, $ip, $port);
	if ( ! $result ) { return false; }

        //Envíamos el comando a ejecutar por prolog
	socket_write($socket, $toSend, strlen($toSend));
	
        //Cerramos el socket.
	socket_close($socket);
	
        return true;
    }
}