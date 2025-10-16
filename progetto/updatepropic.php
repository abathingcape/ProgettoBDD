<?php
session_start();
include 'connessione.php';

$target_directory="cartella_foto/";
$target_file = basename($_FILES["profileImage"]["name"]); 
$target_salvataggio=$target_directory . $target_file;
$sql_select_utente = "UPDATE utenti SET FotoP = ? WHERE IDutente = ? ";               //aggiorna la nuova immagine del profilo
$stmt_select_utente = $conn->prepare($sql_select_utente);                           
$stmt_select_utente->bind_param("si",$target_file , $_SESSION['user']);
$stmt_select_utente->execute();
$stmt_select_utente->close();

move_uploaded_file($_FILES["profileImage"]["tmp_name"], $target_salvataggio);         //salva il file nel path designato


echo $target_file;
?>