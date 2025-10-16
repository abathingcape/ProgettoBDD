<?php
session_start();
header('Content-Type: application/json');
include 'connessione.php';
$valorericerca = "%" . $_GET["q"] . "%";

$sql_select_ricerca = "SELECT Username, IDutente 
                                    FROM utenti AS u
                                    WHERE Username LIKE ? 
                                    AND IDutente != ?
                                    AND IDutente NOT IN (           /* con NOT IN controlliamo che l'utente non sia già cogestore*/                     
                                        SELECT IDcoautore
                                        FROM cogestori
                                    )
                                    LIMIT 3";                  //cerca l'utente che verrè inserito come coautore 
$stmt_select_ricerca = $conn->prepare($sql_select_ricerca); 
$stmt_select_ricerca->bind_param("ss", $valorericerca, $_SESSION["user"]);
$stmt_select_ricerca->execute();
$resultQuery = $stmt_select_ricerca->get_result();
$arr = array();
if ($valorericerca != "%%") {                                                                                                   //i primi 3 valori verranno messi in un array associativo, i dati verrano codificati in json, questo permetterà l'invio della query
    while ($row = $resultQuery->fetch_assoc()) {
        $arr[] = array(
            'username' => $row['Username'],
            'id' => $row['IDutente']
        );
    }
    $json_array = json_encode($arr);
    echo $json_array;
}
?>