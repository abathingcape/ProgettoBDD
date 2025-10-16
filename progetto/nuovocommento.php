<?php
session_start();
include 'connessione.php';
$commento=$_POST["nuovocommento"];
$id_post=$_POST["id_post"];
if ($commento==""){                                                                                           //controlla che il commento inserito abbia del contenuto e meno di 70 caratteri
    echo "Non puoi pubblicare un commento vuoto";
}else if (strlen($commento)>70){
    echo "Il commento può avere massimo 70 caratteri";
}else{
    $sql_insert_commento = "INSERT INTO commento (TestoComm, IdPostCommento, IdUtCommento) VALUES ( ?, ?, ?)";            //inseriamo il commento all'inerno del database  
    $stmt_insert_commento = $conn->prepare($sql_insert_commento);                           
    $stmt_insert_commento->bind_param("sii", $commento, $id_post, $_SESSION["user"]);
    $stmt_insert_commento->execute();
    $stmt_insert_commento->close();

    $select_commenti="SELECT c.IdPostCommento AS idpost, c.TestoComm AS testo, c.Data AS datapost,                          /*cerchiamo l'ultimo commento inserito e lo visualizziamo nella stampa successiva */
                            u.Username AS username, u.IDutente AS Idutente, c.IdCommento AS idcommento
                        FROM commento as c
                        JOIN utenti as u ON c.IdUtCommento = u.IDutente
                        WHERE c.IdPostCommento = ?
                        ORDER BY datapost DESC LIMIT 1";
      $stmt_commenti=$conn->prepare($select_commenti);
      $stmt_commenti->bind_param("i",$id_post);
      $stmt_commenti->execute();
      $resultQuery2=  $stmt_commenti->get_result();
      $stmt_commenti -> close();

      $row=$resultQuery2->fetch_assoc();
    
        echo '
                        <div class="utentecommento">
                        <i class="fa-solid fa-user" style="color: black; font-size: 16px"></i>
                        <a href="profilo.html.php?id=' . $row["Idutente"] . '"> ' . $row["username"] . '</a>
                        </div>
                        <div class="testodata">
                        <p class="testo"> ' . $row["testo"] . '<p>';
                        
                        echo '<p class="data"> ' . $row["datapost"] . '<p>';

                        if ($row["Idutente"]==$_SESSION["user"]){                                                   //permette all'utente di quella sessione di eliminare il proprio commento 
                            echo '<form action="eliminacommento.php" method="post">
                            <input type="hidden" name="id_post" value="' .$id_post . '">
                            <input type="hidden" name="id_commento" value="' .$row["idcommento"] . '">
                            <input class="eliminacommentotasto" type="submit" value="">';
                            }
                        
                        echo '</div>';
}
?>