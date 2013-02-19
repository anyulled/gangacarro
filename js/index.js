var url = "http://" + document.location.host + "/includes/json.php";
function verificarDominio() {
    /* VERIFICACION DE DOMINIO */
    if (document.location.host === "gangacarro.com.ve" || document.location.host === "www.gangacarro.com.ve") {
        url = "http://" + document.location.host + "/includes/json.php";
    }
    else
    {
        url = "http://" + document.location.host + "/gangacarro/includes/json.php";
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
function modal() {
    $(".modal").modal("show");
    $(".modal").modal("hide");
    $(".close, #cerrar").click(function() {
        $(this).closest(".modal").modal("hide");
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
    if ($("#estado").length > 0) {
        llenarEstados("#estado");
        $("#estado").change(function() {
            llenarCiudades($(this).val(), "#ciudad");
        });
    }
    if ($("#estado").length > 0) {
        llenarMarcas("#marca");
        $("#marca").change(function() {
            llenarModelos($(this).val(), "#modelo");
        });
    }
    if ($("#tipo_vehiculo").length > 0) {
        llenarTiposVehiculo("#tipo_vehiculo");
    }
}
function eventosGlobales() {
    /* Configuración Global para peticiones ajax y envío de formularios*/
    $(document).ajaxStart(function() {
        setTimeout(function() {
            $(".modal").modal("show");
        }, 500);
    });
    $(document).ajaxSuccess(function() {
        setTimeout(function() {
            $(".modal").modal("hide");
        }, 500);
    });
    $(document).ajaxError(function() {
        setTimeout(function() {
            $(".modal").modal("hide");
        }, 250);
    });
    $("form").bind("submit", function() {
        $(".modal").modal("show");
    });
}
function validar_formulario() {
    $("form").each(function() {
        console.log("validando " + $(this).attr("name"));
        $(this).validate();
    });
}
function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}
$(document).ready(function() {
    eventosGlobales();
    modal();
    verificarDominio();
    llenar_marcas_estados_tipos();
    activar_desactivar();
    validar_formulario();
});