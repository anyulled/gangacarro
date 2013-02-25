$(document).ready(function() {
    $("input[type='file']").attr("disabled", "true");
    
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
        }
    });

    $(".eliminar_foto").click(function() {
        link = $(this);
        if (confirm("Realmente desea borrar esta foto?")) {
            url = "../includes/Json.php";
            data = new Object();
            data.accion = 'eliminar_imagen';
            data.id_imagen_carro = $(this).data("id");
            $.getJSON(url, data, function(data) {
                if (data.suceed) {
                    link.closest(".controls").find("input[type=file]").removeAttr("disabled");
                    link.closest("img").remove().end().remove();
                }
            })
        }
        return false;
    });

    $(".agregar_foto").click(function(){
        $(this).closest(".controls").find("input[type=file]").removeAttr("disabled").trigger("click");
        return false;
    });
});