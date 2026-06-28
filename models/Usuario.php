<?php 

// models/Usuario.php
    require_once __DIR__ . '/../config/conexion.php';

    class Usuario{
        private $db;

        public function __construct(){
            //Importamos la clase Conectar y establecemos la conexión a la base de datos
            $this->db = Conexion::conexion();
        }

        public function obtenerPorEmail($email){
            try {
                $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetch();
            } catch (PDOException $e) {
                echo "Error al ejecutar la consulta: " . $e->getMessage();
                return false;
            }
        }
    }
?>
