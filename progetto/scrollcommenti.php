<?php 
  session_start();
  include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		}
$id_post=$_GET["id_post"];
$indice=$_GET["indice"];
$select_commenti="SELECT c.IdPostCommento AS idpost, c.TestoComm AS testo, c.Data AS datapost,                /*seleziona i commenti di ogni post */
                        u.Username AS username, u.IDutente AS Idutente, c.IdCommento AS idcommento
                    FROM commento as c
                    JOIN utenti as u ON c.IdUtCommento = u.IDutente
                    WHERE c.IdPostCommento = ?
                    ORDER BY datapost DESC LIMIT 2 OFFSET ?";                                                 /*ne aggiunge due alla volta */
$stmt_commenti=$conn->prepare($select_commenti);
$stmt_commenti->bind_param("ii",$id_post, $indice);
$stmt_commenti->execute();
$resultQuery2=  $stmt_commenti->get_result();
$stmt_commenti -> close();  
while ($row = $resultQuery2 -> fetch_assoc()){                                                                  //stampa tutti i commenti
    echo '
          <div class="utentecommento">
            <i class="fa-solid fa-user" style="color: black; font-size: 16px"></i>
            <a href="profilo.html.php?id=' . $row["Idutente"] . '"> ' . $row["username"] . '</a>
          </div>
          <div class="testodata">
            <p class="testo"> ' . $row["testo"] . '<p>';
          
            echo '<p class="data"> ' . $row["datapost"] . '<p>';

            if ($row["Idutente"]==$_SESSION["user"]){                                                         //se l'utente è lo stesso della sessione, appare il cestino per l'eliminazione
              echo '<button onclick=window.location.href="eliminacommento.php?id_commento=' . $row["idcommento"] . '&id_ut=' . $row["Idutente"] . '&id_post=' . $_GET["id_post"] . '" style="background-color: #fff; border: none;"><i class="fa-solid fa-trash"></i></button>';
              }
            
          echo '</div>';
          
  }
?>