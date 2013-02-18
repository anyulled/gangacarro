<?php

/**
 * @author Anyul Rivas
 */
class publicacion extends db implements crud {

    const tabla = "publicacion";

    public function actualizar($id, $data) {
        return $this->update(self::tabla, $data, array("id" => $id));
    }

    public function borrar($id) {
        return $this->delete(self::tabla, array('id' => $id));
    }

    public function insertar($data) {
        return $this->insert(self::tabla, $data);
    }

    public function listar() {
        return $this->dame_query("select * from " . self::tabla);
    }
    
    public function listar_tipos(){
        return $this->dame_query("select * from tipo_publicacion");
    }

    public function ver($id) {
        return $this->select("*", self::tabla, array("id" => $id));
    }

}

?>
