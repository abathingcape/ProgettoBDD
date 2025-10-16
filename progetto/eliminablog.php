<?php
  session_start();
  include 'connessione.php';
  if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location:errorpage.html.php");
    exit();
  }  
    if ((!isset($_SESSION['user']))) {                       //facciamo un controllo di sessione, controlliamo effettivamente che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia stata inizializzata
          header('Location:index.html.php');                                                             
      exit;                                                                                      
    }else{
          $id_blog=$_POST["id_blog"];                                                                //prendiamo l'id del blog e lo eliminiamo
          $delete_blog="DELETE FROM blog WHERE IdBlog= ?";
          $stmt_delete_blog=$conn->prepare($delete_blog);
          $stmt_delete_blog->bind_param("i", $id_blog);
          $stmt_delete_blog->execute();
          $stmt_delete_blog->close();
          header('location:index.html.php');
      }
