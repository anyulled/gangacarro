var mensaje = "";
$(document).ready(function() {
    $.validator.addMethod(
            "fecha",
            function(value, element) {
                return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
            },
            "Fecha Inválida.");
    $.validator.addMethod("alphanumeric", 
            function(value, element) {
        return this.optional(element) || /^[a-z0-9\-]+$/i.test(value);
    }, 
            "Este campo debe tener solo letras numeros y guiones.");
    $.validator.setDefaults({
        errorClass: "help-inline",
        validClass: "help-inline",
        highlight: function(element) {
            $(element).parents(".control-group:eq(0)").removeClass("success").addClass("error");
        },
        unhighlight: function(element) {
            $(element).parents(".control-group:eq(0)").removeClass("error").addClass("success").find("label[generated='true']").remove();
        }
    });

    $("input[type='submit']").click(function() {
        var valor = $(this).val();
        mensaje = "¿Confirma que desea " + valor + " este elemento ?";
        if ($(this).parents("form").eq(0).valid()) {
            return confirm(mensaje);
        } else {
            return false;
        }
    });
});