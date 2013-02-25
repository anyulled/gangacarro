<?php

/**
 * Clase para la gestión de autos en el portal.
 *
 * @author anyul
 */
class Auto extends db implements crud {

    private $imagen;
    public $errores = array();

    // <editor-fold defaultstate="collapsed" desc="consulta base">

    const consulta_base = "select carro.*
            , color.nombre color
            , traccion_vehiculo.nombre traccion
            , transmision_vehiculo.nombre transmision
            , marca.nombre marca, marca.id marca_id
            , modelo.nombre modelo
            , estado.nombre estado
            , tipo_vehiculo.nombre tipo
            , direccion_vehiculo.nombre direccion
            , imagen_carros.url_imagen url_imagen
            , concat(usuario.nombre,' ', usuario.apellido) usuario
            from carro
        inner join color on carro.color_id = color.id
        inner join marca on carro.marca_id = marca.id
        inner join modelo on carro.modelo_id = modelo.id
        inner join transmision_vehiculo on carro.transmision_vehiculo_id = transmision_vehiculo.id
        inner join traccion_vehiculo on carro.traccion_vehiculo_id = traccion_vehiculo.id
        inner join tipo_vehiculo on carro.tipo_vehiculo = tipo_vehiculo.id
        inner join imagen_carros on imagen_carros.carro_id = carro.id and tipo_imagen_id = 1
        inner join usuario on carro.usuario_id = usuario.id
        inner join estado on usuario.estado_id = estado.id
        inner join direccion_vehiculo on carro.direccion_vehiculo_id = direccion_vehiculo.id";

// </editor-fold>

    private function ingresarAuto($data) {
        // <editor-fold defaultstate="collapsed" desc="Data">
        $datos = $data['auto'];
        unset($datos['accion']);
        $datos['status'] = 0; //inactivo por defecto
        $datos['comentario'] = htmlentities($data['auto']['comentario'], ENT_QUOTES, "UTF-8");
        $datos['usuario_id'] = $data['user']['id'];
        // </editor-fold>

        try {
            $resultado = $this->insert("carro", $datos);

            $carroId = $resultado['insert_id'];
            if ($carroId > 0) {
                foreach ($data['images'] as $indice => $tempImagen) {
                    $num = substr($indice, strlen($indice) - 1, 1);
                    if ($tempImagen['error'] == 0) {
                        // <editor-fold defaultstate="collapsed" desc="mover imagen e insertar en DB">
                        $nombre_imagen = 'carro-' . $resultado['insert_id'] . "-" . $num . ".jpg";
                        // <editor-fold defaultstate="collapsed" desc="mover y redimensionar imagen">
                        $exito = $this->mover_imagen($tempImagen, $nombre_imagen);
                        if ($exito) {
                            // </editor-fold>
                            // <editor-fold defaultstate="collapsed" desc="Insertar Imagenes">
                            $this->insertar_imagen_bd($num, $nombre_imagen, $carroId);
                        }
                        // </editor-fold>
                    } elseif ($tempImagen['error'] != 4) {
                        trigger_error("Error al cargar la foto. " . var_export($tempImagen, 1), E_USER_ERROR);
                        $mensaje_error = Misc::error_carga_imagen($tempImagen['error']);
                        array_push($this->errores, "No se pudo cargar la foto " . $num . ": " . $mensaje_error);
                    }
                    $num++;
                    // </editor-fold>
                }
            } else {
                throw new Exception("No se pudo cargar el vehiculo");
            }
        } catch (Exception $exc) {
            // <editor-fold defaultstate="collapsed" desc="manejo d excepcion">
            foreach ($data['images'] as $value) {
                $filename = '../images/carros/' . basename($value['name']);
                if (is_file($filename)) {
                    unset($filename);
                }
            }
            // </editor-fold>
            $resultado['suceed'] = false;
            $resultado['errores'] = "No se pudo cargar el vehiculo";
            trigger_error("Error al registrar vehiculo:" . $exc->getMessage() . "\n" . var_export($datos, 1) . "Resultado:" . var_export($resultado, 1), E_USER_ERROR);
        }
        return $resultado;
    }

    private function borrarAuto($id) {
        $result = $this->delete("carro", array('id' => $id));
        return $result;
    }

