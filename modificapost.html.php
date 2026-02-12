<?php
  session_start();
  include 'connessione.php';
  if ((isset($_GET["id_ut"])) and $_GET["id_ut"]==$_SESSION["user"]){
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		}
    else{
      if (isset($_GET['id_post']) && is_numeric($_GET['id_post']))          //controlliamo che l'id del blog sia stato madato via url (se stiam cercando effettivamente un blog)
        $id_p= $_GET['id_post'];
      $select_post = "SELECT TitoloPost, DescPost
                      FROM post
                      WHERE IdPost= ? AND IdUt = ?
                      LIMIT 1";
      $stmt_post=$conn->prepare($select_post);
      $stmt_post->bind_param("ii",$id_p, $_SESSION["user"]);                                    /*la query restutuisce i dati già presenti nel blog*/
      $stmt_post->execute();
      $resultQuery=$stmt_post->get_result();
      $stmt_post -> close();

      if ($resultQuery->num_rows == 0) {
        header("location:errorpage.html.php");
        exit();
      }


      $fetch_ass = $resultQuery -> fetch_assoc();
      $descpost=$fetch_ass["DescPost"]; 
    }
  }else
     header('Location:index.html.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Post</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> 
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" type="text/css" href="homepage.css">
  <link rel="stylesheet" type="text/css" href="creablog.css">
  <script>
  $(document).ready(function() {
    $.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)                     //leggiamo la grandezza del file inserito, deve essere minore del valore impostato a filesize (2Mb)
    });
    $("#formModificaPost").validate({																			                //validazione degli input con jquery validator, inserito nella form
        rules: {                                                      
            Titolo:{
              required: true,
              maxlength:50
            },
            DescPost:{
              required: true,
              maxlength:1500
            },
            FotoPost1: {
                extension: "jpg|jpeg|png",                          
                filesize: 2097152
            },
            FotoPost2: {
                extension: "jpg|jpeg|png",                        
                filesize: 2097152
            }

        },
        messages: {
          Titolo:{
              required: "Inserire un titolo",
              maxlength: "Il titolo può essere lungo massimo 50 caratteri"
            },
            DescPost:{
              required: "Inserire una descrizione",
              maxlength: "La descrizione del post può essere lunga massimo 1500 caratteri"
            },
            FotoPost1: {
                extension: "Inserire un file .jpg|.jpeg.|.png",                          
                filesize: "Grandezza massima 2 MB"
            },
            FotoPost2: {
                extension: "Inserire un file .jpg|.jpeg.|.png",                           //tipi di file supportati
                filesize: "Grandezza massima 2 MB"
            }
      }
    }); 
    $('#FotoPost1').on('change', function() {                                                     //permette la sostituzione della foto profilo
        var inputFile = document.getElementById('FotoPost1');
        var file=inputFile.files[0];                                                                    //.files prende il contenuto all'elemento 
        if (file) {
          var formData = new FormData();
          formData.append('file', file); 
          $.ajax({                                                                                      //avviamo la nostra chiamata ajax
                type: "POST",                                                                           //chiamata di tipo post
                url: 'controllafoto.php',                                                                //il contenuto della form sarà la voto che verrà caricata
                contentType: false,  
                processData: false,                                                                          //data sono i dati che passeremo al php, in questo caso tutto contenuto in formData
                data: formData,
                success: function(data) {                                                              
                        if (data == "Il file esiste già."){
                        $("#error_message1").html(data);
                        $("#error_message1").show();                  
                        $("#error_message1").css('color', 'red');
                        $("#error_message1").css("font-size","20px");
                      }else $("#error_message1").hide();  
                }
            });
        }
      });
      $('#FotoPost2').on('change', function() {                                                     //permette la sostituzione della foto profilo
        var inputFile = document.getElementById('FotoPost2');
        var file=inputFile.files[0];                                                                    //.files prende il contenuto all'elemento 
        if (file) {
          var formData = new FormData();
          formData.append('file', file); 
          $.ajax({                                                                                      //avviamo la nostra chiamata ajax
                type: "POST",                                                                           //chiamata di tipo post
                url: 'controllafoto.php',                                                                //il contenuto della form sarà la voto che verrà caricata
                contentType: false,  
                processData: false,                                                                          //data sono i dati che passeremo al php, in questo caso tutto contenuto in formData
                data: formData,
                success: function(data) {                                                              
                        if (data == "Il file esiste già."){
                        $("#error_message2").html(data);
                        $("#error_message2").show();                  
                        $("#error_message2").css('color', 'red');
                        $("#error_message2").css("font-size","20px");
                        }else $("#error_message2").hide();   
                }
            });
        }
      });
});
  </script>
</head>

<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
        <!--qui andranno stelle e commenti-->
        <p> Modifica il post </p>
        <form id="formModificaPost" action="modificapost.php?id_post=<?php echo $id_p . '&id_ut=' . $_GET["id_ut"];?>" method="post" enctype="multipart/form-data">     <!--form che raccoglie tutti i dati-->
            
            <div class="campo">                                                                                                           
            <label for="Titolo"> Titolo post: </label>  
            <input id="Titolo" type="text" name="Titolo" value="<?php echo'' . $fetch_ass["TitoloPost"] . '';?>">
            </div>
            <div class="campo">
            <label for="Descrizione"> Descrizione: </label>
            <textarea id ="DescPost" class="casellatesto" name="DescPost" rows="10"> <?php echo $descpost;?></textarea>
            </div>
            <div class="campo">
            <input type="file" id ="FotoPost1" name="FotoPost1">
            <p id="error_message1" hidden></p>
            </div>
            <div class="campo">
            <input type="file" id ="FotoPost2" name="FotoPost2">
            <p id="error_message2" hidden></p>                    
            </div>                                                                                                  <!--al caricamento della pagina saranno già visibili tutti i dati modificabili -->
            <div class="campo">
                <input id="ModProfilo" type="submit" value="Modifica post">
            </div>
      </div>
  </div>
</div>

</body>

</html>