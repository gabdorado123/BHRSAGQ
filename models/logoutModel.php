<?php
session_start();  
session_unset(); 
session_destroy();
session_regenerate_id(true);

if (isset($_COOKIE['user_id']))
{
    setcookie('user_id', '', time() - 3600, '/', '', true, true);  
}

if (isset($_COOKIE['remember_me'])) 
{
    setcookie('remember_me', '', time() - 3600, '/', '', true, true); 
}


header('Location: ../index.php'); 
exit();
?>
