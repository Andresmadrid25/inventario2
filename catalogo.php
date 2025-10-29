<?php
session_start();
require 'conexion.php';

// Obtener productos desde la tabla articulos (tipo 'PC','teclado','disco duro','mouse')
// Para catálogo, filtrar solo tipos relevantes (o mostrar todos)
$stmt = $pdo->query("SELECT * FROM articulos ORDER BY id DESC");
$productos = $stmt->fetchAll();
<link rel="stylesheet" href="style.css">
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Catálogo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Volver</a>
    <div>
      <a class="btn btn-outline-primary" href="cart.php">Ver Carrito</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <h3>Catálogo</h3>
  <div class="row">
    <?php if (empty($productos)): ?>
      <div class="alert alert-info">No hay productos en el catálogo. Agrega desde gestión de artículos.</div>
    <?php endif;?>
    <?php foreach($productos as $p): ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
          <?php if($p['imagen']): ?>
            <img src="<?=htmlspecialchars($p['imagen'])?>" class="card-img-top" style="height:200px;object-fit:cover;">
          <?php else: ?>
            <img src="images/placeholder.png" class="card-img-top" style="height:200px;object-fit:cover;">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?=htmlspecialchars($p['nombre'])?></h5>
            <p class="card-text">Tipo: <?=$p['tipo']?> — Unidades: <?=$p['unidades']?></p>
            <div class="mt-auto">
              <input type="number" min="1" max="<?=max(1,$p['unidades'])?>" value="1" class="form-control mb-2 qty-<?=$p['id']?>">
              <button class="btn btn-success w-100" onclick="addToCart(<?=$p['id']?>,'<?=addslashes(htmlspecialchars($p['nombre']))?>',<?=$p['unidades']?>)">Agregar al carrito</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach;?>
  </div>
</div>

<script>
function addToCart(id, nombre, unidades) {
  const qty = document.querySelector('.qty-' + id).value;
  fetch('cart.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({action:'add', id:id, nombre:nombre, qty: qty})
  }).then(r=>r.json()).then(res=>{
    if (res.ok) {
      alert('Agregado al carrito');
    } else alert(res.msg || 'Error');
  });
}
</script>
</body>
</html>
