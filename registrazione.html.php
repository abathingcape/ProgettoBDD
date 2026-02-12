<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="bloginprogress.css">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrazione</title>
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> 
    <script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script> 
    <!--<script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>-->
    <script>
$(document).ready(function() {
    $.validator.addMethod("cognome", function(value) {
        return /^[a-zA-Z]+(\s[a-zA-Z]+)*$/.test(value);             //in caso il cognome sia di più parole
        });
    $.validator.addMethod("pwcheck", function(value) {                  // validatore per controllare la complessità della password
        return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value)                    // possibili caratteri inseribili
        && /[a-z]/.test(value)                                           // ha una lettera
        && /\d/.test(value)                                              // ha un numero
        && /[A-Z]/.test(value)
    });
$("#formRegistrazione").on("submit", function(event) {                  //controllo con jquery
    if($(this).valid()){                                                //con questo valid aspettiamo che tutti i dati nella form siano inseriti e validi
        event.preventDefault();                                         //prevent default ferma l'invio dei dati per controllarli con php
        $("#return_message").hide();                                    
        var formData=new FormData(this);                                //variabile in cui mettiamo tutti i dati della form
        $.ajax({                                                        //avviamo la nostra chiamata ajax
            type: "POST",                                               //chiamata di tipo post
            url:$(this).attr('action'),                                 //l'url sarà ciò che è contenuto nell'action della form
            processData: false,                                         
			contentType: false,
            data: formData,                                             //data sono i dati che passeremo al php, in questo caso tutto contenuto in formData
            success: function(data) {                                   //passiamo i dati alla funzione
                if(data == "OK"){                                       //questo data diventerà la risposta del php, le risposte visualizzate sono quindi scritte nel file php
                    location.replace("index.html.php");                 //il controllo è andato a buon fine, sarà creato l'account e sarai indirizzato all'index
					} else{
						$("#return_message").show();                    //il controllo non è andato a buon fine, verranno fatti comparire i messaggi d'errore e non ci sarà nessun indirizzamento o dato salvato
                        $("#return_message").css('color', 'red');
						$("#return_message").text(data);
					}
            }
        });
    }
});
    
    $("#formRegistrazione").validate({                                  //jquery validator per controllare che tutti i dati siano inseriti nelle caselle
        rules: {
            nome: {
                required: true,
                lettersonly: true
            },
            cognome: {
                required: true,
                cognome: true 
            },
            username:{
                required:true,
                maxlength:20,
                minlength:4,
            },
            email: {
                required: true,
            },
            password: {
                pwcheck: true,
                required: true,
                minlength: 7
            },
            re_pass: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            nome: {
                required: "Inserire il proprio nome",
                lettersonly: "Inserire correttamente il proprio nome"
            },
            cognome: {
                required: "Inserire il proprio cognome",
                cognome:"inserire correttamente il proprio cognome"
            },
            username:{
                required:"Inserire il proprio username",
                maxlength:"L'username può contenere al massimo 20 caratteri",
                minlength:"l'username deve contenere almeno 4 caratteri"
            },
            email: {
                required: "Inserire l'indirizzo email",
            },
            password: {
                pwcheck: "La password deve essere possedere almeno: un numero, un carattere minuscolo e un carattere maiuscolo",
                required: "Inserire la password",
                minlength: "La password deve essere lunga almeno 7 caratteri"
            },
            re_pass: {
                required: "Inserire la conferma della password",
                equalTo: "Le password non corrispondono"
            }
        }
    });    
  });

</script> 
</head>

<body>

    <div class="main">
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <form method="POST" class="register-form" action="registrazione.php" id="formRegistrazione">
                            <h4 style="text-align: center; margin-bottom: 50px; color: white;">Registrati</h4>
                            <div class="form-group">
                                <label for="nome"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="nome" id="nome" placeholder="Nome"/>
                            </div>
                            <div class="form-group">
                                <label for="cognome"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="cognome" id="cognome" placeholder="Cognome"/>
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="text" name="email" id="email" placeholder="Email"/>
                            </div>
                            <div class="form-group">
                                <label for="username"><i class="zmdi zmdi-email"></i></label>
                                <input type="text" name="username" id="username" placeholder="Username"/>
                            </div>
                            <div class="form-group">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="Password"/>
                            </div>
                            <div class="form-group">
                                <label for="ConfermaPassword"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="re_pass" id="re_pass" placeholder="Ripeti la password"/>
                            </div>
                            <p id="return_message"></p>
                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="signup" class="form-submit" value="Crea account"/>
                            </div>
                            <div class="tornalogin">
                                Hai gia un'account? <a href="login.html.php">Accedi</a>
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <img src="logoBIP/logo_bianco3.svg" alt="BootstrapBrain Logo" width="400px">
                        
                        <div class="timeline">
                            <h5 style="text-align: center; padding: 5px;">Iscriviti a questo blog e potrai:</h5>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <h2>Creare post e blog</h2>
                                    <p>Puoi creare blog e post riguardo qualsiasi argomento. Crea categorie ed aiutaci a far crescere il blog</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <h2>Cooperare con altri utenti</h2>
                                    <p>Collabora con altri utenti nella creazione di post, confronta le tue idee e mettiti alla prova</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <h2>Ricevere premi</h2>
                                    <p>Ogni contributo all'interno del blog ti permetterà di salire di livello ricevendo dei fantastici riconoscimenti</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

</body>
