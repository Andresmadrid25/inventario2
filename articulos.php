<?php
session_start();
require 'conexion.php';
if (!isset($_SESSION['user'])) header('Location: index.php');

$action = $_GET['action'] ?? '';
// Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'];
    $unidades = (int)$_POST['unidades'];
    $tipo = $_POST['tipo'];
    $bodega = $_POST['bodega'];

    // manejar imagen subida
    $imagen = null;
    if (!empty($_FILES['imagen']['name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre_f = 'img_'.time().'.'.$ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__.'/images/'.$nombre_f);
        $imagen = 'images/'.$nombre_f;
    }

    if ($id) {
        // update
        if ($imagen) {
            $stmt = $pdo->prepare("UPDATE articulos SET nombre=?, unidades=?, tipo=?, bodega=?, imagen=? WHERE id=?");
            $stmt->execute([$nombre,$unidades,$tipo,$bodega,$imagen,$id]);
        } else {
            $stmt = $pdo->prepare("UPDATE articulos SET nombre=?, unidades=?, tipo=?, bodega=? WHERE id=?");
            $stmt->execute([$nombre,$unidades,$tipo,$bodega,$id]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO articulos (nombre, unidades, tipo, bodega, imagen) VALUES (?,?,?,?,?)");
        $stmt->execute([$nombre,$unidades,$tipo,$bodega,$imagen]);
    }
    header('Location: articulos.php');
    exit;
}

// delete
if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM articulos WHERE id=?");
    $stmt->execute([$_GET['id']]);
    header('Location: articulos.php');
    exit;
}

// fetch for edit
$edit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM articulos WHERE id=?");
    $stmt->execute([$_GET['id']]);
    $edit = $stmt->fetch();
}

// list all
$stmt = $pdo->query("SELECT * FROM articulos ORDER BY id DESC");
$articulos = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Artículos - Inventario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Volver</a>
    <div><a href="catalogo.php" class="btn btn-success btn-sm">Ver Catálogo</a></div>
  </div>
</nav>

<div class="container py-4">
  <div class="row">
    <div class="col-md-5">
      <div class="card mb-3">
        <div class="card-body">
          <h5><?= $edit ? "Editar artículo" : "Agregar artículo" ?></h5>
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
            <div class="mb-2">
              <label>Nombre</label>
              <input class="form-control" name="nombre" required value="<?= $edit['nombre'] ?? '' ?>">
            </div>
            <div class="mb-2">
              <label>Unidades</label>
              <input type="number" class="form-control" name="unidades" required value="<?= $edit['unidades'] ?? 0 ?>">
            </div>
            <div class="mb-2">
              <label>Tipo</label>
              <select class="form-control" name="tipo" required>
                <?php $tipos=['PC','teclado','disco duro','mouse']; foreach($tipos as $t): ?>
                  <option value="<?=$t?>" <?= (isset($edit['tipo']) && $edit['tipo']==$t)?'selected':'' ?>><?=$t?></option>
                <?php endforeach;?>
              </select>
            </div>
            <div class="mb-2">
              <label>Bodega</label>
              <select class="form-control" name="bodega" required>
                <?php $bodegas=['norte','sur','oriente','occidente']; foreach($bodegas as $b): ?>
                  <option value="<?=$b?>" <?= (isset($edit['bodega']) && $edit['bodega']==$b)?'selected':'' ?>><?=$b?></option>
                <?php endforeach;?>
              </select>
            </div>
            <div class="mb-2">
              <label>Imagen (opcional)</label>
              <input type="file" name="imagen" accept="image/*" class="form-control">
            </div>
            <button class="btn btn-primary"><?= $edit ? "Actualizar" : "Agregar" ?></button>
            <?php if($edit): ?>
              <a href="articulos.php" class="btn btn-secondary">Cancelar</a>
            <?php endif;?>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      <h5>Lista de artículos</h5>
      <table class="table table-striped">
        <thead><tr><th>ID</th><th>Nombre</th><th>Unid.</th><th>Tipo</th><th>Bodega</th><th>Imagen</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach($articulos as $a): ?>
            <tr>
              <td><?=$a['id']?></td>
              <td><?=htmlspecialchars($a['nombre'])?></td>
              <td><?=$a['unidades']?></td>
              <td><?=$a['tipo']?></td>
              <td><?=$a['bodega']?></td>
              <td>
                <?php if($a['imagen']): ?>
                  <img src="<?=htmlspecialchars($a['imagen'])?>" style="height:40px;">
                <?php endif; ?>
              </td>
              <td>
                <a class="btn btn-sm btn-info" href="articulos.php?action=edit&id=<?=$a['id']?>">Editar</a>
                <a class="btn btn-sm btn-danger" href="articulos.php?action=delete&id=<?=$a['id']?>"
                   onclick="return confirm('Eliminar artículo?')">Eliminar</a>
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
