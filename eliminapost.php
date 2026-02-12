<?php
session_start();
include 'connessione.php';
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location:errorpage.html.php");
    exit();
  }  
if ((!isset($_SESSION['user']))) {                       //facciamo un controllo di sessione, controlliamo effettivamente 
    header('Location:index.html.php');                                                             //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
    exit;                                                                                      //stata inizializzata
    }else{
        $id_post=$_POST["id_post"];
    $delete_post="DELETE FROM post WHERE idPost= ?";
    $stmt_delete_post=$conn->prepare($delete_post);                                             //prendiamo l'id del post e lo eliminiamo
    $stmt_delete_post->bind_param("i", $id_post );
    $stmt_delete_post->execute();
    $stmt_delete_post->close();
    header('Location:profilo.html.php?id=' . $SESSION["user"]. '');
    }

?>