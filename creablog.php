<?php
session_start();
include 'connessione.php';
if (!isset($_SESSION['user'])){
  header('Location:login.html.php');
  exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
  header("Location:errorpage.html.php");
  exit();
}  
$Titolo=$_POST["titolo"];
$DescBlog=$_POST["desc"];
$Categoria=$_POST["categoria"];
$Cogestore=$_POST["IDutente"];


$target_directory="cartella_foto/";
$target_file = basename($_FILES["immagine"]["name"]); 
$target_salvataggio=$target_directory . $target_file;                                     //con target dir uniamo il nome del file all'altro pezzo di percorso in modo da 
$uploadOk = 1;                                                                            //ottenere il path completo all'immagine inserita da inserire nel database
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));                   //(cartella_foto/nomefoto) cosi da sapere la directory in cui sarà salvata la foto


if ($target_salvataggio!="cartella_foto/"){
  $check = getimagesize($_FILES["immagine"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }

if ($_FILES["immagine"]["size"] > 2097152) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}
if (file_exists($target_salvataggio)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";


} else {
move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_salvataggio);
}
} 
else{
  $target_file=NULL;
}


$stmt = $conn->prepare("INSERT INTO blog (Titolo, DescBlog, FotoBlog, IdAutore, IdCat) VALUES ( ?, ?, ?, ?, ?)");  //insert per inserire i dati del blog nel db
$stmt->bind_param("sssii", $Titolo, $DescBlog, $target_file, $_SESSION['user'], $Categoria);
$stmt->execute();
$stmt->close();


if ($Cogestore!=""){
$lastInsertedId = mysqli_insert_id($conn);

$stmt2 = $conn->prepare("INSERT INTO cogestori (IDcoautore, IDblog) VALUES ( ?, ?)");                              //inseriamo, in caso di coautore, l'utente nell'apposita tabella
$stmt2->bind_param("ii", $Cogestore, $lastInsertedId );
$stmt2->execute();
$stmt2->close();
}

header('Location:index.html.php');
?>


