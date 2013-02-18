<?php
include_once 'twig/lib/Twig/ExtensionInterface.php';
include_once 'twig/lib/Twig/Extension.php';

class extensiones extends Twig_Extension {

    public function getName() {
        return 'MiExtension';
    }

    /**
     * Trunca un texto a una longitud determinada sin cortar las palabras y agrega puntos suspensivos
     * @param String $input texto a truncar
     * @param Integer $length longitud
     * @return String el texto truncado 
     */
    public static function trim_text($input, $length) {
        return misc::trim_text($input, $length);
    }

    public static function url_sortable($campo, $direccion) {
        return misc::url_sortable($campo, $direccion);
    }

    public function getFunctions() {
        return array(
            'url_sortable' => new Twig_Function_Method($this, 'url_sortable'),
            'trim_text' => new Twig_Function_Method($this, 'trim_text')
        );
    }
}
?>
