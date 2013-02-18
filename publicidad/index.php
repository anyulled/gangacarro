<?php

// <editor-fold defaultstate="collapsed" desc="init">
include '../includes/constants.php';
$pagina = "publicidad/index.html.twig";
$variables = array();
$variables['accion'] = "consultar";
$variables['modolectura'] = true;
$pb = new publicacion();
$usuario = new usuario();
$usuario->confirmar_miembro();
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Controlador">
if (isset($_POST['accion'])) {
    $data = $_POST;
    unset($data['accion']);
    $data['usuario_id'] = $_SESSION['usuario']['id'];
    switch ($_POST['accion']) {
        case "Registrar":
            unset($data['id']);
            $result = $pb->insertar($data);
            if ($result['suceed']) {
                $variables['tipomensaje'] = "alert-info";
                $variables['mensaje'] = "Registro creado con exito";
                $pagina = "publicidad/index.html.twig";
            } else {
                $variables['tipomensaje'] = "alert-warn";
                $variables['mensaje'] = "No se puedo crear el registro. por favor intente de nuevo";
                $variables['codigo'] = $result;
                $pagina = "publicidad/formulario.html.twig";
            }
            break;
        case "Editar":
            $id = $_POST['id'];
            $result = $pb->actualizar($id, $data);
            if ($result['suceed']) {
                $variables['tipomensaje'] = "alert-info";
                $variables['mensaje'] = "Registro editado con exito";
                $pagina = "publicidad/index.html.twig";
            } else {
                $variables['publicacion'] = $_POST;
                $variables['tipomensaje'] = "alert-warn";
                $variables['codigo'] = $result;
                $variables['mensaje'] = "No se puedo editar el registro. por favor intente de nuevo";
                $pagina = "publicidad/formulario.html.twig";
            }
            break;
        case "Eliminar":
            $id = $_POST['id'];
            $result = $pb->borrar($id);
            if ($result['suceed']) {
                $variables['tipomensaje'] = "alert-info";
                $variables['mensaje'] = "Registro eliminado con exito";
                $_GET['accion'] = "listar";
            } else {
                $variables['codigo'] = $result;
                $variables['tipomensaje'] = "alert-warn";
                $variables['mensaje'] = "No se puedo eliminar el registro. por favor intente de nuevo";
                $pagina = "publicidad/index.html.twig";
            }
            break;
        default:
            $variables['mensaje'] = "Opcion invalida";
            $variables['tipomensaje'] = "alert-error";
            break;
    }
}
if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case "listar":
            $pagina = "publicidad/index.html.twig";
            $publicidad = $pb->listar();
            $variables['publicaciones'] = $publicidad['data'];
            break;
        case "nuevo":
            $tipos = $pb->listar_tipos();
            $variables['tipopublicaciones'] = $tipos['data'];
            $variables['accion'] = "Registrar";
            $variables['modolectura'] = FALSE;
            $pagina = "publicidad/formulario.html.twig";
            break;
        case "ver":
            $publicacion = $pb->ver($_GET['id']);
            if (count($publicacion['data']) > 0) {
                $tipos = $pb->listar_tipos();
                $variables['tipopublicaciones'] = $tipos['data'];
                $variables['publicacion'] = $publicacion['data'][0];
                $pagina = "publicidad/formulario.html.twig";
            } else {
                $variables['tipomensaje'] = "alert-error";
                $variables['mensaje'] = "no se pudo cargar la publicacion";
            }
            break;
        case "editar":
            $publicacion = $pb->ver($_GET['id']);
            if (count($publicacion['data']) > 0) {
                $tipos = $pb->listar_tipos();
                $variables['tipopublicaciones'] = $tipos['data'];
                $variables['modolectura'] = FALSE;
                $variables['accion'] = "Editar";
                $variables['publicacion'] = $publicacion['data'][0];
                $pagina = "publicidad/formulario.html.twig";
            } else {
                $variables['tipomensaje'] = "alert-error";
                $variables['mensaje'] = "no se pudo cargar la publicacion";
            }
            break;
        default:
            $variables['mensaje'] = "Opcion invalida";
            $variables['tipomensaje'] = "alert-error";
            break;
    }
} else {
    $pagina = "publicidad/index.html.twig";
    $publicidad = $pb->listar();
    $variables['publicaciones'] = $publicidad['data'];
}
//</editor-fold>

echo $twig->render($pagina, $variables);
?>
