<?php 
class MHistorialPedidos extends CI_Model {
    
    /**
     * Computa el alta de un nuevo pedido en la tabla Historial_pedidos.
     * Return true o false en caso de operacion exitosa o fallida.
     */
    public function alta($id_cliente, $fecha, $egreso, $max_arribo, $origen, $destino, $dni, $id_calificacion){
        $time_egreso = new DateTime($egreso);
        $time_arribo = new DateTime($max_arribo);
        $diff;
        
        if ($time_egreso <= $time_arribo){
            $cumplio = true;
            $diff = $time_arribo->diff($time_egreso)->format('%H:%I:%S');
        }else{
            $cumplio = false;
            $diff = $time_egreso->diff($time_arribo)->format('%H:%I:%S');
        }
        
        $datos = array(
            'id_cliente' => $id_cliente,
            'fecha' => $fecha,
            'origen' => $origen,
            'destino' => $destino,
            'dni_conductor' => $dni,
            'id_calificacion' => $id_calificacion,
            'a_tiempo' => ($cumplio ? true : false),
            'diferencia' => $diff
        );
        
        return $this->db->insert('Historial_pedidos', $datos);
    }
    
    /**
     * Computa y retorna el listado de viajes realizados por un cliente con id_cliente $id.
     * Lista una cantidad de $limite, si este valor es mayor o igual a 1; caso contrario, lista todos.
     * $resultado = Array(Fecha, Origen, Destino, A_tiempo, Diferencia, Nombre).
     */
    public function listar($id, $limite){
        $consulta = 'SELECT hp.fecha, hp.origen, hp.destino, hp.a_tiempo, hp.diferencia, e.nombre ';
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
