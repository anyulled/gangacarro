<?php

require_once('db.php');
$db = new db();
$result = null;

if (isset($_GET['query'])) {
    $tablaspermitidas = array("estado", "ciudad", "modelo", "marca", "tipo_vehiculo");
    if (in_array($_GET['query'], $tablaspermitidas)) {
        $consulta = "select * from ";
        $consulta .= ( isset($_GET['query'])) ? $_GET['query'] : "estado";
        $consulta.= ( isset($_GET['where'])) ? " where " . $_GET['where'] . " = " . $_GET['campo'] : "";
        if ($_GET['query'] == "modelo" || $_GET['query'] == "marca") {
            $consulta.= " order by nombre ASC";
        }
        $result = $db->dame_query($consulta);
    } else {
        trigger_error("Query incorrecto: " . var_export($_GET, 1));
        $result = array(
            "suceed" => false,
            "info" => "Consulta no permitida.");
    }
} elseif (isset($_GET['accion'])) {
    $result['suceed'] = FALSE;
    switch ($_GET['accion']) {
        case "eliminar_imagen":
            if (isset($_GET['id_imagen_carro'])) {
                $auto = new Auto();
                $result = $auto->eliminar_imagen($_GET['id_imagen_carro']);
            }
            break;
        default:
            $result = array(
                "suceed" => false,
                "info" => "operacion no permitida.");
            break;
    }
} else {
    $result = array("suceed" => false, "info" => "query mal formado.");
}
echo json_encode($result);
?>