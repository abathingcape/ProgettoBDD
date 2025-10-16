<?php
session_start();
include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		}
        $id_post=$_GET["id_post"];
        $delete_post_like ="DELETE FROM valutazioni WHERE IdVotoPost = ? AND IDutVoto = ?";
        $stmt_post_nonlike = $conn->prepare($delete_post_like);                                                                 //inserirà nella tabella valutazioni la coppia idutente e idpost
        $stmt_post_nonlike->bind_param("ii", $id_post, $_SESSION["user"]);
        $stmt_post_nonlike->execute();
        $stmt_post_nonlike->close();

        $count_contalike = "SELECT COUNT(IdVotoPost) AS NumeroLike FROM Valutazioni WHERE IdVotoPost= ?";
        $stmt_contalike = $conn->prepare($count_contalike);                                                                     //ci restituirà il numero di likle per il post aggiornato
        $stmt_contalike->bind_param("i",$id_post);
        $stmt_contalike->execute(); 
        $resultQuery4=$stmt_contalike->get_result();
        $stmt_contalike->close();
        $contalike=$resultQuery4->fetch_assoc();

        $update_like="UPDATE post SET mi_piace = ? WHERE IdPost = ?";
        $stmt_update_like = $conn->prepare($update_like);                                                                       //sovrascriverà con il nuovo numero di like ricevuti dal post
        $stmt_update_like->bind_param("ii",$contalike["NumeroLike"],$id_post);
        $stmt_update_like->execute();
        $stmt_update_like->close();

        echo '<i id ="like" class="fa-regular fa-thumbs-up"></i>
        <h2>' . $contalike["NumeroLike"] . '</h2>';                                                                             //mostrerà nella pagina post.html.php l'icona del pollice piena e il numero di like attuali del post


?>