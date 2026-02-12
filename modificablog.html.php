<?php
  session_start();
  include 'connessione.php';
  if ((isset($_GET["id_ut"])) and $_GET["id_ut"]==$_SESSION["user"]){
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:index.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		}
    else{
      if (isset($_GET['id_blog']) && is_numeric($_GET['id_blog']))      //controlliamo che l'id del blog sia stato madato via url (se stiam cercando effettivamente un blog)
        $id_blog= $_GET['id_blog'];
      $select_blog = "SELECT Titolo, DescBlog
                        FROM blog                                       /*la query restutuisce i dati già presenti nel blog*/
                        WHERE IdBlog= ? AND IdAutore = ?";
      $stmt_blog=$conn->prepare($select_blog);
      $stmt_blog->bind_param("ii",$id_blog, $_SESSION["user"]);
      $stmt_blog->execute();
      $resultQuery=$stmt_blog->get_result();
      $stmt_blog -> close();

      if ($resultQuery->num_rows == 0) {
        header("location:errorpage.html.php");
        exit();
      }
      $fetch_ass = $resultQuery -> fetch_assoc();
    }
  }else
     header('Location:index.html.php');                                 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Blog</title>
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
    $.validator.addMethod('filesize', function (value, element, param) {                        //leggiamo la grandezza del file inserito, deve essere minore del valore impostato a filesize (2Mb)
    return this.optional(element) || (element.files[0].size <= param)
    });

    $("#cerca").keyup(function(){                                                               //la stessa barra di ricerca presente in creablog, serve a cercare il coautore                                                                 
      query=this.value;
      if (query.length>0){
        $.ajax({
          type: "GET",
          url: "ricercautente.php",
          data: {q:query}, 
          dataType:"json",
          success: function(data){
            if (data.length>0){
            $("#return_message").hide();
            $(".lista").show();
            if (data.length==1){
              $(".primo_risultato").show();
              $(".primo_risultato").html(data[0]['username']);
              $(".primo_risultato").val(data[0]['id']);

              $(".secondo_risultato").hide();
              $(".terzo_risultato").hide();

              $(".secondo_risultato").html("");
              $(".terzo_risultato").html("");

            }else if (data.length==2){
              $(".primo_risultato").show();
              $(".secondo_risultato").show();

              $(".primo_risultato").html(data[0]['username']);
              $(".primo_risultato").val(data[0]['id']);

              $(".secondo_risultato").html(data[1]['username']);
              $(".secondo_risultato").val(data[1]['id']);

              $(".terzo_risultato").hide();
              $(".terzo_risultato").html("");

            }else if (data.length==3){

              $(".primo_risultato").show();
              $(".secondo_risultato").show();
              $(".terzo_risultato").show();

              $(".primo_risultato").html(data[0]['username']);
              $(".primo_risultato").val(data[0]['id']);

              $(".secondo_risultato").html(data[1]['username']);
              $(".secondo_risultato").val(data[1]['id']);

              $(".terzo_risultato").html(data[2]['username']);
              $(".terzo_risultato").val(data[2]['id']);
            }
          }
          else {
            $("#IDutente").val("");
            $("#return_message").show();
            $(".lista").hide();
          }
        }
        });
      }else{
        $(".lista").hide();
        $("#return_message").hide();
      }
      });
      $(document).on('click', function(event){  
        if(!event.target.closest('.lista')){
        $(".lista").hide();
  }
});
    $(document).on('click', '.primo_risultato', function() {                                    //stessa gestione delle riposte della lista di creablog
      var valoreprimorisultato=$(".primo_risultato").html();
      var valoreprimoid=$(".primo_risultato").val();
      $("#cerca").val(valoreprimorisultato); 
      $("#IDutente").val(valoreprimoid);
      $(".lista").hide();
    });
    $(document).on('click', '.secondo_risultato', function() {
      var valoresecondorisultato=$(".secondo_risultato").html();
      var valoresecondoid=$(".secondo_risultato").val();
      $("#cerca").val(valoresecondorisultato);
      $("#IDutente").val(valoresecondoid);
      $(".lista").hide();
    });
    $(document).on('click', '.terzo_risultato', function() {
      var valoreterzorisultato=$(".terzo_risultato").html();
      var valoreterzoid=$(".terzo_risultato").val();
      $("#cerca").val(valoreterzorisultato);
      $("#IDutente").val(valoreterzoid);
      $(".lista").hide();
    });

    $('#FotoBlog').on('change', function() {                                                     //permette la sostituzione della foto profilo
        var inputFile = document.getElementById('FotoBlog');
        var file=inputFile.files[0];                                                                    //.files prende il contenuto all'elemento 
        if (file) {
          var formData = new FormData();                                                                //controlliamo che il file inserito non esista già nel database
          formData.append('file', file); 
          $.ajax({                                                                                      //avviamo la nostra chiamata ajax
                type: "POST",                                                                           //chiamata di tipo post
                url: 'controllafoto.php',                                                                //il contenuto della form sarà la voto che verrà caricata
                contentType: false,  
                processData: false,                                                                          //data sono i dati che passeremo al php, in questo caso tutto contenuto in formData
                data: formData,
                success: function(data) {                                                              
                        if (data == "Il file esiste già."){
                        $("#error_message").html(data);
                        $("#error_message").show();                  
                        $("#error_message").css('color', 'red');
                        $("#error_message").css("font-size","20px");
                        }else $("#error_message").hide();
                }
            });
        }
      });

      $("#formModificaBlog").validate({																			//jquery validation per verifiare che i campi siano compilati correttamente
        rules: {
            Titolo:{
                required: true,
                maxlength: 50
            },
            DescBlog: {
                required: true,
                maxlength: 500
            },
            FotoBlog: {
                extension: "jpg|jpeg|png",                         
                filesize: 209715
        },
      },
        messages: {
            Titolo:{
                required:"Inserire il titolo del blog",
                maxlength: "Il titolo del blog può essere lungo massimo 50 caratteri"
            },
            DescBlog: {
                required: "Inserire la descrizione del blog",
                maxlength: "La descrizione del blog può essere lunga massimo 500 caratteri"
            },
            FotoBlog: {
                extension: "Inserire un file .jpg|.jpeg.|.png",                        
                filesize: "Grandezza massima 2 MB"

      }
    }
    }); 
  });
  </script>
