<?php
session_start();

$_SESSION = [];

$session_name=session_name();

if(isset($COOKIE[$session_name])) {
    setcookie($sesson_name,'',time()-3600);
}

session_destroy();

header('Location:index.php');
exit;
?>
