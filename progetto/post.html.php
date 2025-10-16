<?php
  session_start();
  include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		}
    else if (isset($_GET['id_post']) && is_numeric($_GET['id_post'])) {                                                                                        //controlla che l'id post sia stato passato via url
        $id_p= $_GET['id_post'];
      $select_post = "SELECT p.IdPost AS idPost, p.IdUt AS idutente, b.IdBlog AS idblog, p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1,              /*query che ricava i dati presenti nel post in cui si trova l'utente*/
                      p.FotoPost2 AS Foto_post2, u.Username AS Username, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.IDutente AS IDutente, p.DescPost AS DescrizionePost,
                      p.mi_piace AS MiPiace
                      FROM post AS p
                      JOIN utenti AS u ON p.IdUt = u.IDutente
                      JOIN blog AS b ON p.IdBl = b.IdBlog
                      WHERE p.IdPost = ?
                      LIMIT 1";
      $stmt_post=$conn->prepare($select_post);
      $stmt_post->bind_param("i",$id_p);
      $stmt_post->execute();
      $resultQuery=$stmt_post->get_result();
      $stmt_post -> close();
      $fetch_ass = $resultQuery -> fetch_assoc();

      if ($resultQuery->num_rows == 0){                                                                               //controlla se il post esiste, altrimenti da errore
        header('Location:errorpage.html.php'); 
      }

      $select_commenti="SELECT c.IdPostCommento AS idpost, c.TestoComm AS testo, c.Data AS datapost,                  /*query che cerca i commenti del post*/
                               u.Username AS username, u.IDutente AS Idutente, c.IdCommento AS idcommento      
                        FROM commento as c                                                                                    
                        JOIN utenti as u ON c.IdUtCommento = u.IDutente
                        WHERE c.IdPostCommento = ?
                        ORDER BY datapost DESC LIMIT 4";
      $stmt_commenti=$conn->prepare($select_commenti);
      $stmt_commenti->bind_param("i",$id_p);
      $stmt_commenti->execute();
      $resultQuery2=  $stmt_commenti->get_result();
      $stmt_commenti -> close();
      
      if ($_SESSION['premium']== true){                                                                               //se l'utente è premium controlla se il post è stato salvato
      $select_controllo_salvati="SELECT *
                                  FROM salvati 
                                  WHERE IDpostSalvato = ? AND IdutSalva = ?";
      $stmt_controllo_salvati=$conn->prepare($select_controllo_salvati);
      $stmt_controllo_salvati->bind_param("ii",$id_p, $_SESSION["user"]);
      $stmt_controllo_salvati->execute();
      $resultQuery3=$stmt_controllo_salvati->get_result();
      $stmt_controllo_salvati -> close();
      }
      $select_controllo_like="SELECT * FROM Valutazioni WHERE IDutVoto = ? AND IdVotoPost = ?";                       //controlla se l'utente ha messo like al post
      $stmt_controllolike = $conn->prepare($select_controllo_like);
      $stmt_controllolike->bind_param("ii",$_SESSION["user"], $id_p);
      $stmt_controllolike->execute();
      $resultQuery5=$stmt_controllolike->get_result();
      $stmt_controllolike->close(); 
      }else header('Location:index.html.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Post</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="homepage.css">
  <link rel="stylesheet" type="text/css" href="post.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {                                                                   
      var indice = 4;
      id_post=$(".salvavaluta").data('post-id');              
        $('.salva').on("click", '#salva', function (event){                                              //evento click che permette di salvare il post
          $.ajax({                                                        
                method: 'GET',                                             
                url: "salvapost.php",                                     
                data: {id_post:id_post},                                   
                success: function(data) {                                                                    
                   $('.salva').html(data);
                  }
            });
        
        });


        $('.salva').on("click", '#salvato', function (event){
          $.ajax({                                                                                      //evento click che permette di rimuovere il post dai salvati        
                method: 'GET',                                             
                url: "eliminasalvapost.php",                                                       
                data: {id_post:id_post},                                             
                success: function(data) {                                            
                  $('.salva').html(data);
                  }
                      
            });
        
        });


        $('.salvavaluta').on("click", '#like', function (event){                                        //permette di mettere like ad un post
          $.ajax({                                                        
                method: 'GET',                                             
                url: "likeaggiunto.php",                                                      
                data: {id_post:id_post},                                            
                success: function(data) {                                                                      
                    $(".like").html(data);
                  }
            });
        });


        $('.salvavaluta').on("click", '#nonlike', function (event){                                     //permette di rimuovere il like dal post
          $.ajax({                                                       
                method: 'GET',                                            
                url: "likerimosso.php",                                                     
                data: {id_post:id_post},                                             
                success: function(data) {                                                                     
                    $(".like").html(data);
                  }
            });
        });

        $(".caricacommenti").on("click", function(event){                                               //evento che permette il caricamento di altri commenti
            $.ajax({
                type: "GET",
                url:"scrollcommenti.php",
                data: {"indice":indice,
                        "id_post":id_post},
                success: function(data) {
                  $(".listacommenti").append(data);
                }
            });
            indice+=2;
        });

        $("#formNuovoCommento").on("submit", function(event) {                                          // evento che permette l'invio del commento tramite il tasto "INVIA"                                                                     
            event.preventDefault();
            var formData=new FormData(this);                                
            $.ajax({                                                        
                type: "POST",                                               
                url:$(this).attr('action'),                                 
                processData: false,                                         
                contentType: false,
                data: formData,                                            
                success: function(data) {                                   
                    if(data !="Il commento può avere massimo 70 caratteri" || data !="Non puoi pubblicare un commento vuoto"){                     //in caso rispetta i requisiti, stampa il commento                         
                            $("#return_message").hide(); 
                            $("#nuovocommento").val("");
                            $(".listacommenti").prepend(data);
                    }
                            else{
                            $("#return_message").show();                    //il controllo non è andato a buon fine, verranno fatti comparire i messaggi d'errore e non ci sarà nessun indirizzamento o dato salvato
                            $("#return_message").css('color', 'red');
                            $("#return_message").text(data);
                            }
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
     
        <div class="Titolo_post">
          <h1><?php
            echo ''. $fetch_ass["Titolo_post"] . '';
          ?></h1>
        </div>
        <div class="Nome_Blog">
          <p>blog: </p>
          <?php echo '<a href="blog.html.php?id_blog='. $fetch_ass["idblog"] .'">
           '. $fetch_ass["Nome_Blog"] . '';
          ?> </a>
        </div>
        <div class="utentedata">
          <div class="Username">
          <i class="fa-solid fa-user" style="color: #1d1e2f"></i>
          <?php echo'<a href="profilo.html.php?id='. $fetch_ass["idutente"] . '">'.
          $fetch_ass["Username"] . '';
            ?></a>
          </div>  
          -
          <div class="Dataeora">
            <i class="fa-solid fa-clock" style="color: #1d1e2f"></i>
            <p><?php
              echo ''. $fetch_ass["Dataeora"] . '';
            ?></p>
          </div> 
        </div>
        <div class="DescrizionePost">
          <p><?php
            echo ''. $fetch_ass["DescrizionePost"] . '';
          ?></p>
        </div>    
        <!--fare IF per controllare se il post ha 1 o due foto-->
        <div class="FotoContainer">
          <div class="FotoPost1">
          <?php echo '<img src="cartella_foto/' . $fetch_ass["Foto_post1"] . '"';
              if ($fetch_ass["Foto_post1"]==NULL){                                                              //contolla se la foto post 1 è null, non mostra l'elemento immagine 
                echo ' hidden="true"';  
              }
              ?>>
          </div>     
          <div class="FotoPost2">                                           
          <?php echo '<img src="cartella_foto/' . $fetch_ass["Foto_post2"] . '"';
              if ($fetch_ass["Foto_post2"]==NULL){                                                              //contolla se la foto post 2 è null, non mostra l'elemento immagine 
                echo ' hidden="true"';
              }
              ?>>
          </div> 
        </div>

        <?php echo '<div data-post-id="' . $fetch_ass["idPost"] . '"class="salvavaluta">'; ?>
            <div class="like">
              <?php
              if ($resultQuery5->num_rows>0){                                                                   //se il controllo like risulta positivo mostra il pollice pieno, altrimenti mostra quello vuoto
                echo '<i id ="nonlike" class="fa-solid fa-thumbs-up"></i>
                <h2>' . $fetch_ass["MiPiace"] . '</h2>';
              }else{
                echo '
                <i id ="like" class="fa-regular fa-thumbs-up"></i>
                <h2 class="conta">' . $fetch_ass["MiPiace"] . '</h2>';
              }
              ?>
            </div>
            <div class="salva">
            <?php if ($_SESSION["premium"]==true){                                                               //se l'uitente è premium mostra l'icona salvato
                    if ($resultQuery3->num_rows>0){                                                               // se il post è stato salvato mostra l'icona piena, altrimenti mostra l'icona vuota
                      echo '<i id="salvato" class="fa-solid fa-bookmark"></i>';
                    }else{
                      echo '<i id="salva" class="fa-regular fa-bookmark"></i>';
                    }
            }
              ?>
            </div>
        </div>

        <div class="modificapost">
          <?php  
          if ($fetch_ass["idutente"]==$_SESSION["user"])                                                          //se l'utente è lo stesso della sessione corrente, permette la modifica o l'eliminazione del post
                echo "<button class=\"modificaposttasto\" onclick=\"window.location.href='modificapost.html.php?id_post=" . $id_p . "&id_ut=" . $fetch_ass["idutente"] ."'\"> Modifica Post </button>
                <form action=\"eliminapost.php\" method=\"post\">
                <input type=\"hidden\" name=\"id_post\" value=\"" . $id_p . "\">
                <input class=\"eliminaposttasto\" type=\"submit\" value=\"Elimina post\">
            </form>";
              ?>
        </div>  
        <div class="commenti">
          <div class="scrivicommento">
            <form id="formNuovoCommento" action="nuovocommento.php" method="post">
              <input type="hidden" name="id_post" value="<?php echo $id_p;?>">
              <input type="text" id="nuovocommento" name="nuovocommento" placeholder="Aggiungi un commento!">
              <input class="mandacommento" id="mandacommento" type="submit">
            </form>
          </div>
          <p id="return_message" hidden></p>
          <div class="listacommenti">
            <?php
            while ($row = $resultQuery2 -> fetch_assoc()){                                                        //stampa tutti i commenti
              echo '
                    <div class="utentecommento">
                      <i class="fa-solid fa-user" style="color: black; font-size: 16px"></i>
                      <a href="profilo.html.php?id=' . $row["Idutente"] . '"> ' . $row["username"] . '</a>
                    </div>
                    <div class="testodata">
                      <p class="testo"> ' . $row["testo"] . '<p>';
                    
                      echo '<p class="data"> ' . $row["datapost"] . '<p>';

                      if ($row["Idutente"]==$_SESSION["user"]){                                                   //permette all'utente di quella sessione di eliminare il proprio commento 
                        echo '<form action="eliminacommento.php" method="post">
                        <input type="hidden" name="id_post" value="' .$id_p . '">
                        <input type="hidden" name="id_commento" value="' .$row["idcommento"] . '">
                        <input class="eliminacommentotasto" type="submit" value="">';
                        }
                      
                    echo '</div>';
                    
            }
            ?>
          </div>
          <div class="piùcommenti">
            <button class="caricacommenti">Carica più commenti</button>
          </div>
          
      </div>
  </div>
</div>

</body>

</html>