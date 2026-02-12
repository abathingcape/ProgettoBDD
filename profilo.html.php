<?php
  session_start();
  include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
    header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
	}else{
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {                            //controlla se l'id è stato passato da GET
      $puntatoreutente = $_GET['id'];
    }else{
      header("location:index.html.php");
      exit();
    }
    $select_idutente = "SELECT * FROM utenti WHERE IDutente = ?";                  //controlliamo che l'username o email inseriti siano contenuti all'interno del database
    
    $stmt_select_idutente = $conn->prepare($select_idutente);
    $stmt_select_idutente->bind_param("i", $puntatoreutente);
    $stmt_select_idutente->execute();
    $resultQuery=$stmt_select_idutente->get_result();
    $fetch_ass=$resultQuery->fetch_assoc();
    $stmt_select_idutente->close();
    
    if ($resultQuery->num_rows == 0){                                               //se l'utente non esiste compare una pagnia di errore
      header("location:errorpage.html.php");
      exit();
    }

    $select_post = "SELECT post.TitoloPost AS NomePost, blog.Titolo AS NomeBlog, post.DataOra AS DataOra,                     /*cerca i post del profilo */
                          post.FotoPost1 AS Foto_post1, post. FotoPost2 AS Foto_post2, utenti.Username AS NomeUtente, utenti.IDutente AS IDutente,
                    post.IdPost AS idPost
                    FROM post
                    JOIN blog ON post.IdBl = blog.IdBlog
                    JOIN utenti ON post.IdUt = utenti.IDutente
                    WHERE utenti.IDutente = ? LIMIT 6";

    $stmt_select_post = $conn->prepare($select_post);
    $stmt_select_post->bind_param("i", $puntatoreutente);
    $stmt_select_post->execute();
    $resultQuery2=$stmt_select_post->get_result();
    $stmt_select_post->close(); 

    $select_blogs = "SELECT b.IdAutore AS idautore, b.Titolo AS Titolo_Blog, b.DescBlog AS Descrizione_Blog,               /*cerca i blog del profilo */
                          b.FotoBlog AS Foto_Blog, u.Username AS Username_Autore, b.IdBlog AS idBlog
                      FROM blog AS b
                      JOIN utenti AS u ON b.IdAutore = u.IDutente AND u.IDutente = ? LIMIT 6";          

    $stmt_select_blogs = $conn->prepare($select_blogs);
    $stmt_select_blogs -> bind_param("i", $puntatoreutente);
    $stmt_select_blogs->execute();
    $resultQuery1=$stmt_select_blogs->get_result();
    $stmt_select_blogs->close(); 

    $controllo_premium=false;                                                                     //imposta di base l'abbonamento a false
    if ($fetch_ass["InizioAbb"]!=NULL){                                                           //se l'utente ha una data di inizio di abbonamento e non ha superato i 30gg, l'utente sarà premium
      $timestamp_corrente = date('Y-m-d');
      $data_inizio = new DateTime($fetch_ass["InizioAbb"]); 
      $data_inizio->add(new DateInterval("P30D")); 
      $data_fine = $data_inizio->format('Y-m-d');
      if  ($timestamp_corrente<$data_fine){
        $controllo_premium=true;
    }
  }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>homepage</title>
  <?php
  if ($puntatoreutente == $_SESSION['user'])                                                        //se id della sessione coincide con l'id dell'utente, gli permette di modificare la foto profilo
  echo '
    <style>
  .profile-picture:hover #preview-image {
    content: url("cartella_foto/aggiornaimg.png");
  }
  </style>'

  ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="homepage.css">
  <link rel="stylesheet" type="text/css" href="profilo.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script> 
    $(document).ready(function() {                                                                          //mosgtra i primi 6 blog e 6 post inizialmente
      var indiceblog=6;                                                                                     //inidici da cui partirà lo scroll dei pose e blog successivi
      var indicepost=6;
      var tipopost="profilo";
      var tipoblog="profilo";
      $(".listapostprofilo").hide();

      $(".listapostprofilo").on('click',".postprofilo-image", function(){                                   //permette di aprire il post
        id_post=$(this).data('post-id')
        id_utente=$(".profile").data("id-utente")
        window.location.href = 'post.html.php?id_post='+id_post;
      });

      $(".listablog").on('click',".blog-image", function(){                                                 //permette di aprire il blog
        id_blog=$(this).data('blog-id')
        window.location.href = 'blog.html.php?id_blog='+id_blog;
      });

      $('.selettoreBlog').on('click',function() {                                                           //tasto selezione blog
          blog=true;
          $(".listablog").show();
          $(".listapostprofilo").hide();
      });

      $('.selettorePost').on('click',function() {                                                           //tasto selezione blog
          blog=false;
          $(".listablog").hide();
          $(".listapostprofilo").show();
      });

      $('#profile-image').on('change', function() {                                                     //permette la sostituzione della foto profilo
        var inputFile = document.getElementById('profile-image');
        var file=inputFile.files[0];                                                                    //.files prende il contenuto all'elemento 
        if (file) {
          var formData = new FormData();
          formData.append('profileImage', file);
          $.ajax({                                                                                      //avviamo la nostra chiamata ajax
                type: "POST",                                                                           //chiamata di tipo post
                url: 'updatepropic.php',                                                                //il contenuto della form sarà la voto che verrà caricata
                processData: false,                                         
                contentType: false,
                data: formData,                                                                          //data sono i dati che passeremo al php, in questo caso tutto contenuto in formData
                success: function(data) {                                                              
                  location.reload();
                }
            });

        }
      });

      $(document).scroll(function() {                                                                      //scroll dei blog, nella chiamata ajax includeremo la variabile "indice", punto di partenza per lo scroll
      var y = $(this).scrollTop();
      if (y + $(window).height() >= ($(document).height()-1)){
        if ($(".listapostprofilo").is(":hidden")){
        $.ajax({
            type: "GET",
            url:"scrollblog.php",
            data: {"indice":indiceblog,
                    "tipoblog":tipoblog},
            success: function(data) {
               $(".listablog").append(data);
               indiceblog+=2;
            }
        });
        }
        if ($(".listablog").is(":hidden")){                                                                 //scroll dei post, nella chiamata ajax includeremo la variabile "indice", punto di partenza per lo scroll
          $.ajax({
            type: "GET",
            url:"scrollpost.php",
            data: {"indice":indicepost,
                    "tipopost":tipopost},
            success: function(data) {
               $(".listapostprofilo").append(data);
               indicepost+=2;
            }
        });
        }
      }
      });
    });

  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
        <div class="allpage">
          <div class="profile" data-id-utente="<?php echo ''. $puntatoreutente.''?>">
            <div class="profile-picture">
                <?php
                if ($puntatoreutente==$_SESSION["user"])                                                    //se l'utente è lo stesso della sessione consente di cambiare foto profilo
                  echo '<input type="file" id="profile-image" name= "profileImage" accept="image/*">';
                ?>
                <label for="profile-image" id="profile-label">
                    <img id="preview-image" <?php echo "src= 'cartella_foto/". $fetch_ass['FotoP'] . "'";?> alt="Foto Profilo">
                </label>
            </div>
            <div class="profile-info">
              <?php
              
                echo '<div class="nomemodifica">';
                  echo '<h1>' . $fetch_ass['Nome'] . ' ' . $fetch_ass['Cognome'] . '</h1>';
                  if ($puntatoreutente==$_SESSION["user"])                                                  //se l'utente è lo stesso della sessione, consente di modificare il profilo
                    echo "<button class=\"modificaprofilo\" onclick=\"window.location.href='modificaprofilo.html.php'\"><i class=\"fa-solid fa-pen\"></i></button>";
                  echo "</div>";
                
                echo '<p>Username: <span>'. $fetch_ass['Username'] . '</span></p>
                      <p>Email: <span>' . $fetch_ass['Email'] . '</span></p>';
                ?>
              <?php
              if ($puntatoreutente==$_SESSION["user"])                                                       //se l'utente è lo stesso della sessione, consente di eliminare il profilo
                echo "<button class=\"eliminaprofilo\" onclick=\"window.location.href='eliminaprofilo.php'\"> Elimina Profilo </button>";
                echo '</div>';
              if ($puntatoreutente==$_SESSION["user"] and $_SESSION["premium"]!=true){                        //se l'utente è lo stesso della sessione ma non è premium, compare il tasto "diventapremium"
                echo "<button class=\"diventaPremium\" onclick=\"window.location.href='pagamento.html.php'\"> Diventa utente Premium </button>";}
              if ($controllo_premium){                                                                         //se l'utente è lo stesso della sessione ed è premium, compare la sceritta "utente premium"
                echo '<div class="premium"><p>Utente Premium </p><i class="fa-solid fa-crown"></i></div>';
              }
              }
            ?>
          </div>
          <div class="selezione">
            <button class="selettoreBlog">Blog</button>
            <button class="selettorePost">Post</button>
            </div>
        <div class="listablog">
          <?php
            while ($row = $resultQuery1->fetch_assoc()) {                                                     //stampa tutti i blog di qeul profilo
                  echo '<div class="blog">
                          <div class="blog-image" data-blog-id="' . $row["idBlog"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $row['Foto_Blog'] . '\');">
                              <h1>' . $row['Titolo_Blog'] . '</h1>
                          </div>
                          <div class="blog-header">
                          <div class="utenteblog">
                          <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["idautore"] . '>' . $row["Username_Autore"] . '</a>
                          </div>
                          </div>
                        </div>';
            }
          ?>
        </div>
        <div class="listapostprofilo">
          <?php
            while ($row = $resultQuery2->fetch_assoc()) {                                                     //stampa tutti i post di quel profilo
              if ($row['Foto_post1']==NULL){$fotopost=$row['Foto_post2']; }else $fotopost=$row['Foto_post1'];
                  echo '
                        <div class="postprofilo-image" data-post-id="'. $row["idPost"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $fotopost . '\');">
                          
                          <div class="autorepostprofilo">
                            <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["IDutente"] . '>' . $row["NomeUtente"] . '</a>
                          </div>
                          <h1>' . $row['NomePost'] . '</h1>
                          <h3>' . $row["NomeBlog"] . '</h3>
                          <div class="dataorapostprofilo"> 
                            <i class="fa-solid fa-clock"></i><p>' . $row['DataOra'] . '</p>
                          </div>

                        </div>';
            }
          ?>
        </div>
        
        
        </div>
        
        
        
        
      </div>
  </div>
</div>

</body>

</html>