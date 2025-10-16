<?php
  session_start();
  include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                                //facciamo un controllo di sessione, controlliamo effettivamente 
    header('Location:login.html.php');                                            //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                                         //stata inizializzata
	}else{
      $id_utente=$_SESSION["user"];                                                        //prendiamo l'utente con la post di modo che nessuno possa effettuare l'azione se non l'utente designato
      $nomecognome=$_POST["nomecognome"];
      $numerocarta=$_POST["numerocarta"];
      $scadenza=$_POST["scadenza"];
      $cvv=$_POST["cvv"];

      if (!isset($_POST['nomecognome'])or !isset($_POST['numerocarta'])or !isset($_POST['scadenza']) or !isset($_SESSION['cvv'])){
        header('location:errorpage.html.php');
      }

      $timestamp_corrente = date('Y-m-d');                                        // Genera il timestamp corrente in formato MySQL
      $select_utente_abb = "UPDATE utenti SET InizioAbb = ? WHERE IDutente = ?";
      $stmt_utente_abb=$conn->prepare($select_utente_abb);
      $stmt_utente_abb->bind_param("si",$timestamp_corrente,$id_ut);              //update con data corrente e l'id dell'utente
      $stmt_utente_abb->execute();
      $stmt_utente_abb -> close();
      $_SESSION["premium"]=true;                                                  //inizio la variabile di sessione premium a true
      echo "OK";
    }