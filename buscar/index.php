<?php

require '../includes/constants.php';
$pag = new paginacion();
$auto = new auto();
$pagina = "busqueda.html.twig";
$variables = array();

if (isset($_GET['buscar'])) {
// <editor-fold defaultstate="collapsed" desc="armar busqueda">
    $parametros = 0;
    $and = " and ";
    $where = " where ";
    $query = $auto::consulta_base;
// <editor-fold defaultstate="collapsed" desc="parametros">
    if (isset($_GET['marca']) && !empty($_GET['marca'])) {
        $query.=($parametros == 0 ? $where : $and) . " marca.id = {$_GET['marca']} ";
        $parametros++;
    }
    if (isset($_GET['modelo']) && !empty($_GET['modelo'])) {
        $query.=($parametros == 0 ? $where : $and) . " modelo.id = {$_GET['modelo']} ";
        $parametros++;
    }
    if (isset($_GET['estado']) && !empty($_GET['estado'])) {
        $query.=($parametros == 0 ? $where : $and) . " estado.id = {$_GET['estado']} ";
        $parametros++;
    }
    if (isset($_GET['preciomin']) && !empty($_GET['preciomin'])) {
        $query.=($parametros == 0 ? $where : $and) . " carro.precio >= {$_GET['preciomin']} ";
        $parametros++;
    }
    if (isset($_GET['preciomax']) && !empty($_GET['preciomax'])) {
        $query.=($parametros == 0 ? $where : $and) . " carro.precio <= {$_GET['preciomax']} ";
        $parametros++;
    }
    if (isset($_GET['aniomin']) && !empty($_GET['aniomin'])) {
        $query.=($parametros == 0 ? $where : $and) . " carro.anio >= {$_GET['aniomin']} ";
        $parametros++;
    }
    if (isset($_GET['aniomax']) && !empty($_GET['aniomax'])) {
        $query.=($parametros == 0 ? $where : $and) . " carro.anio <= {$_GET['aniomax']} ";
        $parametros++;
    }
    $pag->paginar($query, 12); 
// </editor-fold>
if (isset($_GET['order']) && isset($_GET['dir'])) {
    $query.=" order by {$_GET['order']} {$_GET['dir']}";
} else {
    $where .=" order by carros.id desc";
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="paginar resultados">
    if (count($pag->registros) > 0) {
        $variables['resultados'] = $pag->registros;
        $variables['paginado'] = $pag->mostrar_paginado_lista();
    } else {
        $variables['mensaje'] = "No se encontraron resultados para este criterio de busqueda.";
        $variables['tipomensaje'] = "alert-info";
        $variables['codigo'] = $pag->query;
    }
// </editor-fold>
}


echo $twig->render($pagina, $variables);
?>
