<?php
include('conex.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $evento = trim($_POST['evento']);
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $usuario = trim($_POST['usuario']);
    $patente = trim($_POST['patente']);
    $zona = trim($_POST['zona']); // Cambiado de "espacio" a "zona"

    // Verificar que no haya reservas duplicadas
    $check_query = $conexion->prepare("SELECT * FROM INFO1170_Reservas WHERE fecha = ? AND hora_inicio = ? AND zona = ?");
    $check_query->bind_param("sss", $fecha, $hora_inicio, $zona);
    $check_query->execute();
    $check_query->store_result();

    if ($check_query->num_rows > 0) {
        echo "<script>alert('La zona ya está reservada para esa fecha y hora.'); window.history.back();</script>";
        $check_query->close();
        exit();
    }
    $check_query->close();

    // Insertar la nueva reserva
    $insert_query = $conexion->prepare("INSERT INTO INFO1170_Reservas (evento, fecha, hora_inicio, hora_fin, usuario, patente, zona) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert_query->bind_param("sssssss", $evento, $fecha, $hora_inicio, $hora_fin, $usuario, $patente, $zona);

    if ($insert_query->execute()) {
        echo "<script>alert('Reserva realizada con éxito.'); window.location.href = 'reservas.php';</script>";
    } else {
        echo "<script>alert('Error al realizar la reserva.'); window.history.back();</script>";
    }

    $insert_query->close();
    $conexion->close();
}
?>
