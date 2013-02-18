var active = true;
var url = "http://" + document.location.host + "/includes/Json.php";
function verificarDominio() {
    /* VERIFICACION DE DOMINIO */
    if (document.location.host === "gangacarro.com.ve" || document.location.host === "www.gangacarro.com.ve") {
        url = "http://" + document.location.host + "/includes/Json.php";
    }
    else
    {
        url = "http://" + document.location.host + "/gangacarro/includes/Json.php";
    }

}
function llenarEstados(select) {
    $(select + " option").remove();
    $("<option value=''> - Seleccione </option>").appendTo(select);
    $.getJSON(url, {
        query: "estado"
    }, function(data) {
        if (data.suceed) {
            for (var elemento in data.data) {
                if (typeof data.data[elemento] === "object") {
                    $("<option value='" + data.data[elemento].id + "'>" + data.data[elemento].nombre + "</option>").appendTo(select);
                }
            }
        }
    });
}
function llenarCiudades(estado_id, select) {
    $(select + " option").remove();
    $("<option value=''> - Seleccione </option>").appendTo(select);
    $.getJSON(url, {
        query: "ciudad",
        where: "estado_id",
        campo: estado_id
    }, function(data) {
        if (data.suceed) {
            for (var elemento in data.data) {
                if (typeof data.data[elemento] === "object") {
                    $("<option value='" + data.data[elemento].id + "'>" + data.data[elemento].nombre + "</option>").appendTo(select);
                }
            }
        }
    });
}
function llenarTiposVehiculo(select) {
    $(select + " option").remove();
    $("<option value=''> - Seleccione</option>").appendTo(select);
    $.getJSON(url, {
        query: "tipo_vehiculo"
    }, function(data) {
        if (data.suceed) {
            for (var elemento in data.data) {
                if (typeof data.data[elemento] === "object") {
                    $("<option value='" + data.data[elemento].id + "'>" + data.data[elemento].nombre + "</option>").appendTo(select);
                }
            }
        }
    });
}
function llenarMarcas(select) {
    $(select + " option").remove();
    $("<option value=''> - Seleccione</option>").appendTo(select);
    $.getJSON(url, {
        query: "marca"
    }, function(data) {
        if (data.suceed) {
            for (var elemento in data.data) {
                if (typeof data.data[elemento] === "object") {
                    $("<option value='" + data.data[elemento].id + "'>" + toTitleCase(data.data[elemento].nombre) + "</option>").appendTo(select);
                }
            }
        }
    });
}
function llenarModelos(marca_id, select) {
    $(select + " option").remove();
    $("<option value=''> - Seleccione </option>").appendTo(select);
    $.getJSON(url, {
        query: "modelo",
        where: "marca_id",
        campo: marca_id
    }, function(data) {
        if (data.suceed) {
            for (var elemento in data.data) {
                if (typeof data.data[elemento] === "object") {
                    $("<option value='" + data.data[elemento].id + "'>" + toTitleCase(data.data[elemento].nombre) + "</option>").appendTo(select);
                }
            }
        }
    });
}

function activar_desactivar() {
    $("a.seleccionarTodos").click(function() {
        $(this).parent("div").next("div.row").find("input:checkbox").attr("checked", true);
        return false;
    });
    $("a.deseleccionarTodos").click(function() {
        $(this).parent("div").next("div.row").find("input:checkbox").attr("checked", false);
        return false;
    });
}
function llenar_marcas_estados_tipos() {
    /* LLENAR ESTADOS Y MARCAS */
    llenarEstados("#estado");
    llenarMarcas("#marca");
    llenarTiposVehiculo("#tipo_vehiculo");
    $("#estado").change(function() {
        llenarCiudades($(this).val(), "#ciudad");
    });
    $("#marca").change(function() {
        llenarModelos($(this).val(), "#modelo");
    });
}
function eventosGlobales() {
    /* Configuración Global para peticiones ajax y envío de formularios*/
    $("body").ajaxStart(function() {
        setTimeout(function() {
            /*$.fancybox('<h3>Cargando...</h3>', {
             modal: true,
             centerOnScroll: true,
             transitionIn: 'none'
             });*/
        }, 500);
    });
    $("body").ajaxSuccess(function() {
        setTimeout(function() {
            /*$.fancybox.close();*/
        }, 500);
    });
    $("body").ajaxError(function() {
        setTimeout(function() {
            /*$.fancybox.close();*/
        }, 250);
    });
    $("form").bind("submit", function() {
        /*$.fancybox('<h4>Procesando, por favor espere...</h4>', {
         modal: true,
         centerOnScroll: true,
         transitionIn: 'none'
         });
         setTimeout(function() {
         $.fancybox.close();
         }, 250);
         */
    });
}
function validar_formulario() {
    $("form").each(function() {
        $(this).validate();
    });
}
function toTitleCase(str)
{
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}
$(document).ready(function() {
    eventosGlobales();
    verificarDominio();
    llenar_marcas_estados_tipos();
    activar_desactivar();
    //validar_formulario();
});