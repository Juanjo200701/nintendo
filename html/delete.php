<?php
session_start();
require 'database.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'] ?? 0;
if ($id) {
    $stmt = $conexion->prepare("DELETE FROM games WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
header("Location: dashboard.php");
exit();
?>