<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends CI_Controller {
    
    /**
     * Chequea que el usuario se encuentre logueado como cliente.
     * Carga la vista cleintes si hay sesión abierta como cliente.
     * Redirige a Intranet si hay sesión abierta como empleado.
     * Redirige a Inicio si no hay sesión iniciada.
     */
    public function index(){
        //Chequea que el usuario esté logueado correctamente
        $this->check_login_redirect();
        
        $data = array();
        $data['historial'] = $this->MHistorialPedidos->listar($this->session->userdata('cid'),10);
        $data['viajes_actuales'] = $this->MPedidos->listar($this->session->userdata('cid'));
        
        $this->load->view('clientes', $data);
    }
    
    /**
     * Realiza el alta de un nuevo pedido para un cliente.
     * 
     * @return VIA AJAX
     * $resultado['data'] = Array(Ingreso, Origen, Destino, Max_arribo, Estado).
     * $resultado['error'] = Tipo de error en caso de corresponder.
     */ 
    public function alta_pedido(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        
        //Chequea que el usuario sea un cliente logueado
        if($this->session->userdata('cid') !== null){
            //Chequea que el cliente no tenga viajes finalizados sin calificar
            if (empty($this->MPedidos->get_sin_calificar($this->session->userdata('cid')))){
                $cid = $this->session->userdata('cid');
                $origen = $this->input->post('origen');
                $destino = $this->input->post('destino');
                $margen = $this->input->post('margen');
                $referencia = $this->input->post('referencia');
                $telefono = $this->input->post('telefono');
                $lat_origen = $this->input->post('lat_origen');
                $long_origen = $this->input->post('long_origen');
                $lat_destino = $this->input->post('lat_destino');
                $long_destino = $this->input->post('long_destino');
                $distancia = $this->input->post('distancia');
                $demora = $this->input->post('demora');

                $fecha_actual = date("Y-m-d H:i:s");
                $fecha_suma = strtotime ('+'.$margen.' minute' , strtotime ( $fecha_actual ) ) ;
                $fecha_max = date("Y-m-d H:i:s", $fecha_suma);

                //Alta de pedido, SIN INDICAR PRIORIDAD. Generación de conexiones tanto entre pedidos como entre pedido-recurso.
                $this->db->trans_start();

                if (($pid = $this->MPedidos->alta($cid, $origen, $destino, $lat_origen, $long_origen, $lat_destino, $long_destino, $demora, $distancia, $referencia, $fecha_actual, $fecha_max, $telefono))!=-1){
                    $this->MConexiones->generar($pid, $lat_origen, $long_origen, $lat_destino, $long_destino);
                    $resultado['data'] = $this->MPedidos->listar($cid);
                    $this->db->trans_complete();
                }else{
                    $resultado['error'] = 'El alta no pudo realizarse correctamente.';
                }
            }else{
                $resultado['error'] = 'Tiene viajes finalizados sin calificar.';
            }
        }else{
            $resultado['error'] = 'Usuario sin permisos.';
        }
        echo json_encode($resultado);
    }
    
    /**
     * Computa el chequeo de estado de los viajes actuales y del historial, para un cliente.
     * Adicionalmente retorna las posiciones actuales de los recursos asociados a aquellos pedidos
     * en estado de atención efectivo (estado = A_despachar, Despachado).
     * 
     * @return VIA AJAX
     * $resultado['data']['historial_viajes'] = Array(Fecha, Origen, Destino, A_tiempo, Nombre).
     * $resultado['data']['estado_viajes'] = Array(Ingreso, Origen, Destino, Lat_origen, Long_origen, Lat_destino, Long_destino, Max_arribo, Estado, Id_recurso).
     * $resultado['data']['estado_recursos'] = Array(Id_pedido, Id_recurso, Ult_latitud, Ult_longitud).
     * $resultado['error'] = Tipo de error en caso de corresponder.
     */
    public function check(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        if($this->session->userdata('cid') !== null){
            $cid = $this->session->userdata('cid');
            $resultado['data']['historial_viajes'] = $this->MHistorialPedidos->listar($cid, 10);
            $resultado['data']['estado_viajes'] = $this->MPedidos->listar($cid);
            $resultado['data']['estado_recursos'] = $this->MRecursos->get_asociados($this->session->userdata('cid'));
        }else{
            $resultado['error'] = 'Usuario sin permisos.';
        }
        echo json_encode($resultado);
    }
    
    /**
     * Computa y retorna los datos asociados a un recurso cuyo id es $id.
     * 
     * @return VIA AJAX
     * $resultado['data']= Array(Id, Dni, Nombre, Patente, Marca, Modelo, Color).
     */
    public function info_recurso(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        if($this->session->userdata('cid') !== null){
            $id = $this->input->post('id_recurso');
            $resultado['data'] = $this->MRecursos->get_info($id);
        }else{
            $resultado['error'] = 'Usuario sin permisos.';
        }
        echo json_encode($resultado);
    }
    
    /**
     * Computa la calificación asociada a un viaje finalizado, y elimina este último
     * como parte de viajes actuales pasándolo al historial del cliente.
     * Elimina todo información de la BD referente al viaje archivado.
     * 
     * @return VIA AJAX
     * $resultado['data']= Array().
     * $resultado['error] = Error, en caso de corresponder.
     */
    public function calificar_viaje(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        if($this->session->userdata('cid') !== null){
            $cid = $this->session->userdata('cid');
            $id_viaje = $this->input->post('id_viaje');
            $id_recurso = $this->input->post('id_recurso');
            $calificacion = $this->input->post('calificacion');
            $comentarios = $this->input->post('comentarios');
            
            //Obtenemos la info del pedido, recurso y su asociacion cuyos ids son $id_viaje, $id_recurso respectivamente.
            $info_pedido = $this->MPedidos->get_info($id_viaje);
            $info_recurso = $this->MRecursos->get_info($id_recurso);
            $asociacion = $this->MPedidos->get_asociacion($id_viaje);
            
            //Si tal viaje existe, pertenece al usuario actual y está asociado con el recurso indicado
            if ( (!empty($info_pedido)) && (!empty($info_recurso)) && (!empty($asociacion)) ){
                if (($info_pedido['id_cliente'] == $cid) && ($asociacion['id_recurso'] == $id_recurso)){
                    if ($asociacion['estado'] == "Finalizado"){
                        $this->db->trans_start();
                                              
                        //Alta de calificación
                        $id_calificacion = $this->MCalificaciones->alta($info_recurso['dni'], $calificacion, $comentarios);
                       
                        //Si la calificación fue dada de alta exitosamente
                        if ($id_calificacion !== -1){               
                            //Eliminamos el pedido, y los metadatos asoaciados, y generamos un registro de historial
                            if ($this->MPedidos->eliminar($id_viaje)){
                                if ($this->MConexiones->eliminar($id_viaje)){
                                    if ($this->MHistorialPedidos->alta($cid, $info_pedido['ingreso'], $info_pedido['salida'], $info_pedido['max_arribo'], $info_pedido['origen'], $info_pedido['destino'], $info_recurso['dni'],$id_calificacion)){
                                        $this->db->trans_complete();
                                    }else{
                                        $resultado['error'] = 'Se produjo un error al intentar calificar.';
                                    }
                                }else{
                                    $resultado['error'] = 'Se produjo un error al intentar calificar.';
                                }
                            }else{
                                $resultado['error'] = 'Se produjo un error al intentar calificar.';
                            }
                        }else{
                            $resultado['error'] = 'Se produjo un error al intentar calificar.';
                        }
                    }else{
                        $resultado['error'] = 'Viaje en curso. No se puede calificar.';
                    }
                }else{
                    $resultado['error'] = 'Viaje inexistente para el usuario actual.';
                }
            }else{
                $resultado['error'] = 'Viaje inexistente para el usuario actual.';
            }
        }else{
            $resultado['error'] = 'Usuario sin permisos.';
        }
        echo json_encode($resultado);
    }
    
    /**
     * Edita el teléfono de un cliente.
     * 
     * @return VIA AJAX
     * $data['error'] = Tipo de error en caso de corresponder.
     */ 
    public function editar_telefono(){
        $this->control_origen_ajax();
        
        $resultado = array(); 
        $resultado['data'] = array();
        if($this->session->userdata('cid') !== null){
            $cid = $this->session->userdata('cid');
            $telefono = $this->input->post('telefono');
            
            //Editamos el campo teléfono del cliente que solicita.
            if ($this->MClientes->editar_telefono($cid, $telefono)){
                $this->session->set_userdata('telefono', $telefono);
            }else{
                $resultado['error'] = 'Error al intentar editar el teléfono.';
            }
        }else{
            $resultado['error'] = 'Usuario sin permisos.';
        }
        echo json_encode($resultado);
    }
    
    /**
     * Chequea que el origen de la petición actual sea vía ajax.
     * En caso de no serlo, redirige al index del sitio; caso contrario, no 
     * redirige.
     */
    private function control_origen_ajax(){
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            redirect(site_url());
        }
    }
    
    /**
     * Cheque el estado del usuario actual y redirige al controlador correspondiente;
     * si es empleado, al controlador intranet; si no es cliente, al index del sitio;
     * si es cliente, no redirige.
     */
    private function check_login_redirect(){
        if (!($this->session->userdata('eid') === NULL)){
            redirect(site_url()."intranet");
        }else{
            if (($this->session->userdata('cid') === NULL)){
                redirect(site_url());
            }
        }
    }
}
