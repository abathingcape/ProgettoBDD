<?php
    session_start();
	  if (!isset($_SESSION['user']) and $_SESSION['premium']!= true){
		  header('Location:login.html.php');
		  exit;
    }
    include 'connessione.php';
    $select_postsalvati = "SELECT p.IdPost AS idpost, p.IdUt AS idutente, p.TitoloPost AS titolopost, p.FotoPost1 AS Foto_post1,                   /*query che cerca gli ultimi 9 post salvati dall'utente, gli altri verranno caricati con lo scroll */
                                  p.FotoPost2 AS Foto_post2, p.DataOra AS dataora, b.Titolo AS NomeBlog, u.Username AS username
                            FROM post p
                            JOIN salvati s ON p.IdPost = s.IDpostSalvato
                            JOIN utenti u ON s.IDutSalva = u.IDutente
                            JOIN blog b ON p.IdBl = b.IdBlog
                            WHERE s.IDutSalva = ? ORDER BY dataora DESC LIMIT 9";         
    $stmt_select_postsalvati = $conn->prepare($select_postsalvati);
    $stmt_select_postsalvati -> bind_param ("i", $_SESSION['user']);
    $stmt_select_postsalvati->execute();
    $resultQuery=$stmt_select_postsalvati->get_result();
    $stmt_select_postsalvati->close();

?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> 
<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script>
  $(document).ready(function() {
    var indice=9;
    var tipopost="salvati";
  $(".lista_post").on('click', '.post-image', function() {                                  //permette di cliccare e quindi aprire un post
    id_post=$(this).data('post-id');
    window.location.href = 'post.html.php?id_post='+id_post;
  });
  $(document).scroll(function() {                                                           //scroll dopo i primi 9 post, 3 alla volta
      var y = $(this).scrollTop();
      if (y + $(window).height() >= ($(document).height())){
        $.ajax({
            type: "GET",
            url:"scrollpost.php",
            data: {"indice":indice,
                    "tipopost":tipopost},
            success: function(data) {
               $(".lista_post").append(data);                                               //aggiunge all'ultimo figlio i post che appariranno
            }
        });
        indice+=3;
        }
      });
});
</script>
    <link rel="stylesheet" type="text/css" href="homepage.css">
</head>

<body>

<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
        <div class="saved">
            <h1>Post salvati</h1>
        </div> 
        <div class="lista_post">
        <?php
          while ($row = $resultQuery -> fetch_assoc()){                                                           //stampa tutti i post salvati
            if ($row['Foto_post1']==NULL){$fotopost=$row['Foto_post2']; }else $fotopost=$row['Foto_post1'];       //controlla se la foto1 c'è e in caso mostra la 2s
          echo '
                <div class="post-image" data-post-id="' . $row["idpost"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $fotopost . '\');">
                    <div class="autorepost">
                      <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["idutente"] . '>' . $row["username"] . '</a>
                    </div>
                    <h1>' . $row["titolopost"] . '</h1>
                    <h3>' . $row["NomeBlog"] . '</h3>
                    <div class="dataorapost">
                      <i class="fa-solid fa-clock"></i><p>' . $row["dataora"] . '</p>
                    </div>
      
                </div>';
          }
          ?>
        </div>
    
  </div>
</div>

</body>

</html>