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
        $result = array("suceed" => false, "info" => "Consulta no permitida.");
    }
} else {
    $result = array("suceed" => false, "info" => "query mal formado.");
}
echo json_encode($result);
?>