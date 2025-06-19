<?php
include 'conexion.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?return_to=checkout.php');
    exit;
}

// Verificar si hay productos en el carrito
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Simulación de base de datos
$products = [
    1 => ['name' => 'Smartphone X200', 'price' => 599.99, 'image' => 'product1.jpg'],
    2 => ['name' => 'Auriculares Bluetooth', 'price' => 129.99, 'image' => 'product2.jpg'],
    3 => ['name' => 'Smart Watch Pro', 'price' => 299.99, 'image' => 'product3.jpg'],
    4 => ['name' => 'Tablet 10"', 'price' => 89.99, 'image' => 'product4.jpg'],
];

// Calcular totales
$subtotal = 0;
$discount = 0;
$shipping = 9.99;
$tax_rate = 0.08; // 8%

foreach ($_SESSION['cart'] as $product_id => $item) {
    if (isset($products[$product_id])) {
        $price = $products[$product_id]['price'];
        $quantity = $item['quantity'];
        $subtotal += $price * $quantity;
    }
}

// Aplicar descuento si existe
if (isset($_SESSION['coupon'])) {
    $discount = $subtotal * ($_SESSION['coupon']['discount'] / 100);
}

// Calcular impuestos y total
$tax = ($subtotal - $discount) * $tax_rate;
$total = $subtotal - $discount + $shipping + $tax;

