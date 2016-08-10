<?php 
class MHistorialPedidos extends CI_Model {
    
    /**
     * Computa el alta de un nuevo pedido en la tabla Historial_pedidos.
     * Return true o false en caso de operacion exitosa o fallida.
     */
    public function alta($id_cliente, $fecha, $egreso, $max_arribo, $origen, $destino, $dni, $id_calificacion){
        $time_egreso = new DateTime($egreso);
        $time_arribo = new DateTime($max_arribo);
        
        $cumplio = $time_egreso <= $time_arribo;
        
        $datos = array(
            'id_cliente' => $id_cliente,
            'fecha' => $fecha,
            'origen' => $origen,
            'destino' => $destino,
            'dni_conductor' => $dni,
            'id_calificacion' => $id_calificacion,
            'a_tiempo' => $cumplio
        );
        
        return $this->db->insert('Historial_pedidos', $datos);
    }
    
    /**
     * Computa y retorna el listado de viajes realizados por un cliente con id_cliente $id.
     * Lista una cantidad de $limite, si este valor es mayor o igual a 1; caso contrario, lista todos.
     * $resultado = Array(Fecha, Origen, Destino, A_tiempo, Nombre).
     */
    public function listar($id, $limite){
        $consulta = 'SELECT hp.fecha, hp.origen, hp.destino, hp.a_tiempo, e.nombre ';
        $consulta .= 'FROM historial_pedidos hp LEFT JOIN empleados e ';
        $consulta .= 'ON hp.dni_conductor = e.dni ';
        $consulta .= 'WHERE hp.id_cliente = '.$id.' ';
        $consulta .= 'ORDER BY fecha DESC ';
        if ($limite >= 1 ){
            $consulta .= 'LIMIT '.$limite.' ';
        }
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
}
