<?php
  session_start();
  if (!isset($_SESSION['user'])){
      header('Location:login.html.php');
      exit;
}
include 'connessione.php';
$indice=$_GET["indice"];
$tipoblog=$_GET["tipoblog"];
if ($tipoblog=="blog"){                                                                                                     //se la variabile $tipoblog è blog
$select_blogs = "SELECT b.Titolo AS Titolo_Blog, b.DescBlog AS Descrizione_Blog, b.FotoBlog AS Foto_Blog,                   /*la query restuisce i blog aggiuntivi per la pagina listablog */
                        u.Username AS Username_Autore, b.IdAutore AS IDutente, b.IdBlog AS idBlog
                     FROM blog AS b
                     JOIN utenti AS u ON b.IdAutore = u.IDutente ORDER BY idBlog DESC LIMIT 3 OFFSET ?";                    //ne carica tre alla volta, mettendo come offset l'indice
$stmt_select_blogs = $conn->prepare($select_blogs);
$stmt_select_blogs->bind_param("i",$indice);
$stmt_select_blogs->execute();
$resultQuery1=$stmt_select_blogs->get_result();
$stmt_select_blogs->close(); 
}else if($tipoblog=="profilo"){                                                                                             //se la variabile $tipoblog è profilo
    $select_blogs = "SELECT b.IdAutore AS IDutente, b.Titolo AS Titolo_Blog, b.DescBlog AS Descrizione_Blog,                /*la query restuisce i blog aggiuntivi per la pagina profilo */
                        b.FotoBlog AS Foto_Blog, u.Username AS Username_Autore, b.IdBlog AS idBlog
                      FROM blog AS b
                      JOIN utenti AS u ON b.IdAutore = u.IDutente AND u.IDutente = ? LIMIT 2 OFFSET ?";                     //ne carica tre alla volta, mettendo come offset l'indice

    $stmt_select_blogs = $conn->prepare($select_blogs);
    $stmt_select_blogs -> bind_param("ii",$_SESSION["user"],$indice);
    $stmt_select_blogs->execute();
    $resultQuery1=$stmt_select_blogs->get_result();
    $stmt_select_blogs->close(); 
}else if($tipoblog=="categoria"){                                                                                                                    //se la variabile $tipoblog è categoria
    $id_cat=$_GET["id_cat"];
    $select_blog = "SELECT DISTINCT b.Titolo AS Titolo_Blog, b.FotoBlog AS Foto_Blog, b.IdBlog AS idBlog,                                            /*la query restuisce i blog aggiuntivi per ogni categoria*/
                                    B.IdAutore AS IDutente, u.Username AS Username_Autore, b.IdBlog AS idblog, c.NomeCategoria AS nomecategoria      /*Distinct perchè i post presenti sia in categoria che in sottocategoria li troverebbe due volte */
                           FROM blog b
                           JOIN categoria AS c ON b.IdCat = c.IDcategoria
                           JOIN utenti AS u ON b.IdAutore = u.IDutente
                           WHERE c.IDcategoria = ? LIMIT 3 OFFSET ?";
    $stmt_select_blog = $conn-> prepare($select_blog);
    $stmt_select_blog -> bind_param("ii", $id_cat, $indice);
    $stmt_select_blog->execute();
    $resultQuery1 = $stmt_select_blog -> get_result();
    $stmt_select_blog -> close();
}

while ($row = $resultQuery1->fetch_assoc()) {                                                                                                       //stampa i blog
echo '<div class="blog">
<div class="blog-image" data-blog-id="' . $row["idBlog"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $row['Foto_Blog'] . '\');">
    <h1>' . $row['Titolo_Blog'] . '</h1>
</div>
<div class="blog-header">
    <div class="utenteblog">
     <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["IDutente"] . '>' . $row["Username_Autore"] . '</a>
    </div>
</div>
</div>';
}
?>