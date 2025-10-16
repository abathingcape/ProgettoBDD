<?php
session_start();
include 'connessione.php';
$fotostandard="addfoto.png";
$nome=$_POST["nome"];                                                                       //Le post servono per prendere i dati dalle form della pagina html, criterio Name
$cognome=$_POST["cognome"];
$user=$_POST["username"];
$email = $_POST["email"];
$password=$_POST["password"];
$Cpassword=$_POST["re_pass"];
$password_hashed=password_hash($password, PASSWORD_BCRYPT);                                 //Password hash è una funzione che permette di criptare la password (hash)

if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
{ 
    $sql_select_mail = "SELECT * FROM utenti WHERE Email = ?";                              //query per controllare se la mail è già presente nel database, fa tutto ajax quindi non
    $stmt_select_mail = $conn->prepare($sql_select_mail);                                   //c'è ricaricamento della pagina, è fatto tutto in maniera asincrona
    $stmt_select_mail->bind_param("s", $email);                                             //prepared statement ci permettono di usare la stessa query con dati differenti per ottimizzare
    $stmt_select_mail->execute();                                                           //bind param è usato per passare delle variabili invece che valori, la s rappresenta che il valore
    $esistenti_mail=$stmt_select_mail->get_result();                                        //è una stringa
    $stmt_select_mail->close();                                                             //chiudiamo la query

    $sql_select_username = "SELECT * FROM utenti WHERE Username = ?";                       //stessi principi della query sopra ma controlliamo che l'username non sia già preso
    $stmt_select_username = $conn->prepare($sql_select_username);
    $stmt_select_username->bind_param("s", $user);
    $stmt_select_username->execute();
    $esistenti_username=$stmt_select_username->get_result();
    $stmt_select_username->close(); 
    /*prova*/
    if (($esistenti_mail->num_rows>0)||($esistenti_username->num_rows>0)){                  //controlliamo quindi se le variabili dei risultati delle nostre query sono maggiore di 1
        echo "Indirizzo email o username non disponibili";                                  //num_rows ci restituisce il numero di righe del risultato della nostra query
    }else{
        $stmt = $conn->prepare("INSERT INTO utenti (Nome, Cognome, Username, Email, Password, FotoP) VALUES ( ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nome, $cognome, $user, $email, $password_hashed,$fotostandard);
        $stmt->execute();                                                                   //stesso discorso per le query di prima ma siamo alla fine, i dati hanno passato tutti i controlli quindi   
        $stmt->close();                                                                     //possono essere inseriti
        $lastInsertedId = mysqli_insert_id($conn);                                          //con mysqli_insert_id prendiamo l'id dell'utente registrato e lo impostiamo come variabile di sessione
        $_SESSION['user'] = $lastInsertedId; 
        $_SESSION['premium']=false; 
        echo "OK";
        exit;
        }
} else {
     echo "Inserire un indirizzo email valido."; 
} 

?>