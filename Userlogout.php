<?php
session_start();
$_SESSION=array();
session_destroy();
redirect("index.php");
function redirect($url, $statusCode = 303)
{
   header('Location: ' . $url, true, $statusCode);
   die();
}
?>