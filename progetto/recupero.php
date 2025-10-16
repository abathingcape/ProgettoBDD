<?php
session_start();
include 'connessione.php';

$email=$_POST["email"];
$password=$_POST["new_password"];
$password_hashed=password_hash($password, PASSWORD_BCRYPT);

$sql_select_mail = "SELECT Email FROM utenti WHERE Email = ?";                          //query per controllare se la mail è già presente nel database, chiamata ajax (non ricarica la pagina)
$stmt_select_mail = $conn->prepare($sql_select_mail);                                   
$stmt_select_mail->bind_param("s", $email);                                             
$stmt_select_mail->execute();                                                           
$esistenti_mail=$stmt_select_mail->get_result();                                       
$stmt_select_mail->close();  

if ($esistenti_mail->num_rows>0){
        $sql_select_utente = "UPDATE utenti SET Password = ? WHERE Email = ? ";             //anche qui hashiamo la nuova password e facciamo un update query andando a cercare    
        $stmt_select_utente = $conn->prepare($sql_select_utente);                           //l'utente che ci interessa attraverso la mail e inseriamo la nuova password
        $stmt_select_utente->bind_param("ss", $password_hashed, $email);
        $stmt_select_utente->execute();
        $stmt_select_utente->close();
        echo "OK";
}else{
    echo "Inserie correttamente il proprio indirizzo email";
}
?>