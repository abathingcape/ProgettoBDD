<?php   
session_start();                                                    /*distruggiamo la sessione */
session_unset(); 
session_destroy(); 
header('Location:login.html.php'); 
exit();
?>