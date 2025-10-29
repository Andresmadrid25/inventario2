<?php
session_start();
require 'conexion.php';
if (!isset($_SESSION['user'])) header('Location: index.php');
if ($_SESSION['user']['cedula'] !== '1111') {
    die('Acceso denegado. Sólo admin.');
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $pwd = $_POST['password'] ?? null;
    $idOld = $_POST['idOld'] ?? null;

    if ($idOld) {
        if ($pwd) {
            $hash = password_hash($pwd, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET cedula=?, nombre=?, password=? WHERE cedula=?");
            $stmt->execute([$cedula,$nombre,$hash,$idOld]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET cedula=?, nombre=? WHERE cedula=?");
            $stmt->execute([$cedula,$nombre,$idOld]);
        }
    } else {
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (cedula, nombre, password) VALUES (?,?,?)");
        $stmt->execute([$cedula,$nombre,$hash]);
    }
    header('Location: usuarios.php'); exit;
}

if ($action === 'delete' && isset($_GET['cedula'])) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE cedula=?");
    $stmt->execute([$_GET['cedula']]);
    header('Location: usuarios.php'); exit;
}

$edit = null;
if ($action === 'edit' && isset($_GET['cedula'])) {
    $stmt = $pdo->prepare("SELECT cedula,nombre FROM usuarios WHERE cedula=?");
    $stmt->execute([$_GET['cedula']]);
    $edit = $stmt->fetch();
}

$stmt = $pdo->query("SELECT cedula,nombre FROM usuarios ORDER BY creado_en DESC");
$usuarios = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Usuarios - Inventario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Volver</a>
  </div>
</nav>

<div class="container py-4">
  <div class="row">
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5><?= $edit ? "Editar usuario" : "Agregar usuario" ?></h5>
          <form method="post">
            <input type="hidden" name="idOld" value="<?= $edit['cedula'] ?? '' ?>">
            <div class="mb-2">
              <label>Cédula</label>
              <input name="cedula" class="form-control" required value="<?= $edit['cedula'] ?? '' ?>">
            </div>
            <div class="mb-2">
              <label>Nombre</label>
              <input name="nombre" class="form-control" required value="<?= $edit['nombre'] ?? '' ?>">
            </div>
            <div class="mb-2">
              <label>Contraseña (si dejas en blanco no cambia)</label>
              <input type="password" name="password" class="form-control" <?= $edit ? '' : 'required' ?>>
            </div>
            <button class="btn btn-primary"><?= $edit ? "Actualizar" : "Agregar" ?></button>
            <?php if($edit): ?><a href="usuarios.php" class="btn btn-secondary">Cancelar</a><?php endif;?>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <h5>Usuarios</h5>
      <table class="table table-striped">
        <thead><tr><th>Cédula</th><th>Nombre</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach($usuarios as $u): ?>
            <tr>
              <td><?=$u['cedula']?></td>
              <td><?=htmlspecialchars($u['nombre'])?></td>
              <td>
                <a class="btn btn-sm btn-info" href="usuarios.php?action=edit&cedula=<?=$u['cedula']?>">Editar</a>
                <?php if($u['cedula'] !== '1111'): ?>
                  <a class="btn btn-sm btn-danger" href="usuarios.php?action=delete&cedula=<?=$u['cedula']?>"
                     onclick="return confirm('Eliminar usuario?')">Eliminar</a>
                <?php endif;?>
              </td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
