<?php
session_start();

//kill session variables
unset($_SESSION['username']);
unset($_SESSION['password_hash']);
$_SESSION = array(); // reset session array
session_destroy();   // destroy session.
header('Location: login.php');