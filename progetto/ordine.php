<?php
session_start();
include 'connessione.php';
if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
    header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
    exit;                                                               //stata inizializzata
    } else {
        $ordine=$_GET["ordine"];
        if ($ordine==""){                                                                                                                              //questo if controlla, tramite GET, qual è l'ordinamento di post richiesto e lo fornisce
            $select_post = "SELECT p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1, p. FotoPost2 AS Foto_post2,                                  /* restituisce i post random */
                            u.Username AS Username, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.IDutente AS IDutente, p.IdPost AS Id_post
                      FROM post AS p
                      JOIN utenti AS u ON p.IdUt = u.IDutente
                      JOIN blog AS b ON p.IdBl = b.IdBlog 
                      ORDER BY RAND() LIMIT 9";
            $stmt_post=$conn->prepare($select_post);
            $stmt_post->execute();
            $resultQuery=$stmt_post->get_result();
        }else if ($ordine=="ordinadecresc"){
            $select_post_desc = "SELECT p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1, p. FotoPost2 AS Foto_post2,                             /*ordina per data discendente */
                                u.Username AS Username, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.IDutente AS IDutente, p.IdPost AS Id_post
                        FROM post AS p
                        JOIN utenti AS u ON p.IdUt = u.IDutente
                        JOIN blog AS b ON p.IdBl = b.IdBlog
                        ORDER BY Dataeora DESC LIMIT 9";
            $stmt_post_desc=$conn->prepare($select_post_desc);
            $stmt_post_desc->execute();
            $resultQuery=$stmt_post_desc->get_result();
        }else if ($ordine=="ordinacresc"){
            $select_post_cresc = "SELECT p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1, p. FotoPost2 AS Foto_post2,                                /*ordina per data crescente*/
                                  u.Username AS Username, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.IDutente AS IDutente, p.IdPost AS Id_post
                        FROM post AS p
                        JOIN utenti AS u ON p.IdUt = u.IDutente
                        JOIN blog AS b ON p.IdBl = b.IdBlog
                        ORDER BY Dataeora ASC LIMIT 9";
            $stmt_post_cresc=$conn->prepare($select_post_cresc);
            $stmt_post_cresc->execute();
            $resultQuery=$stmt_post_cresc->get_result();
        }else if($ordine=="piùvotato"){
            $select_voto_decresc ="SELECT p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1, p. FotoPost2 AS Foto_post2,                              /*ordina per più votato */
                                    u.Username AS Username, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.IDutente AS IDutente, p.IdPost AS Id_post
                                    FROM post AS p
                                    JOIN utenti AS u ON p.IdUt = u.IDutente
                                    JOIN blog AS b ON p.IdBl = b.IdBlog
                                    ORDER BY mi_piace DESC LIMIT 9";
            $stmt_voto_decresc=$conn->prepare($select_voto_decresc);
            $stmt_voto_decresc->execute();
            $resultQuery=$stmt_voto_decresc->get_result();
        }else if ($ordine=="menovotato"){
            $select_voto_cresc ="SELECT p.TitoloPost AS Titolo_post, p.FotoPost1 AS Foto_post1, p. FotoPost2 AS Foto_post2,                                   /*ordina per meno votato */
                                u.Username AS Username, p.DataOra AS Dataeora, b.Titolo AS Nome_Blog, u.IDutente AS IDutente, p.IdPost AS Id_post
                                    FROM post AS p
                                    JOIN utenti AS u ON p.IdUt = u.IDutente
                                    JOIN blog AS b ON p.IdBl = b.IdBlog
                                    ORDER BY mi_piace ASC LIMIT 9";
            $stmt_voto_cresc=$conn->prepare($select_voto_cresc);
            $stmt_voto_cresc->execute();
            $resultQuery=$stmt_voto_cresc->get_result();
        }
        while ($row = $resultQuery -> fetch_assoc()){ 
            if ($row['Foto_post1']==NULL){$fotopost=$row['Foto_post2']; }else $fotopost=$row['Foto_post1'];                                    /*stampa i post richiesti */
          echo '
                <div class="post-image" data-post-id="' . $row["Id_post"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $fotopost . '\');">
                    <div class="autorepost">
                      <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["IDutente"] . '>' . $row["Username"] . '</a>
                    </div>
                    <h1>' . $row["Titolo_post"] . '</h1>
                    <h3>' . $row["Nome_Blog"] . '</h3>
                    <div class="dataorapost">s
                      <i class="fa-solid fa-clock"></i><p>' . $row["Dataeora"] . '</p>
                    </div>
      
                </div>';
          }
    }