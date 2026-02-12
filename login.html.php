<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="csslogin.css">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> 
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
	<script>
$(document).ready(function() {																			
    $("#formLogin").on("submit", function(event) {																	//gestione dell'evento submit della form
    if($(this).valid()){																							//controlla che tutte le validazione di jquery validation siano corrette
        event.preventDefault();																						//blocca l'invio della form
        $("#return_message").hide(); 							
        var formData=new FormData(this);																			//mette i dati della form nella formdata
        $.ajax({
            type: "POST",
            url:$(this).attr('action'),
            processData: false,
			contentType: false,
            data: formData,
            success: function(data) {
                if(data == "OK"){
                    location.replace("index.html.php");																//se la chiamata ajax ha come risposta "OK" allora la registrazione è andata a buon fine
					}else{																							//e l'utente verrà indirizzato nella home
						$("#return_message").show();
                        $("#return_message").css('color', 'red');													//in caso contrario compariranno i messagi di avvertimento 
						$("#return_message").text(data);
					}
            }
        });
    }
});
    $("#formLogin").validate({																			//controlliamo con jquery validation gli input dell'utente
        rules: {
            username:{
                required:true
            },
            password: {
                required: true
            }
        },
        messages: {
            username:{
                required:"Inserire la propria email o il proprio username"
            },
            password: {
                required: "Inserire la password"
            }
        },
		errorPlacement: function(error, element) {         								//restituisce gli errori nell'elemento successico alla cella stessa (ognuno sotto la rispettiva cella)
            if (element.attr("name") == "username") {
                error.insertAfter(element.closest('.form-floating'));
            } else if (element.attr("name") == "password") {
                error.insertAfter(element.closest('.form-floating'));
            } else {
                error.insertAfter(element);
            }
        }
    }); 
});
</script>
    <title>Blog in progress</title>
</head>

<body>
<section>
	<div class="container">
	  <div class="row justify-content-center">
		<div class="col-12 col-xxl-11">
		  <div class="card h1000">
			<div class="row g-0">
			  <div class="sx col-12 col-md-6">
				<div class="logo text-center mb-5 pt-4">
					<a href="#!">
					  <img src="logoBIP/logo_bianco3.svg" alt="BootstrapBrain Logo" width="400px">
					</a>
				  </div>
				<h2 class="text-center text-white pt-3 w-75 mx-auto border-top border-white">Accedi per condividere i tuoi post e esplorare nuovi contenuti. Siamo felici di averti qui!</h2>		
			  </div>
			  <div class="dx col-12 col-md-6 d-flex align-items-center justify-content-center">
				<div class="col-12 col-lg-11 col-xl-10">
				  <div class="card-body p-3 p-md-4 p-xl-5">
					<div class="row">
					  <div class="col-12">
						<div class="mb-5 mt-5">
							<div class="accedi">
								<h4>Accedi</h4>
							</div>
						</div>
					  </div>
					</div>
					<form id="formLogin" action="login.php" method="post">                    <!--la form con cui prenderemo i dati forniti dall'utente nel login-->
					  <div class="row gy-3 overflow-hidden">
						<div class="col-12 mt-4">
						  <div class="form-floating border-bottom-0 error-placement">
							<input type="text" class="form-control border-0" name="username" id="username" placeholder="name@example.com" requierd>
						  </div>
						</div>
						<div class="col-12 mt-4">
						  <div class="form-floating border-bottom-0 error-placement">
							<input type="password" class="form-control border-0 " name="password" id="password" value="" placeholder="Password" required>
						  </div>
						</div>
						<p id="return_message"></p>
						<div class="col-12">
						  <div class="d-grid">
							<button class="btn btn-lg mt-5 " type="submit" id="btnlogin">Login</button>
						  </div>
						</div>
					  </div>
					</form>
					<div class="row mb-5">
					  <div class="col-12">
						<div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-center mt-4 mb-4">
						  <a href="registrazione.html.php" class="link-secondary text-decoration-none text-white-50">Crea un nuovo account</a>
						  <a href="recupero.html.php" class="link-secondary text-decoration-none text-white-50">Password dimenticata?</a>
						</div>
					  </div>
					</div>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </section>
</body>
</html>