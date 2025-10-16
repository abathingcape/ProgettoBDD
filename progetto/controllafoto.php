<?php
    session_start();
	  if (!isset($_SESSION['user'])){
		  header('Location:login.html.php');
		  exit;
    }

  $target_directory="cartella_foto/";
  $target_file = basename($_FILES["file"]["name"]); 
  $target_salvataggio=$target_directory . $target_file; 
  if (file_exists($target_salvataggio)) {
    echo "Il file esiste già.";
    exit();
    
  }else{ echo "OK";
    exit();}
?>
