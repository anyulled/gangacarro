<?php

/**
 * Clase para manejo de usuarios
 *
 * @author anyul
 */
class usuario extends db implements crud {

    const tabla = "usuario";

    public function insertar($data) {
        $data["password"] = md5($data["password"]);
        return $this->insert(self::tabla, $data);
    }

    public function actualizar($id, $data) {
        if(isset($data["password"])){
            $data["password"] = md5($data["password"]);
        }
        return $this->update(self::tabla, $data, array("id" => $id));
    }

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
        return $this->dame_query("select usuario.*, tipo_usuario.nombre rol from usuario inner join tipo_usuario on usuario.tipo_usuario = tipo_usuario.id");
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
            $result = $this->dame_query("select usuario.*  from usuario where login='$usuario' and password='$password_encriptado'");
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
        session_start();
        if (!isset($_SESSION['status']) || $_SESSION['status'] != 'logueado' || !isset($_SESSION['usuario']))
            $this->logout();
    }

    /**
     * Cierra la sesión 
     */
    public function logout() {
        $_SESSION = array();
            session_unset();
            session_destroy();
    }

    public function listar_grupos() {
        return $this->select("*", "grupo");
    }

}

?>