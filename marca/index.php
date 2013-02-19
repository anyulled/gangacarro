<?php

require '../includes/constants.php';
$db = new Auto();
$pagina = "marca/index.html.twig";
$variables = array();

if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case "ver":
            if (isset($_GET['marca'])) {
                $marca = $db->dame_query("select * from marca where nombre = '{$_GET['marca']}'");
                if ($marca['suceed'] && count($marca['data']) > 0) {
                    $carros = $db->listar($marca['data'][0]['id'], null, 9);
                    $modelos = $db->dame_query("select * from modelo where marca_id = {$marca['data'][0]['id']}");
                    $variables['marca'] = $marca['data'][0];
                    $variables['carros'] = $carros['data'];
                    $variables['modelos'] = $modelos['data'];
                } else {
                    $variables['codigo'] = $marca;
                    $variables['mensaje'] = "marca no encontrada";
                    $variables['tipomensaje'] = ALERT_INFO;
                    break;
                }
            }
            if (isset($_GET['modelo'])) {
                $_GET['modelo'] = str_replace("-", " ", $_GET['modelo']);
                $modelo = $db->dame_query("select * from modelo where nombre = '{$_GET['modelo']}' and marca_id = {$marca['data'][0]['id']}");
                if ($modelo['suceed'] && count($modelo['data']) > 0) {
                    $carros = $db->listar($marca['data'][0]['id'], $modelo['data'][0]['id'], 9);
                    $variables['carros'] = $carros['data'];
                    $variables['modelo'] = $modelo['data'][0];
                } else {
                    $variables['codigo'] = $modelo;
                    $variables['mensaje'] = "modelo no encontrado";
                    $variables['tipomensaje'] = ALERT_INFO;
                }
            }
            break;
        default:
            $variables['mensaje'] = "opcion invalida";
            $variables['tipomensaje'] = ALERT_ERROR;
            break;
    }
}

echo $twig->render($pagina, $variables);
?>
