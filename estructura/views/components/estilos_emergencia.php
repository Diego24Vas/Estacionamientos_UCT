<?php
/**
 * Componente de estilos de emergencia para cuando los archivos CSS externos no cargan
 * Incluye este archivo en las páginas principales como solución temporal
 */
?>
<style>
/* =================================
   ESTILOS DE EMERGENCIA INLINE
   Tema profesional UCT
   ================================= */

/* Variables CSS para consistencia */
:root {
    --uct-blue: #2c5aa0;
    --uct-light-blue: #4a90e2;
    --uct-lighter-blue: #7bb3f0;
    --uct-accent: #0066cc;
    --success-color: #28a745;
    --error-color: #dc3545;
    --warning-color: #ffc107;
    --gray-light: #f8f9fa;
    --gray-medium: #6c757d;
    --text-dark: #343a40;
}

/* Reset básico */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--text-dark);
    background: linear-gradient(135deg, var(--uct-blue) 0%, var(--uct-light-blue) 100%);
    min-height: 100vh;
}

/* Contenedores principales */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.form-container, .form-box {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
    max-width: 500px;
    margin: 50px auto;
}

/* Headers */
h1, h2, h3 {
    color: var(--uct-blue);
    margin-bottom: 20px;
}

h1 { font-size: 2.5em; text-align: center; }
h2 { font-size: 2em; text-align: center; }
h3 { font-size: 1.5em; }

/* Formularios */
.inputbox, .form-group {
    position: relative;
    margin: 20px 0;
}

.inputbox input, 
.form-group input,
.form-group select {
    width: 100%;
    padding: 15px;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    font-size: 16px;
    background: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.inputbox input:focus,
.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--uct-accent);
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

/* Labels */
.inputbox label,
.form-group label {
    position: absolute;
    top: 15px;
    left: 15px;
    color: var(--gray-medium);
    font-size: 16px;
    transition: all 0.3s ease;
    pointer-events: none;
    background: white;
    padding: 0 5px;
}

.inputbox input:focus ~ label,
.inputbox input:not(:placeholder-shown) ~ label {
    top: -8px;
    left: 10px;
    font-size: 12px;
    color: var(--uct-accent);
    font-weight: bold;
}

/* Botones */
button, .btn {
    background: linear-gradient(45deg, var(--uct-blue), var(--uct-light-blue));
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    box-shadow: 0 4px 15px rgba(44, 90, 160, 0.3);
}

button:hover, .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 90, 160, 0.4);
    background: linear-gradient(45deg, var(--uct-light-blue), var(--uct-lighter-blue));
}

button:active, .btn:active {
    transform: translateY(0);
}

/* Botones específicos */
.btn-success { 
    background: linear-gradient(45deg, var(--success-color), #34ce57);
}

.btn-danger { 
    background: linear-gradient(45deg, var(--error-color), #e55a6b);
}

.btn-warning { 
    background: linear-gradient(45deg, var(--warning-color), #ffcd39);
    color: #333;
}

/* Tablas */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e1e5e9;
}

th {
    background: var(--uct-blue);
    color: white;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 14px;
}

tr:nth-child(even) {
    background: var(--gray-light);
}

tr:hover {
    background: rgba(74, 144, 226, 0.1);
}

/* Header/Navigation */
header {
    background: rgba(255, 255, 255, 0.95);
    padding: 15px 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}

nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    align-items: center;
}

nav ul li {
    margin: 0 20px;
}

nav ul li a {
    color: var(--uct-blue);
    text-decoration: none;
    font-weight: bold;
    padding: 10px 15px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

nav ul li a:hover {
    background: var(--uct-blue);
    color: white;
}

/* Logo */
#img-logo {
    text-align: center;
    margin-bottom: 30px;
}

#img-logo img {
    max-width: 150px;
    height: auto;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: var(--uct-blue);
    color: white;
    padding: 20px 0;
    z-index: 1000;
}

.sidebar h2 {
    color: white;
    text-align: center;
    margin-bottom: 30px;
}

.sidebar ul {
    list-style: none;
}

.sidebar ul li {
    margin: 10px 0;
}

.sidebar ul li a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    padding: 15px 20px;
    display: block;
    transition: all 0.3s ease;
}

.sidebar ul li a:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

/* Content con sidebar */
.main-content {
    margin-left: 250px;
    padding: 20px;
}

/* Cards */
.card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin: 20px 0;
}

/* Alerts */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    border: 1px solid var(--success-color);
    color: #155724;
}

.alert-error {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid var(--error-color);
    color: #721c24;
}

.alert-warning {
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid var(--warning-color);
    color: #856404;
}

/* Footer */
footer {
    background: var(--uct-blue);
    color: white;
    padding: 40px 0;
    margin-top: 50px;
}

.footer {
    display: flex;
    justify-content: space-around;
    text-align: center;
    max-width: 1200px;
    margin: 0 auto;
}

.footer-column h3 {
    color: white;
    margin-bottom: 15px;
}

.footer-column a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    display: block;
    margin: 5px 0;
    transition: color 0.3s ease;
}

.footer-column a:hover {
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .form-container, .form-box {
        margin: 20px;
        padding: 20px;
    }
    
    .footer {
        flex-direction: column;
    }
    
    .container {
        padding: 10px;
    }
}

/* Estados de carga */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    border: 2px solid var(--uct-blue);
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    transform: translate(-50%, -50%);
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Utilidades */
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }
.mb-20 { margin-bottom: 20px; }
.mt-20 { margin-top: 20px; }
.p-20 { padding: 20px; }
.hidden { display: none; }
.visible { display: block; }

/* Animaciones de entrada */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Estados de error específicos */
.form-error input {
    border-color: var(--error-color);
    background: rgba(220, 53, 69, 0.05);
}

.form-success input {
    border-color: var(--success-color);
    background: rgba(40, 167, 69, 0.05);
}
</style>

<script>
/* JavaScript básico para funcionalidad esencial */
document.addEventListener('DOMContentLoaded', function() {
    // Animación de fade-in para elementos
    const elements = document.querySelectorAll('.form-container, .card, .alert');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('fade-in');
        }, index * 100);
    });
    
    // Mejora de UX para formularios
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
    
    // Loading state para botones
    const buttons = document.querySelectorAll('button[type="submit"]');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('loading');
            this.disabled = true;
            
            // Restaurar después de 3 segundos (por si hay error)
            setTimeout(() => {
                this.classList.remove('loading');
                this.disabled = false;
            }, 3000);
        });
    });
});
</script>
