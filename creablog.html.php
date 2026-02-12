<?php
    session_start();
	  if (!isset($_SESSION['user'])){
		  header('Location:login.html.php');
		  exit;
    }
    include 'connessione.php';
    $select_categoria = "SELECT * FROM categoria";                        //query per vedere tutte le categorie nella piattaforma
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
    $('#categoria').change(function(){
    adjustSize($(this));
  });
    $("#cerca").keyup(function(){                                     //tutto ciò che succede per cercare un coautore da aggiungere nel blog
      query=this.value;
      if (query.length>0){
        $.ajax({
          type: "GET",
          url: "ricercautente.php",
          data: {q:query}, 
          dataType:"json",
          success: function(data){
            if (data.length>0){                                         // se i risultati sono maggiori di 1 mostra la barra delle risposte e nasconde il messaggio di avvertimento 
            $("#return_message").hide();
            $(".lista").show();
            if (data.length==1){                                        // vari if per gestire i vari risultati nella barra di ricerca per igni caso, ogni caso scorro l'array fornito da json per assegnare i valori alle risposte visualizzate
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
          else {                                                        //quando non ottengo alcun risultato dalla ricerca mostra il messaggio di avvertimento 
            $("#IDutente").val("");
            $("#return_message").show();
            $(".lista").hide();
          }
        }
        });
      }else{
        $(".lista").hide();                                     //in caso non ci sia niente nella barra di ricerca, rimuove la lista delle ripsoste
        $("#return_message").hide();                                // e rimuove il messaggio di avvertimento 
      }
      });
      $(document).on('click', function(event){                                  //per far si che quando clicco fuori dalla barra di ricerca scompaia

        if(!event.target.closest('.lista')){                                    //tranne per il caso che io stia selezionando un elemento della lista

        $(".lista").hide();
  }
});
    $(document).on('click', '.primo_risultato', function() {
      var valoreprimorisultato=$(".primo_risultato").html();                    //click event per gestire i click dei risultati
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
    $('#imageInput').on('change', function() {                                                     //permette la sostituzione della foto profilo
        var inputFile = document.getElementById('imageInput');
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
    $.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
    });
    $("#formCreaBlog").validate({																			//jquery validation per verifiare che i campi siano compilati correttamente
        rules: {
            titolo:{
                required: true,
                maxlength: 50
            },
            desc: {
                required: true,
                maxlength: 500
            },
            immagine: {
                extension: "jpg|jpeg|png",                         
                filesize: 2097152
              },
            categoria:{
                required: true

            }  

        },
        messages: {
            titolo:{
                required:"Inserire il titolo del blog",
                maxlength: "Il titolo del blog può essere lungo massimo 50 caratteri"
            },
            desc: {
                required: "Inserire la descrizione del blog",
                maxlength: "La descrizione del blog può essere lunga massimo 500 caratteri"
            },
            immagine: {
                extension: "Inserire un file .jpg|.jpeg.|.png",                        
                filesize: "Grandezza massima 2 MB"
              },
            categoria:{
                required: "Deve appartenere almeno ad una categoria"

            }  
      }
    }); 
});
function adjustSize(selectElement) {
  // Imposta temporaneamente la misura del menu a discesa in base al numero di opzioni
  selectElement.prop('size', selectElement.find('option').length);
  
  // Rimuove il focus per far sì che la misura del menu a discesa torni a 1
  selectElement.blur();
}
</script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<?php include "header.html.php"?>
    <div>
      <div class='dashboard-content'>
        <p> Crea il tuo blog </p>
        <form id="formCreaBlog" action="creablog.php" method="post" enctype="multipart/form-data">                     <!--Form per avere i dati per i blog--> <!--enctype per includere piu tipi di file, in questo caso immagini-->
          <div class="campo">
            <label for="Titolo"> Titolo: </label>
            <input id="Titolo" type="text" name="titolo">
          </div>
          <div class="campo">
            <label for="DescBlog"> Descrizione: </label>
            <textarea class="casellatesto" name="desc" rows="4"></textarea>
          </div>
          <div class="campo">
            <label for="imageInput">Inserisci un immagine:</label>
            <input type="file" class="inseriscimmagine" id="imageInput" name="immagine" accept="image/*">
            <p id="error_message" hidden></p>
          </div>
          <div class="campo">
            <label for="SelCategoria">Seleziona una categoria: </label> 
            <select name="categoria" id="categoria" onfocus="this.size = 8" onchange="this.blur()" onblur="this.size = 1; this.blur()">
              <option value="">-- Seleziona --</option>
                  <?php
                    while ($row = $resultQuery->fetch_assoc()) {                                                              //stampo tutte le categorie selezionabili in cui può essere inserito il blog
                      echo '<option value="' . $row['IDcategoria'] . '">' . $row['NomeCategoria'] . '</option>';
                  }
                  ?>
            </select>
          </div>
          <div class="campo" id="ricerca">
          <label for="ricerca">Seleziona un coautore:</label>
            <div class="ricerca">
              <input type="text" hint="cerca" id="cerca"></input>
            </div>
            <div class="result">
              <input type="hidden" id="IDutente" name="IDutente" value="">                                           
                <ul class="lista" hidden="true">
                  <li class="primo_risultato" hidden="true"></li>
                  <li class="secondo_risultato" hidden="true"></li>
                  <li class="terzo_risultato" hidden="true"></li>
                </ul>
                <p id="return_message" hidden="true">Utente non trovato</p>
            </div>
          </div>
          <div class="campo">
          <input id="mandablog" type="submit" value="Crea blog">
          </div>
        </form>  
      </div>
      </div>      
  </div>
</div>
</body>

</html>