<?php
    session_start();
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
		header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
		}else{
    include 'connessione.php';
    $select_idutente = "SELECT Nome, Cognome, Username, Email FROM utenti WHERE IDutente = ? LIMIT 1";                  //prendiamo i dati del profilo che stiamo modificando
    $stmt_select_idutente = $conn->prepare($select_idutente);
    $stmt_select_idutente->bind_param("i", $_SESSION["user"]);
    $stmt_select_idutente->execute();
    $resultQuery=$stmt_select_idutente->get_result();
    $fetch_ass=$resultQuery->fetch_assoc();
    $stmt_select_idutente->close();
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>homepage</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="homepage.css">
  <link rel="stylesheet" type="text/css" href="creablog.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script>
    $(document).ready(function() {
        $.validator.addMethod("cognome", function(value) {
        return /^[a-zA-Z]+(\s[a-zA-Z]+)*$/.test(value);             //controlla che ciò che sia stato inserito è parola spazio parola
        });
        $("#formModificaProfilo").validate({                                  //jquery validator per controllare che tutti i dati siano inseriti nelle celle
        rules: {
            Nome: {
                required: true
            },
            Cognome: {
                required: true,
                cognome: true
            },
            Username:{
                required:true,
                maxlength:20,
                minlength:4
            },
            Email: {
                required: true
            }
        },
        messages: {
            Nome: {
                required: "Inserire il proprio nome"
            },
            Cognome: {
                required: "Inserire il proprio cognome",
                cognome: "Inserire correttamente il proprio cognome"
            },
            Username:{
                required:"Inserire il proprio username",
                maxlength:"L'username può contenere al massimo 20 caratteri",
                minlength:"l'username deve contenere almeno 4 caratteri"
            },
            Email: {
                required: "Inserire l'indirizzo email"
            }
        }
    }); 
        $("#formModificaProfilo").on("submit", function(event) {                  //controllo con jquery                                             //con questo valid aspettiamo che tutti i dati nella form siano inseriti e validi
        if($(this).valid()){
            event.preventDefault();                                         //prevent default ferma l'invio dei dati per controllarli con php                                    
            var formData=new FormData(this);                                //variabile in cui mettiamo tutti i dati della form
            $.ajax({                                                        //avviamo la nostra chiamata ajax
                type: "POST",                                               //chiamata di tipo post
                url:$(this).attr('action'),                                 //l'url sarà ciò che è contenuto nell'action della form
                processData: false,                                         
                contentType: false,
                data: formData,                                             //data sono i dati che passeremo al php, in questo caso tutto contenuto in formData
                success: function(data) {                                   //passiamo i dati alla funzione
                    if(data == "OK"){                                       //questo data diventerà la risposta del php, le risposte visualizzate sono quindi scritte nel file php
                        location.replace("profilo.html.php");                 //il controllo è andato a buon fine, sarà creato l'account e sarai indirizzato all'index
                        } else{
                            $("#return_message").show();                    //il controllo non è andato a buon fine, verranno fatti comparire i messaggi d'errore e non ci sarà nessun indirizzamento o dato salvato
                            $("#return_message").css('color', 'red');
                            $("#return_message").text(data);
                        }
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
        <!--Qui va tutto il contenuto della pagina-->  
        <p> Modifica il tuo profilo </p>
        <form id="formModificaProfilo" action="modificaprofilo.php" method="post" enctype="multipart/form-data">
            <?php echo'
            <div class="campo">
            <label for="Nome"> Nome: </label>
            <input id="Nome" type="text" name="Nome" value="' . $fetch_ass["Nome"] . '">
            </div>
            <div class="campo">
            <label for="Cognome"> Cognome: </label>
            <input type="text" id ="Cognome" name="Cognome" value="' . $fetch_ass["Cognome"] . '">
            </div>
            <div class="campo">
            <label for="Email"> Email: </label>
            <input type="text" id ="Email" name="Email" value="' . $fetch_ass["Email"] . '">
            </div>
            <div class="campo">
            <label for="Username"> Username: </label>
            <input type="text" id ="Username" name="Username" value="' . $fetch_ass["Username"] . '">
            </div>';
            ?>
            <p id="return_message" hidden="true"></p>
            <div class="campo">
                <input id="ModProfilo" type="submit" value="Modifica">
            </div>
        </form>
      </div>
  </div>
</div>

</body>

</html>