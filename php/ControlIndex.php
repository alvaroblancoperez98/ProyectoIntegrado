
<?php
include '../utilidades/bd.php';

//Funcion de mostrar libros
function Stock()
{
    $con = conexionBd();
    $sql = "SELECT * FROM stock";
    $resultado = mysqli_query($con, $sql);
    if (mysqli_num_rows($resultado) > 0) {
        echo "<table class='table'>";
        echo "<thead class='table-dark'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>precio</th><th>Imagen</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($fila = mysqli_fetch_assoc($resultado)) {
            echo "<tr>";
            echo "<td>" . $fila['id'] . "</td>";
            echo "<td>" . $fila['nombre'] . "</td>";
            echo "<td>" . $fila['descripcion'] . "</td>";
            echo "<td>" . $fila['precio'] . "</td>";
            echo "<td><img src=" . "../images/" . $fila['imagen'] . ".png" . "></td>";
            echo "<td><form method='post' action=''>";
            echo "<input type='hidden' name='Stock' value='" . $fila['nombre'] . "' />";
            echo "<input type='hidden' name='precio' value='" . $fila['precio'] . "' />";
            echo "<input type='hidden' name='usuario' value='<?php echo " . $_SESSION['nombre'] . " ?>' />";
            echo "<button class='btn btn-primary' name='añadircarrito'>Añadir al carrito</button>";
            echo "</form></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-warning' role='alert'>No se encontraron resultados</div>";
    }

    //Boton de añadir al carrito 
    if (isset($_POST['añadircarrito'])) {
        $fechaActual = date('Y-m-d');
        $usuario = $_SESSION['nombre'];
        $nombreStock = $_POST['Stock'];
        $precio = $_POST['precio'];
        $sentencia = $con->prepare("SELECT * FROM carrito WHERE Stock = ? AND usuario = ?");
        $sentencia->bind_param("ss", $nombreStock, $usuario);
        $sentencia->execute();
        $resultado = $sentencia->get_result();

        if (mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);
            $id = $fila['id'];
            $total = $fila['total'] + $precio;

            $sentencia = $con->prepare("UPDATE carrito SET total = ? WHERE id = ?");
            $sentencia->bind_param("ii", $total, $id);
            $sentencia->execute();
        } else {
            $sentencia = $con->prepare("INSERT INTO carrito(Stock, total, usuario, fecha_pedido) VALUES (?, ?, ?, ?)");
            $sentencia->execute([$nombreStock, $precio, $usuario, $fechaActual]);
        }
    }

    mysqli_close($con);
}

?>