<?php
session_start();
include 'connessione.php';
if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
    header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;     
    }else{
    $delete_utente="DELETE FROM utenti WHERE IDutente= ?";
    $stmt_delete_utente=$conn->prepare($delete_utente);                  // prende l'id del profilo, lo elimina e distrugge la sessione
    $stmt_delete_utente->bind_param("i", $_SESSION['user'] );
    $stmt_delete_utente->execute();
    $stmt_delete_utente->close();
    header('Location:login.html.php');
    session_unset();
    session_destroy(); 
    exit();
    }
?>