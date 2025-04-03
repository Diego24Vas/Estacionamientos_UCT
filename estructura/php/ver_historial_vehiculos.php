<?php include('cabecera.php'); ?> 
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/estilos_footer.css">
<h2 class="text-center my-4">Historial de Vehículos</h2>
<div class="container">
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Vehículo ID</th>
                <th>Patente</th>
                <th>Espacio de Estacionamiento</th>
                <th>Acción</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include('conex.php'); 

            // Paginación
            $limit = 10; // Número de registros por página
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Consulta con JOIN entre HistorialRegistros y VehiculosRegistrados
            $query = "SELECT 
                            hr.IdVehiculo, 
                            vr.patente, 
                            vr.espacio_estacionamiento, 
                            hr.fecha, 
                            hr.accion 
                      FROM 
                            INFO1170_HistorialRegistros hr
                      JOIN 
                            INFO1170_VehiculosRegistrados vr ON hr.IdVehiculo = vr.id
                            ORDER BY hr.fecha DESC
                      LIMIT $limit OFFSET $offset";

            // Ejecutar la consulta
            $result = $conexion->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['IdVehiculo']}</td>
                            <td>{$row['patente']}</td>
                            <td>{$row['espacio_estacionamiento']}</td>
                            <td>{$row['accion']}</td>
                            <td>{$row['fecha']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No hay registros</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php
        $result_total = $conexion->query("SELECT COUNT(*) AS total FROM INFO1170_HistorialRegistros");
        $total_rows = $result_total->fetch_assoc()['total'];
        $total_pages = ceil($total_rows / $limit);

        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='ver_historial_vehiculos.php?page=$i'>$i</a> ";
        }
        ?>
    </div>
</div>

<?php include('pie.php'); ?>
