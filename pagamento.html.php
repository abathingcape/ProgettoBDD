<?php
  session_start();
  include 'connessione.php';
	if (!isset($_SESSION['user'])) {                                      //facciamo un controllo di sessione, controlliamo effettivamente 
        header('Location:login.html.php');                                  //che un utente abbia fatto l'accesso e che quindi la variabile user di sessione sia
		exit;                                                               //stata inizializzata
    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="pagamento.css">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> 
    <script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
    $(document).ready(function() {
        $.validator.addMethod("nomecognome", function(value) {
        return /^[a-zA-Z]+(\s[a-zA-Z]+)+$/.test(value);             //controlla che ciò che sia stato inserito è parola spazio parola
        });
        $.validator.addMethod("datacheck", function(value) {
        var split = value.split("/")                            //split appena incontriamo una stringa
        var mm = +split[0]
        var yyyy = +split[1]

        if (isNaN(mm) || isNaN(yyyy)) return false;             //controlliamo che i valori inseriti sono delle stringhe

        if (mm < 1 || mm > 13) return false;                    //controlliamo che il mese è tra 1 e 12

        if (yyyy < 99)                                          //controlla se la data è più piccola di 99   
           return false;                      

        var today = new Date();
        if (yyyy < today.getFullYear()) return false;                                   //controlla se la data è nel passato
        if (yyyy === today.getFullYear() && mm < today.getMonth() + 1) return false;

        return true;
    });
        $("#formPagamento").on("submit", function(event) {                  //controllo con jquery
        if($(this).valid()){                                                //con questo valid aspettiamo che tutti i dati nella form siano inseriti e validi
        event.preventDefault();                                         //prevent default ferma l'invio dei dati per controllarli con php                                                         
        var formData=new FormData(this);                                //variabile in cui mettiamo tutti i dati della form
        $.ajax({                                                        //avviamo la nostra chiamata ajax
            type: "POST",                                               //chiamata di tipo post
            url:$(this).attr('action'), 
            data: formData,                                                      //l'url sarà ciò che è contenuto nell'action della form                                          
            success: function(data) {                                   //passiamo i dati alla funzione
                if(data == "OK"){                                       //questo data diventerà la risposta del php, le risposte visualizzate sono quindi scritte nel file php
                    location.replace("index.html.php");                 //il controllo è andato a buon fine, sarà creato l'account e sarai indirizzato all'index
					}else{
                        $("#return_message").show();
                    }
            }
        });
    }
    });
        $("#formPagamento").validate({                                  //jquery validator per controllare che tutti i dati siano inseriti nelle caselle
        rules: {
            nomecognome: {
                required: true,
                nomecognome:true
            },
            numerocarta:{
                required:true,
                maxlength:16,
                minlength:16,
                number: true
            },
            scadenza:{
                required:true,
                datacheck:true
            },
            cvv:{
                required:true,
                maxlength:3,
                minlength:3,
                number:true
            }
            },
            messages: {
            nomecognome: {
                    required: "Inserire il proprio nome e cognome",
                    nomecognome:"Inserire correttamente il proprio nome e cognome"
                },
                numerocarta:{
                    required:"Inserire il proprio numero di carta",
                    maxlength:"Inserire correttamente il proprio numero di carta",
                    minlength:"inserire correttamente il proprio numero di carta",
                    number: "inserire correttamente il proprio numero di carta"
                },
                scadenza:{
                    required:"Inserire la data di scadenza",
                    datacheck:"Inserire correttamente la data di scadenza"
                },
                cvv:{
                    required:"Inserire il cvv",
                    maxlength:"Inserire correttamente il cvv",
                    minlength:"Inserire correttamente il cvv",
                    number:"Inserire correttamente il cvv"
                }
            }
        });
});     
    </script>
    <title>Blog in progress</title>
</head>

<body>
    <div class="container p-0">
        <div class="card px-4">
            <div class="logo text-center mb-5 pt-5">
                <a href="#!">
                  <img src="logoBIP/logo_bianco3.svg" alt="BootstrapBrain Logo" width="400px">
                </a>
            </div>
            <p class="h8 py-3 pt-3 border-top border-white">Premium</p>
            <form  id="formPagamento" action="abbonamento.php">
                <div class="row gx-3 m-3 pb-4">
                    <div class="col-12">
                        <div class="d-flex flex-column">
                            <p class="text mb-1">Nome e Cognome</p>
                            <input name="nomecognome" class="form-control mb-3" type="text">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-column">
                            <p class="text mb-1">Numero di carta</p>
                            <input name="numerocarta" class="form-control mb-3" type="text" placeholder="5555 5555 5555 5555">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex flex-column">
                            <p class="text mb-1">Scadenza</p>
                            <input name="scadenza" class="form-control mb-3" type="text" placeholder="MM/YYYY">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex flex-column">
                            <p class="text mb-1">CVV/CVC</p>
                            <input name="cvv" class="form-control mb-3 pt-2 " type="password" placeholder="***">
                        </div>
                    </div>
                    <div class="col-12">
                        <input type="submit" name="signup" id="signup" class="form-submit" value="Abbonati"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>