<?php
session_start();
include 'connessione.php';
if ((isset($_GET["id_ut"])) and $_GET["id_ut"]==$_SESSION["user"]){
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:index.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		}else{
            $titolo=$_POST["Titolo"];
            $descrizione=$_POST["DescPost"];

            $id_p=$_GET['id_post'];

            $target_directory="cartella_foto/";
            $target_file1 = basename($_FILES["FotoPost1"]["name"]);                                     //target_directory conterrà la cartella dove verrà inserita la foto
            $target_salvataggio1=$target_directory . $target_file1;                                     //target_salvataggio il path completo
            $target_file2 = basename($_FILES["FotoPost2"]["name"]); 
            $target_salvataggio2=$target_directory . $target_file2;

            if ($target_salvataggio1=="cartella_foto/" and $target_salvataggio2=="cartella_foto/"){                             //con questo if controlleremo se è stato selezionato un file da inserire nel blog, in caso sia stato inserito si sostituirà a quello già presente
                $select_post = "UPDATE post SET TitoloPost = ?, DescPost = ? WHERE IdPost = ?";                                 //in caso contrario non verrà sostituito
                $stmt_post=$conn->prepare($select_post);                                                                        //in caso sia stato selezionato un cogestore verrà aggiunto 
                $stmt_post->bind_param("ssi",$titolo, $descrizione, $id_p);
                $stmt_post->execute();
                $stmt_post -> close();
                header('Location:index.html.php');
                

            }else if ($target_salvataggio2=="cartella_foto/" or file_exists($target_salvataggio2)){
                move_uploaded_file($_FILES["FotoPost1"]["tmp_name"], $target_salvataggio1);
                $target_file2=NULL;
                $select_post = "UPDATE post SET TitoloPost = ?, FotoPost1 = ?, DescPost = ? WHERE IdPost = ?";
                $stmt_post=$conn->prepare($select_post);
                $stmt_post->bind_param("sssi",$titolo, $target_file1, $descrizione, $id_p);
                $stmt_post->execute();
                $stmt_post -> close();
                header('Location:index.html.php');
                
            }else if ($target_salvataggio1=="cartella_foto/" or file_exists($target_salvataggio1)){                                                                  
                move_uploaded_file($_FILES["FotoPost2"]["tmp_name"], $target_salvataggio2);
                $target_file1=NULL;
                $select_post = "UPDATE post SET TitoloPost = ?, FotoPost2 = ?, DescPost = ? WHERE IdPost = ?";
                $stmt_post=$conn->prepare($select_post);
                $stmt_post->bind_param("sssi",$titolo, $target_file2, $descrizione, $id_p);
                $stmt_post->execute();
                $stmt_post -> close();
                header('Location:index.html.php');
            }else {
                move_uploaded_file($_FILES["FotoPost1"]["tmp_name"], $target_salvataggio1);                                             
                move_uploaded_file($_FILES["FotoPost2"]["tmp_name"], $target_salvataggio2);
                $select_post = "UPDATE post SET TitoloPost = ?, FotoPost1 = ?, FotoPost2 = ?, DescPost = ? WHERE IdPost = ?";
                $stmt_post=$conn->prepare($select_post);
                $stmt_post->bind_param("ssssi",$titolo, $target_file1, $target_file2, $descrizione, $id_p);
                $stmt_post->execute();
                $stmt_post -> close();
                header('Location:index.html.php');
            }
        }
}else header('Location:index.html.php');

?>