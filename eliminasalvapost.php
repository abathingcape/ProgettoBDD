<?php
 session_start();
 include 'connessione.php';
   if (!isset($_SESSION['user']) or $_SESSION["premium"]==false) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
       header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
       exit;      
   }else{
    $id_post=$_GET["id_post"];

    $select_postsalvati = "SELECT *
                            FROM salvati
                            WHERE IDutSalva = ? AND IDpostSalvato = ? ";         
    $stmt_select_postsalvati = $conn->prepare($select_postsalvati);
    $stmt_select_postsalvati -> bind_param ("ii", $_SESSION['user'], $id_post);
    $stmt_select_postsalvati->execute();
    $resultQuery=$stmt_select_postsalvati->get_result();
    $stmt_select_postsalvati->close();

    if ($resultQuery->num_rows == 0) {
      header("location:errorpage.html.php");
      exit();
    }

    $delete_postsalvato="DELETE FROM salvati WHERE IDutSalva= ? AND IDpostSalvato = ?";
    $stmt_delete_postsalvato=$conn->prepare($delete_postsalvato);                                         // prende l'id del post salvato e lo rimuove dai post salvati
    $stmt_delete_postsalvato->bind_param("ii",$_SESSION["user"],$id_post);
    $stmt_delete_postsalvato->execute();
    $stmt_delete_postsalvato->close();
    echo '<i id="salva" class="fa-regular fa-bookmark"></i>';                                           // stampa nella pagina html l'icona del post salvabile (vuota)
   }
?>