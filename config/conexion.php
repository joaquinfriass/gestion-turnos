<?php 

    Class Conectar{
        public static function conexion(){
    //1. Definimos las credenciales de acceso
    $host = "localhost";
    $db = "gestion_turnos";
    $user = "root";
    $pass = "root";
    $charset = "utf8mb4";

    //2. Construir el DSN (Data Source Name)
    $dsn = "mysql:host=$host;port=3308;dbname=$db;charset=$charset";

    //3. Configurar opciones de PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Acticva el reporte de errores graves
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devuelve los datos como arrays asociativos
        PDO::ATTR_EMULATE_PREPARES => false, // Desactiva la emulación para usar consultas preparadas reales
    ];

    //4. Realizar la conexión utilizando el bloque Try-Catch
    try {
        //Creamos la instancia del objeto PDO
        $pdo = new PDO($dsn, $user, $pass, $options);
        echo "Conexión exitosa a la base de datos.";
    } catch (PDOException $e) {
        // Si ocurre un error, se captura la excepción y se muestra el mensaje de error
        echo "Error de conexión: " . $e->getMessage();
        exit; // Detiene la ejecución del script en caso de error
    }
}}
    
?>