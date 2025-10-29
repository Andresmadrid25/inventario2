<?php
session_start();
if (!isset($_SESSION['user'])) header('Location: index.php');
$cedula = $_SESSION['user']['cedula'];
$nombre = $_SESSION['user']['nombre'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - Inventario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">Inventario</a>
    <div class="ms-auto">
      <span class="me-3">Hola, <?=htmlspecialchars($nombre)?></span>
      <a class="btn btn-outline-secondary btn-sm" href="logout.php">Salir</a>
    </div>
  </div>
</nav>

<div class="container py-5">
  <?php if ($cedula === '1111'): ?>
    <div class="row">
      <div class="col-md-6">
        <a href="usuarios.php" class="btn btn-primary w-100 mb-3">Gestión de Usuarios</a>
      </div>
      <div class="col-md-6">
        <a href="articulos.php" class="btn btn-danger w-100 mb-3">Gestión de Artículos</a>
      </div>
    </div>
  <?php else: ?>
    <div class="row">
      <div class="col-md-12">
        <a href="articulos.php" class="btn btn-danger w-100 mb-3">Gestión de Artículos.</a>
      </div>
    </div>
  <?php endif; ?>
  <a href="catalogo.php" class="btn btn-success">Ir al Catálogo público.</a>
</div>
</body>
</html>
