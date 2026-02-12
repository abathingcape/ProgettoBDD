<?php
session_start();
include 'connessione.php';                                                                              //memorizziamo i dati ricevuti da post                                                  
$user = $_POST['username'];
$password = $_POST['password']; 

$sql_select_username = "SELECT * FROM utenti WHERE Username = ? OR Email = ? LIMIT 1";                  //la query controlla che l'username o email inseriti siano contenuti all'interno del database e restituisce anche la password
$stmt_select_username = $conn->prepare($sql_select_username);
$stmt_select_username->bind_param("ss", $user,$user);
$stmt_select_username->execute();
$resultQuery=$stmt_select_username->get_result();
$fetch_ass=$resultQuery->fetch_assoc();
$stmt_select_username->close();


if ($fetch_ass==null || (password_verify($password,$fetch_ass['Password']))==false){                    //controlliamo che la password inserita sia giusta, con verify decrpiteremo l'altra password e la confronteremo con quella messa
    echo "Username o password errati";                          
}else {
    $_SESSION['user']=$fetch_ass['IDutente'];                                                        //in caso che entrambe le operazioni siano andate a buon fine accederemo quindi all'index
    $_SESSION["premium"]=false;
    if ($fetch_ass["InizioAbb"]!=NULL){
    $timestamp_corrente = date('Y-m-d');
    $data_inizio = new DateTime($fetch_ass["InizioAbb"]);                                             //controlliamo che l'abbonamento sia attivo, facendo il confronto tra il timestramp corrente e la data di abbonamento +30gg
    $data_inizio->add(new DateInterval("P30D")); 
    $data_fine = $data_inizio->format('Y-m-d');
    if  ($timestamp_corrente<$data_fine){
    $_SESSION["premium"]=true;
    }
    }
    echo "OK";
    exit;
}
?>