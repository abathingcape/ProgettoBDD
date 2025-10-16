<?php
    session_start();
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		} else {
		echo "Benvenuto ".
		$_SESSION['user'];
		}
    include 'connessione.php';
      $select_categorie = "SELECT NomeCategoria, DescCategoria, IDcategoria FROM categoria WHERE categoriaPadre IS NULL";                  
      $stmt_select_categorie = $conn->prepare($select_categorie);                                                                             //restituisce le prime 10 categorie padre
      $stmt_select_categorie->execute();  
      $resultQuery=$stmt_select_categorie->get_result();
      $stmt_select_categorie->close(); 
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>homepage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="homepage.css">
  <link rel="stylesheet" type="text/css" href="listacategorie.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $(".categoria").on('click', '.categoria-header', function() {                           //gestisce il click sulla categoria
          id_cat=$(this).data('categoria-id');
          window.location.href = 'categoria.html.php?id_cat='+id_cat;
      });

    });
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
        <?php
        while ($row = $resultQuery->fetch_assoc()) {                                        //stampa tutte le categorie padre
          echo '<div class="Categoria">
        <div class="categoria-header" data-categoria-id=' . $row["IDcategoria"]. '>
            <h1>' . $row['NomeCategoria'] . '</h1>
            <p>' . $row['DescCategoria'] . '</p>
        </div>
        </div>';
        }
        ?>   
      </div>
  </div>
</div>

</body>

</html>