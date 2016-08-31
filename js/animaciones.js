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
    
    // Evento de mouse por encima de las estrellas
    $('.estrellasValoracion').hover(
        // El mouse se posiciona sobre el elemento
        function(){
            // Rellenamos las estrellas desde la primera y hasta la actual 
            $(this).prevAll().andSelf().addClass('estrellaVotar');

            // Quitamos relleno a las estrellas desde la siguiente a la actual y hasta la última
            $(this).nextAll().removeClass('estrellaValoracion'); 
         },

        // El mouse deja de posicionarse sobre el elemento
        function(){
            // Obtenemos el valor de la valoracion actual
            var valor = $('#cv_valor').val();
            
            if (valor !== 0){
                // Rellenamos las estrellas según la valoración indicada.
                $(this).parent().find('#e' + valor).prevAll().andSelf().addClass('estrellaVotar');
                
                // Quitamos relleno a las estrellas sobren
                $(this).parent().find('#e'+ valor).nextAll().removeClass('estrellaVotar');
            }else{
                // Quitamos relleno a las estrellas sobren
                $(this).parent().find('#e0').nextAll().andSelf().removeClass('estrellaVotar');
            }
        }
    );
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