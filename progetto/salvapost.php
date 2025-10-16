<?php
 session_start();
 include 'connessione.php';
   if (!isset($_SESSION['user']) or $_SESSION["premium"]==false) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
       header('Location:login.html.php');                                                               //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
       exit;      
   }else{
    $id_post=$_GET["id_post"];
    $insert_post_salvati ="INSERT INTO salvati (IDutSalva, IDpostSalvato) VALUES ( ?, ?)";               //inserisce il post tra i post salvati
    $stmt_post_salvati = $conn->prepare($insert_post_salvati);
    $stmt_post_salvati->bind_param("ii", $_SESSION["user"], $id_post);
    $stmt_post_salvati->execute();
    $stmt_post_salvati->close();
    echo '<i id="salvato" class="fa-solid fa-bookmark"></i>';                                             //cambia l'icona "salvato" da vuota a piena
   }
?>