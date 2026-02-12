<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname="cappag";

$conn = new mysqli($servername, $username, $password,$dbname);                  //file di connessione al db contenuto in ogni pagina

if (!$conn) {
  die("Connection failed: " . $conn->connect_error);
}
?>