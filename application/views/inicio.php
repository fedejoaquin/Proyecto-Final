<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>

<!doctype html>

<html lang="en">
    <head>
        <link rel="icon" type="image/png" href="<?php echo site_url()."/img/favicon.png";?>" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <title>Auto SMART :: Inicio </title>
        <?php include 'componentes/recursos.phtml'; ?>
    </head>
    <body>
        <!-- BOTONERA PRINCIPAL -->
        <nav class="white" role="navigation">
            <div class="nav-wrapper container">
                
                <!-- BOTONES PANTALLA NORMAL -->
                <ul class="left">
                    <li><a class="brand-logo" href="<?php echo site_url(); ?>"><img class="img-logo" src="<?php echo site_url()?>img/Auto_Smart.png"</a></li>
                </ul>

                <ul class="right hide-on-med-and-down">
                    <li><a target="_blank" href="<?php echo site_url() ?>simulador">Simulador</a></li>
                    <li><a class="modal-trigger" data-target="modal_nosotros">Nosotros</a></li>
                    <li><a class="modal-trigger" data-target="intranet">Intranet</a></li>
                    <li><a class="modal-trigger" data-target="login">Iniciar sesión</a></li>
                </ul>

                <!-- BOTONES CELULARES -->
                <ul id="nav-mobile" class="side-nav">
                    <li>
                        <a class="modal-trigger" data-target="login">
                            <i class="material-icons left">account_circle</i>
                            Iniciar sesión
                        </a>
                    </li>
                    <li>
                        <a class="modal-trigger" data-target="intranet">
                            <i class="material-icons left">local_taxi</i>
                            Intranet
                        </a>
                    </li>
                    <li>
                        <a class="modal-trigger" data-target="modal_nosotros">
                            <i class="material-icons left">info</i>
                            Nosotros
                        </a>
                    </li>
                </ul>
                
                <!-- ICONO MENU PARA CELULARES -->
                <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
            </div>
        </nav>
        
        <!--PRIMER PARALLAX -->
        <div id="index-banner" class="parallax-container">
            <div class="section no-pad-bot">
                <div class="container">
                    <br><br>
                    <h1 class="header center teal-text text-lighten-2">Auto SMART</h1>
                    <div class="row center">
                        <h5 class="white-text header col s12 light">Una moderna e inteligente forma de trasladarse por la ciudad.</h5>
                    </div>
                    <div class="row center">
                        <h5 class="white-text header col s12 light">Eficiencia. Rapidez. Seguridad. Todo a un click de distancia.</h5>
                    </div>
                    <br><br>
                </div>
            </div>
            <div class="parallax">
                <img src="<?php echo site_url(); ?>img/Inicio.jpg" alt="Imagen no encontrada">
            </div>
        </div>

        <!--SECCION DATOS TRES COLUMNAS -->
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center teal-text"><i class="material-icons">flash_on</i></h2>
                            <h5 class="center">Inteligencia Artificial aplicada</h5>
                            <p class="light">
                                No somos el típico sistema de despacho de autos. Aplicando inteligencia artificial, logramos explotar la eficiencia en cuanto al uso del tiempo disponible por los usuarios, como las demoras estimadas por sobre las distancias desde el origen de tu pedido y hacia el destino del mismo.
                            </p>
                        </div>
                    </div>

                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center teal-text"><i class="material-icons">group</i></h2>
                            <h5 class="center">Visión de solidaridad</h5>
                            <p class="light">
                                Cooperamos para el beneficio cliente-empresa. Nuestro principal objetivo es maximizar las ganancias, así como los beneficios de los clientes; para esto nos basamos en el concepto de solidaridad, explotando los tiempos disponibles de espera de cada cliente, y a la vez asegurando el cumplimientos de los plazos comprometidos, haciendo uso de tecnologías de vanguardia.
                            </p> 
                        </div>
                    </div>

                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center teal-text"><i class="material-icons">settings</i></h2>
                            <h5 class="center">De simple uso</h5>
                            <p class="light">
                                Con un diseño responsive, adecuado para el uso desde plataformas móviles como webs, garantizamos el fácil uso de nuestro sistema para que la experiencia de usuario sea satisfactoria y adecuada a las demandas actuales. </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEGUNDO PARALLAX -->
        <div class="parallax-container valign-wrapper">
            <div class="section no-pad-bot">
                <div class="container">
                  <div class="row center">
                    <h5 class="header col s12 light">Seguimiento en tiempo real via Google Maps API</h5>
                  </div>
                </div>
            </div>
            <div class="parallax"><img src="<?php echo site_url(); ?>img/Inicio_2.jpg" alt="Imagen no encontrada"></div>
        </div>

        <!-- SECCION DATOS GOOGLE API -->
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12 center">
                        <h3><i class="mdi-content-send brown-text"></i></h3>
                        <h4>Seguimiento permanente</h4>
                        <p class="justify light">
                            Indicanos por dónde te pasamos a buscar, y hacia dónde vas, y ¡listo! Mediante el uso de tecnologías como Google Maps API, nuestra aplicación permite observar en tiempo real el posicionamiento del auto que se asignará para cumplir con tu pedido. Además, ofrecemos la posibilidad de la visualización del estado de la flota actual para la empresa, tendiendo a un control rápido, simple y en tiempo real del posicionamiento actual de cada recurso.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MODAL LOGIN -->
        <div id="login" class="modal">
            <hr>
            <h5 class="center">Iniciando Sesión</h5>
            <hr>
            <h6 class="center">..: Seleccione el modo para iniciar sesión :..</h6>
            <div class="modal-content">
                <div class="row center">
                    <div class="col s12 m3">
                        <a class="btn login" href="<?php echo site_url()?>login/loginFacebook">FACEBOOK</a>
                    </div>
                    <div class="col s12 m3">
                        <a class="btn login" href="<?php echo site_url()?>login/loginGMail">GOOGLE+</a>
                    </div>
                    <div class="col s12 m3">
                        <a id="btn-registrar" class="btn login">Registrarme</a>
                    </div>
                    <div class="col s12 m3">
                        <a id="btn-ingresar" class="btn login">Ingresar</a>
                    </div>
                    <?php 
                        if ($hayError){ ?>
                            <div class="col s10 m10 offset-s1 offset-m1 message_error"><?php echo $error; ?> </div>
                    <?php } ?>
                </div>
                <div id="div_registrar" class="row div_hide">
                    <?php 
                        $attr = array('class'=>'col s12 m6 offset-m3', 'autocomplete' => 'off');
                        echo form_open('login/registrar_cliente',$attr); 
                    ?>
                            <div class="input-field col s12">
                                <input id="r_username" name="r_username" type="text" class="validate" minlength=5>
                                <label for="r_username" data-error="Min: 5 caracteres">Usuario</label>
                                <?php echo form_error('r_username', '<div class="txt-form-error">','</div>'); ?>
                            </div>
                            <div class="input-field col s12">
                                <input id="r_telefono" name="r_telefono" type="number" class="validate" minlength=8>
                                <label for="r_telefono" data-error="Min: 8 números">Teléfono</label>
                                <?php echo form_error('r_telefono', '<div class="txt-form-error">','</div>'); ?>
                            </div>
                            <div class="input-field col s12">
                                <input id="r_password" name="r_password" type="password" class="validate" minlength=5>
                                <label for="r_password" data-error="Min: 5 caracteres">Password</label>
                                <?php echo form_error('r_password', '<div class="txt-form-error">','</div>'); ?>
                            </div>
                            <div class="input-field col s12">
                                <input id="r_confirm_password" name="r_confirm_password" type="password" class="validate" minlength=5>
                                <label for="r_confirm_password" data-error="Min: 5 caracteres">Confirme Password</label>
                                <?php echo form_error('r_confirm_password', '<div class="txt-form-error">','</div>'); ?>
                            </div>
                            <div class="center">
                                <button class="btn" type="submit" name="action">OK</button>
                            </div>
                        </form>
                </div>
                <div id="div_ingresar" class="row div_hide">
                    <?php 
                        $attr = array('class'=>'col s12 m6 offset-m3', 'autocomplete' => 'off');
                        echo form_open('login/login_cliente',$attr); 
                    ?>
                            <div class="input-field col s12">
                                <input id="username" name="username" type="text" class="validate">
                                <label for="username">Usuario</label>
                                <?php echo form_error('username', '<div class="txt-form-error">','</div>'); ?>
                            </div>
                            <div class="input-field col s12">
                                <input id="password" name="password" type="password" class="validate">
                                <label for="password">Password</label>
                                <?php echo form_error('password', '<div class="txt-form-error">','</div>'); ?>
                            </div>
                            <div class="center">
                                <button class="btn" type="submit" name="action">OK</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
        
        <!-- MODAL INTRANET -->
        <div id="intranet" class="modal">
            <hr>
            <h5 class="center">Accediendo a Intranet</h5>
            <hr>
            <h6 class="center">..: Indique DNI y password :..</h6>
            <div class="modal-content">
                <?php 
                    if ($hayError){ ?>
                        <div class="col s10 m10 offset-s1 offset-m1 message_error"><?php echo $error; ?> </div>
                <?php } ?>
                <div class="row">
                    <?php 
                        $attr = array('class'=>'col s12 m6 offset-m3', 'autocomplete' => 'off');
                        echo form_open('login/login_empleado',$attr); 
                    ?>
                            <div class="input-field col s12">
                                <input id="i_dni" name="i_dni" type="text" class="validate">
                                <label for="i_dni">DNI</label>
                                <?php echo form_error('i_dni', '<div class="txt-form-error">','</div>'); ?>
                            </div>
                            <div class="input-field col s12">
                                <input id="i_password" name="i_password" type="password" class="validate">
                                <label for="i_password">Password</label>
                                <?php echo form_error('i_password', '<div class="txt-form-error">','</div>'); ?>
                            </div>
                            <div class="center">
                                <button class="btn" type="submit" name="action">OK</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
        
        <!-- MODAL NOSOTROS -->
        <?php include 'componentes/modal_nosotros.phtml'; ?>

        <!-- FOOTER -->
        <?php include 'componentes/footer.phtml'; ?>
        
    </body>
</html>

