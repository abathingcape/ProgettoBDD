<?php
session_start();
if (!isset($_SESSION['user'])){
    header('Location:login.html.php');
    exit;
}
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location:errorpage.html.php");
    exit();
  }               
include 'connessione.php';
$Nome=$_POST["Nome"];
$DescCat=$_POST["Desc"];
$catPadre=$_POST["sottocategoria"];


if ($catPadre==""){                                                                                      //l'if controlla se il campo categoriapadre esiste e in caso non esista la categoria è una padre
$stmt = $conn->prepare("INSERT INTO categoria (NomeCategoria, DescCategoria) VALUES ( ?, ?)");           //query che inserisce la categoria padre
$stmt->bind_param("ss", $Nome, $DescCat);
$stmt->execute();
$stmt->close();
}else {                                                                                                 //in caso esista il campo categoriapadre è una sottocategoria
    $stmt = $conn->prepare("INSERT INTO categoria (NomeCategoria, DescCategoria, categoriaPadre) VALUES ( ?, ?, ?)");  //query che inserisce la sottocategoria
    $stmt->bind_param("ssi", $Nome, $DescCat, $catPadre);
    $stmt->execute();
    $stmt->close();
}
header('Location:index.html.php');
?>
