<?php require 'includes/bootstrap.php';need_login();$adm=user()['role']==='admin';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){need_admin();csrf_check();$a=$_POST['action']??'';$id=(int)($_POST['id']??0);
if($a==='delete'){try{$pdo->prepare('DELETE FROM items WHERE id=?')->execute([$id]);logact("delete item $id");}catch(PDOException $x){$err='Alat tidak bisa dihapus (sudah ada riwayat sewa).';}}
elseif($a==='save'){$n=trim($_POST['name']??'');$c=trim($_POST['category']??'');
$p=filter_var($_POST['price']??'',FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);$s=filter_var($_POST['stock']??'',FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
if($n===''||$c===''||$p===false||$s===false||mb_strlen($n)>120)$err='Input tidak valid.';
elseif($id){$pdo->prepare('UPDATE items SET name=?,category=?,price_per_day=?,stock=? WHERE id=?')->execute([$n,$c,$p,$s,$id]);logact("update item $id");}
else{$pdo->prepare('INSERT INTO items(name,category,price_per_day,stock) VALUES(?,?,?,?)')->execute([$n,$c,$p,$s]);logact('create item');}}
$edit=null;if($adm&&isset($_GET['edit'])){$q=$pdo->prepare('SELECT * FROM items WHERE id=?');$q->execute([(int)$_GET['edit']]);$edit=$q->fetch()?:null;}
head('Daftar Alat Hiking');if($err)echo '<p class="err">'.e($err).'</p>';
if($adm){?><form method="post"><?=csrf_field()?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=e($edit['id']??0)?>">
<input name="name" placeholder="Nama alat" required value="<?=e($edit['name']??'')?>"><input name="category" placeholder="Kategori" required value="<?=e($edit['category']??'')?>"><input name="price" type="number" min="0" placeholder="Harga/hari" required value="<?=e($edit['price_per_day']??'')?>"><input name="stock" type="number" min="0" placeholder="Stok" required value="<?=e($edit['stock']??'')?>"><button><?=$edit?'Ubah':'Tambah'?></button></form><?php }
echo '<table><tr><th>Nama</th><th>Kategori</th><th>Harga/hari</th><th>Stok</th>'.($adm?'<th>Aksi</th>':'').'</tr>';
foreach($pdo->query('SELECT * FROM items ORDER BY name') as $r){echo '<tr><td>'.e($r['name']).'</td><td>'.e($r['category']).'</td><td>Rp'.number_format((int)$r['price_per_day'],0,',','.').'</td><td>'.e($r['stock']).'</td>';
if($adm)echo '<td><a href="?edit='.(int)$r['id'].'">Ubah</a> <form method="post" style="display:inline" onsubmit="return confirm(\'Hapus?\')">'.csrf_field().'<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="'.(int)$r['id'].'"><button>Hapus</button></form></td>';echo '</tr>';}
echo '</table>';
