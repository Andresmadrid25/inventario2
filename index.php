<?php
session_start();
require 'conexion.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE cedula = ?");
    $stmt->execute([$cedula]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user'] = ['cedula'=>$user['cedula'], 'nombre'=>$user['nombre']];
        header('Location: dashboard.php');
        exit;
    } else {
        $err = "Cédula o contraseña incorrecta.";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - Inventario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets.css">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="card-title mb-3">Iniciar sesión</h4>
          <?php if($err): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($err)?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Cédula</label>
              <input class="form-control" name="cedula" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input class="form-control" type="password" name="password" required>
            </div>
            <button class="btn btn-primary w-100">Entrar</button>
          </form>
        </div>
      </div>
      <p class="text-muted mt-2 small">Admin: cédula 1111 / contraseña 1234</p>
    </div>
  </div>
</div>
</body>
</html>
