<?php
declare(strict_types=1);
$f=__DIR__.'/../.env'; $E=is_file($f)?(parse_ini_file($f)?:[]):[];
function env(string $k):string{global $E;$v=getenv($k);return $v!==false?$v:($E[$k]??'');}
$https=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')||($_SERVER['HTTP_X_FORWARDED_PROTO']??'')==='https';
if(!$https&&env('APP_ENV')==='production'){header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],true,301);exit;}
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; frame-ancestors 'none'");
header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
ini_set('display_errors','0');ini_set('session.use_strict_mode','1');
session_name('RSID');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$https,'httponly'=>true,'samesite'=>'Lax']);
session_start();
if(isset($_SESSION['t'])&&time()-$_SESSION['t']>1800){session_unset();session_destroy();session_start();}
if(isset($_SESSION['uid']))$_SESSION['t']=time();
try{$pdo=new PDO('mysql:host='.env('DB_HOST').';dbname='.env('DB_NAME').';charset=utf8mb4',env('DB_USER'),env('DB_PASS'),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
catch(Throwable $x){http_response_code(500);exit('Database error');}
function e($s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function csrf():string{return $_SESSION['csrf']??=bin2hex(random_bytes(32));}
function csrf_field():string{return '<input type="hidden" name="csrf" value="'.csrf().'">';}
function csrf_check():void{if(!hash_equals($_SESSION['csrf']??'',(string)($_POST['csrf']??''))){http_response_code(403);exit('CSRF token tidak valid');}}
function user():?array{return isset($_SESSION['uid'])?['id'=>(int)$_SESSION['uid'],'name'=>$_SESSION['name'],'role'=>$_SESSION['role']]:null;}
function need_login():void{if(!user()){header('Location: login.php');exit;}}
function need_admin():void{need_login();if(user()['role']!=='admin'){http_response_code(403);exit('Akses ditolak');}}
function logact(string $a):void{global $pdo;$pdo->prepare('INSERT INTO activity_logs(user_id,action,ip) VALUES(?,?,?)')->execute([user()['id']??null,$a,$_SERVER['REMOTE_ADDR']??'']);}
function head(string $t):void{$u=user();
echo '<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($t).' - Rimba Shelter</title><style>body{font-family:system-ui;max-width:900px;margin:auto;padding:1rem}nav a{margin-right:1rem}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccc;padding:.4rem}input,select{margin:.2rem 0;padding:.4rem}.err{color:#b00}.ok{color:#070}</style><nav><b>⛺ Rimba Shelter</b> | <a href="items.php">Alat</a>';
if($u){echo '<a href="rent.php">Penyewaan</a>';if($u['role']==='admin')echo '<a href="report.php">Laporan</a>';
echo '<form method="post" action="logout.php" style="display:inline">'.csrf_field().'<button>Logout ('.e($u['name']).')</button></form>';}
else echo '<a href="login.php">Login</a><a href="register.php">Daftar</a>';
echo '</nav><h2>'.e($t).'</h2>';}