// Procesar el pedido
$order_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Validar datos del formulario
    $errors = [];
    
    // Datos de envío
    $shipping_data = [
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'address' => trim($_POST['address']),
        'city' => trim($_POST['city']),
        'state' => trim($_POST['state']),
        'zip' => trim($_POST['zip']),
        'country' => trim($_POST['country']),
        'shipping_method' => $_POST['shipping_method']
    ];
    
    // Validar campos requeridos
    $required_fields = ['first_name', 'last_name', 'email', 'address', 'city', 'zip', 'country'];
    foreach ($required_fields as $field) {
        if (empty($shipping_data[$field])) {
            $errors[$field] = 'Este campo es obligatorio';
        }
    }
    
    // Validar email
    if (!filter_var($shipping_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email inválido';
    }
    
    // Si no hay errores, procesar el pedido
    if (empty($errors)) {
        // Aquí normalmente se guardaría en la base de datos
        // y se procesaría el pago con una pasarela
        
        // Generar número de pedido
        $order_number = 'ORD-' . time() . '-' . mt_rand(1000, 9999);
        
        // Guardar datos de pedido en sesión para mostrar en confirmación
        $_SESSION['order_data'] = [
            'order_number' => $order_number,
            'shipping' => $shipping_data,
            'products' => $_SESSION['cart'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_cost' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => $_POST['payment_method']
        ];
        
        // Vaciar carrito y cupón
        unset($_SESSION['cart']);
        unset($_SESSION['coupon']);
        
        // Redirigir a página de confirmación
        header('Location: order_success.php');
        exit;
    }
}
include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Pedido - Mi Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/Global.css">
</head>
<body>
    <!-- Encabezado del checkout -->
    <div class="checkout-header">
        <div class="container">
            <h1 class="text-center mb-4">Finalizar Compra</h1>
            
            <!-- Pasos del checkout -->
            <div class="checkout-steps">
                <div class="step completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Carrito</div>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-label">Envío y Pago</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">Confirmación</div>
                </div>
            </div>
        </div>
    </div>

    <div class="checkout-container">
        <form method="POST" class="row g-4">
            <!-- Columna izquierda: Información de envío y pago -->
            <div class="col-lg-7">
                <!-- Información de envío -->
                <div class="checkout-card">
                    <h3 class="checkout-title"><i class="fas fa-truck me-2"></i>Información de Envío</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Nombre *</label>
                            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                                   id="first_name" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['first_name'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Apellido *</label>
                            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                                   id="last_name" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['last_name'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $_SESSION['email'] ?? '') ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección *</label>
                        <input type="text" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" 
                               id="address" name="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required>
                        <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback"><?= $errors['address'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">Ciudad *</label>
                            <input type="text" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" 
                                   id="city" name="city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" required>
                            <?php if (isset($errors['city'])): ?>
                                <div class="invalid-feedback"><?= $errors['city'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">Provincia</label>
                            <input type="text" class="form-control" id="state" name="state" 
                                   value="<?= htmlspecialchars($_POST['state'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="zip" class="form-label">Código Postal *</label>
                            <input type="text" class="form-control <?= isset($errors['zip']) ? 'is-invalid' : '' ?>" 
                                   id="zip" name="zip" value="<?= htmlspecialchars($_POST['zip'] ?? '') ?>" required>
                            <?php if (isset($errors['zip'])): ?>
                                <div class="invalid-feedback"><?= $errors['zip'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">País *</label>
                            <select class="form-select <?= isset($errors['country']) ? 'is-invalid' : '' ?>" 
                                    id="country" name="country" required>
                                <option value="">Seleccionar país</option>
                                <option value="AR" <?= ($_POST['country'] ?? '') === 'AR' ? 'selected' : '' ?>>Argentina</option>
                                <option value="ES" <?= ($_POST['country'] ?? '') === 'ES' ? 'selected' : '' ?>>España</option>
                                <option value="MX" <?= ($_POST['country'] ?? '') === 'MX' ? 'selected' : '' ?>>México</option>
                                <option value="CO" <?= ($_POST['country'] ?? '') === 'CO' ? 'selected' : '' ?>>Colombia</option>
                                <option value="CL" <?= ($_POST['country'] ?? '') === 'CL' ? 'selected' : '' ?>>Chile</option>
                                <option value="PE" <?= ($_POST['country'] ?? '') === 'PE' ? 'selected' : '' ?>>Perú</option>
                                <option value="US" <?= ($_POST['country'] ?? '') === 'US' ? 'selected' : '' ?>>Estados Unidos</option>
                            </select>
                            <?php if (isset($errors['country'])): ?>
                                <div class="invalid-feedback"><?= $errors['country'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Método de envío -->
                <div class="checkout-card">
                    <h3 class="checkout-title"><i class="fas fa-shipping-fast me-2"></i>Método de Envío</h3>
                    
                    <div class="shipping-method selected" data-value="standard">
                        <div class="shipping-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Envío Estándar</h5>
                            <p class="mb-0">Entrega en 3-5 días hábiles</p>
                        </div>
                        <div class="ms-auto fw-bold">$9.99</div>
                        <input type="radio" name="shipping_method" value="standard" checked style="display: none;">
                    </div>
                    
                    <div class="shipping-method" data-value="express">
                        <div class="shipping-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Envío Express</h5>
                            <p class="mb-0">Entrega en 24-48 horas</p>
                        </div>
                        <div class="ms-auto fw-bold">$19.99</div>
                        <input type="radio" name="shipping_method" value="express" style="display: none;">
                    </div>
                </div>
                
                <!-- Método de pago -->
                <div class="checkout-card">
                    <h3 class="checkout-title"><i class="fas fa-credit-card me-2"></i>Método de Pago</h3>
                    
                    <div class="payment-method selected" data-value="credit">
                        <div class="d-flex align-items-center">
                            <div class="payment-icon">
                                <i class="far fa-credit-card"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Tarjeta de Crédito/Débito</h5>
                                <p class="mb-0">Paga con Visa, Mastercard, American Express</p>
                            </div>
                        </div>
                        <input type="radio" name="payment_method" value="credit" checked style="display: none;">
                        
                        <!-- Formulario de tarjeta de crédito -->
                        <div class="credit-card-form">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="card_number" class="form-label">Número de Tarjeta</label>
                                    <input type="text" class="form-control" id="card_number" 
                                           placeholder="0000 0000 0000 0000">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="card_name" class="form-label">Nombre en la Tarjeta</label>
                                    <input type="text" class="form-control" id="card_name" 
                                           placeholder="Como aparece en la tarjeta">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="card_expiry" class="form-label">Vencimiento</label>
                                    <input type="text" class="form-control" id="card_expiry" 
                                           placeholder="MM/AA">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="card_cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="card_cvv" 
                                           placeholder="123">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-method" data-value="paypal">
                        <div class="d-flex align-items-center">
                            <div class="payment-icon">
                                <i class="fab fa-paypal"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">PayPal</h5>
                                <p class="mb-0">Paga con tu cuenta PayPal</p>
                            </div>
                        </div>
                        <input type="radio" name="payment_method" value="paypal" style="display: none;">
                    </div>
                    
                    <div class="payment-method" data-value="transfer">
                        <div class="d-flex align-items-center">
                            <div class="payment-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Transferencia Bancaria</h5>
                                <p class="mb-0">Realiza una transferencia bancaria</p>
                            </div>
                        </div>
                        <input type="radio" name="payment_method" value="transfer" style="display: none;">
                    </div>
                </div>
            </div>
            
            <!-- Columna derecha: Resumen del pedido -->
            <div class="col-lg-5">
                <div class="checkout-card">
                    <h3 class="checkout-title"><i class="fas fa-receipt me-2"></i>Resumen del Pedido</h3>
                    
                    <div class="order-summary mb-4">
                        <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                            <?php if (isset($products[$product_id])): ?>
                                <?php $product = $products[$product_id]; ?>
                                <div class="product-row">
                                    <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="product-img">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= $product['name'] ?></h6>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Cantidad: <?= $item['quantity'] ?></span>
                                            <span class="fw-bold">$<?= number_format($product['price'] * $item['quantity'], 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <div class="summary-item">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value">$<?= number_format($subtotal, 2) ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['coupon'])): ?>
                            <div class="summary-item">
                                <span class="summary-label">Descuento (<?= $_SESSION['coupon']['code'] ?>)</span>
                                <span class="summary-value text-danger">-$<?= number_format($discount, 2) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="summary-item">
                            <span class="summary-label">Envío</span>
                            <span class="summary-value">$<?= number_format($shipping, 2) ?></span>
                        </div>
                        
                        <div class="summary-item">
                            <span class="summary-label">Impuestos (<?= $tax_rate * 100 ?>%)</span>
                            <span class="summary-value">$<?= number_format($tax, 2) ?></span>
                        </div>
                        
                        <div class="summary-item total-row">
                            <span>Total</span>
                            <span>$<?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" class="btn-place-order">
                        <i class="fas fa-lock me-2"></i>Realizar Pedido
                    </button>
                    
                    <div class="mt-3 text-center">
                        <p class="small text-muted">Al realizar tu pedido, aceptas nuestros <a href="#">Términos y Condiciones</a> y <a href="#">Política de Privacidad</a>.</p>
                    </div>
                </div>
                
                <div class="checkout-card">
                    <h5 class="mb-3"><i class="fas fa-shield-alt me-2 text-primary"></i>Compra Segura</h5>
                    <p class="small text-muted">Tus datos están protegidos con cifrado SSL de 256-bit. No almacenamos información de tu tarjeta de crédito.</p>
                    <div class="d-flex justify-content-center mt-3">
                        <img src="https://via.placeholder.com/50x30" alt="Visa" class="me-2">
                        <img src="https://via.placeholder.com/50x30" alt="Mastercard" class="me-2">
                        <img src="https://via.placeholder.com/50x30" alt="PayPal" class="me-2">
                        <img src="https://via.placeholder.com/50x30" alt="SSL Secure">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Seleccionar método de envío
        document.querySelectorAll('.shipping-method').forEach(method => {
            method.addEventListener('click', function() {
                // Quitar selección actual
                document.querySelectorAll('.shipping-method').forEach(m => {
                    m.classList.remove('selected');
                });
                
                // Seleccionar nuevo método
                this.classList.add('selected');
                
                // Actualizar radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                // Actualizar costo de envío en el resumen (simulado)
                if (this.dataset.value === 'express') {
                    document.querySelector('.summary-item:nth-child(3) .summary-value').textContent = '$19.99';
                    // Aquí deberías recalcular los totales
                } else {
                    document.querySelector('.summary-item:nth-child(3) .summary-value').textContent = '$9.99';
                }
            });
        });
        
        // Seleccionar método de pago
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                // Quitar selección actual
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('selected');
                    m.classList.remove('payment-active');
                });
                
                // Seleccionar nuevo método
                this.classList.add('selected');
                this.classList.add('payment-active');
                
                // Actualizar radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });
        
        // Formatear número de tarjeta
        const cardNumber = document.getElementById('card_number');
        if (cardNumber) {
            cardNumber.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                value = value.replace(/(\d{4})/g, '$1 ').trim();
                this.value = value.substring(0, 19);
            });
        }
        
        // Formatear fecha de vencimiento
        const cardExpiry = document.getElementById('card_expiry');
        if (cardExpiry) {
            cardExpiry.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                this.value = value.substring(0, 5);
            });
        }
        
        // Formatear CVV
        const cardCvv = document.getElementById('card_cvv');
        if (cardCvv) {
            cardCvv.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').substring(0, 3);
            });
        }
        
        // Confirmar al realizar pedido
        document.querySelector('[name="place_order"]').addEventListener('click', function() {
            if (!confirm('¿Confirmas que deseas realizar este pedido?')) {
                event.preventDefault();
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php
       include 'footer.php';
    ?>
</body>
</html>