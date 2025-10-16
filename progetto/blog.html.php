<?php
  session_start();
  include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                                                                    //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                                                                //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                                                                             //stata inizializzata
		}else{
            if (isset($_GET['id_blog']) && is_numeric($_GET['id_blog'])){ 
                $id_blog= $_GET['id_blog'];
                $select_blog = "SELECT b.IdBlog AS idblog, b.Titolo AS Titolo_Blog, b.DescBlog AS Descrizione_Blog,     
                                  b.FotoBlog AS Foto_Blog, u.Username AS Username_Autore, b.IdAutore AS IDutente      
                                FROM blog AS b
                                JOIN utenti AS u ON b.IdAutore = u.IDutente 
                                WHERE b.IdBlog = ? ";
                $stmt_blog=$conn->prepare($select_blog);                                                              //query per prendere il blog che vogliamo vedere
                $stmt_blog->bind_param("i",$id_blog);
                $stmt_blog->execute();
                $resultQuery1=$stmt_blog->get_result();
                $stmt_blog -> close();
                if ($resultQuery1->num_rows==0){
                  header("Location:errorpage.html.php");
                  exit();
                }
                $fetch_ass = $resultQuery1 -> fetch_assoc();

                $select_post = "SELECT IdPost, TitoloPost, FotoPost1, FotoPost2, 
                            DataOra, DescPost, u.IDutente, u.username
                            FROM post AS p
                            JOIN utenti AS u ON p.IdUt = u.IDutente   
                            WHERE IdBl = ?
                            ORDER BY DataOra
                            DESC LIMIT 4 ";
                
                $stmt_post=$conn->prepare($select_post);                                                            //query per prendere i primi 4 post dentro al blog  
                $stmt_post->bind_param("i",$id_blog);
                $stmt_post->execute();
                $resultQuery2=$stmt_post->get_result();
                $stmt_post -> close();
                
                $select_coautori="SELECT u.Username AS co_username, cg.IDcoautore AS idcoautore
                                    FROM cogestori AS cg
                                    JOIN blog AS b ON b.IdBlog = cg.IDblog
                                    JOIN utenti AS u ON u.IDutente = cg.IDcoautore
                                    WHERE cg.IDblog = ?";
                $stmt_coautori=$conn->prepare($select_coautori);                                                    //query per prendere i coautori del blog
                $stmt_coautori->bind_param("i",$id_blog);
                $stmt_coautori->execute();
                $resultQuery3=$stmt_coautori->get_result();
                $stmt_coautori -> close();

                $select_categoria = "SELECT c.NomeCategoria AS nomecategoria
                                    FROM categoria AS c
                                    JOIN blog AS b ON b.IdCat = c.IDcategoria
                                    WHERE b.IdBlog = ?";
                $stmt_categoria = $conn -> prepare($select_categoria);                                              //query per prendere la categoria in cui si trova il blog
                $stmt_categoria -> bind_param("i",$id_blog);
                $stmt_categoria -> execute();
                $resultQuery4 = $stmt_categoria -> get_result();
                $cat = $resultQuery4->fetch_assoc();
                $stmt_categoria -> close();
            }else header("Location:index.html.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Post</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="homepage.css">
  <link rel="stylesheet" type="text/css" href="blog.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {   
      var id_blog=$('.strutturablog').data('blog-id');
      var tipopost="blog";
      var indice=4;
      $(".strutturapost").on('click','.postblog-image', function(){                                                //click event che fa andare sulal pagina del post cliccato
        id_post=$(this).data('post-id');
        window.location.href = 'post.html.php?id_post='+id_post;
      });
      $(document).scroll(function() {                                                                              //controllo funzione scroll per aggiungere altri post in caso scorriamo
      var y = $(this).scrollTop();                                                                                 //e ce ne siano alri nel blog
        if (y + $(window).height() >= ($(document).height()-1)){
          $.ajax({
              type: "GET",
              url:"scrollpost.php",
              data: {"indice":indice,
                      "tipopost":tipopost,
                      "idblog":id_blog},
              success: function(data) {
                $(".strutturapost").append(data);
              }
          });
          indice+=2;
          }
        });
      });
    </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
        <div class="strutturablog" data-blog-id="<?php echo''. $fetch_ass["idblog"] . '';?>">
       <?php if ($fetch_ass["Foto_Blog"]!=NULL) 
                echo '<div class="fotoblog">
                <img src="cartella_foto/<?php
                    ' . $fetch_ass["Foto_Blog"] . '
                ">
            </div>';?>
            <div class="infoblog">
                <h1><?php
                    echo ''. $fetch_ass["Titolo_Blog"] . '';
                ?></h1>
                <h2> <?php echo $cat["nomecategoria"]; ?></h2>                                                        <!--stampo tutte le informazioni del blog -->
                <div class="aut_coaut">
                    <div class="autore">
                        <i class="fa-solid fa-user" style="color: #1d1e2f"></i>
                        <?php echo "<a href='profilo.html.php?id=". $fetch_ass["IDutente"] . "'>"    
                         . $fetch_ass["Username_Autore"] . "</a>";?>
                    </div>    
                    <div class= "coautori">
                        <p>Coautore/i: <?php if ($resultQuery3->num_rows==0){echo "nessuno";}?></p>
                        <?php while ($row = $resultQuery3 -> fetch_assoc()){ 
                          echo'<a href="profilo.html.php?id=' . $row["idcoautore"] . '" class="coaut">' . $row["co_username"] . '</a>';
                         }
                        ?>
                    </div>    
                </div>
                <div class="DescrizioneBlog">
                    <p><?php
                        echo ''. $fetch_ass["Descrizione_Blog"] . '';
                    ?></p>
                </div> 
                <?php 
                if ($fetch_ass["IDutente"]==$_SESSION["user"]){                                                       //bottoni per modifica e elminazione post con controllo con utente della sessione
                echo' <div class="deladd">
                  <div class=modificablog>  
                        <button class="modificablogtasto" onclick="window.location.href=\'modificablog.html.php?id_blog=' . $id_blog . '&id_ut=' . $fetch_ass["IDutente"] .'\'"> MODIFICA </button>
                  </div>
                  |
                  <div class=eliminablog>
                      <form action="eliminablog.php" method="post">
                          <input type="hidden" name="id_blog" value="' . $id_blog . '">
                          <input class="eliminablogtasto" type="submit" value="ELIMINA">
                      </form>
                  </div>   
                </div>'; 
                }?>  
            </div>
            
        </div>
        <div class="titolopostinblog">
            <?php if ($resultQuery2->num_rows>0) {echo "<h1>I post di questo blog:</h1>";
                  }else echo "<h1>Nessun post in questo blog</h1>";
                  ?>
        </div>
        <div class="strutturapost">                                                                       <!-- stampo i post del blog (in caso ce ne siano)-->
        <?php
            while ($row = $resultQuery2->fetch_assoc()) {
              if ($row['FotoPost1']==NULL){$fotopost=$row['FotoPost2']; }else $fotopost=$row['FotoPost1'];
              echo '
                        <div class="postblog-image" data-post-id="'. $row["IdPost"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $fotopost . '\');">
                          
                          <div class="autorepostblog">
                            <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["IDutente"] . '>' . $row["username"] . '</a>
                          </div>
                          <h1>' . $row['TitoloPost'] . '</h1>
                          <div class="dataorapostblog"> 
                            <i class="fa-solid fa-clock"></i><p>' . $row['DataOra'] . '</p>
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