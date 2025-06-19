<?php
include 'conexion.php';
$logged_in = isset($_SESSION['user_id']);
include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/Global.css">
</head>
<body>

    <!-- Sección Hero -->
    <section class="hero-section d-flex align-items-center text-white">
        <div class="container text-center">
            <h1 class="display-3 fw-bold mb-4">Bienvenido a Nuestra Tienda</h1>
            <p class="lead mb-5">Encuentra los mejores productos a precios increíbles</p>
            <a href="products.php" class="btn btn-primary btn-lg px-5 py-3">
                <i class="fas fa-shopping-bag me-2"></i>Comprar Ahora
            </a>
        </div>
    </section>

    <!-- Productos destacados -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold">Productos Destacados</h2>
                <a href="products.php" class="btn btn-outline-primary">Ver Todos <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            
            <div class="row g-4">
                <!-- Producto 1 -->
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-danger">Oferta</span>
                                <span class="text-success fw-bold">$59.99</span>
                            </div>
                            <h5 class="card-title">Smartphone X200</h5>
                            <p class="card-text text-muted small">Último modelo con cámara de 64MP</p>
                        </div>
                        <div class="card-footer bg-white">
                            <button class="btn btn-primary w-100">
                                <i class="fas fa-cart-plus me-2"></i>Añadir al Carrito
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Producto 2 -->
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                        <div class="card-body">
                            <span class="text-success fw-bold">$129.99</span>
                            <h5 class="card-title mt-2">Auriculares Bluetooth</h5>
                            <p class="card-text text-muted small">Cancelación de ruido premium</p>
                        </div>
                        <div class="card-footer bg-white">
                            <button class="btn btn-primary w-100">
                                <i class="fas fa-cart-plus me-2"></i>Añadir al Carrito
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Producto 3 -->
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-success">Nuevo</span>
                                <span class="text-success fw-bold">$299.99</span>
                            </div>
                            <h5 class="card-title">Smart Watch Pro</h5>
                            <p class="card-text text-muted small">Monitoreo de salud avanzado</p>
                        </div>
                        <div class="card-footer bg-white">
                            <button class="btn btn-primary w-100">
                                <i class="fas fa-cart-plus me-2"></i>Añadir al Carrito
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Producto 4 -->
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                        <div class="card-body">
                            <span class="text-success fw-bold">$89.99</span>
                            <h5 class="card-title mt-2">Tablet 10"</h5>
                            <p class="card-text text-muted small">128GB almacenamiento, 8GB RAM</p>
                        </div>
                        <div class="card-footer bg-white">
                            <button class="btn btn-primary w-100">
                                <i class="fas fa-cart-plus me-2"></i>Añadir al Carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Características -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h4>Envío Rápido</h4>
                    <p class="text-muted">Entrega en 24-48 horas en toda la región</p>
                </div>
                <div class="col-md-4">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Pago Seguro</h4>
                    <p class="text-muted">Transacciones protegidas con cifrado SSL</p>
                </div>
                <div class="col-md-4">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>Soporte 24/7</h4>
                    <p class="text-muted">Asistencia técnica disponible en todo momento</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Banner promocional -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">¡Oferta Especial de Verano!</h2>
            <p class="lead mb-5">Hasta 50% de descuento en toda la colección de electrónicos</p>
            <a href="sale.php" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                <i class="fas fa-fire me-2"></i>Ver Ofertas
            </a>
        </div>
    </section>

    <!-- Testimonios -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Lo que Dicen Nuestros Clientes</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Cliente">
                                <div>
                                    <h5 class="mb-0">María López</h5>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text">"Excelente servicio al cliente y entrega rápida. Los productos llegaron en perfecto estado."</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Cliente">
                                <div>
                                    <h5 class="mb-0">Carlos Rodríguez</h5>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text">"La calidad de los productos superó mis expectativas. Definitivamente volveré a comprar."</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Cliente">
                                <div>
                                    <h5 class="mb-0">Ana Martínez</h5>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text">"El proceso de compra fue muy sencillo y recibí asistencia inmediata cuando tuve una pregunta."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php 
    include 'footer.php';
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>