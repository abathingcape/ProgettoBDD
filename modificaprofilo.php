<?php
session_start();
include 'connessione.php';
$nome=$_POST["Nome"];
$cognome=$_POST["Cognome"];
$email=$_POST["Email"];
$username=$_POST["Username"];


    $sql_select_username = "SELECT * FROM utenti WHERE Username = ? AND IDutente != ?";                       //query che controlla se l'username inserito sia già stato preso oppure no
    $stmt_select_username = $conn->prepare($sql_select_username);                                             
    $stmt_select_username->bind_param("si", $username, $_SESSION["user"]);
    $stmt_select_username->execute();
    $esistenti_username=$stmt_select_username->get_result();
    $stmt_select_username->close();

    $sql_select_mail = "SELECT * FROM utenti WHERE Email = ? AND IDutente != ?";                       //controlla se la mail è già stata inserita
    $stmt_select_mail = $conn->prepare($sql_select_mail);
    $stmt_select_mail->bind_param("si", $email, $_SESSION["user"]);
    $stmt_select_mail->execute();
    $esistenti_mail=$stmt_select_mail->get_result();
    $stmt_select_mail->close();  

if (($esistenti_mail->num_rows==0) and ($esistenti_username->num_rows==0)){                                                         //controlliamo che le due query abbiano fdato risultati negativi 
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {                                                                                //controlliamo che la mail sia stata inserita in un formato corretto
        $sql_select_utente = "UPDATE utenti SET Nome = ?, Cognome = ?, Email = ?, Username = ?  WHERE IDutente = ? ";             //aggiorniamo i dati del profilo  
        $stmt_select_utente = $conn->prepare($sql_select_utente);                                                                   
        $stmt_select_utente->bind_param("ssssi", $nome, $cognome, $email, $username, $_SESSION['user']);
        $stmt_select_utente->execute();
        $stmt_select_utente->close();
        echo "OK";
    }else{
        echo "Inserire un indirizzo mail valido";

}
}else echo "Inserire un indirizzo mail o username valido";
?>