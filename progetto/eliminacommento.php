<?php
  session_start();
  include 'connessione.php';
	if ((!isset($_SESSION['user']))) {                       //facciamo un controllo di sessione, controlliamo effettivamente 
        header('Location:index.html.php');                                                             //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                                                      //stata inizializzata
	}else{
        $id_commento=$_POST["id_commento"];
        $id_post=$_POST["id_post"];                                                        // prendiamo l'id del commento e lo eliminiamo
        $delete_commento="DELETE FROM commento WHERE IdCommento= ?";
        $stmt_delete_commento=$conn->prepare($delete_commento);
        $stmt_delete_commento->bind_param("i", $id_commento);
        $stmt_delete_commento->execute();
        $stmt_delete_commento->close();
        header("Location:post.html.php?id_post=". $id_post."");
        
    }