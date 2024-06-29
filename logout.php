<?php

class Logout{
    public function logout(){
        session_start(); // Start the session
        // Unset all of the session variables
        unset($_SESSION);
        // Destroy the session
        session_destroy();
        // Redirect to the login page or any other page you want
        header("Location: index.php");
        exit;
    }
}

$logout = new Logout();
$logout->logout();