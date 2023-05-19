<?php
include '../utilidades/bd.php';

//Función de mostrar carrito
function carrito(){
    $con = conexionBd();
    $usuario = $_SESSION['nombre'];
    $sql="SELECT * FROM `carrito` WHERE `usuario` = '$usuario'";
    $resultado = mysqli_query($con, $sql);
    
    if (mysqli_num_rows($resultado) > 0) {
        echo "<table class='table'>";
        echo "<thead class='table-dark'>";
        echo "<tr><th>ID</th><th>Título</th><th>Total</th><th>Usuario</th><th>Fecha de pedido</th><th>Acciones</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($fila = mysqli_fetch_assoc($resultado)) {
            echo "<tr>";
            echo "<td>" . $fila['id'] . "</td>";
            echo "<td>" . $fila['Stock'] . "</td>";
            echo "<td>" . $fila['total'] . "</td>";
            echo "<td>" . $fila['usuario'] . "</td>";
            echo "<td>" . $fila['fecha_pedido'] . "</td>";
            echo "<td><form method='post' action=''>";
            echo "<input type='hidden' name='id' value='" . $fila['id'] . "' />";
            echo "<input type='hidden' name='Stock' value='" . $fila['Stock'] . "' />";
            echo "<input type='hidden' name='usuario' value='" . $_SESSION['nombre'] . "' />";
            echo "<button name='comprar' class='btn btn-primary'>Hacer pedido</button>";
            echo "<button type='submit' name='borrar' class='btn btn-danger'>Borrar del carrito</button>";
            echo "</form></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='alert alert-warning' role='alert'>No se encontraron resultados</div>";
    }

    //boton de borrar del carrito
    if (isset($_POST['borrar'])) {
        $id = $_POST['id'];
        $sentencia = $con->prepare("DELETE FROM carrito WHERE `carrito`.`id` = ?");
        $sentencia->bind_param("i", $id);
        $sentencia->execute();

        header("Location: carrito.php");
            exit();
    }

    //boton de comprar y hacer un pedido
    if (isset($_POST['comprar'])) {
        $usuario = $_SESSION['nombre'];
        $id = $_POST['id'];
        obtenerPedido($id, $usuario);
        $nombreStock = $_SESSION['carrito'][0];
        $cantidad = 1;
        $precio = $_SESSION['carrito'][1];
        $direccion = $_SESSION['carrito'][2];
        $fechaActual = date('Y-m-d');
    
        $sentencia = $con->prepare("SELECT * FROM pedido WHERE Stock = ? AND usuario = ?");
        $sentencia->bind_param("ss", $nombreStock, $usuario);
        $sentencia->execute();
        $resultado = $sentencia->get_result();
        if (mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);
            $idPedido = $fila['id'];
            $total = $precio + $precio;
            $recuento =  $cantidad+1;
            $sentencia = $con->prepare("UPDATE pedido SET precio = ? WHERE id = ?");
            $sentencia2 = $con->prepare("UPDATE pedido SET total = ? WHERE id = ?");
            $sentencia->bind_param("ii", $total, $idPedido);
            $sentencia2->bind_param("ii", $recuento, $idPedido);
            $sentencia->execute();
            $sentencia2->execute();
        } else {
            $sentencia = $con->prepare("INSERT INTO pedido(Stock, precio, total, usuario, direccion, fecha) VALUES (?, ?, ?, ?, ?, ?)");
            $sentencia->execute([$nombreStock, $precio, $cantidad, $usuario, $direccion, $fechaActual]);
        }
    
        return true;
    }
mysqli_close($con);
}

//Funcion de obtener direccion pasandole el usuario
function obtenerDireccion($usuario) {
    $con = conexionBd();
    $sentencia = $con->prepare("SELECT direccion FROM usuario WHERE nombre = ?");
    $sentencia->bind_param("s", $usuario);
    $sentencia->execute();
    $resultado = $sentencia->get_result();
    $direccion = "";
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $direccion = $fila['direccion'];
    }
    mysqli_close($con);
    return $direccion;
}

//Funcion de obtener pedido dependieno de la id y el usuario para obtener la direccion
function obtenerPedido($id, $usuario){
    $con = conexionBd();
    $sentencia = $con->prepare("SELECT * FROM carrito WHERE id = ?");
    $sentencia->bind_param("s", $id);
    $sentencia->execute();
    $resultado = $sentencia->get_result();
    $nombreStock="";
    $total = 0;
    $direccion=obtenerDireccion($usuario);
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $nombreStock = $fila['Stock'];
        $total = $fila['total'];
    }

    $_SESSION['carrito'][0] = $nombreStock;
    $_SESSION['carrito'][1] = $total;
    $_SESSION['carrito'][2] = $direccion;
    
    mysqli_close($con);
}
?>