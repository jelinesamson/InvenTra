<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    require_once("env.php");
     try{
          
     $conn = mysqli_connect( DB_HOST,
                             DB_USER,
                             DB_PASSWORD,
                             DB_NAME);
     }  
     catch(mysqli_sql_exception){
          echo "Could not Connect to the server! <br>";
     }                    

     function requireLogin() {
    if (!isset($_SESSION['account_id'])) {
        header("Location: ../");
        exit();
    }
}