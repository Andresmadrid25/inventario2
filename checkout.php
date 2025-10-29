<?php
session_start();
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: catalogo.php');
    exit;
}
$cart = $_SESSION['cart'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Checkout</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- jsPDF -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
  <h3>Confirmar compra</h3>
  <div class="card p-3 mb-3">
    <h5>Resumen</h5>
    <ul>
      <?php foreach($cart as $c): ?>
        <li><?=htmlspecialchars($c['nombre'])?> — Cantidad: <?=$c['qty']?></li>
      <?php endforeach;?>
    </ul>
  </div>
  <div class="mb-3">
    <label>Nombre de quien compra</label>
    <input id="buyer" class="form-control" placeholder="Tu nombre">
  </div>
  <button id="confirmBtn" class="btn btn-primary">Confirmar y descargar recibo (PDF)</button>
  <a href="catalogo.php" class="btn btn-secondary">Volver</a>
</div>

<script>
const { jsPDF } = window.jspdf;
document.getElementById('confirmBtn').addEventListener('click', () => {
  const buyer = document.getElementById('buyer').value || 'Cliente';
  const doc = new jsPDF();
  doc.setFontSize(16);
  doc.text('Recibo de compra', 14, 20);
  doc.setFontSize(11);
  doc.text('Comprador: ' + buyer, 14, 30);
  doc.text('Fecha: ' + new Date().toLocaleString(), 14, 38);
  doc.text('-- Productos --', 14, 46);
  let y = 54;
  <?php foreach($cart as $c): ?>
    doc.text('<?= addslashes(htmlspecialchars($c['nombre'])) ?> x <?= $c['qty'] ?>', 14, y);
    y += 8;
  <?php endforeach; ?>
  doc.text('Gracias por su compra!', 14, y+8);
  doc.save('recibo_compra_'+Date.now()+'.pdf');

  // limpiar carrito en servidor con fetch
  fetch('checkout.php?clear=1').then(()=>{ window.location.href='catalogo.php'; });
});
</script>
<link rel="stylesheet" href="style.css">
</html>

<?php
// limpiar carrito si llega clear
if (isset($_GET['clear']) && $_GET['clear'] == '1') {
    unset($_SESSION['cart']);
    // devolver una respuesta vacía
    exit;
}
?>
