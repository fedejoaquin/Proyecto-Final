<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    
    /**
     * Chequea que el usuario no se encuentre logueado.
     * Redirige a Clientes o Intranet si hay sesión abierta.
     * Redirige a Inicio si no hay sesión abierta.
     */
    public function index(){
        $this->chequear_login_redirect();
        redirect(site_url());
    }
    
    /**
    * Login local de empleados registrados. 
    * Valida que el usuario no se encuentre logueado, en tal caso redirige al controlador que corresponda. 
    * Valida los datos ingresados, loguea y redirege al usuario a Empleados en caso de corresponder.
    * Caso contrario redirige a Inicio.
    */
    public function login_empleado(){
        $this->chequear_login_redirect();
        
        //Chequeo de datos enviados por el formulario
        if ($this->form_validation->run('login/login_empleado') == FALSE){
            $data['hayError'] = true;
            $data['error'] = 'Error validando campos ingresados.';
            $this->load->view('inicio', $data);
        }else{ 
            $dni = $this->input->post('i_dni');
            $password = $this->input->post('i_password');
            
            //Consulta en busca de un empleados con dni $dni
            $resultado = $this->MEmpleados->ver($dni);

            //Si no hay usuario.
            if( count($resultado) === 0 ){
                $data['hayError'] = true;
                $data['error'] = 'Usuario inválido.';
                $this->load->view('inicio', $data);
            }else{
                //Hash de password enviado por el empleado
                $hash_pass = hash('sha256',$password);
                $hash_pass_db = $resultado['password'];

                //Chequeo de contraseña correcta. 
                if ( $hash_pass === $hash_pass_db ){
                    
                    //Parseamos los roles arrojados por la BD.
                    $roles_array = $this->MEmpleados->get_roles($dni);
                    $roles = array();
                    
                    for($i=0; $i<count($roles_array); $i++){
                        array_push($roles, $roles_array[$i]['rol']);
                    }
                    
                    //Creo la sesión del empleado, con sus datos.
                    $this->session->set_userdata('eid',$dni);
                    $this->session->set_userdata('nombre',$resultado['nombre']);
                    $this->session->set_userdata('roles', $roles );
                    
                    redirect(site_url()."intranet");
                    
                }else{
                    //Pass incorrecto
                    $data['hayError'] = true;
                    $data['error'] = 'Password inválido.';
                    $this->load->view('inicio', $data);
                } 
            }
        }
    }
    
    /**
    * Login local de cliente registrado. 
    * Valida que el usuario no se encuentre logueado, en tal caso redirige al controlador que corresponda. 
    * Valida los datos ingresados, y loguea al usuario como cliente en caso de corresponder.
    * Caso contrario redirige a Inicio.
    */
    public function login_cliente(){
        $this->chequear_login_redirect();
        
        //Chequeo de datos enviados por el formulario
        if ($this->form_validation->run('login/login_cliente') == FALSE){
            $data['hayError'] = true;
            $data['error'] = 'Error validando campos ingresados.';
            $this->load->view('inicio', $data);
        }else{ 
            $usuario = $this->input->post('username');
            $password = $this->input->post('password');
            
            //Consulta en busca de un cliente con nombre $usuario
            $resultado = $this->MClientes->ver_por_nombre($usuario, -1);

            //Si no hay usuario.
            if( count($resultado) === 0 ){
                $data['hayError'] = true;
                $data['error'] = 'Usuario inválido.';
                $this->load->view('inicio', $data);
            }else{
                //Hash de password enviado por el empleado
                $hash_pass = hash('sha256',$password);
                $hash_pass_db = $resultado['password'];

                //Chequeo de contraseña correcta. 
                if ( $hash_pass === $hash_pass_db ){
                    //Creo la sesión del cliente, con sus datos.
                    $this->session->set_userdata('cid',$resultado['id']);
                    $this->session->set_userdata('nombre',$usuario);

                    redirect(site_url()."clientes");
                }else{
                    //Pass incorrecto
                    $data['hayError'] = true;
                    $data['error'] = 'Password inválido.';
                    $this->load->view('inicio', $data);
                } 
            }
        }
    }
    
    /**
    * Registro local de cliente. 
    * Valida la creación de la cuenta con el username indicado; en caso de éxito,
    * crea la cuenta, loguea al usuario, y redirige hacia clientes.
    * Caso contrario redirige a Inicio.
    */
    public function registrar_cliente(){
        $this->chequear_login_redirect();
        
        //Chequeo de datos enviados por el formulario
        if ($this->form_validation->run('login/registrar_cliente') == FALSE){
            $data['hayError'] = true;
            $data['error'] = 'Error validando campos ingresados.';
            $this->load->view('inicio', $data);
        }else{ 
            $usuario = $this->input->post('r_username');
            $password = $this->input->post('r_password');
            
            //Consulta en busca de un cliente con nombre $usuario
            $resultado = $this->MClientes->ver_por_nombre($usuario, -1);
            
            //Si no existe un usuario con dicho username.
            if( count($resultado) === 0 ){
                $datos['nombre'] = $usuario;
                $datos['password'] = $password;
                $datos['id_ws'] = -1;
                
                //Damos de alta al nuevo cliente
                if ( $this->MClientes->alta($datos) ){
                    $resultado = $this->MClientes->ver_por_nombre($usuario, -1);
                    
                    //Creo la sesión del cliente, con sus datos.
                    $this->session->set_userdata('cid',$resultado['id']);
                    $this->session->set_userdata('nombre',$usuario);

                    redirect(site_url()."clientes");
                }else{
                    //Error al generar el alta de cliente
                    $data['hayError'] = true;
                    $data['error'] = 'Se produjo un error de procesamiento.';
                    $this->load->view('inicio', $data);
                }
            }else{
                //Username ya utilizado
                $data['hayError'] = true;
                $data['error'] = 'Nombre de usuario ya utilizado.';
                $this->load->view('inicio', $data);
            }
        }
    }
    
    /**
     * Login vía FACEBOOK. 
     * Valida los tokens, crea la info de session, utilizando la librerías de Facebook.
     * Redirige a clientes/index.
     */
    public function loginFacebook(){             
        $this->chequear_login_redirect();
        
        $this->load->library('facebook', array('appId' => '859567024175774', 'secret' => 'ef384ba72352d826abddd994adffac73'));
        //Si esta procesando el callback de Facebook exitoso, proceso los datos y creo sesión
         if($this->facebook->getUser()){
            $dataUser = $this->facebook->api('/me/');
            
            $datos['nombre'] = $dataUser['name'];
            $datos['password'] = '';
            $datos['id_ws'] = $dataUser['id'];
            
            //Consultamos la existencia de una cuenta con los datos actuales indicados por Facebook
            $resultado = $this->MClientes->ver_por_nombre($datos['nombre'], $datos['id_ws']);
            
            ///Si el cliente no esta registrado con cuenta proviniente de loginFacebook se crea la nueva cuenta
            if (count($resultado) === 0){
                //Damos de alta al nuevo cliente proviniente de loginFacebook
                if ($this->MClientes->alta($datos)){
                    $resultado = $this->MClientes->ver_por_nombre($datos['nombre'], $datos['id_ws']);

                    //Creo la sesión del cliente, con sus datos.
                    $this->session->set_userdata('cid',$resultado['id']);
                    $this->session->set_userdata('nombre',$datos['nombre']);

                    redirect(site_url()."clientes");
                }else{
                    //Error al generar el alta de cliente
                    $data['hayError'] = true;
                    $data['error'] = 'Se produjo un error al intentar loguearse.';
                    $this->load->view('inicio', $data);
                }
            }else{
                //Creo la sesión del cliente, con sus datos.
                $this->session->set_userdata('cid',$resultado['id']);
                $this->session->set_userdata('nombre',$resultado['nombre']);

                redirect(site_url()."clientes");
            }
        }else{
            //Se intenta loguear con facebook, iniciamos la autenticación
            redirect( $this->facebook->getLoginUrl());
        }
    }
    
    /**
     * Login vía GMAIL. 
     * Valida los tokens, crea la info de session, utilizando la librerías de Google+.
     * Redirige a clientes/index.
     */
    public function loginGMail(){
        $this->chequear_login_redirect();
        
        //Include two files from google-php-client library in controller
        require_once APPPATH . "libraries/google-api/src/Google/autoload.php";

        // Store values in variables from project created in Google Developer Console
        $client_id = '972647013173-tfur0284g6nldqpqkt032tbs8p57b1vj.apps.googleusercontent.com';
        $client_secret = 'Ve35Io2R7N8txylkHwKVMtKr';
        $redirect_uri = 'http://localhost/PF/login/loginGMail';
        $simple_api_key = 'AIzaSyBLevig4FVa6JYaTiUo-jRzsoGnjiooYAg';

        // Create Client Request to access Google API
        $client = new Google_Client();
        $client->setApplicationName("IA-Taxis Web");
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->setDeveloperKey($simple_api_key);
        $client->addScope("https://www.googleapis.com/auth/userinfo.profile");

        // Send Client Request
        $objOAuthService = new Google_Service_Oauth2($client);

        // Add Access Token to Session
        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $this->session->set_userdata('access_token', $client->getAccessToken());
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }

        // Set Access Token to make Request
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']){
            $client->setAccessToken($this->session->userdata('access_token'));
        }

        // Get User Data from Google and store them in $data
        if ($client->getAccessToken()) {
            $dataUser = $objOAuthService->userinfo->get();
            
            $datos['nombre'] = $dataUser['name'];
            $datos['password'] = '';
            $datos['id_ws'] = $dataUser['id'];
            
            //Consultamos la existencia de una cuenta con los datos actuales indicados por Google+
            $resultado = $this->MClientes->ver_por_nombre($datos['nombre'], $datos['id_ws']);
            
            ///Si el cliente no esta registrado con cuenta proviniente de loginGoogle+ se crea la nueva cuenta
            if (count($resultado) === 0){
                //Damos de alta al nuevo cliente proviniente de loginGoogle+
                if ($this->MClientes->alta($datos)){
                    $resultado = $this->MClientes->ver_por_nombre($datos['nombre'], $datos['id_ws']);

                    //Creo la sesión del cliente, con sus datos.
                    $this->session->set_userdata('cid',$resultado['id']);
                    $this->session->set_userdata('nombre',$datos['nombre']);

                    redirect(site_url()."clientes");
                }else{
                    //Error al generar el alta de cliente
                    $data['hayError'] = true;
                    $data['error'] = 'Se produjo un error al intentar loguearse.';
                    $this->load->view('inicio', $data);
                }
            }else{
                //Creo la sesión del cliente, con sus datos.
                $this->session->set_userdata('cid',$resultado['id']);
                $this->session->set_userdata('nombre',$resultado['nombre']);

                redirect(site_url()."clientes");
            }
            
        } else {
            $authUrl = $client->createAuthUrl();
            redirect($authUrl); 
        }
    }
    
    /**
     * Destruye la session. Redirige a Inicio.
     */
    public function logout(){
        $this->session->sess_destroy();  
        redirect(site_url());
    }
    
    /**
    * Chequea los datos de session. 
    * - Si la session indica que ya se logueó, entonces redirige al controlador correspondiente.
    */
    private function chequear_login_redirect(){
        if (!($this->session->userdata('eid') === NULL)){
            redirect(site_url()."intranet");
        }else{
            if (!($this->session->userdata('cid') === NULL)){
                redirect(site_url()."clientes");
            }
        }
    }
}
