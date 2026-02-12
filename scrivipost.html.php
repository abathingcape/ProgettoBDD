<?php
    session_start();
	  if (!isset($_SESSION['user'])){
		  header('Location:login.html.php');
		  exit;
    }
    
    include 'connessione.php';
    $select_blog = "SELECT * FROM blog WHERE IdAutore= ?";                                                              //cerca i blog dell'autore che sta scrivendo il post                                            
    $stmt_select_blog = $conn->prepare($select_blog);
    $stmt_select_blog->bind_param("i", $_SESSION["user"]);
    $stmt_select_blog->execute();
    $resultQuery1=$stmt_select_blog->get_result();
    $stmt_select_blog->close();


    $select_coaut = "SELECT blog.Titolo, blog.IdBlog FROM blog INNER JOIN cogestori ON blog.IdBlog=cogestori.IDblog       /*cerca i blog in cui l'utente è coautore*/
                      WHERE cogestori.IDcoautore= ?";       
    $stmt_select_coaut = $conn->prepare($select_coaut);
    $stmt_select_coaut->bind_param("i", $_SESSION["user"]);
    $stmt_select_coaut->execute();
    $resultQuery2=$stmt_select_coaut->get_result();
    $stmt_select_coaut->close();
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
    $.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
});
    $("#formCreaPost").validate({																			//jquery validator verifica i dati della form
        rules: {
            titolo:{
                required: true,
                maxlength: 50
            },
            testo: {
                required: true,
                maxlength: 1500
            },
            immagine1: {
                extension: "jpg|jpeg|png",                       
                filesize: 2097152
            },
            immagine2: {
                extension: "jpg|jpeg|png",                         
                filesize: 2097152
            },
            blog:{
                required: true
            }  

        },
        messages: {
            titolo:{
                required:"Inserire il titolo del blog",
                maxlength: "Il titolo del post può essere lungo massimo 50 caratteri"
            },
            testo: {
                required: "Inserire la descrizione del blog",
                maxlength: "La descrizione del post può essere lunga massimo 1500 caratteri"
            },
            immagine1: {
                extension: "Inserire un file .jpg|.jpeg.|.png",                         
                filesize: "Grandezza massima 2 MB"
            },
            immagine2: {
                extension: "Inserire un file .jpg|.jpeg.|.png",                          
                filesize: "Grandezza massima 2 MB"
            },
            blog:{
                required: "Deve appartenere almeno ad un blog"

            }  
      }
    }); 
    $('#imageInput1').on('change', function() {                                                     //permette la sostituzione della foto profilo
        var inputFile = document.getElementById('imageInput1');
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
      $('#imageInput2').on('change', function() {                                                     //permette la sostituzione della foto profilo
        var inputFile = document.getElementById('imageInput2');
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<?php include "header.html.php"?>
    <div>
      <div class='dashboard-content'> 
        <p> Scrivi un post </p>
        <form id="formCreaPost" action="scrivipost.php" method="post" enctype="multipart/form-data">
          <div class="campo">
            <label for="Titolo"> Titolo: </label>
            <input id="Titolo" type="text" name="titolo">
          </div>
          <div class="campo">
            <label for="testopost"> Testo: </label>
            <textarea class="casellatesto" name="testo" rows="10"></textarea>
          </div>
          <div class="campo">
            <label for="imageInput">Inserisci le immagini:</label>
            <input type="file" class="inseriscimmagine" id="imageInput1" name="immagine1" accept="image/*">
            <p id="error_message1" hidden></p>
          </div>
          <div class="campo">
            <input type="file" class="inseriscimmagine" id="imageInput2" name="immagine2" accept="image/*">
            <p id="error_message2" hidden></p>
          </div>
          <div class="campo">
            <label for="blog">In quale blog? </label> 
            <select name="blog" id="blog">
              <option value="">-- Seleziona --</option>
                <?php
                  while ($row = $resultQuery1->fetch_assoc()) {                                                         //stampa i blog dell'utente che sta scrivendo il post
                    echo '<option value="' . $row['IdBlog'] . '">' . $row['Titolo'] . '</option>';
                  }
                  while ($row2 = $resultQuery2->fetch_assoc()) {
                    echo '<option value="' . $row2['IdBlog'] . '">' . $row2['Titolo'] . '</option>';                    //stampa i blog in cui, l'utente che sta scrivendo il post, è coautore
                  }
                ?>
            </select>
          </div>
          <div class="campo">
          <input id="mandablog" type="submit" value="Crea Post">
          </div>
        </form>  
      </div>
      </div>      
  </div>
</div>
</body>

</html>