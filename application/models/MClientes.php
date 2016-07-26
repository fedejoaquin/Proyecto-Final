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
