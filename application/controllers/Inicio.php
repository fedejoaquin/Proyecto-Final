<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {
    
    /**
     * Chequea que el usuario no se encuentre logueado.
     * Redirige a Clientes o Intranet si hay sesión abierta.
     * Carga la vista Inicio si no hay sesión abierta.
     */
    public function index(){
        if (!($this->session->userdata('eid') === NULL)){
            redirect(site_url()."intranet");
        }else{
            if (!($this->session->userdata('cid') === NULL)){
                redirect(site_url()."clientes");
            }
        }
        
        $data['hayError'] = false;
        $this->load->view('inicio', $data);
    }
}
