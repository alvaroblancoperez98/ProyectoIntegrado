<?php

session_start();

include '../utilidades/seguridad.php';
include '../utilidades/bd.php';


      $nombre = "";
      $psw = "";
      $correcto = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = test_input($_POST["nombre"]);
    $psw = test_input($_POST["psw"]);
    //aqui con el md5() lo encriptas para comprobar
    if (comprobarUsuarioBd($nombre, md5($psw))== true){
        $_SESSION['nombre'] = $nombre;
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error_login']="Credenciales son incorrectas";
    }
}
?>