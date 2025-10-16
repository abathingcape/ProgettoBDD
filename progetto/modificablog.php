<?php
session_start();
include 'connessione.php';
if ((isset($_GET["id_ut"])) and $_GET["id_ut"]==$_SESSION["user"]){
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:errorpage.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		}else{
$titolo=$_POST["Titolo"];
$descrizione=$_POST["DescBlog"];
$Cogestore =$_POST["IDutente"];

$id_blog=$_GET['id_blog'];

$target_directory="cartella_foto/";
$target_file = basename($_FILES["FotoBlog"]["name"]);                                                       //target_directory conterrà la cartella dove verrà inserita la foto
$target_salvataggio=$target_directory . $target_file;                                                       //target_salvataggio il path completo

if ($target_salvataggio=="cartella_foto/" or file_exists($target_salvataggio)){                                                                 //con questo if controlleremo se è stato selezionato un file da inserire nel blog, in caso sia stato inserito si sostituirà a quello già presente
    $select_blog = "UPDATE Blog SET Titolo = ?, DescBlog = ? WHERE IdBlog = ?";                             //in caso contrario non verrà sostituito
    $stmt_blog=$conn->prepare($select_blog);
    $stmt_blog->bind_param("ssi",$titolo, $descrizione, $id_blog);
    $stmt_blog->execute();
    $stmt_blog -> close();
    header('Location:index.html.php');

}else{
    move_uploaded_file($_FILES["FotoBlog"]["tmp_name"], $target_salvataggio);
    $select_blog = "UPDATE Blog SET Titolo = ?, DescBlog = ?, FotoBlog = ? WHERE IdBlog = ?";
    $stmt_blog=$conn->prepare($select_blog);
    $stmt_blog->bind_param("sssi",$titolo, $descrizione, $target_file, $id_blog);
    $stmt_blog->execute();
    $stmt_blog -> close();
    header('Location:index.html.php');
}
if ($Cogestore!=""){                                                                                        //in caso sia stato selezionato un cogestore verrà aggiunto 
    $stmt2 = $conn->prepare("INSERT INTO cogestori (IDcoautore, IDblog) VALUES ( ?, ?)");
    $stmt2->bind_param("ii", $Cogestore, $id_blog);
    $stmt2->execute();
    $stmt2->close();
    }        
}
}
else{
header('Location:index.html.php');}
?>