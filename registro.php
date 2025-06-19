<?php
include 'conexion.php';
session_start();

// Si el usuario ya está autenticado, redirigir
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Procesar formulario
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Recoger y sanitizar datos
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validaciones
    if (empty($username)) {
        $errors['username'] = 'El nombre de usuario es requerido';
    } else if (strlen($username) < 4) {
        $errors['username'] = 'El usuario debe tener al menos 4 caracteres';
    }
    
    if (empty($email)) {
        $errors['email'] = 'El correo electrónico es requerido';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El formato de email es inválido';
    }
    
    if (empty($password)) {
        $errors['password'] = 'La contraseña es requerida';
    } else if (strlen($password) < 8) {
        $errors['password'] = 'La contraseña debe tener al menos 8 caracteres';
    } else if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors['password'] = 'Debe incluir mayúsculas, números y símbolos';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Las contraseñas no coinciden';
    }
    
    // Verificar si el usuario o email existen
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            $errors['general'] = 'El usuario o email ya están registrados';
        }
    }
    
    // Registrar usuario si no hay errores
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');
        
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $created_at]);
            
            $success = true;
        } catch (PDOException $e) {
            $errors['general'] = 'Error al registrar: ' . $e->getMessage();
        }
    }
}
include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Mi Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/Global.css">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h2><i class="fas fa-user-plus me-2"></i>Crear Cuenta</h2>
                <p class="mb-0">Únete a nuestra comunidad de compras</p>
            </div>
            
            <div class="register-body">
                <!-- Indicador de pasos -->
                <div class="step-indicator">
                    <div class="step active">
                        <div class="step-number">1</div>
                        <div class="step-label">Información</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-label">Verificación</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-label">Completado</div>
                    </div>
                </div>
                
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-2x mb-3"></i>
                        <h4>¡Registro exitoso!</h4>
                        <p>Tu cuenta ha sido creada correctamente</p>
                        <a href="login.php" class="btn btn-success mt-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                    </div>
                <?php else: ?>
                    <form method="POST" id="registerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Nombre de Usuario *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                                           id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                                           placeholder="Elige un nombre de usuario" required>
                                </div>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback d-block"><?= $errors['username'] ?></div>
                                <?php else: ?>
                                    <div class="form-text">Mínimo 4 caracteres</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                           placeholder="tu@email.com" required>
                                </div>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback d-block"><?= $errors['email'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
                                <div class="password-container">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                               id="password" name="password" placeholder="Crea una contraseña segura" required>
                                    </div>
                                    <span class="password-toggle" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                
                                <div class="progress mt-2">
                                    <div class="progress-bar" id="password-strength" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div class="password-strength-text" id="password-strength-text">Seguridad de la contraseña</div>
                                
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback d-block"><?= $errors['password'] ?></div>
                                <?php else: ?>
                                    <div class="form-text">Mínimo 8 caracteres con mayúsculas, números y símbolos</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Contraseña *</label>
                                <div class="password-container">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                               id="confirm_password" name="confirm_password" placeholder="Repite tu contraseña" required>
                                    </div>
                                    <span class="password-toggle" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback d-block"><?= $errors['confirm_password'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Preferencias</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" checked>
                                <label class="form-check-label" for="newsletter">
                                    Recibir ofertas exclusivas por email
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Acepto los <a href="#" class="text-success">términos y condiciones</a> y la 
                                    <a href="#" class="text-success">política de privacidad</a>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" name="register" class="btn btn-register w-100">
                            <i class="fas fa-user-plus me-2"></i>Registrarse
                        </button>
                    </form>
                <?php endif; ?>
                
                <div class="login-link">
                    ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar contraseña
        function setupPasswordToggle(inputId, toggleId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            
            if (toggle && input) {
                toggle.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }
        }
        
        // Configurar toggles
        setupPasswordToggle('password', 'togglePassword');
        setupPasswordToggle('confirm_password', 'toggleConfirmPassword');
        
        // Validación de fortaleza de contraseña
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('password-strength');
        const strengthText = document.getElementById('password-strength-text');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let text = '';
            let color = '';
            
            // Verificar longitud
            if (password.length >= 8) strength += 25;
            
            // Verificar mayúsculas
            if (/[A-Z]/.test(password)) strength += 25;
            
            // Verificar números
            if (/[0-9]/.test(password)) strength += 25;
            
            // Verificar símbolos
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;
            
            // Asignar texto y color según fuerza
            if (password.length === 0) {
                text = 'Seguridad de la contraseña';
                color = 'bg-secondary';
            } else if (strength < 50) {
                text = 'Débil';
                color = 'bg-danger';
            } else if (strength < 75) {
                text = 'Moderada';
                color = 'bg-warning';
            } else {
                text = 'Fuerte';
                color = 'bg-success';
            }
            
            // Actualizar UI
            strengthBar.style.width = strength + '%';
            strengthBar.className = 'progress-bar ' + color;
            strengthText.textContent = text;
            strengthText.className = 'password-strength-text ' + color.replace('bg-', 'text-');
        });
        
        // Validar coincidencia de contraseñas
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        function validatePasswordMatch() {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('is-invalid');
            } else {
                confirmPasswordInput.classList.remove('is-invalid');
            }
        }
        
        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        
        // Validación de términos
        const form = document.getElementById('registerForm');
        const termsCheckbox = document.getElementById('terms');
        
        form.addEventListener('submit', function(e) {
            if (!termsCheckbox.checked) {
                e.preventDefault();
                termsCheckbox.classList.add('is-invalid');
                alert('Debes aceptar los términos y condiciones para continuar');
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php
        include 'footer.php'
    ?>
</body>
</html>