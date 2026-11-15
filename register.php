<?php require 'includes/bootstrap.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
$n=trim($_POST['name']??'');$m=strtolower(trim($_POST['email']??''));$p=(string)($_POST['password']??'');
if($n===''||mb_strlen($n)>100)$err='Nama wajib diisi (maks 100).';
elseif(!filter_var($m,FILTER_VALIDATE_EMAIL))$err='Email tidak valid.';
elseif(strlen($p)<8||!preg_match('/[A-Za-z]/',$p)||!preg_match('/\d/',$p))$err='Password min. 8 karakter, kombinasi huruf dan angka.';
else{try{$pdo->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,'user')")->execute([$n,$m,password_hash($p,PASSWORD_BCRYPT)]);
header('Location: login.php?ok=1');exit;}catch(PDOException $x){$err='Email sudah terdaftar.';}}}
head('Daftar');if($err)echo '<p class="err">'.e($err).'</p>';?>
<form method="post"><?=csrf_field()?>Nama<br><input name="name" required maxlength="100" value="<?=e($_POST['name']??'')?>"><br>Email<br><input type="email" name="email" required><br>Password<br><input type="password" name="password" required minlength="8"><br><button>Daftar</button></form>
