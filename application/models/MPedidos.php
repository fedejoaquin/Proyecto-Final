<?php 
class MPedidos extends CI_Model {
    
    /**
     * Computa el alta de un pedido, registrándolo en las tablas Pedidos, Pedidos Sin Procesar, y Pedidos Procesados,
     * con los datos parametrizados.
     * Retorna el id del pedido nuevo. En caso de error, retorna -1.
     */
    public function alta($cid, $origen, $destino, $lat_origen, $long_origen, $lat_destino, $long_destino, $demora, $distancia, $referencia, $fecha_actual, $fecha_max, $telefono, $prioridad){
        
        $data_pedido = array(
            'id_cliente' => $cid,
            'origen' => $origen,
            'destino' => $destino,
            'lat_origen' => $lat_origen,
            'long_origen' => $long_origen,
            'lat_destino' => $lat_destino,
            'long_destino' => $long_destino,
            'referencia_adicional' => $referencia,
            'ingreso' => $fecha_actual,
            'max_arribo' => $fecha_max,
            'telefono' => $telefono,
            'demora' => $demora,
            'distancia' => $distancia
        );
        
        //Alta del pedido.
        $resultado = $this->db->insert('Pedidos',$data_pedido);
        
        //Consultamos el ID del pedido dado de alta.
        $id_pedido = $this->db->insert_id();
        
        $data_pedido_sin_procesar = array(
            'id_pedido' => $id_pedido,
            'prioridad' => $prioridad
        );
        
        $data_pedido_procesado = array(
            'id_pedido' => $id_pedido,
            'id_recurso' => 0,
            'orden' => 0,
            'estado' => 'Procesando'
        ); 
        
        //Alta del pedido sin procesar.
        $resultado = $resultado && $this->db->insert('Pedidos_sin_procesar',$data_pedido_sin_procesar);
        
        //Alta del pedido procesado.
        $resultado = $resultado && $this->db->insert('Pedidos_procesados',$data_pedido_procesado);
        
        if ($resultado){
            return $id_pedido;
        }else{
            return -1;
        }
    }
    
