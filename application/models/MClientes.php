<?php 
class MClientes extends CI_Model {
    
    /**
     * Computa el alta de un cliente con los datos parametrizados en $datos.
     * Retorna TRUE/FALSE en caso de exito o falla.
     */
    public function alta($datos){
        
        $password = hash('sha256',$datos['password']);
        
        $data = array(
            'nombre' => $datos['nombre'],
            'telefono' => $datos['telefono'],
            'password' => $password,
            'id_ws' => $datos['id_ws']
        );
        
        //Alta del empleado
        return $this->db->insert('Clientes',$data);
    }
    
    /**
     * Computa el incremento de un viaje realizado por el cliente, id es $id_cliente. Considera incrementar las cantidades
     * de viajes, longitud y anticipaciones, teniendo en cuenta los valores 1, $longitud, y $anticipacion respectivamente.
     * Retorna True o False indicanco ejecucion exitosa o no.
     */
    public function sumar_viaje($id_cliente, $longitud, $anticipacion){
        $consulta = 'UPDATE Clientes ';
        $consulta .= 'SET cant_viajes = cant_viajes + 1, long_viajes = long_viajes + '.$longitud.', antic_viajes = antic_viajes + '.$anticipacion.' ';
        $consulta .= 'WHERE id = '.$id_cliente.' ';
    
        return $this->db->query($consulta);
    }
    
    /**
     * Computa el incremento de las cancelaciones para un cliente cuyo id es $id_cliente. Considera la cantidad
     * a incrementar igual a $cancelaciones. Particularmente corrije los valores de longitud y anticipacion que figuran para 
     * el dado cliente, decrementándolos en las cantidades indicadas por $longitud y $anticipacion, respectivamente.
     * Retorna True o False indicanco ejecucion exitosa o no.
     */
    public function sumar_cancelacion($id_cliente, $longitud, $anticipacion, $cancelaciones){
        $consulta = 'UPDATE Clientes ';
        $consulta .= 'SET cant_viajes = cant_viajes - '.$cancelaciones.', long_viajes = long_viajes - '.$longitud.', antic_viajes = antic_viajes - '.$anticipacion.' ';
        $consulta .= 'WHERE id = '.$id_cliente.' ';
        
        return $this->db->query($consulta);
    }
    
    /**
     * Computa y retorna la prioridad asociada para un dado cliente, cuyo id es $id.
     * La prioridad es un estimado considerando:
     * $prioridad = Cant_viajes * 0.25 + Longitud_promedio * 0.0025 + Anticipacion_promedio * 0.5 - Cancelaciones * 0.25.
     */
    public function calcular_prioridad($id){
        $cliente = $this->ver_por_id($id);
        $cant_viajes = $cliente['cant_viajes'];
        $longitudes = $cliente['long_viajes'];
        $anticipacion = $cliente['antic_viajes'];
        $cancelaciones = $cliente['cancelaciones'];
        
        if ($cant_viajes != 0){
            return ($cant_viajes * 0.25) + (($longitudes / $cant_viajes)*0.0025) + (($anticipacion / $cant_viajes)*0.50) - ($cancelaciones*0.25);
        }else{
            return 1;
        }
    }
    
    /**
     * Computa y retorna el registro de un cliente con id $id, si es que existe.
     */
    public function ver_por_id($id){
        $consulta = 'SELECT * ';
        $consulta .= 'FROM Clientes ';
        $consulta .= 'WHERE id = "'.$id.'" ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->row_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna el registro de un cliente con nombre $nombre y id webservice $id_ws, si es que existe.
     */
    public function ver_por_nombre($nombre, $id_ws){
        $consulta = 'SELECT * ';
        $consulta .= 'FROM Clientes ';
        $consulta .= 'WHERE nombre = "'.$nombre.'" and id_ws = "'.$id_ws.'"';
        
        $query = $this->db->query($consulta);
        $resultado = $query->row_array();
        
        return $resultado;
    }
    
    /**
     * Edita el campo teléfono de un cliente con id $cid.
     */
    public function editar_telefono($cid, $telefono){
        $data = array(
            'telefono' => $telefono,
        );
        $this->db->where('id', $cid);
        return $this->db->update('Clientes', $data );
    }
}
