<?php
  session_start();
  if (!isset($_SESSION['user'])){
    header('Location:login.html.php');
    exit;
  }
include "connessione.php";
$indice=$_GET["indice"];
$tipopost=$_GET["tipopost"];

if ($tipopost=="index"){                                                                                                                              //se l'utente si trova nella homepage
$select_post = "SELECT p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1, p.FotoPost2 AS Foto_post2,                                             /*la query seleziona tutti i post*/
                      u.Username AS Username, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.IDutente AS IDutente, p.IdPost AS Id_post
                      FROM post AS p
                      JOIN utenti AS u ON p.IdUt = u.IDutente
                      JOIN blog AS b ON p.IdBl = b.IdBlog
                      ORDER BY RAND() LIMIT 3 OFFSET ?";                                                                                              //ne carica 3 ad ogni scroll ripartendo dall'offset aggiornato
     $stmt_select_post = $conn->prepare($select_post);
     $stmt_select_post->bind_param("i",$indice);
     $stmt_select_post->execute();
     $resultQuery1=$stmt_select_post->get_result();
     $stmt_select_post->close(); 

}else if ($tipopost=="salvati"){                                                                                                                      //se l'utente è nella pagina post salvati
  $select_postsalvati = "SELECT p.IdPost AS Id_post, p.IdUt AS IDutente, p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1,                      /*la query seleziona tutti i post*/
                                p.FotoPost2 AS Foto_post2, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.Username AS Username
                            FROM post p
                            JOIN salvati s ON p.IdPost = s.IDpostSalvato
                            JOIN utenti u ON s.IDutSalva = u.IDutente
                            JOIN blog b ON p.IdBl = b.IdBlog
                            WHERE s.IDutSalva = ? LIMIT 3 OFFSET ?";                                                                                  //ne carica 3 ad ogni scroll ripartendo dall'offset aggiornato
    $stmt_select_postsalvati = $conn->prepare($select_postsalvati);
    $stmt_select_postsalvati -> bind_param ("ii", $_SESSION['user'],$indice);
    $stmt_select_postsalvati->execute();
    $resultQuery1=$stmt_select_postsalvati->get_result();
    $stmt_select_postsalvati->close();
}else if ($tipopost=="profilo"){                                                                                                                       //se l'utente si trova nella pagina profilo
    $select_post = "SELECT post.TitoloPost AS Titolo_post, blog.Titolo AS Nome_Blog, post.DataOra AS Dataeora, post.FotoPost1 AS Foto_post1,           /*la query seleziona tutti i post*/
                            post. FotoPost2 AS Foto_post2, utenti.Username AS Username, utenti.IDutente AS IDutente,
    post.IdPost AS Id_post
    FROM post
    JOIN blog ON post.IdBl = blog.IdBlog
    JOIN utenti ON post.IdUt = utenti.IDutente
    WHERE utenti.IDutente = ? LIMIT 2 OFFSET ?";                                                                                                        //ne carica 2 ad ogni scroll ripartendo dall'offset aggiornato

    $stmt_select_post = $conn->prepare($select_post);
    $stmt_select_post->bind_param("ii", $_SESSION["user"], $indice);
    $stmt_select_post->execute();
    $resultQuery1=$stmt_select_post->get_result();
    $stmt_select_post->close();
    while ($row = $resultQuery1->fetch_assoc()) {                                                                                                     //stampa i post nella pagina profilo
      if ($row['Foto_post1']==NULL){$fotopost=$row['Foto_post2']; }else $fotopost=$row['Foto_post1'];                                                 //se non c'è la foto 1, seleziona la foto 2
          echo '
                <div class="postprofilo-image" data-post-id="'. $row["Id_post"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $fotopost . '\');">
                  
                  <div class="autorepostprofilo">
                    <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["IDutente"] . '>' . $row["Username"] . '</a>
                  </div>
                  <h1>' . $row['Titolo_post'] . '</h1>
                  <h3>' . $row["Nome_Blog"] . '</h3>
                  <div class="dataorapostprofilo"> 
                    <i class="fa-solid fa-clock"></i><p>' . $row['Dataeora'] . '</p>
                  </div>

                </div>';
    }
    exit();
}else if ($tipopost="blog"){
      $id_blog=$_GET["idblog"];
      $select_post = "SELECT p.IdUt AS IDutente, u.username AS Username, p.IdPost AS Id_post, TitoloPost, FotoPost1, FotoPost2, DataOra, DescPost       /*seleziona i post per ogni blog */
                            FROM post AS p
                            JOIN utenti AS u ON p.IdUt = u.IDutente  
                            WHERE IdBl = ?
                            ORDER BY DataOra
                            DESC LIMIT 2 OFFSET ?";
      $stmt_post=$conn->prepare($select_post);
      $stmt_post->bind_param("ii",$id_blog,$indice);
      $stmt_post->execute();
      $resultQuery2=$stmt_post->get_result();
      $stmt_post -> close();
      while ($row = $resultQuery2->fetch_assoc()) {
        if ($row['FotoPost1']==NULL){$fotopost=$row['FotoPost2']; }else $fotopost=$row['s'];                          //fa il controllo sulle foto e stampa i post per ogni blog
        echo '
                  <div class="postblog-image" data-post-id="'. $row["Id_post"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $fotopost . '\');">      
                    
                    <div class="autorepostblog">
                      <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["IDutente"] . '>' . $row["Username"] . '</a>
                    </div>
                    <h1>' . $row['TitoloPost'] . '</h1>
                    <div class="dataorapostblog"> 
                      <i class="fa-solid fa-clock"></i><p>' . $row['DataOra'] . '</p>
                    </div>

                  </div>';
      }
      exit();
}

while ($row = $resultQuery1 -> fetch_assoc()){                                                                          //stampa i post per la homepage e per i salvati
  if ($row['Foto_post1']==NULL){$fotopost=$row['Foto_post2']; }else $fotopost=$row['Foto_post1'];  
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