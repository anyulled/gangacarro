<?php
session_start();
include 'includes/constants.php';
$variables = array();
if (isset($_POST['submit'])) {
    $usuario = new usuario();
    $resultado = $usuario->login($_POST['login'], $_POST['password']);
    if ($resultado['suceed'] == false) {
        $variables['tipomensaje'] = ALERT_ERROR;
        $variables['mensaje'] = $resultado['error'];
        $variables['codigo'] = $resultado;
    }
}
echo $twig->render("login.html.twig", $variables);
if (DEBUG)
    var_dump("GET", $_GET, "POST", $_POST, "FILES", $_FILES);
?>