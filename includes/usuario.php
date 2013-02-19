<?php

/**
 * Clase para manejo de usuarios
 *
 * @author anyul
 */
class usuario extends db implements crud {

    const tabla = "usuario";
/**
 * inserta un nuevo usuario en la base de datos
 * <p>Convierte el <i>password</i> en <b>md5</b> </p>
 * @param Array $data Arreglo de datos
 * @return Mixed
 * @throws Exception
 */
    public function insertar($data) {
        $data["password"] = md5($data["password"]);
        try {
            $this->comprobar_existencia("login", $data['login']);
        } catch (Exception $exc) {
            throw new Exception("Este nombre de usuario ya existe");
        }
        try {
            $this->comprobar_existencia("email", $data['email']);
        } catch (Exception $exc) {
            throw new Exception("Ya existe un usuario registrado con este email");
        }

        return $this->insert(self::tabla, $data);
    }

    /**
     * Comprueba la existencia de un registro a través de un campo determinado.
     * <p>Devuelve una excepción en caso afirmativo</p>
     * @param String $campo Campo de la tabla usuario
     * @param String $valor Valor del campo
     * @throws Exception devuelve una excepción en caso afirmativo
     */
    private function comprobar_existencia($campo, $valor) {
        $usuario = $this->select($campo, self::tabla, array($campo => $valor));
        if ($usuario['suceed'] && count($usuario['data']) > 0) {
            throw new Exception("Registro ya existe");
        };
    }

    /**
     * Actualiza un usuario de la base de datos.
     * <p>Convierte el <i>password</i> en <b>md5</b> </p>
     * @param Integer $id id del registro
     * @param Array $data Arreglo de datos actualizados
     * @return Mixed
     */
    public function actualizar($id, $data) {
        if (isset($data["password"])) {
            $data["password"] = md5($data["password"]);
        }
        return $this->update(self::tabla, $data, array("id" => $id));
    }

    /**
     * Borra un usuario de la base de datos
     * @param Integer $id id del registro
     * @return Mixed
     */
    public function borrar($id) {
        return $this->delete(self::tabla, array("id" => $id));
    }

    /**
     * funcion que retorna una matriz de datos de un usuario determinado
     * @param int $id id del usuario
     * @return array arreglo de datos
     */
    public function ver($id) {
        $result = $this->dame_query("select usuario.*,
            estado.nombre estado,
            ciudad.nombre ciudad
            from usuario 
        inner join estado on usuario.estado_id = estado.id
        inner join ciudad on usuario.ciudad_id = ciudad.id
        where usuario.id =  $id");
        return $result;
    }

    /**
     * funcion que retorna un arreglo de usuarios
     * @return Array
     */
    public function listar() {
        return $this->dame_query("select usuario.*, tipo_usuario.nombre rol 
            from usuario 
            inner join tipo_usuario on usuario.tipo_usuario = tipo_usuario.id");
    }

    /**
     * Gestiona el inicio de sesión en el sistema
     * @param String $usuario
     * @param String $password
     * @return boolean 
     */
    public function login($usuario, $password) {
        $result = array();
        $password_encriptado = md5($password);
        try {
            $result = $this->dame_query("select usuario.*, tipo_usuario.nombre rol from usuario
            inner join tipo_usuario on tipo_usuario.id = usuario.tipo_usuario
            where login='$usuario' and password='$password_encriptado'");
            if ($result['suceed'] == 'true' && count($result['data']) > 0) {
                unset($result['data'][0]['password']);
                session_start();
                $_SESSION['usuario'] = $result['data'][0];
                $_SESSION['status'] = 'logueado';
                header("location:" . URL_SISTEMA . "usuario/");
                return $result;
            } else {
                $result['suceed'] = false;
                $result['error'] = "Login y/o clave inválidos";
                return $result;
            }
        } catch (Exception $exc) {
            trigger_error($exc->getTraceAsString(), E_USER_NOTICE);
            $result['suceed'] = false;
            $result['error'] = "Error desconocido. Contacte al administrador del sistema.";
            return $result;
        }
    }

    /**
     * Confirma que el usuario sea haya logueado en el sistema 
     */
    public function confirmar_miembro() {
        @session_start();
        if (!isset($_SESSION['status']) || $_SESSION['status'] != 'logueado' || !isset($_SESSION['usuario']))
            $this->logout();
    }

    /**
     * Cierra la sesión 
     */
    public function logout() {
        @session_start();
        $_SESSION = array();
        session_unset();
        session_destroy();
        session_write_close();
        header("location: " . URL_SISTEMA . "login.php");
    }

    /**
     * Listar tipos de usuario
     * @return Array
     */
    public function listar_grupos() {
        return $this->select("*", "tipo_usuario");
    }

}

?>