<?php
include 'conexion.php';
session_start();

// Verificar si hay datos de pedido
if (!isset($_SESSION['order_data'])) {
    header('Location: index.php');
    exit;
}

$order = $_SESSION['order_data'];
unset($_SESSION['order_data']);
include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - Mi Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/Global.css">
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="mb-3">¡Pedido Confirmado!</h1>
            <p class="lead mb-4">Gracias por tu compra. Tu pedido ha sido procesado exitosamente.</p>
            <div class="alert alert-success">
                <h4 class="alert-heading">Número de Pedido: <?= $order['order_number'] ?></h4>
                <p>Hemos enviado un correo electrónico con los detalles de tu pedido a <strong><?= $order['shipping']['email'] ?></strong></p>
            </div>
            
            <div class="order-details">
                <h4 class="mb-4 text-center">Resumen del Pedido</h4>
                
                <div class="detail-row">
                    <div class="detail-label">Fecha del Pedido:</div>
                    <div class="detail-value"><?= date('d/m/Y H:i') ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Método de Pago:</div>
                    <div class="detail-value">
                        <?php 
                        switch ($order['payment_method']) {
                            case 'credit': echo 'Tarjeta de Crédito/Débito'; break;
                            case 'paypal': echo 'PayPal'; break;
                            case 'transfer': echo 'Transferencia Bancaria'; break;
                            default: echo $order['payment_method'];
                        }
                        ?>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Total Pagado:</div>
                    <div class="detail-value">$<?= number_format($order['total'], 2) ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Dirección de Envío:</div>
                    <div class="detail-value">
                        <?= $order['shipping']['first_name'] ?> <?= $order['shipping']['last_name'] ?><br>
                        <?= $order['shipping']['address'] ?><br>
                        <?= $order['shipping']['city'] ?>, <?= $order['shipping']['state'] ?><br>
                        <?= $order['shipping']['zip'] ?>, <?= $order['shipping']['country'] ?>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Contacto:</div>
                    <div class="detail-value">
                        <?= $order['shipping']['email'] ?><br>
                        <?= $order['shipping']['phone'] ? $order['shipping']['phone'] : 'N/A' ?>
                    </div>
                </div>
            </div>
            
            <p class="mb-4">Tu pedido será enviado en los próximos días. Recibirás un correo electrónico con el número de seguimiento cuando sea despachado.</p>
            
            <div class="d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-continue">
                    <i class="fas fa-home me-2"></i>Seguir Comprando
                </a>
                <a href="orders.php" class="btn btn-outline-primary">
                    <i class="fas fa-box me-2"></i>Ver Mis Pedidos
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php
     include 'footer.php';
    ?>
</body>
</html>