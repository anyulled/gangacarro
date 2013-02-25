<?php

include '../includes/constants.php';
$pagina = "local/index.html.twig";
$variables = array();
if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case "":
            break;
        default:
            break;
    }
}
if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case "":
            break;
        default:
            break;
    }
}
echo $twig->render($pagina, $variables);
?>