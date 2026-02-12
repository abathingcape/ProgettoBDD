<?php
  session_start();
  include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                                                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                                                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                                                                               //stata inizializzata
		}else{ if (isset($_GET['id_cat']) && is_numeric($_GET['id_cat'])){
            $id_cat = $_GET["id_cat"];
            }else{
              header("location:index.html.php");
              exit();
            }
            
            $select_categoria = "SELECT NomeCategoria, DescCategoria, categoriaPadre /*categoria corrente*/
                                      FROM categoria 
                                      WHERE IDcategoria = ?";
            $stmt_select_categoria = $conn-> prepare($select_categoria);
            $stmt_select_categoria -> bind_param("i", $id_cat);
            $stmt_select_categoria->execute();
            $resultQuery1 = $stmt_select_categoria -> get_result();
            $stmt_select_categoria -> close();

            if ($resultQuery1->num_rows == 0) {
              header("location:errorpage.html.php");
              exit();
            }
            $select_blog = "SELECT DISTINCT b.Titolo AS titolo, b.FotoBlog AS fotoblog, b.IdBlog AS idblog,
             B.IdAutore AS idutente, u.Username AS username, b.IdBlog AS idblog, c.NomeCategoria AS nomecategoria      /*Distinct perchè i blog presenti sia in categoria che in sottocategoria li troverebbe due volte */
                           FROM blog b
                           JOIN categoria AS c ON b.IdCat = c.IDcategoria
                           JOIN utenti AS u ON b.IdAutore = u.IDutente
                           WHERE c.IDcategoria = ? LIMIT 6";
            $stmt_select_blog = $conn-> prepare($select_blog);      
            $stmt_select_blog -> bind_param("i", $id_cat);
            $stmt_select_blog->execute();
            $resultQuery = $stmt_select_blog -> get_result();
            $stmt_select_blog -> close();

            $cat=$resultQuery1->fetch_assoc();

            $select_sottocategoria = "SELECT c.NomeCategoria AS nomecategoria, c.IDcategoria AS idcategoria       /*sottocategorie*/
                                      FROM categoria AS c
                                      WHERE categoriaPadre = ?";
            $stmt_select_sottocategoria = $conn-> prepare($select_sottocategoria);
            $stmt_select_sottocategoria -> bind_param("i", $id_cat);
            $stmt_select_sottocategoria->execute();
            $resultQuery2 = $stmt_select_sottocategoria -> get_result();
            $stmt_select_sottocategoria -> close();

            if ($cat["categoriaPadre"]!=NULL){
            $select_cat_padre = "SELECT c.NomeCategoria AS nomecategoriapadre, c.IDcategoria AS idcategoriapadre     /*ricavo quale è la categoria padre in caso siamo in una sottocategoria */
                                      FROM categoria AS c
                                      WHERE IDcategoria = ?";
            $stmt_select_cat_padre = $conn-> prepare($select_cat_padre);
            $stmt_select_cat_padre -> bind_param("i", $cat["categoriaPadre"]);
            $stmt_select_cat_padre->execute();
            $resultQuery3 = $stmt_select_cat_padre -> get_result();
            $stmt_select_cat_padre -> close();

            $cat_padre=$resultQuery3->fetch_assoc();
            }
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
  <link rel="stylesheet" type="text/css" href="categoria.css">
  <link rel="stylesheet" type="text/css" href="listablog.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      var tipoblog="categoria";
      var indice=6;
      var id_c=$(".titolodesc").data('cat-id');
      $(".listablog").on('click', '.blog-image', function() {                                   //click event per andare nel blog cliccato
      var id_blog=$(this).data('blog-id')
      window.location.href = 'blog.html.php?id_blog='+id_blog;
      });

      $(".listasottocategorie").on('click', '.categoria-header', function() {                   //click event per andare nella sottocategoria
      var id_cat=$(this).data('categoria-id')
      window.location.href = 'categoria.html.php?id_cat='+id_cat;
      });

      $(document).scroll(function() {                                                           //evento scroll per caricare altri blogg
      var y = $(this).scrollTop();
      if (y + $(window).height() >= ($(document).height())){
        $.ajax({
            type: "GET",
            url:"scrollblog.php",
            data: {"indice":indice,
                    "tipoblog":tipoblog,
                  "id_cat":id_c},
            success: function(data) {
               $(".listablog").append(data);
            }
        });
        indice+=3;
        }
      });
    });
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>


<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
        <div class="tornaa">                                                                  <!--Reference categoria padre-->
          <?php
            if ($cat["categoriaPadre"]!=NULL){
              echo '<a href="categoria.html.php?id_cat=' . $cat_padre["idcategoriapadre"] . '"> <i class="fa-solid fa-arrow-left"></i> Torna a: ' . $cat_padre["nomecategoriapadre"] . '</a>';
            }
          ?>
        </div>
        <div class="titolodesc" data-cat-id="<?php echo'' . $_GET["id_cat"] . '';?>">
            <?php echo '<h4>' . $cat["NomeCategoria"] . '</h4>
                        <p>' . $cat["DescCategoria"] . '</p';
                    ?>  
        </div>
      <div class="sottocategoria">
        <?php if ($resultQuery2->num_rows>0) echo "<h2>Sottocategorie</h2>";?>              <!--Stampo sottocategorie in caso ce ne siano-->
      </div>
        <div class="listasottocategorie">
          <?php
          while ($row = $resultQuery2-> fetch_assoc()){                                             
            echo '<div class="Categoria">
                    <div class="categoria-header" data-categoria-id=' . $row["idcategoria"]. '>
                    <h1>' . $row['nomecategoria'] . '</h1>
                  </div>
                  </div>';
          }
          
          ?>
        </div>
        <?php if ($resultQuery->num_rows>0) echo"<h3>I blog di questa categoria</h3>"; ?>             <!--Stampo i blog appartenenti alla categoria in caso ce ne siano-->
        <div class="listablog">
        <?php
          while ($row = $resultQuery->fetch_assoc()) {
              echo '<div class="blog">
                      <div class="blog-image" data-blog-id="' . $row["idblog"] . '" data-background="image" style="background-image: url(\'cartella_foto/' . $row['fotoblog'] . '\');">
                          <h1>' . $row['titolo'] . '</h1>
                      </div>
                      <div class="blog-header">
                          <div class="utenteblog">
                           <i class="fa-solid fa-user"></i><a href=profilo.html.php?id=' . $row["idutente"] . '>' . $row["username"] . '</a>
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