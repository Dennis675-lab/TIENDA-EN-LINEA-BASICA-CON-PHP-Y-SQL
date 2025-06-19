<?php
include 'conexion.php'!
session_start();

// Si el usuario ya está autenticado, redirigir
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Procesar formulario
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Ambos campos son requeridos';
    } else {
        // Buscar usuario en la base de datos
        $stmt = $pdo->prepare("SELECT id, password FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            
            // Redirigir a página principal
            header('Location: index.php');
            exit;
        } else {
            $error = 'Credenciales incorrectas';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Mi Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/Global.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="fas fa-lock me-2"></i>Iniciar Sesión</h2>
                <p class="mb-0">Accede a tu cuenta de Mi Tienda</p>
            </div>
            
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario o Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Ingresa tu usuario o email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="password-container">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Ingresa tu contraseña" required>
                            </div>
                            <span class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Recordar mi cuenta</label>
                        <a href="#" class="float-end text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                    </button>
                </form>
                
                <div class="divider">
                    <span>O inicia sesión con</span>
                </div>
                
                <div class="social-login">
                    <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn google"><i class="fab fa-google"></i></a>
                    <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                </div>
                
                <div class="register-link">
                    ¿No tienes una cuenta? <a href="register.php">Regístrate ahora</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar contraseña
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Recordar usuario al recargar
        document.addEventListener('DOMContentLoaded', function() {
            const rememberCheckbox = document.getElementById('remember');
            const usernameInput = document.getElementById('username');
            
            // Verificar si hay credenciales guardadas
            if (localStorage.getItem('remember') === 'true') {
                rememberCheckbox.checked = true;
                usernameInput.value = localStorage.getItem('username') || '';
            }
            
            // Guardar credenciales al marcar "Recordar"
            rememberCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    localStorage.setItem('remember', 'true');
                    localStorage.setItem('username', usernameInput.value);
                } else {
                    localStorage.removeItem('remember');
                    localStorage.removeItem('username');
                }
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php
        include 'footer.php';
    ?>
</body>
</html>