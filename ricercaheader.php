<?php
session_start();
include 'connessione.php';
$valorericerca = "%" . $_POST["query"] . "%";

$sql_select_blog = "SELECT Titolo, IdBlog FROM blog WHERE Titolo LIKE ?";                                                   //cerca il titolo dei blog all'inetrno della piattaforma          
$sql_select_blog = $conn->prepare($sql_select_blog);
$sql_select_blog->bind_param("s", $valorericerca);
$sql_select_blog->execute();
$resultQuery1=$sql_select_blog->get_result();
$sql_select_blog->close();

if ($resultQuery1->num_rows>0){                                                                                             //se i blog esistono li farà comparire nella lista dei rusultati
echo "<li class='topicricerca'> Blog</li>";
while($row=$resultQuery1->fetch_assoc()){
    echo "<li class='risultato'><a class='linkricerca' href='blog.html.php?id_blog=" . $row["IdBlog"] . "'>" . $row["Titolo"] . "</a></li>";

}
}
$sql_select_utenti = "SELECT Username, IDutente FROM utenti WHERE Username LIKE ?";                                         //cerca il nome degli utenti e li stampa nella lista       
$stmt_select_utenti = $conn->prepare($sql_select_utenti);   
$stmt_select_utenti->bind_param("s", $valorericerca);
$stmt_select_utenti->execute();
$resultQuery2=$stmt_select_utenti->get_result();
$stmt_select_utenti->close();

if ($resultQuery2->num_rows>0){
echo "<li class='topicricerca'> Utenti</li>";
while($row=$resultQuery2->fetch_assoc()){
    
    echo "<li class='risultato'><a class='linkricerca' href='profilo.html.php?id=" . $row["IDutente"] . "'>" . $row["Username"] . "</a></li>";

}
}
$sql_select_post = "SELECT TitoloPost, IdPost FROM post WHERE TitoloPost LIKE ?";                                           //cerca il titolo dei post e lo stampa nella lista della ricerca          
$stmt_select_post = $conn->prepare($sql_select_post);
$stmt_select_post->bind_param("s", $valorericerca);
$stmt_select_post->execute();
$resultQuery3=$stmt_select_post->get_result();
$stmt_select_post->close();

if ($resultQuery3->num_rows>0){
echo "<li class='topicricerca'> Post</li>";
while($row=$resultQuery3->fetch_assoc()){
    
    echo "<li class='risultato'><a class='linkricerca' href='post.html.php?id_post=" . $row["IdPost"] . "'>" . $row["TitoloPost"] . "</a></li>";

}
}
$sql_select_categorie = "SELECT NomeCategoria, IDcategoria FROM categoria WHERE NomeCategoria LIKE ?";                         //cerca il nome della categoria e lo stampa nella ricerca            
$stmt_select_categorie = $conn->prepare($sql_select_categorie);
$stmt_select_categorie->bind_param("s", $valorericerca);
$stmt_select_categorie->execute();
$resultQuery3=$stmt_select_categorie->get_result();
$stmt_select_categorie->close();

if ($resultQuery3->num_rows>0){
echo "<li class='topicricerca'> categorie</li>";
while($row=$resultQuery3->fetch_assoc()){
    
    echo "<li class='risultato'><a class='linkricerca' href='categoria.html.php?id_cat=" . $row["IDcategoria"] . "'>" . $row["NomeCategoria"] . "</a></li>";

}
}
?>