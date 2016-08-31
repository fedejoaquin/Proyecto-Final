<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<!doctype html>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <title>Auto SMART :: Clientes </title>
        
        <?php include 'componentes/recursos.phtml'; ?>
        <script src="<?php echo site_url(); ?>js/clientes/controlador.js"></script>
        <script src="<?php echo site_url(); ?>js/clientes/vista.js"></script>
        <script src="<?php echo site_url(); ?>js/mapa.js"></script>
        <script 
            async defer 
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTSGP0llQCtftW0W4QVaEh60dzNyOv09M&callback=mapa.init">         
        </script> 
        
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
                    <li><a class="modal-trigger" data-target="nosotros">Nosotros</a></li>
                    <li>
                        <a class="dropdown-button" href="#" data-activates="dropAcciones">
                            <i class="material-icons right">arrow_drop_down</i>
                            Acciones
                        </a>
                        <ul id='dropAcciones' class='dropdown-content'>
                            <li>
                                <a href="#datos_personales" onClick="clientes.datos.ver()">
                                    <i class="material-icons left">assignment_ind</i>
                                    Mis datos
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#solicitar_viaje" onClick="clientes.viajes.solicitar()">
                                    <i class="material-icons left">add_circle</i>
                                    Solicitar viaje
                                </a>
                            </li>
                            <li>
                                <a href="#estado" onClick="clientes.viajes.estado()">
                                    <i class="material-icons left">satellite</i>
                                    Ver estado viajes
                                </a>
                            </li>
                            <li>
                                <a href="#historial_viajes" onClick="clientes.viajes.ver_historial()">
                                    <i class="material-icons left">history</i>
                                    Historial viajes
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="<?php echo site_url(); ?>login/logout">Cerrar sesión</a></li>
                </ul>

                <!-- BOTONES CELULARES -->
                <ul id="nav-mobile" class="side-nav">
                    <li>
                        <a class="modal-trigger" data-target="nosotros">
                            <i class="material-icons left">info</i>
                            Nosotros
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#datos_personales" onClick="clientes.datos.ver()">
                            <i class="material-icons left">assignment_ind</i>
                            Mis datos
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#solicitar_viaje" onClick="clientes.viajes.solicitar()">
                            <i class="material-icons left">add_circle</i>
                            Solicitar viaje
                        </a>
                    </li>
                    <li>
                        <a href="#estado" onClick="clientes.viajes.estado()">
                            <i class="material-icons left">satellite</i>
                            Ver estado viajes
                        </a>
                    </li>
                    <li>
                        <a href="#historial_viajes" onClick="clientes.viajes.ver_historial()">
                            <i class="material-icons left">history</i>
                            Historial viajes
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="<?php echo site_url(); ?>login/logout">
                            <i class="material-icons left">account_circle</i>
                            Cerrar sesión
                        </a>
                    </li>
                </ul>
                
                <!-- ICONO MENU PARA CELULARES -->
                <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
            </div>
        </nav>
        
        <!-- CUERPO PRINCIPAL -->
        <div class="row">
            <h5 class="row txt-cabecera">¡Bienvenido al sistema Auto-SMART!</h5>
            
            <div id="div_mis_viajes" class="col s12 m8 offset-m2 div_hide">
                <div class="row">
                    <h6 class="col s12 m12 center-align">..: DATOS VIAJES :..</h6>
                </div>
                <div class="row">
                    <table class="responsive-table striped highlight">
                        <thead>
                            <tr>
                                <td>Fecha</td>
                                <td>Origen</td>
                                <td>Destino</td>
                                <td>Conductor</td>
                                <td>¿A tiempo?</td>
                                <td>Diferencia</td>
                            </tr>
                        </thead>
                        <tbody id="tblHistorialViajes">
                            <?php foreach ($historial as $viaje) { ?>
                                <tr>
                                    <td> <?php echo $viaje['fecha']; ?> </td>
                                    <td> <?php echo $viaje['origen']; ?> </td>
                                    <td> <?php echo $viaje['destino']; ?> </td>
                                    <td> <?php echo $viaje['nombre']; ?> </td>
                                    <td> <?php echo ( $viaje['a_tiempo'] ? 'Sí' : 'No' ); ?> </td>
                                    <td class="<?php  echo ($viaje['a_tiempo'] ? 'tiempo_ok' : 'tiempo_error'); ?>"> 
                                        <b>
                                            <?php echo ($viaje['a_tiempo'] ? '-' : '+' ).$viaje['diferencia']; ?>
                                        </b> 
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DIV PARA INGRESAR NUEVO VIAJE -->
            <div id="div_nuevo_viaje" class="col s12 m8 offset-m2">
                <div class="row">
                    <h6 class="col s12 m12 center-align">..: NUEVO VIAJE :..</h6>
                </div> 
                <div class="row">
                    <div class="input-field col s12 m6">
                        <input id="origen_viaje" class="validate" type="text" minlength=3 placeholder="Ingrese origen...">
                        <label for="origen_viaje" data-error="Min: 3 caracteres">Origen</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="destino_viaje" class="validate" type="text" minlength=3 placeholder="Ingrese destino...">
                        <label for="destino_viaje" data-error="Min: 3 caracteres">Destino</label>
                    </div>
                    <div class="input-field col s6 m3">
                        <select id="hora_max" onchange="clientes.viajes.set_margen()">
                            <option value="0" disabled selected>Sin datos</option>
                            <option value="5">5 min.</option>
                            <option value="10">10 min.</option>
                            <option value="15">15 min.</option>
                            <option value="20">20 min.</option>
                            <option value="25">25 min.</option>
                            <option value="30">30 min.</option>
                            <option value="35">35 min.</option>
                            <option value="40">40 min.</option>
                        </select>
                        <label>Margen tiempo</label>
                    </div>
                    <div class="input-field col s6 m3">
                        <input id="hora_arribo" type="text" placeholder="Sin datos." disabled >
                        <label for="hora_arribo">Hora max arribo dest.</label>
                    </div>
                    <div class="col s10 m3 offset-m1 offset-s1">
                        <a class="dropdown-button btn" href="#" data-activates="dropValidacion">
                            <i class="material-icons right">arrow_drop_down</i>
                            Acciones
                        </a>
                        <ul id='dropValidacion' class='dropdown-content'>
                            <li>
                                <a href="#validar" onclick="clientes.viajes.validar()">
                                    <i class="material-icons left">my_location</i>
                                    Validar
                                </a>
                            </li>
                            <li>
                                <a href="#confirmar" onClick="clientes.viajes.pre_confirmar()">
                                    <i class="material-icons left">done</i>
                                    Confirmar..
                                </a>
                            </li>
                        </ul>
                    </div>  
                </div>
            </div>

            <!-- DIV PARA VISTA EN TIEMPO REAL VIAJE -->
            <div id="div_viaje_estado" class="col s12 m8 offset-m2 div_hide">
                <div class="row">
                    <h6 class="col s12 m12 center-align">..: ESTADO VIAJE/s ACTUAL/es :..</h6>
                </div>
                <div class="row">
                    <table class="responsive-table striped highlight">
                        <thead>
                            <tr>
                                <td>Origen</td>
                                <td>Destino</td>
                                <td>Margen Arribo</td>
                                <td>Estado</td>
                                <td>Oper.</td>
                            </tr>
                        </thead>
                        <tbody id="tblEstadoViajes">
                            <?php foreach ($viajes_actuales as $viaje) { ?>
                                <tr id="<?php echo 'fila_'.$viaje['id'] ?>">
                                    <?php $datos = "'".$viaje['id']."','".$viaje['ingreso']."','".$viaje['estado']."',".$viaje['id_recurso']; ?>
                                    <td> <?php echo $viaje['origen']; ?> </td>
                                    <td> <?php echo $viaje['destino']; ?> </td>
                                    <td> <?php echo $viaje['max_arribo']; ?> </td>
                                    <td> <?php echo $viaje['estado']; ?> </td>
                                    <td> 
                                        <a class="btn-floating" onclick="clientes.viajes.info(<?php echo $datos; ?>)">
                                            <i class="material-icons">info</i>
                                        </a>
                                        <?php 
                                        if ($viaje['estado'] !== "Finalizado"){ 
                                            $clase = "disabled";
                                        }else{
                                            $clase = "";
                                        }?>
                                        <a class="btn-floating <?php echo $clase;?>" onclick="clientes.viajes.calificacion.calificar(<?php echo $viaje['id'].",".$viaje['id_recurso'].",'".$viaje['estado']."'"; ?>)">
                                            <i class="material-icons">stars</i>
                                        </a>
                                    </td>
                                    
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- DIV PARA DATOS CLIENTE -->
            <div id="div_mis_datos" class="col s12 m8 offset-m2 div_hide">
                <div class="row">
                    <h6 class="col s12 m12 center-align">..: MIS DATOS :..</h6>
                </div>
                <div class="row">
                    <div class="input-field col s6 m3">
                        <input id="id_usuario" type="text" value="<?php echo $this->session->userdata('cid'); ?>" disabled>
                        <label for="id_usuario">ID usuario</label>
                    </div>
                    <div class="input-field col s12 m9">
                        <input id="nombre_usuario" type="text" value="<?php echo $this->session->userdata('nombre'); ?>" disabled>
                        <label for="nombre_usuario">Nombre usuario</label>
                    </div>
                    <div class="input-field col s6 m6">
                        <input id="md_telefono" type="number" value="<?php echo $this->session->userdata('telefono'); ?>">
                        <label for="md_telefono">Telefono</label>
                    </div>
                    <div class="input-field col s6 m3">
                        <a class="btn" onclick="clientes.datos.editar()">
                            <i class="material-icons">edit</i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!--DIV PARA VISUALIZACIÓN DEL MAPA -->
            <div class="row center-align">
                <div id="div_mapa" class="col s10 m8 offset-s1 offset-m2 mapa">
                </div>
            </div> 
        </div>
         
        <!-- MODAL CONFIRMAR VIAJE -->
        <div id="confirmar_viaje" class="modal">
            <hr>
            <h5 class="center">Confirmando viaje</h5>
            <hr>
            <h6 class="center">..: Confirme los datos ingresados :..</h6>
            <div class="modal-content">
                <div class="row">
                    <div class="input-field col s12">
                        <input id="telefono" name="telefono" type="number" value="<?php echo $this->session->userdata('telefono'); ?>">
                        <label for="telefono">Telefono</label>
                    </div>
                    <div class="input-field col s12">
                        <input id="referencia" name="referencia" type="text" placeholder="Ej: Rejas verdes; edificio vecino Empresa g.SA" maxlength="50">
                        <label for="referencia">Referencia adicional</label>
                    </div>
                    <div class="center">
                        <button class="btn" onclick="clientes.viajes.confirmar()">OK</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MODAL INFO ESTADO VIAJE -->
        <div id="estado_viaje" class="modal">
            <hr>
            <h5 class="center">Info Estado Viaje</h5>
            <hr>
            <h6 class="center">..: Estado actual en el sistema :..</h6>
            <div class="modal-content">
                <div class="row">
                    <div class="input-field col s6 l2">
                        <input id="ev_id" type="text" value="" placeholder="">
                        <label for="ev_id">ID</label>
                    </div>
                    <div class="input-field col s12 l6">
                        <input id="ev_ingreso" type="text" value="" placeholder="">
                        <label for="ev_ingreso">Ingreso</label>
                    </div>
                    <div class="input-field col s12 l4">
                        <input id="ev_estado" type="text" value="" placeholder="">
                        <label for="ev_estado">Estado</label>
                    </div>
                    <div class="input-field col s12 l6">
                        <input id="ev_conductor" type="text" value="" placeholder="">
                        <label for="ev_conductor">Conductor Asociado</label>
                    </div>
                    <div class="input-field col s12 l3">
                        <input id="ev_patente" type="text" value="" placeholder="">
                        <label for="ev_patente">Patente auto</label>
                    </div>
                    <div class="input-field col s12 l3">
                        <input id="ev_marca" type="text" value="" placeholder="">
                        <label for="ev_marca">Marca auto</label>
                    </div>
                    <div class="input-field col s12 l3">
                        <input id="ev_modelo" type="text" value="" placeholder="">
                        <label for="ev_modelo">Modelo auto</label>
                    </div>
                    <div class="input-field col s12 l3">
                        <input id="ev_color" type="text" value="" placeholder="">
                        <label for="ev_color">Color auto</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MODAL CALIFICAR VIAJE -->
        <div id="calificar_viaje" class="modal">
            <hr>
            <h5 class="center">Calificando viaje</h5>
            <hr>
            <h6 class="center">..: Su opinión nos resulta de interés :..</h6>
            <div class="modal-content">
                <div class="row">
                    <div class="input-field col s6 m2 offset-m1">
                        <input id="cv_id" type="text" disabled value="" placeholder="">
                        <label for="cv_id">ID Viaj.</label>
                    </div>
                    <div class="input-field col s6 m2">
                        <input id="cv_id_recurso" type="text" disabled value="" placeholder="">
                        <label for="cv_id_recuros">ID Rec.</label>
                    </div>
                    <div class="input-field col m2">
                        <input id="cv_valor" disabled type="number" placeholder="0">
                        <label for="cv_valor">Calif.</label>
                    </div>
                    <div class="input-field col s12 m4">
                        <div class="divValoracion">
                            <div id="e1" class="estrellasValoracion"></div>
                            <div id="e2" class="estrellasValoracion"></div>
                            <div id="e3" class="estrellasValoracion"></div>
                            <div id="e4" class="estrellasValoracion"></div>
                            <div id="e5" class="estrellasValoracion"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12 m10 offset-m1">
                        <input id="cv_comentario" type="text" maxlength="100" value="" placeholder="Mejorariría ...">
                        <label for="cv_comentario">Comentario adicional</label>
                    </div>
                </div>
                <div class="row">
                    <div class="center">
                        <button class="btn" onclick="clientes.viajes.calificacion.confirmar()">OK</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MODAL NOSOTROS -->
        <?php include 'componentes/modal_nosotros.phtml'; ?>
        
        <!-- MODAL ESPERA -->
        <?php include 'componentes/modal_espera.phtml'; ?>

        <!-- FOOTER -->
        <?php include 'componentes/footer.phtml'; ?>
    </body>
</html>