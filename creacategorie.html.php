<?php
    session_start();
	if (!isset($_SESSION['user'])){
		header('Location:login.html.php');
		exit;
    }
    include 'connessione.php';
    $select_categoria = "SELECT * FROM categoria WHERE categoriaPadre	IS NULL";                  
    $stmt_select_categoria = $conn->prepare($select_categoria);
    $stmt_select_categoria->execute();
    $resultQuery=$stmt_select_categoria->get_result();
    $stmt_select_categoria->close();
        ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>creablog</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="homepage.css">
  <link rel="stylesheet" type="text/css" href="creablog.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> 
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
  $(document).ready(function() {
    $('#sottocat').click(function() {                                       //gestire l'evento della checkbox per far apparire le categorie padre
    $("#sottoC").toggle(this.checked);
});
    $("#formCreaCategoria").validate({																			//form per regolare gli input
        rules: {
            Nome:{
                required: true,
                maxlength: 30
            },
            Desc: {
                required: true,
                maxlength: 400
            }
        },
        messages: {
            Nome:{
                required:"Inserire il nome della categoria",
                maxlength:"il nome della categoria può essere lungo massimo 30 caratteri"
            },
            Desc: {
                required: "Inserire la descrizione della categoria",
                maxlength: "La descrizione può essere lunga massimo 400 caratteri"
            }
      }
    }); 
});
</script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<?php include "header.html.php"?>
    <div>
      <div class='dashboard-content'>
        <!--Qui va tutto il contenuto della pagina-->  
        <p> Crea una categoria </p>
        <form id="formCreaCategoria" action="creacategorie.php" method="post">
            <div class="campo">
            <label for="Nome"> Nome: </label>
            <input id="Nome" type="text" name="Nome">
            </div>
            <div class="campo">
            <label for="DescCat"> Descrizione: </label>
            <textarea class="casellatesto" name="Desc" rows="4"></textarea>
            </div>
            <div class="campo">
             <label for="sottocat"> È una sottocategoria?</label>  <input type="checkbox" id="sottocat" name="sottocat" value="sottocat">
            </div>
            <div class="campo" id="sottoC"  style="display:none">
              <label for="SelCategoria">Seleziona la categoria principale: </label>
              <select name="sottocategoria" id="sottocategoria">                                                      
              <option value="">-- Seleziona --</option>
                  <?php
                    while ($row = $resultQuery->fetch_assoc()) {
                      echo '<option value="' . $row['IDcategoria'] . '">' . $row['NomeCategoria'] . '</option>';            //in caso di check, mostiamo le categorie padre 
                  }
                  ?>
            </select> 
            </div>
            <div class="campo">
                <input id="mandacat" type="submit" value="Crea categoria">
            </div>
        </form>  
      </div>
      </div>      
  </div>
</div>
</body>

</html>