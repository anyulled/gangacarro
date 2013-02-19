<?php

require '../includes/constants.php';
$pagina = "carro/index.html.twig";
$variables = array();
$db = new db();
$auto = new auto();
$usuario = new usuario();
$pag = new paginacion();
if (!isset($_POST['accion']) && !isset($_GET['accion'])) {
    $_GET['accion'] = "nada";
}

if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case "ver":
            $carro = $auto->ver($_GET['id']);
            $pagina = "carro/ver.html.twig";
            if ($carro['suceed'] && count($carro['data']) > 0) {
                $imagenes = $auto->ver_imagenes_carro($carro['data'][0]['id']);
                $usuario = $usuario->ver($carro['data'][0]['usuario_id']);
                $variables['carro'] = $carro['data'][0];
                $variables['imagenes'] = $imagenes['data'];
                $variables['vendedor'] = $usuario['data'][0];
            } else {
                $variables['mensaje'] = "Carro no encontrado";
                $variables['tipomensaje'] = ALERT_ERROR;
            }
            break;
        case "listar":
            $pag->paginar($auto::consulta_base, 10);
            $variables['paginado'] = $pag->mostrar_paginado_lista(false);
            $variables['registros'] = $pag->registros;
            $pagina = "carro/admin.html.twig";
            break;
        case "consultar":
            $carro = $auto->ver($_GET['id']);
            $variables = llenar_selects();
            $variables['registro'] = $carro['data'][0];
            $variables['accion'] = "ver";
            $pagina = "carro/formulario.html.twig";
            break;
        case "registrar":
            $pagina = "carro/formulario.html.twig";
            $variables = llenar_selects();
            break;
        case 'editar':
            $pagina = "carro/formulario.html.twig";
            $result = $auto->ver($_GET['id']);
            if ($result['suceed'] && count($result['data']) > 0) {
                $variables = llenar_selects($result['data'][0]['marca_id']);
            } else {
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo cargar el registro";
            }
            break;
        default:
            $marcas = $db->dame_query("select * from marca");
            $pagina = "carro/index.html.twig";
            $carros = $auto->listar(null, null, 12);
            $variables['marcas'] = $marcas['data'];
            $variables['carros'] = $carros['data'];
            break;
    }
}

If (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'Registrar':
            $data['auto'] = $_POST;
            $data['images'] = $_FILES;
            $data['user'] = $_SESSION['usuario'];
            unset($_POST['accion']);
            $result = $auto->insertar($data);
            if ($result['suceed']) {
                $variables['mensaje'] = "Registro creado con exito";
            } else {
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo agregar el registro. Por favor intente de nuevo";
                $variables['codigo'] = $result;
            }
            break;
        case "Editar":
            $id = $_POST['id'];
            $auto = new Auto();
            $data['auto'] = $_POST;
            $data['images'] = $_FILES;
            $data['user'] = $_SESSION['usuario'];
            unset($_POST['accion']);
            $result = $auto->actualizar($id, $data);
            if ($result['suceed']) {
                $variables['tipomensaje'] = ALERT_SUCCESS;
                $variables['mensaje'] = "Registro editado con exito";
            } else {
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo editar el registro. Por favor intente de nuevo";
                $variables['codigo'] = $result;
            }
            break;
        case "Eliminar":
            $id = $_POST['id'];
            $result = $auto->borrar($id);
            if ($result['suceed']) {
                $variables['tipomensaje'] = ALERT_SUCCESS;
                $variables['mensaje'] = "Registro eliminado con exito";
            } else {
                $variables['tipomensaje'] = ALERT_ERROR;
                $variables['mensaje'] = "No se pudo eliminar el registro. Por favor intente de nuevo";
                $variables['codigo'] = $result;
            }
            break;
        default:
            $variables['tipomensaje'] = ALERT_INFO;
            $variables['mensaje'] = "accion incorrecta";
            $pagina = "carro/admin.html.twig";
            break;
    }
}

function llenar_selects($marca = null) {
    $db = new db();
    $variables = array();
    $tipo_vehiculos = $db->dame_query("select * from tipo_vehiculo");
    $colores = $db->dame_query("select * from color");
    $transmision = $db->dame_query("select * from transmision_vehiculo");
    $traccion = $db->dame_query("select * from traccion_vehiculo");
    $direccion = $db->select("*", "direccion_vehiculo");
    $marcas = $db->select("*", "marca");
    if ($marca != null) {
        $modelos = $db->select("*", "modelo", array("marca.id" => $marca));
    } else {
        $modelos = $db->select("*", "modelo");
    }
    $variables['modelos'] = $modelos['data'];
    $variables['marcas'] = $marcas['data'];
    $variables['tipo_vehiculos'] = $tipo_vehiculos['data'];
    $variables['colores_vehiculo'] = $colores['data'];
    $variables['transmision_vehiculo'] = $transmision['data'];
    $variables['traccion_vehiculo'] = $traccion['data'];
    $variables['direccion_vehiculo'] = $direccion['data'];
    return $variables;
}

echo $twig->render($pagina, $variables);
?>