</head>

<body>

<?php include "header.html.php"?>

      <div class='dashboard-content'>
        <p> Modifica il blog </p>
        <form id="formModificaBlog" action="modificablog.php?id_blog=<?php echo $id_blog . '&id_ut=' . $_GET["id_ut"];?>" method="post" enctype="multipart/form-data">  <!--form che raccoglie tutti i dati-->                                                                                                                                                
            <div class="campo">
            <label for="Titolo"> Titolo Blog: </label>                                                                                                                
            <input id="Titolo" type="text" name="Titolo" value="<?php echo'' . $fetch_ass["Titolo"] . '';?>">
            </div>
            <div class="campo">
            <label for="DescBlog"> Descrizione: </label>
            <textarea id ="DescBlog" class="casellatesto" name="DescBlog" rows="4"> <?php echo''. $fetch_ass["Titolo"] . '';?></textarea>
            </div>
            <div class="campo">
            <input type="file" id ="FotoBlog" name="FotoBlog">
            </div>
            <p id="error_message" hidden></p>                                                                                                                                   <!--al caricamento della pagina saranno già visibili tutti i dati modificabili */-->
            <div class="campo" id="ricerca">
          <label for="ricerca">Aggiungi un coautore:</label>
            <div class="ricerca">
              <input type="text" hint="cerca" id="cerca"></input>
            </div>
            <div class="result">
              <input type="hidden" id="IDutente" name="IDutente" value="" >
                <ul class="lista" hidden="true">
                  <li class="primo_risultato" hidden="true"></li>
                  <li class="secondo_risultato" hidden="true"></li>
                  <li class="terzo_risultato" hidden="true"></li>
                </ul>
                <p id="return_message" hidden="true">Utente non trovato</p>
            </div>
            <div class="campo">
                <input id="ModBlog" type="submit" value="Modifica blog">
            </div>
          </form>
      </div>
  </div>
</div>

</body>

</html>