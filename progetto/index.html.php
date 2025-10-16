<?php
    session_start();
    include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		} else {
      $select_post = "SELECT p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1, p. FotoPost2 AS Foto_post2,                                   /*query che prende 9 post presenti nella piattaforma*/
                            u.Username AS Username, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.IDutente AS IDutente, p.IdPost AS Id_post
                      FROM post AS p
                      JOIN utenti AS u ON p.IdUt = u.IDutente                           
                      JOIN blog AS b ON p.IdBl = b.IdBlog 
                      ORDER BY RAND() LIMIT 9";                                                                                                     //in orrdine casuale
      $stmt_post=$conn->prepare($select_post);
      $stmt_post->execute();
      $resultQuery=$stmt_post->get_result();
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
  <?php if ($_SESSION["premium"]!=true)
  echo "<style>
          .headerpaginapost h1{
            margin-top:70px;
          }
        </style>";?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>  
  <script>
  $(document).ready(function() {                                                                              
    var indice=9;                                                                                                 //l'indice tiene conto di quanti post abbiamo sullo schermo in questo momento 
    var tipopost="index";                                                                                         // serve a includere il tipo di scroll dalla pagina scrollpost.php
  $(".lista_post").on('click', '.post-image', function() {                                                        //gestione del click per aprire il post
    id_post=$(this).data('post-id');
    window.location.href = 'post.html.php?id_post='+id_post;
  });
  $(document).scroll(function() {                                                                                 //serve a controllare se con lo scorrimento siamo arrivati a fine pagina
      var y = $(this).scrollTop();
      if (y + $(window).height() >= ($(document).height()-1)){
        $.ajax({
            type: "GET",
            url:"scrollpost.php",
            data: {"indice":indice,
                    "tipopost":tipopost},
            success: function(data) {
               $(".lista_post").append(data);                                                                     //dalla GET riceveremo da appendere all'ultimo figlio di listapost altri 3 post che vogliamo vedere, non presenti sulla pagina
            }
        });
        indice+=3;
        }
      });
      $('.ordine').change(function(){                                                                             //funzione che si attiva ogni volta che viene fatto un cambio dell'ordine della visualizzazione dei post da parte dell'utente premium
        var ordine = $(this).val();
        $.ajax({
            type: "GET",
            url:"ordine.php",
            data: {"ordine":ordine},
            success: function(data) {
               $(".lista_post").html(data);                                                                       // a seconda del tipo di ordinamento, il contenuto del div listapost verrà sovrascritto dal contenuto della chiamtata ajax
            }
        }); 
      });
  });
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
      <?php if ($_SESSION["premium"]==true)                                                                       //se l'utente è premium può accedere al select che fornisce gli ordinamenti
        echo'
      <select class="ordine">
      <option value="">Ordina per:</option>
      <option value="ordinacresc"> Dal meno recente </option>
      <option value="ordinadecresc"> Dal più recente </option>
      <option value="piùvotato"> Dal più votato </option>
      <option value="menovotato">  Dal meno votato </option>
        </select>';?>    
        <div class="headerpaginapost">
        <h1> Post</h1>
        </div>
        <div class="lista_post">
          <?php
          while ($row = $resultQuery -> fetch_assoc()){ 
            if ($row['Foto_post1']==NULL){$fotopost=$row['Foto_post2']; }else $fotopost=$row['Foto_post1'];        // questo ciclo stampa per la prima volta i 9 post richiesti dalla queri fatta in cima
          echo '
                <div class="post-image" data-post-id="' . $row["Id_post"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $fotopost . '\');">
                    <div class="autorepost">
                      <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["IDutente"] . '>' . $row["Username"] . '</a>
                    </div>
                    <h1>' . $row["Titolo_post"] . '</h1>
                    <h3>' . $row["Nome_Blog"] . '</h3>
                    <div class="dataorapost">
                      <i class="fa-solid fa-clock"></i><p>' . $row["Dataeora"] . '</p>
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