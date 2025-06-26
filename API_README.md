# API REST - Sistema de Estacionamientos UCT

## Descripción
Esta API REST reemplaza las consultas SQL directas del sistema de estacionamientos UCT, proporcionando endpoints RESTful para todas las operaciones principales.

## Características Implementadas

### ✅ API Completa con 3+ Endpoints
- **Autenticación**: Login, registro y verificación de tokens
- **Vehículos**: CRUD completo + validación de patentes
- **Reservas**: Gestión completa + verificación de disponibilidad

### ✅ Documentación Swagger
- Interfaz interactiva para probar la API
- Especificación OpenAPI 3.0 completa
- Documentación de todos los endpoints

### ✅ Conexión con Frontend
- Página de demostración funcional
- JavaScript para consumir la API
- Ejemplos prácticos de integración

## Estructura de la API

```
/api/
├── config/
│   ├── DatabaseConnection.php  # Conexión PDO con patrón Singleton
│   └── Router.php              # Router personalizado para manejar rutas
├── controllers/
│   ├── AuthController.php      # Controlador de autenticación
│   ├── VehicleController.php   # Controlador de vehículos
│   ├── ReservationController.php # Controlador de reservas
│   └── SwaggerController.php   # Controlador de documentación
└── index.php                   # Punto de entrada principal
```

## Endpoints Disponibles

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registrar usuario
- `GET /api/auth/verify` - Verificar token

### Vehículos
- `GET /api/vehicles` - Listar vehículos
- `GET /api/vehicles/{id}` - Obtener vehículo específico
- `POST /api/vehicles` - Crear vehículo
- `DELETE /api/vehicles/{id}` - Eliminar vehículo
- `GET /api/vehicles/validate/{plate}` - Validar patente

### Reservas
- `GET /api/reservations` - Listar reservas
- `GET /api/reservations/{id}` - Obtener reserva específica
- `POST /api/reservations` - Crear reserva
- `DELETE /api/reservations/{id}` - Cancelar reserva
- `GET /api/reservations/availability` - Verificar disponibilidad

### Documentación
- `GET /api/docs` - Documentación Swagger interactiva
- `GET /api/swagger.json` - Especificación OpenAPI

## Instalación y Configuración

### Requisitos
- PHP 7.4 o superior
- MySQL/MariaDB
- XAMPP (recomendado para desarrollo)

### Configuración de Base de Datos
La API utiliza la base de datos `a2024_dvasquez` con las siguientes credenciales por defecto:
- Host: localhost
- Usuario: root
- Contraseña: (vacía)
- Base de datos: a2024_dvasquez

### Estructura de Tablas Requerida
```sql
-- Usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('estudiante', 'docente', 'administrativo', 'admin') DEFAULT 'estudiante',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Vehículos
CREATE TABLE vehiculos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patente VARCHAR(10) UNIQUE NOT NULL,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    color VARCHAR(50),
    usuario_id INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Espacios de Estacionamiento
CREATE TABLE espacios_estacionamiento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero VARCHAR(10) UNIQUE NOT NULL,
    ubicacion VARCHAR(255),
    estado ENUM('disponible', 'ocupado', 'mantenimiento') DEFAULT 'disponible'
);

-- Reservas
CREATE TABLE reservas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    vehiculo_id INT NOT NULL,
    espacio_id INT NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    estado ENUM('activa', 'completada', 'cancelada') DEFAULT 'activa',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id),
    FOREIGN KEY (espacio_id) REFERENCES espacios_estacionamiento(id)
);
```

## Uso de la API

### 1. Autenticación
```javascript
// Login
const response = await fetch('/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'usuario@uct.cl',
        password: 'password123'
    })
});

const data = await response.json();
const token = data.token;
```

### 2. Usar Token en Requests
```javascript
const response = await fetch('/api/vehicles', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
});
```

### 3. Crear Vehículo
```javascript
const response = await fetch('/api/vehicles', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        patente: 'ABC123',
        marca: 'Toyota',
        modelo: 'Corolla',
        color: 'Blanco',
        usuario_id: 1
    })
});
```

### 4. Crear Reserva
```javascript
const response = await fetch('/api/reservations', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        usuario_id: 1,
        vehiculo_id: 1,
        espacio_id: 1,
        fecha_inicio: '2025-06-25 08:00:00',
        fecha_fin: '2025-06-25 18:00:00'
    })
});
```

## Pruebas y Demostración

### 1. Documentación Swagger
Accede a la documentación interactiva:
```
http://localhost/PROYECTO/Estacionamientos_UCT/api/docs
```

### 2. Frontend de Demostración
Abre el archivo de demostración:
```
http://localhost/PROYECTO/Estacionamientos_UCT/frontend_api_demo.html
```

### 3. Prueba Manual con cURL
```bash
# Verificar estado de la API
curl http://localhost/PROYECTO/Estacionamientos_UCT/api/

# Login
curl -X POST http://localhost/PROYECTO/Estacionamientos_UCT/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@uct.cl","password":"admin123"}'

# Listar vehículos (requiere token)
curl http://localhost/PROYECTO/Estacionamientos_UCT/api/vehicles \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Ventajas de la Implementación

### Reemplazo de Consultas SQL
- ✅ Abstracción de la base de datos
- ✅ Seguridad mejorada con prepared statements
- ✅ Validación centralizada de datos
- ✅ Manejo consistente de errores

### Arquitectura RESTful
- ✅ Estándar HTTP/REST
- ✅ Respuestas JSON estructuradas
- ✅ Códigos de estado apropiados
- ✅ CORS habilitado para frontend

### Documentación y Pruebas
- ✅ Swagger UI interactivo
- ✅ Especificación OpenAPI completa
- ✅ Ejemplos de integración
- ✅ Frontend de demostración

## Próximos Pasos

1. **Integración Completa**: Reemplazar las consultas SQL directas en el sistema existente
2. **Autenticación JWT**: Implementar JWT real para mayor seguridad
3. **Validaciones Avanzadas**: Añadir más validaciones de negocio
4. **Rate Limiting**: Implementar límites de peticiones
5. **Logging**: Añadir sistema de logs detallado
6. **Tests Unitarios**: Crear suite de pruebas automatizadas

## Soporte

Para dudas o problemas:
- Revisa la documentación Swagger: `/api/docs`
- Consulta este README
- Verifica los logs de error de PHP