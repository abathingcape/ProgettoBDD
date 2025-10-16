<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="csslogin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupero Password</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script> 
    <script>
        $(document).ready(function() {
            $("#formRecupero").on("submit", function(event) {																	       		
                if($(this).valid()){                                            //con questo valid aspettiamo che tutti i dati nella form siano inseriti e validi
                event.preventDefault();                                         //prevent default ferma l'invio dei dati per controllarli con php
                $("#return_message").hide();                                    
                var formData=new FormData(this);                                //variabile in cui mettiamo tutti i dati della form
                $.ajax({                                                        //avviamo la nostra chiamata ajax
                    type: "POST",                                               //chiamata di tipo post
                    url:$(this).attr('action'),                                 //l'url sarà ciò che è contenuto nell'action della form
                    processData: false,                                         
                    contentType: false,
                    data: formData,                                             //data sono i dati che passeremo al php, in questo caso tutto contenuto in formData
                    success: function(data) {                                   //in caso positivo la password sarà cambiata, in caso negativo comparità un messaggio di errore
                        if(data == "OK"){                                       
                            location.replace("login.html.php");                 
                            } else{
                                $("#return_message").show();                    
                                $("#return_message").css('color', 'red');
                                $("#return_message").text(data);
                            }
                    }
                });
                }
            });   
            $.validator.addMethod("pwcheck", function(value) {                  // validatore per controllare la complessità della password
            return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value)                       // possibili caratteri inseribili
            && /[a-z]/.test(value)                                              // ha una lettera
            && /\d/.test(value)                                                 // ha un numero
            && /[A-Z]/.test(value)
            });

            $("#formRecupero").validate({								        //controlla i dati della form
                rules: {
                    email:{
                        required:true
                    },
                    new_password: {
                        required: true,
                        pwcheck: true,
                    },
                    confirmPassword:{
                        equalTo: "#password"
                    }
                },
                messages: {
                    email:{
                        required:"Inserire la propria email"
                    },
                    new_password: {
                        required: "Inserire la nuova password",
                        pwcheck: "La password deve essere possedere almeno: un numero, un carattere minuscolo e un carattere maiuscolo"
                    },
                    confirmPassword:{
                        equalTo:"Le due password non corrispondono"
                    }
                }
            }); 
        });
</script>
</head>

<body class="corpo">

    <div class="box">
        <div class="logorecupero">
            <img src="logoBIP/logo_bianco3.svg" alt="Logo" width="400px">
        </div>
        <h4 class="recupero">Recupero password</h4>
        <form action="recupero.php" method="post" id="formRecupero" style="padding: 30px 40px;">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" >
            <p id="return_message" hidden></p>
            <label for="password">Nuova Password:</label>
            <input type="password" id="password" name="new_password">

            <label for="confirmPassword">Ripeti Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword"> 

            <button type="submit">Conferma</button>
        </form>
    </div>

</body>
</html>