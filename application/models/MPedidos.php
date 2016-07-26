<?php 
class MPedidos extends CI_Model {
    
    /**
     * Computa el alta de un pedido, registrándolo en las tablas Pedidos, Pedidos Sin Procesar, y Pedidos Procesados,
     * con los datos parametrizados.
     * Retorna TRUE/FALSE en caso de exito o falla.
     */
    public function alta($cid, $origen, $destino, $lat_origen, $long_origen, $lat_destino, $long_destino, $referencia, $fecha_actual, $fecha_max, $telefono){
        
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
            'telefono' => $telefono
        );
        
        $this->db->trans_start();
        
        //Alta del pedido.
        $resultado = $this->db->insert('Pedidos',$data_pedido);
        
        //Consultamos el ID del pedido dado de alta.
        $id_pedido = $this->db->insert_id();
        
        $data_pedido_sin_procesar = array(
            'id_pedido' => $id_pedido,
            'prioridad' => 1
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
            $this->db->trans_complete();
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Computa y retorna los registros de pedidos de un cliente cuyo id es $id; para esto considera
     * sólo los pedidos que aún están en proceso de atención, excluyendo aquellos ya finalizados.
     * $resultado = Array(Id, Ingreso, Origen, Destino, Max_arribo, Estado).
     */
    public function listar($id){
        $consulta = 'SELECT p.id, p.ingreso, p.origen, p.destino, p.max_arribo, pp.estado ';
        $consulta .= 'FROM Pedidos p LEFT JOIN Pedidos_procesados pp ';
        $consulta .= 'ON p.id = pp.id_pedido ';
        $consulta .= 'WHERE p.id_cliente = '.$id.' ';
        $consulta .= 'ORDER BY p.ingreso DESC ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
}