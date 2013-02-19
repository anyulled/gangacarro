<?php
session_start();
require '../includes/constants.php';
$pagina ="contacto/index.html.twig";
$variables = array();

if(isset($_POST['enviar'])){
        unset($_POST['submit']);
        // <editor-fold defaultstate="collapsed" desc="enviar correo">
        $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: gangacarro Daemon <info@gangacarro.com.ve>' . "\r\n";
    if (isset($_POST['email'])) {
        $headers .="Reply-To: {$_POST['email']}";
    }
    if (isset($_POST['mensaje'])) {
        $_POST['mensaje'] = strip_tags($_POST['mensaje']);
    }
    $html = "<h2>Datos de contacto</h2><div>";
    $html.="<p>Nombre:<span>{$_POST['nombre']}</span></p>";
    $html.="<p>Email:<span>{$_POST['email']}</span></p>";
    $html.="<p>Telefono:<span>{$_POST['telefono']}</span></p>";
    $html.="<p>Mensaje:<span>{$_POST['mensaje']}</span></p>";
    $html.="</div>";
    $exito = mail("info@gangacarro.com.ve", "gangacarro.com.ve: Contacto", $html, $headers); 
    // </editor-fold>

    if($exito){
        $variables['tipomensaje'] = ALERT_SUCCESS;
        $variables['mensaje'] = "Mensaje enviado con exito. Sera contactado en breve.";
    }else{
        $variables['tipomensaje'] = ALERT_ERROR;
        $variables['mensaje'] = "No se pudo enviar el mensaje. Por favor intente de nuevo.";
        $variables['form'] = $_POST;
    }
}

echo $twig->render($pagina, $variables);
?>
