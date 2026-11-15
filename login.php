<?php require 'includes/bootstrap.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$ip=$_SERVER['REMOTE_ADDR']??'';
$c=$pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip=? AND created_at>NOW()-INTERVAL 15 MINUTE');$c->execute([$ip]);
if((int)$c->fetchColumn()>=5)$err='Terlalu banyak percobaan. Coba lagi dalam 15 menit.';
else{$s=$pdo->prepare('SELECT * FROM users WHERE email=?');$s->execute([strtolower(trim($_POST['email']??''))]);$u=$s->fetch();
if($u&&password_verify((string)($_POST['password']??''),$u['password_hash'])){
session_regenerate_id(true);$_SESSION['uid']=$u['id'];$_SESSION['name']=$u['name'];$_SESSION['role']=$u['role'];$_SESSION['t']=time();
$pdo->prepare('DELETE FROM login_attempts WHERE ip=?')->execute([$ip]);logact('login');header('Location: items.php');exit;}
$pdo->prepare('INSERT INTO login_attempts(ip) VALUES(?)')->execute([$ip]);$err='Email atau password salah.';}}
head('Login');if(isset($_GET['ok']))echo '<p class="ok">Registrasi berhasil, silakan login.</p>';if($err)echo '<p class="err">'.e($err).'</p>';?>
<form method="post"><?=csrf_field()?>Email<br><input type="email" name="email" required><br>Password<br><input type="password" name="password" required><br><button>Login</button></form>