    /**
     * Computa la eliminación del registro de las tablas Pedidos y Pedidos Procesados, cuyo id es $id.
     * Retorna true o false, indicando operación exitosa o fallida.
     */
    public function eliminar($id){
        $this->db->trans_start();
        
        $this->db->where('id', $id);
        $resultado = $this->db->delete('Pedidos');
        
        $this->db->where('id_pedido', $id);
        $resultado = $resultado && $this->db->delete('Pedidos_procesados');
        
        if ($resultado){
            $this->db->trans_complete();
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Computa la finalización de los pedidos indicados en $ids_pedidos, asignándoles el estado "Finalizado"
     * en la tabla Pedidos_procesados, e indicando la hora de egreso del sistema en la tabla Pedidos.
     * Retorna true o false, indicando operación exitosa o fallida.
     */
    public function finalizar($ids_pedidos){
        
        $data = array( 'estado' => 'Finalizado' );
        $this->db->where_in('id_pedido', $ids_pedidos);
        $resultado = $this->db->update('Pedidos_procesados', $data);
        
        $fecha_actual = $this->MHora->get_hora()['hora_actual'];
        
        $data = array( 'salida' => $fecha_actual );
        $this->db->where_in('id', $ids_pedidos);
        $resultado = $resultado && $this->db->update('Pedidos', $data);
        
        return $resultado;
    }
    
    /**
     * Computa el despacho de aquellos pedidos asociados a un recurso cuyo id pertenece a $recursos.
     * En caso de la existencia de más de un pedido asociado a un mismo recurso, se despacha aquel cuyo columna
     * orden sea menor.
     * $retorno['pedidos_despachados'] = Array(Id_pedido) = Indica los Id_pedido despachados.
     * $retorno['recursos_ocupados'] = Array([Id, Stress]) = Indica los Id_recurso ocupados y su stress asociado.
     * $retorno['resultado'] = True o False indicando operación exitosa o no.
     */
    public function despachar($recursos){
        
        $a_despachar = array();
        $a_ocupar = array();
        $retorno = array();
        
        //Seleccionamos los próximos pedidos que deben ser despachados, ya que se 
        //encuentran asociados al recurso liberado recientemente.
        foreach ($recursos as $recurso){
            $consulta_seleccion = 'SELECT pp.id_pedido, p.demora ';
            $consulta_seleccion .= 'FROM Pedidos_procesados pp LEFT JOIN pedidos p ON pp.id_pedido = p.id ';
            $consulta_seleccion .= 'WHERE pp.id_recurso = '.$recurso['id'].' AND pp.estado = "A_despachar" ';
            $consulta_seleccion .= 'ORDER BY orden ';
            
            $query = $this->db->query($consulta_seleccion);
            $resultado = $query->row_array();
            
            if (!empty($resultado)){
                array_push($a_despachar, $resultado['id_pedido']);
                array_push($a_ocupar, array('id' => $recurso['id'], 'stress' => $resultado['demora']));
            }
        }
        
        //Si existen pedidos a despachar.
        if (!empty($a_despachar)){
            
            //Por cada recurso a despachar, se actualiza su estado a despachado en la tabla Pedidos_procesados.
            $data = array(
                'estado' => 'Despachado'
            );

            $this->db->where_in('id_pedido', $a_despachar);
            if ($this->db->update('Pedidos_procesados', $data)){
                $retorno['resultado'] = true;
                $retorno['pedidos_despachados'] = $a_despachar;
                $retorno['recursos_ocupados'] = $a_ocupar;
                return $retorno;
            }else{
                $retorno['resultado'] = false;
                $retorno['pedidos_despachados'] = array();
                $retorno['recursos_ocupados'] = array();
                return $retorno;
            }
        }else{
            $retorno['resultado'] = true;
            $retorno['pedidos_despachados'] = array();
            $retorno['recursos_ocupados'] = array();
            return $retorno;
        }
    }
    
    /**
     * Computa la actualización de las prioridades de aquellos pedidos que se encuentran sin procesar, y cuyo
     * id es distinto a $id.
     * Incrementa la prioridad en un 25%.
     * Retorna True o False en caso de ejecución exitosa o no.
     */
    public function actualizar_prioridades($id){
        $consulta = 'UPDATE Pedidos_sin_procesar ';
        $consulta .= 'SET prioridad = prioridad * 1.25 ';
        $consulta .= 'WHERE id_pedido != '.$id.' ';
        
        return $this->db->query($consulta);
    }
    
    /**
     * Computa y retorna los registros de pedidos de un cliente cuyo id es $id; para esto considera
     * sólo los pedidos que aún están en proceso de atención, excluyendo aquellos ya finalizados.
     * $resultado = Array(Id, Ingreso, Origen, Destino, Lat_origen, Long_origen, Lat_destino, Long_destino, Max_arribo, Estado, Id_recurso).
     */
    public function listar($id){
        $consulta = 'SELECT p.id, p.ingreso, p.origen, p.destino, p.lat_origen, p.long_origen, p.lat_destino, p.long_destino, p.max_arribo, pp.estado, pp.id_recurso ';
        $consulta .= 'FROM Pedidos p LEFT JOIN Pedidos_procesados pp ';
        $consulta .= 'ON p.id = pp.id_pedido ';
        $consulta .= 'WHERE p.id_cliente = '.$id.' ';
        $consulta .= 'ORDER BY p.ingreso DESC ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna los registros de pedidos que aún no se despacharon ni se encuentran en proceso
     * de despacho (estado = procesando, aceptado)
     * $resultado = Array(Id, Lat_origen, Long_origen, Lat_destino, Long_destino).
     */
    public function get_sin_despachar(){
        $consulta = 'SELECT p.id, p.lat_origen, p.long_origen, p.lat_destino, p.long_destino ';
        $consulta .= 'FROM Pedidos p LEFT JOIN Pedidos_procesados pp ';
        $consulta .= 'ON p.id = pp.id_pedido ';
        $consulta .= 'WHERE pp.estado = "Procesando" OR pp.estado = "Aceptado" ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna los registros de pedidos que se encuentran despachados o a despachar (estado = Despachado, A_despachar)
     * $resultado = Array(Id, Lat_origen, Long_origen, Lat_destino, Long_destino).
     */
    public function get_despachados(){
        $consulta = 'SELECT p.id, p.lat_origen, p.long_origen, p.lat_destino, p.long_destino ';
        $consulta .= 'FROM Pedidos p LEFT JOIN Pedidos_procesados pp ';
        $consulta .= 'ON p.id = pp.id_pedido ';
        $consulta .= 'WHERE pp.estado = "A_despachar" OR pp.estado = "Despachado" ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna la información asociada a un pedido cuyo id es $id.
     * $resultado = Array(Id, Id_cliente, Lat_origen, Long_origen, Lat_destino, Long_destino, Referencia_adicional, 
     *                    Telefono, Origen, Destino, Ingreso, Salida, Max_arribo, Demora, Distancia).
     */
    public function get_info($pid){
        $consulta = 'SELECT * ';
        $consulta .= 'FROM Pedidos ';
        $consulta .= 'WHERE id = '.$pid.' ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->row_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna el registro en el que se asocia un dado pedido cuyo id es $id,
     * con algún recurso.
     * $resultado = Array(Id_pedido, Id_recurso, Orden, Estado). 
     */
    public function get_asociacion($pid){
        $consulta = 'SELECT * ';
        $consulta .= 'FROM Pedidos_procesados ';
        $consulta .= 'WHERE id_pedido = '.$pid.' ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->row_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna los pedidos realizados por el cliente, que están finalizados y sin
     * calificar.
     * $resultado = Array(Id).
     */
    public function get_sin_calificar($cid){
        $consulta = 'SELECT p.id ';
        $consulta .= 'FROM Pedidos p LEFT JOIN Pedidos_procesados pp ON p.id = pp.id_pedido ';
        $consulta .= 'WHERE p.id_cliente = '.$cid.' AND pp.estado = "Finalizado" ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
    
    /**
     * Computa y retorna los datos asociados a los pedidos recientemente despachados y cuyos ids pertenecen a 
     * $ids_pedidos, para el funcionamiento del simulador.
     * $resultado = Array(Id_pedido, Id_recurso, Origen, Destino, Lat_origen, Long_origen, Lat_recurso, Long_recurso, Lat_destino, Long_destino).
     */
    public function get_datos_simulacion($ids_pedidos){
        $consulta = 'SELECT pp.id_pedido, pp.id_recurso, p.origen, p.destino, p.lat_origen, p.long_origen, r.ult_latitud as lat_recurso, r.ult_longitud as long_recurso, p.lat_destino, p.long_destino ';
        $consulta .= 'FROM (Pedidos p LEFT JOIN Pedidos_procesados pp ON p.id = pp.id_pedido) ';
        $consulta .= 'LEFT JOIN Recursos r ON r.id = pp.id_recurso ';
        $consulta .= 'WHERE pp.id_pedido IN ('.  implode(",", $ids_pedidos) .' ) ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
}