$(document).ready(function() {
    llenarMarcas("#marca_id");
    $("#marca_id").change(function() {
        llenarModelos($(this).val(), "#modelo_id");
    });
    $("#registrar").validate({
        rules: {
            precio: {
                digits: true,
                min: 1000},
            placa: {
                alphanumeric: true
            },
            foto1: {
                required: true,
                accept: "jpg|png"
            },
            foto2: {
                accept: "jpg|png"
            },
            foto3: {
                accept: "jpg|png"
            },
            foto4: {
                accept: "jpg|png"
            },
            foto5: {
                accept: "jpg|png"},
            caracteristicas: {
                maxlength: 500
            }
        },
        messages: {
            precio: {digits: "Por favor, no incluya puntos ni comas en el precio"}
        },
        submitHandler: function() {
            console.log("validando");
            if ($("#registrar").valid()) {
                registrar.submit();
            }
        }
    });
});