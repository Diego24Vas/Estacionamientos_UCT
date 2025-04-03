<?php
include('conex.php');

// Verificar si se ha pasado el id del registro por GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Preparar la consulta para eliminar el registro con el id especificado
    $query = "DELETE FROM INFO1170_VehiculosRegistrados WHERE id = ?";
    
    // Preparar y ejecutar la consulta
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id); // "i" para tipo entero
    $stmt->execute();

    // Verificar si la eliminación fue exitosa
    if ($stmt->affected_rows > 0) {
        // Redirigir a la página de registros con un mensaje de éxito
        header("Location: ver_registros_vehiculos.php?mensaje=El registro fue eliminado exitosamente");
    } else {
        // Redirigir con un mensaje de error si no se eliminó nada
        header("Location: ver_registros_vehiculos.php?mensaje=Error al eliminar el registro");
    }

    $stmt->close();
} else {
    // Si no se pasa el id, redirigir con un mensaje de error
    header("Location: ver_registros_vehiculos.php?mensaje=ID no proporcionado");
}
?>
