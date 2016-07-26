//Animaciones javascript via Materialize

$(document).ready(function(){
    $('.button-collapse').sideNav();
    $('.parallax').parallax();
    $('.modal-trigger').leanModal();
    $('ul.tabs').tabs();
    
    $(document).ready(function() {
        $('select').material_select();
    });
    
    $('.dropdown-button').dropdown({
        inDuration: 300,
        outDuration: 225,
        constrain_width: false, // Does not change width of dropdown to that of the activator
        hover: false, // Activate on hover
        gutter: 0, // Spacing from edge
        belowOrigin: true, // Displays dropdown below the button
        alignment: 'left' // Displays dropdown with edge aligned to the left of button
    });
    
    
    $("#btn-registrar").click(function() {
        $("#div_ingresar").hide();
        $("#div_registrar").show("size");
    });
    
    $("#btn-ingresar").click(function() {
        $("#div_registrar").hide();
        $("#div_ingresar").show("size");
    });
});

var auxiliar = {
    
    mensaje : function (mensaje, tiempo, clase){
        Materialize.toast(mensaje, tiempo ,clase);
    },
    
    espera : {
        lanzar : function(){
            $('#modalEspera').openModal();
        },
    
        detener : function(){
            $('#modalEspera').closeModal();
        }
    }
};// FIN AUXILIAR