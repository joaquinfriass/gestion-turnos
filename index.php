<?php
    include 'conexion.php';

    try{
        $sql = "SELECT * FROM usuarios";
        $stmt = $pdo->query($sql);
        $usuarios = $stmt->fetchAll();

        foreach($usuarios as $usuario){
            echo "ID: " . $usuario['id'] . " - Nombre: " . $usuario['nombre'] . "<br>";
        }
    } catch (PDOException $e) {
        echo "Error al ejecutar la consulta: " . $e->getMessage();
    }
    
?>