    private function actualizarAuto($data, $imagen, $id) {
        $datos = $data['auto'];
        $datos['comentario'] = htmlentities($data['auto']['comentario'], ENT_QUOTES, "UTF-8");
        try {
            $result = $this->update("carro", $datos, array("id" => $id));
            if (!$result['suceed']) {
                throw new Exception("Error de actualizacion en BD.");
            }
            foreach ($imagen as $indice => $update_imagen) {
                $num = substr($indice, strlen($indice) - 1, 1);
                //si se carga una imagen y no contiene errores

                if ($update_imagen['error'] == UPLOAD_ERR_OK) {
                    $nombre_imagen = 'carro-' . $id . "-" . $num . ".jpg";
                    $exito = $this->mover_imagen($update_imagen, $nombre_imagen);
                    if ($exito) {
                        $this->insertar_imagen_bd($num, $nombre_imagen, $id);
                    }
                } elseif ($update_imagen['error'] == UPLOAD_ERR_NO_FILE) {
                    //do nothing
                } else {
                    $mensaje_error = Misc::error_carga_imagen($update_imagen['error']);
                    trigger_error($mensaje_error . var_export($update_imagen, 1));
                }
                $num++;
            }
            return $result['suceed'];
        } catch (Exception $exc) {
            trigger_error($exc->getMessage() . var_export($result, 1), E_USER_ERROR);
            return false;
        }
    }

    function mostrarAuto($id) {
        return $this->dame_query(self::consulta_base . " where carro.id=$id");
    }

    private function listarAutos($marca = null, $modelo = null, $limit = null) {
        $query = self::consulta_base;
        if ($marca != null) {
            $query.=" where marca.id = $marca ";
        }
        if ($modelo != null) {
            $query.=" and modelo.id = $modelo ";
        }
        $query.=" order by carro.id desc ";
        if ($limit != null) {
            $query.=" limit $limit ";
        }
        return $this->dame_query($query);
    }

    /**
     * cambia el status de un auto en la bd
     * @param Integer $id el id del auto
     * @param Integer $status el estatus nuevo del registro
     * @return mixed el arreglo con los datos de la operacion 
     */
    function activarAuto($id, $status) {
        $result = $this->update("carro", array('status' => $status), array('id' => $id));
        return $result;
    }

    function vendido($id, $status) {
        $result = $this->update("carro", array('vendido' => $status), array('id' => $id));
        return $result;
    }

    /**
     * Elimina una imagen de un auto en la base de datos
     * @param Integer $id_imagen el id de la imagen
     * @return mixed arreglo con los resultados de la operacion 
     */
    function eliminar_imagen($id_imagen) {
        $result = $this->delete("imagen_carros", array("id" => $id_imagen));
        return $result;
    }

    /**
     * mueve una imagen cargada de la carpeta temporal y le asigna el logo del sitio web
     * @param mixed $imagen arreglo con los datos de la imagen cargada
     * @param String $nombre_imagen el nombre deseado para la imagen
     */
    private function mover_imagen($imagen, $nombre_imagen) {
        $this->imagen = new SimpleImage();
        $exito = move_uploaded_file($imagen['tmp_name'], '../img/carros/' . $nombre_imagen);
        if ($exito) {
            $filename = '../img/carros/' . $nombre_imagen;
            $watermark = "../img/logo.gif";
            $destino = $filename;
            chmod($filename, 0744);
            $this->imagen->load($filename);
            $this->imagen->resize(533, 400);
            $this->imagen->save($filename);
            $this->imagen->merge($filename, $watermark, $destino);
        } else {
            $mensaje_error = Misc::error_carga_imagen($update_imagen['error']);
            trigger_error("No se pudo mover la imagen. " . $mensaje_error);
        }
        return $exito;
    }

    /**
     * insertar imagen de carro en la base de datos
     * @param Integer $num
     * @param String $nombre_imagen
     * @param Integer $id 
     */
    private function insertar_imagen_bd($num, $nombre_imagen, $id) {
        $datosimagen['titulo'] = "GangaCarro.com";
        $datosimagen['tipo_imagen_id'] = ($num == 1) ? 1 : 2;
        $datosimagen['url_imagen'] = $nombre_imagen;
        $datosimagen['carro_id'] = $id;

        $result = $this->insert("imagen_carros", $datosimagen);
        if (!$result['suceed']) {
            trigger_error("No se pudo guardar la imagen - Detalle:" . var_export($result, 1), E_USER_ERROR);
        }
    }

    function traer_marcas_nav() {
        return $this->dame_query("select distinct nombre, url_imagen from marca where id in(10, 11, 17, 18, 25, 29, 31, 42, 55)");
    }

    function ultimas_publicaciones($limit = 4) {
        return $this->dame_query(self::consulta_base . " where carro.status=1 order by rand() limit $limit");
    }

    function listar_tipo_vehiculos() {
        return $this->dame_query("select * from tipo_vehiculo");
    }

    public function actualizar($id, $data) {
        return $this->actualizarAuto($data, $data['imagen'], $id);
    }

    public function borrar($id) {
        return $this->borrarAuto($id);
    }

    public function insertar($data) {
        return $this->ingresarAuto($data);
    }

    public function listar($marca = null, $modelo = null, $limit = null) {
        return $this->listarAutos($marca, $modelo, $limit);
    }

    public function ver($id) {
        return $this->mostrarAuto($id);
    }

    public function ver_imagenes_carro($carro_id) {
        return $this->dame_query("select * from imagen_carros where carro_id = $carro_id");
    }

}

?>
