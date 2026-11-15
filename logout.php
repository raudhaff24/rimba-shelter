<?php require 'includes/bootstrap.php';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();if(user())logact('logout');$_SESSION=[];
setcookie(session_name(),'',time()-3600,'/','',true,true);session_destroy();}
header('Location: login.php');
