<?php require 'includes/bootstrap.php';need_login();$u=user();$adm=$u['role']==='admin';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$a=$_POST['action']??'';
if($a==='rent'){$iid=(int)($_POST['item_id']??0);$q=filter_var($_POST['qty']??'',FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
$s=DateTime::createFromFormat('Y-m-d',(string)($_POST['start']??''));$en=DateTime::createFromFormat('Y-m-d',(string)($_POST['end']??''));
if(!$q||!$s||!$en||$en<$s||$s<new DateTime('today'))$err='Input tidak valid.';
else{$days=$s->diff($en)->days+1;$pdo->beginTransaction();
$st=$pdo->prepare('SELECT * FROM items WHERE id=? FOR UPDATE');$st->execute([$iid]);$it=$st->fetch();
if(!$it||$it['stock']<$q){$pdo->rollBack();$err='Stok tidak mencukupi.';}
else{$pdo->prepare('INSERT INTO rentals(user_id,item_id,qty,start_date,end_date,total) VALUES(?,?,?,?,?,?)')->execute([$u['id'],$iid,$q,$s->format('Y-m-d'),$en->format('Y-m-d'),$days*$q*$it['price_per_day']]);
$pdo->prepare('UPDATE items SET stock=stock-? WHERE id=?')->execute([$q,$iid]);$pdo->commit();logact("rent item $iid x$q");}}}
elseif($a==='return'){need_admin();$pdo->beginTransaction();$st=$pdo->prepare("SELECT * FROM rentals WHERE id=? AND status='active' FOR UPDATE");$st->execute([(int)($_POST['id']??0)]);$r=$st->fetch();
if($r){$pdo->prepare("UPDATE rentals SET status='returned' WHERE id=?")->execute([$r['id']]);$pdo->prepare('UPDATE items SET stock=stock+? WHERE id=?')->execute([$r['qty'],$r['item_id']]);logact('return rental '.$r['id']);}$pdo->commit();}}
head('Penyewaan');if($err)echo '<p class="err">'.e($err).'</p>';
if(!$adm){?><form method="post"><?=csrf_field()?><input type="hidden" name="action" value="rent"><select name="item_id"><?php foreach($pdo->query('SELECT id,name,stock FROM items WHERE stock>0 ORDER BY name') as $i)echo '<option value="'.(int)$i['id'].'">'.e($i['name']).' (stok '.(int)$i['stock'].')</option>';?></select>
<input type="number" name="qty" min="1" value="1" required><input type="date" name="start" required><input type="date" name="end" required><button>Sewa</button></form><?php }
$sql='SELECT r.*,i.name item,u.name uname FROM rentals r JOIN items i ON i.id=r.item_id JOIN users u ON u.id=r.user_id'.($adm?'':' WHERE r.user_id=?').' ORDER BY r.id DESC';
$q=$pdo->prepare($sql);$q->execute($adm?[]:[$u['id']]);
echo '<table><tr><th>Penyewa</th><th>Alat</th><th>Qty</th><th>Periode</th><th>Total</th><th>Status</th>'.($adm?'<th>Aksi</th>':'').'</tr>';
foreach($q as $r){echo '<tr><td>'.e($r['uname']).'</td><td>'.e($r['item']).'</td><td>'.e($r['qty']).'</td><td>'.e($r['start_date']).' s/d '.e($r['end_date']).'</td><td>Rp'.number_format((int)$r['total'],0,',','.').'</td><td>'.e($r['status']).'</td>';
if($adm)echo '<td>'.($r['status']==='active'?'<form method="post">'.csrf_field().'<input type="hidden" name="action" value="return"><input type="hidden" name="id" value="'.(int)$r['id'].'"><button>Kembalikan</button></form>':'').'</td>';echo '</tr>';}
echo '</table>';
