<?php
session_start();
require 'conexion.php';
header('Content-Type: application/json');

$raw = file_get_contents('php://input');
if ($raw) {
    $data = json_decode($raw, true);
    $action = $data['action'] ?? '';
    if ($action === 'add') {
        $id = $data['id'];
        $nombre = $data['nombre'];
        $qty = max(1, (int)$data['qty']);
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        // si ya existe incrementa
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = ['id'=>$id,'nombre'=>$nombre,'qty'=>$qty];
        }
        echo json_encode(['ok'=>true]); exit;
    }
    // otras acciones...
    echo json_encode(['ok'=>false,'msg'=>'Acción no reconocida']); exit;
}

// si no es POST JSON, mostramos la vista del carrito
?>
<?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
  <!doctype html><html><head><meta charset="utf-8"><title>Carrito</title></head><body>
  <div class="container py-4"><h3>Carrito vacío</h3><a href="catalogo.php">Ir al catálogo</a></div></body></html>
<?php else: ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Carrito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h3>Tu carrito</h3>
  <table class="table">
    <thead><tr><th>Producto</th><th>Cantidad</th></tr></thead>
    <tbody>
      <?php foreach($_SESSION['cart'] as $c): ?>
        <tr><td><?=htmlspecialchars($c['nombre'])?></td><td><?=$c['qty']?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a class="btn btn-success" href="checkout.php">Confirmar compra</a>
  <a class="btn btn-secondary" href="catalogo.php">Seguir comprando</a>
</div>
</body>
</html>
<?php endif; ?>
