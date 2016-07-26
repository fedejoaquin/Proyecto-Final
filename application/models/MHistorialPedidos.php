<?php 
class MHistorialPedidos extends CI_Model {
    
    /**
     * Computa y retorna el listado de viajes realizados por un cliente con id_cliente $id.
     */
    public function listar($id){
        $consulta = 'SELECT hp.fecha, hp.origen, hp.destino, hp.a_tiempo, e.nombre ';
        $consulta .= 'FROM historial_pedidos hp LEFT JOIN empleados e ';
        $consulta .= 'ON hp.dni_conductor = e.dni ';
        $consulta .= 'WHERE hp.id_cliente = '.$id.' ';
        $consulta .= 'ORDER BY fecha DESC ';
        
        $query = $this->db->query($consulta);
        $resultado = $query->result_array();
        
        return $resultado;
    }
}
