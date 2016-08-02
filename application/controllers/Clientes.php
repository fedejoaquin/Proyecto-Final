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
        $data['historial'] = $this->MHistorialPedidos->listar($this->session->userdata('cid'));
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
        $resultado = array(); 
        $resultado['data'] = array();
        
        if($this->session->userdata('cid') !== null){
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
            $resultado['error'] = 'Usuario sin permisos.';
        }
        echo json_encode($resultado);
    }
    
    public function check(){
        $resultado = array(); 
        $resultado['data'] = array();
        if($this->session->userdata('cid') !== null){
            $cid = $this->session->userdata('cid');
            $resultado['data']['historial_viajes'] = $this->MHistorialPedidos->listar($cid);
            $resultado['data']['estado_viajes'] = $this->MPedidos->listar($cid);
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
