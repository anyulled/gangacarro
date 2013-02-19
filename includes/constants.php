<?php

require 'extensiones.php';
/**
 * Constantes y configuacion del sistema
 *
 * @author Anyul Rivas
 */
// <editor-fold defaultstate="collapsed" desc="configuracion regional">
date_default_timezone_set("America/Caracas");
ini_set("max_execution_time", 60);
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="init">
$debug = true;
$email_error = false;
$mostrar_error = true;
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Chequeo servidor">
if ($_SERVER['SERVER_NAME'] != "localhost") {
    $host = "mysql6.000webhost.com";
    $user = "a2762740_ggcarro";
    $password = "Gangacarro2013";
    $db = "a2762740_mybd";
    $email_error = true;
    $mostrar_error = false;
    $debug = false;
    $sistema = "/home/a2762740/public_html/";
    define("SERVER_ROOT", $sistema);
    error_reporting(E_ERROR);
} else {
    error_reporting(E_ALL);
    $host = "localhost";
    $user = "root";
    $password = "root";
    $db = "gangacarro";
    $sistema = "/gangacarro/";
    $debug = true;
    define("SERVER_ROOT", $_SERVER['DOCUMENT_ROOT'] . $sistema);
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Acceso a la BD">
define("HOST", "localhost");
define("USER", $user);
define("PASSWORD", $password);
define("DB", $db);
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="configuracion de ficheros del sistema">
define("EMAIL_ERROR", $email_error);
define("EMAIL_CONTACTO", "anyulled@gmail.com");
define("EMAIL_TITULO", "error");
define("MOSTRAR_ERROR", $mostrar_error);
define("DEBUG", $debug);
define("TITULO", "GangaCarro");
/**
 * para las urls
 */
define("ROOT", 'http://' . $_SERVER['SERVER_NAME']);
define("URL_SISTEMA", ROOT . $sistema);
/**
 * para los includes
 */
define("TEMPLATE", SERVER_ROOT . "/template/");
/* para las alertas*/
define("ALERT_SUCCESS", "alert-success");
define("ALERT_INFO", "alert-info");
define("ALERT_ERROR", "alert-error");
//</editor-fold>
//<editor-fold defaultstate="collapsed" desc="Twig">
require_once SERVER_ROOT . 'includes/twig/lib/Twig/Autoloader.php';
//include_once SERVER_ROOT . 'includes/extensiones.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem(SERVER_ROOT . 'template');
$twig = new Twig_Environment($loader, array(
    'debug' => $debug,
    'cache' => SERVER_ROOT . 'cache',
    "auto_reload" => $debug)
);
if (isset($_SESSION))
    $twig->addGlobal("session", $_SESSION);

$twig->addExtension(new extensiones());
$twig->addExtension(new Twig_Extension_Debug());

//</editor-fold>
//<editor-fold defaultstate="collapsed" desc="autoload">
function __autoload($clase) {
    include_once SERVER_ROOT . "includes/" . strtolower($clase) . ".php";
}

spl_autoload_register("__autoload", false);
set_include_path(SERVER_ROOT . "/gangacarro");
//</editor-fold>
//<editor-fold defaultstate="collapsed" desc="cerrar sesión">
if (isset($_GET['logout']) && $_GET['logout'] == true) {
    $user_logout = new usuario();
    $user_logout->logout();
}
//</editor-fold>
?>
