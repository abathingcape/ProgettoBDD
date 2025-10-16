<?php
session_start();
include 'connessione.php';
if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
  header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
  exit;                                                               //stata inizializzata
}else{
  if (isset($_GET['id']) && is_numeric($_GET['id'])) {                //controlla che l'utente può modificare il profilo n cui è (solo se è il suo)
    $puntatoreutente = $_GET['id'];
  }else{
    $puntatoreutente = $_SESSION['user'];                         
  }

    $select_blogs = "SELECT b.Titolo AS Titolo_Blog, b.DescBlog AS Descrizione_Blog, b.FotoBlog AS Foto_Blog, 
                            u.Username AS Username_Autore, b.IdAutore AS IDutente, b.IdBlog AS idBlog                 /*query che restituisce i blog con i relativi nomi utentidei creatori*/
                     FROM blog AS b
                     JOIN utenti AS u ON b.IdAutore = u.IDutente ORDER BY idBlog DESC LIMIT 9"; 
    
    $stmt_select_blogs = $conn->prepare($select_blogs);
    $stmt_select_blogs->execute();
    $resultQuery1=$stmt_select_blogs->get_result();
    $stmt_select_blogs->close(); 
}
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>homepage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="homepage.css">
  <link rel="stylesheet" type="text/css" href="listablog.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script>
    $(document).ready(function() {
      var tipoblog="blog";
      $(".listablog").on('click', '.blog-image', function() {                                 //gestione evento click sul blog
      id_blog=$(this).data('blog-id')
      window.location.href = 'blog.html.php?id_blog='+id_blog;
      });
      var indice=9;
      $(document).scroll(function() {
      var y = $(this).scrollTop();                                                            //scroll presente nella pagina scroll.php
      if (y + $(window).height() >= ($(document).height())){
        $.ajax({
            type: "GET",
            url:"scrollblog.php",
            data: {"indice":indice,
                    "tipoblog":tipoblog},
            success: function(data) {
               $(".listablog").append(data);
            }
        });
        indice+=3;
        }
      });
    });
    </script>
</head>

<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
        <!--Qui va tutto il contenuto della pagina--> 
        <div class="scritta">
            <h1>Blog</h1>
        </div>
        <div class="listablog">
        <?php
          while ($row = $resultQuery1->fetch_assoc()) {                                   //stampa i blog richiesti dalla queru in cima alla pagina
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

        </div>

      </div>
  </div>
</div>

</body>

</html>