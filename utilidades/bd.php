<?php

//Funcion conexion base de datos
function conexionBd(){
    $servername = "localhost";
    $database = "novendountornillo";
    $username = "root";
    $password = "";
    $con = mysqli_connect($servername, $username, $password, $database);
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    return $con;
}

//Funcion que uso en el registro para insertar usuarios
function insertarBd($nombre, $email, $psw, $direccion){
    $con = conexionBd();
    
    $sql = "INSERT INTO `usuario`(`nombre`, `direccion`, `fecha_registro`, `password`, `correo`) VALUES ('$nombre','$direccion',NOW(),'$psw','$email')";
    
if (mysqli_query($con, $sql)) {
      echo "New record created successfully";
      
} else {
      echo "Error: " . $sql . "<br>" . mysqli_error($con);
}
mysqli_close($con);
}

//Funcion inicio de sesión
function comprobarUsuarioBd($nombre, $psw){
    $con = conexionBd();

    $sql = "SELECT * FROM `usuario` WHERE `nombre` = '$nombre' AND `password` = '$psw'";
    $resultado = $con->query($sql);

    if ($resultado->num_rows > 0) {
      session_start();
      $_SESSION["nombre"] = $nombre;
      header("Location: index.php");
    } else {
      header("Location: ../login.php");
    }
    
    $con->close();
}

?>