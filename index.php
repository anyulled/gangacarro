<?php

include 'includes/constants.php';
session_start();
echo $twig->render("index.html.twig", array());
?>
