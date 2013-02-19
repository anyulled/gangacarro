<?php

// <editor-fold defaultstate="collapsed" desc="inicio">
include '../includes/constants.php';
$usuario = new usuario();
$variables = array();
$variables['get'] = $_GET;
$pagina = "usuario/index.html.twig";
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="usuario">
if (isset($_SESSION['usuario'])) {
    $variables['usuario'] = $_SESSION['usuario'];
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="GET">
if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    switch ($accion) {
        case "Registrar":
        case "registrar":
        case "crear":
            $prov = new db();
            $roles = $prov->dame_query("select * from tipo_usuario");
            $variables['roles'] = $roles['data'];
            $estados = $prov->dame_query("select * from estado");
            $variables['estados'] = $estados['data'];
            $pagina = "usuario/editar.html.twig";
            break;
        case "listar":
            $usuario->confirmar_miembro();
            $registros = new paginacion();
            $registros->paginar("select usuario.*, tipo_usuario.nombre rol from usuario inner join tipo_usuario on usuario.tipo_usuario = tipo_usuario.id");
            $variables['registros'] = $registros->registros;
            $variables['paginacion'] = $registros->mostrar_paginado_lista(false);
            $pagina = "usuario/listado.html.twig";
            break;
        case "ver":
            $usuario->confirmar_miembro();
            $variables['modolectura'] = true;
            $prov = new db();
            $id = (isset($_GET['id']) ? $_GET['id'] : $_SESSION['usuario']['ID']);
            $registro = $usuario->ver($id);
            $roles = $prov->dame_query("select * from tipo_usuario");
            $estados = $prov->dame_query("select * from estado");
            $ciudades = $prov->dame_query("select * from ciudad where estado_id = " . $registro['data'][0]['estado_id']);
            if ($registro['suceed'] && count($registro['data']) > 0) {
                $variables['estados'] = $estados['data'];
                $variables['ciudades'] = $ciudades['data'];
                $variables['roles'] = $roles['data'];
                $variables['dato'] = $registro['data'][0];
            } else {
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo cargar el usuario.";
            }
            $pagina = "usuario/editar.html.twig";
            break;
        case "editar":
            $usuario->confirmar_miembro();
            $variables['modolectura'] = false;
            $prov = new db();
            $roles = $prov->dame_query("select * from tipo_usuario");
            $variables['roles'] = $roles['data'];
            $id = (isset($_GET['id']) ? $_GET['id'] : $_SESSION['usuario']['ID']);
            $registro = $usuario->ver($id);
            $estados = $prov->dame_query("select * from estado");
            $ciudades = $prov->dame_query("select * from ciudad where estado_id = " . $registro['data'][0]['estado_id']);
            if ($registro['suceed'] && count($registro['data']) > 0) {
                unset($registro["data"][0]["password"]);
                $variables['estados'] = $estados['data'];
                $variables['ciudades'] = $ciudades['data'];
                $variables['dato'] = $registro['data'][0];
                unset($variables['dato']['password']);
            } else {
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo cargar el usuario.";
            }
            $pagina = "usuario/editar.html.twig";
            break;
        case "cerrar_sesion":
            $usuario->logout();
            break;
        default :
            $pagina = "usuario/index.html.twig";
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="POST">
if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case "registrar":
        case "Registrar":
            $data = $_POST;
            unset($data['accion'], $data['id'], $data['repetir']);
            $data['tipo_usuario'] = 2;
            try {
                $result = $usuario->insertar($data);
                if ($result['suceed']) {
                    $variables['tipomensaje'] = ALERT_SUCCESS;
                    $variables['mensaje'] = "Usuario creado con éxito";
                } else {
                    throw new Exception("No se pudo registrar el usuario",$result);
                }
            } catch (Exception $exc) {
                if(isset($_POST['accion']))
                    $variables['dato'] = $_POST;
                $variables['codigo'] = $exc->getCode();
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = $exc->getMessage();
            }
            break;
        case "crear":
        case "Crear":
            $usuario->confirmar_miembro();
            $data = $_POST;
            unset($data['accion'], $data['id'], $data['repetir']);
            $data['ip'] = $_SERVER['REMOTE_ADDRESS'];
            $resultado = $usuario->insertar($data);
            if ($resultado['suceed']) {
                $variables['tipomensaje'] = ALERT_SUCCESS;
                $variables['mensaje'] = "Usuario agregado con exito.";
                $pagina = "usuario/index.html.twig";
            } else {
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo agregar el usuario.";
                $pagina = "usuario/editar.html.twig";
            }
            break;
        case "editar":
        case "Editar":
            $usuario->confirmar_miembro();
            $data = $_POST;
            unset($data['accion']);
            if ($data["password"] == "") {
                unset($data["password"]);
            }
            $resultado = $usuario->actualizar($_POST['id'], $data);
            if ($resultado['suceed']) {
                $variables['tipomensaje'] = ALERT_SUCCESS;
                $variables['mensaje'] = "Usuario modificado con exito.";
                $pagina = "usuario/index.html.twig";
            } else {
                $variables['codigo'] = $resultado;
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo modificar el usuario.";
                $pagina = "usuario/editar.html.twig";
            }
            break;
        case "Eliminar":
        case "eliminar":
            $usuario->confirmar_miembro();
            $data = $_POST['id'];
            $resultado = $usuario->borrar($_POST['id']);
            var_dump($resultado);
            if ($resultado['suceed']) {
                $variables['tipomensaje'] = ALERT_SUCCESS;
                $variables['mensaje'] = "Usuario eliminado con exito.";
                $pagina = "usuario/index.html.twig";
            } else {
                $variables["tipomensaje"] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo eliminar el usuario.";
                $pagina = "usuario/index.html.twig";
            }
            break;
        default:
            unset($_POST['accion']);
            $resultado = $usuario->actualizar($_POST['id'], $_POST);
            break;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="TWIG">
echo $twig->render($pagina, $variables);
if (DEBUG)
    var_dump("GET", $_GET, "POST", $_POST, "usuario", $_SESSION['usuario'], "FILES", $_FILES);
// </editor-fold>
?>