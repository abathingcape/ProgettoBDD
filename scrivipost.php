<?php
session_start();
include 'connessione.php';
if (!isset($_SESSION['user'])){
    header('Location:login.html.php');
    exit;
}

$Titolo=$_POST["titolo"];
$DescTesto=$_POST["testo"];
$Blog=$_POST["blog"];


$target_directory="cartella_foto/";
$target_file1 = basename($_FILES["immagine1"]["name"]);                                                 
$target_salvataggio1=$target_directory . $target_file1;
if ($target_salvataggio1=="cartella_foto/" or file_exists($target_salvataggio1)){
    $target_file1=NULL;
}else {
    move_uploaded_file($_FILES["immagine1"]["tmp_name"], $target_salvataggio1);                                                 //salva la foto nel path scelto
}

$target_file2 = basename($_FILES["immagine2"]["name"]); 
$target_salvataggio2=$target_directory . $target_file2;
if ($target_salvataggio2=="cartella_foto/" or file_exists($target_salvataggio2)){
    $target_file2=NULL;
}else {
    move_uploaded_file($_FILES["immagine2"]["tmp_name"], $target_salvataggio2);
}


$stmt = $conn->prepare("INSERT INTO post (TitoloPost, DescPost, FotoPost1, FotoPost2, IdUt, IdBl) VALUES ( ?, ?, ?, ?, ?, ?)");                         //inserisce il post nell database
$stmt->bind_param("ssssii", $Titolo, $DescTesto, $target_file1, $target_file2, $_SESSION['user'], $Blog);   
$stmt->execute();
$stmt->close();

header('Location:index.html.php');
?>