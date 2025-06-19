<?php
include 'conexion.php';
session_start();
$logged_in = isset($_SESSION['user_id']);

// Manejar búsqueda y filtros
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'all';
$sort = $_GET['sort'] ?? 'default';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Filtrar productos
$filtered_products = array_filter($products, function($product) use ($search, $category) {
    $matches = true;
    
    if (!empty($search)) {
        $matches = stripos($product['name'], $search) !== false || 
                   stripos($product['description'], $search) !== false;
    }
    
    if ($category !== 'all' && $matches) {
        $matches = $product['category'] === $category;
    }
    
    return $matches;
});

// Ordenar productos
switch ($sort) {
    case 'price_asc':
        usort($filtered_products, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        break;
    case 'price_desc':
        usort($filtered_products, function($a, $b) {
            return $b['price'] <=> $a['price'];
        });
        break;
    case 'rating':
        usort($filtered_products, function($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });
        break;
    case 'name':
        usort($filtered_products, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        break;
}

// Paginación
$per_page = 8;
$total_products = count($filtered_products);
$total_pages = ceil($total_products / $per_page);
$offset = ($page - 1) * $per_page;
$paginated_products = array_slice($filtered_products, $offset, $per_page);

// Categorías disponibles
$categories = array_unique(array_column($products, 'category'));
sort($categories);

// Manejar añadir al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!$logged_in) {
        header('Location: login.php?return_to=products.php');
        exit;
    }
    
    $product_id = intval($_POST['product_id']);
    
    if (isset($products[$product_id])) {
        // Inicializar carrito si no existe
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Añadir producto al carrito o incrementar cantidad
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = ['quantity' => 1];
        }
        
        $added_to_cart = true;
    }
}
include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - Mi Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/Global.css">
</head>
<body>

    <!-- Encabezado del catálogo -->
    <div class="catalog-header">
        <div class="container">
            <h1 class="catalog-title text-center">Nuestro Catálogo de Productos</h1>
            <p class="lead text-center mb-4">Descubre nuestra amplia selección de productos de alta calidad</p>
            
            <div class="search-bar">
                <form method="GET" action="products.php">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" name="search" 
                               placeholder="Buscar productos..." value="<?= htmlspecialchars($search) ?>">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                        <button class="btn btn-light btn-lg" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Filtros y categorías -->
            <div class="col-lg-3">
                <div class="filter-card mb-4">
                    <h3 class="filter-title">Categorías</h3>
                    <ul class="category-list">
                        <li>
                            <a href="?category=all&search=<?= urlencode($search) ?>&sort=<?= $sort ?>" 
                               class="<?= $category === 'all' ? 'active' : '' ?>">
                                <i class="fas fa-list me-2"></i>Todas las categorías
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="?category=<?= urlencode($cat) ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>" 
                                   class="<?= $category === $cat ? 'active' : '' ?>">
                                    <i class="fas fa-folder me-2"></i><?= htmlspecialchars($cat) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="filter-card mb-4">
                    <h3 class="filter-title">Ordenar por</h3>
                    <form method="GET" id="sortForm">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                        <select class="sort-select" name="sort" onchange="document.getElementById('sortForm').submit()">
                            <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>Predeterminado</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Precio: Menor a Mayor</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Precio: Mayor a Menor</option>
                            <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Mejor Valorados</option>
                            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Nombre (A-Z)</option>
                        </select>
                    </form>
                </div>
                
                <div class="filter-card">
                    <h3 class="filter-title">Envío y Devoluciones</h3>
                    <div class="mb-3">
                        <i class="fas fa-shipping-fast text-primary me-2"></i> Envío gratis en pedidos > $200
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-undo text-primary me-2"></i> Devoluciones fáciles en 30 días
                    </div>
                    <div>
                        <i class="fas fa-shield-alt text-primary me-2"></i> Pago seguro
                    </div>
                </div>
            </div>
            
            <!-- Lista de productos -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <?php if ($category !== 'all'): ?>
                            <?= htmlspecialchars($category) ?>
                        <?php else: ?>
                            Todos los productos
                        <?php endif; ?>
                    </h3>
                    <div class="results-count">
                        <?= $total_products ?> productos encontrados
                    </div>
                </div>
                
                <?php if (!empty($paginated_products)): ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($paginated_products as $id => $product): ?>
                            <div class="col">
                                <form method="POST" class="product-form">
                                    <div class="product-card">
                                        <div class="position-relative">
                                            <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="product-img">
                                            <span class="badge bg-primary category-badge"><?= $product['category'] ?></span>
                                        </div>
                                        <div class="product-body">
                                            <h5 class="product-title"><?= $product['name'] ?></h5>
                                            <p class="product-description"><?= $product['description'] ?></p>
                                            
                                            <div class="product-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= floor($product['rating'])): ?>
                                                        <i class="fas fa-star"></i>
                                                    <?php elseif ($i == ceil($product['rating']) && !is_int($product['rating'])): ?>
                                                        <i class="fas fa-star-half-alt"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                                <span class="text-muted ms-2">(<?= $product['rating'] ?>)</span>
                                            </div>
                                            
                                            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                                            
                                            <input type="hidden" name="product_id" value="<?= $id ?>">
                                            <button type="submit" name="add_to_cart" class="btn-add-to-cart">
                                                <i class="fas fa-cart-plus me-2"></i>Añadir al carrito
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <?php if ($total_pages > 1): ?>
                        <nav>
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&sort=<?= $sort ?>&page=<?= $page-1 ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&sort=<?= $sort ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&sort=<?= $sort ?>&page=<?= $page+1 ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-catalog">
                        <div class="empty-catalog-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No se encontraron productos</h3>
                        <p class="text-muted">Intenta ajustar tus filtros de búsqueda</p>
                        <a href="products.php" class="btn btn-primary">
                            <i class="fas fa-undo me-2"></i>Ver todos los productos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alerta de producto añadido -->
    <?php if (isset($added_to_cart)): ?>
        <div class="alert alert-success alert-added" role="alert">
            <i class="fas fa-check-circle me-2"></i> ¡Producto añadido al carrito!
        </div>
    <?php endif; ?>

    <script>
        // Mostrar alerta de producto añadido
        <?php if (isset($added_to_cart)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const alert = document.querySelector('.alert-added');
                if (alert) {
                    alert.style.display = 'block';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 2800);
                }
            });
        <?php endif; ?>
        
        // Actualizar contador del carrito
        <?php if (isset($added_to_cart)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const cartBadge = document.querySelector('.badge-cart');
                if (cartBadge) {
                    const currentCount = parseInt(cartBadge.textContent) || 0;
                    cartBadge.textContent = currentCount + 1;
                }
            });
        <?php endif; ?>
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php
        include 'footer';
    ?>
</body>
</html>