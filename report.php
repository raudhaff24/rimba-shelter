<?php require 'includes/bootstrap.php';need_admin();
$rows=$pdo->query('SELECT i.name,i.category,COUNT(r.id) n,COALESCE(SUM(r.qty),0) q,COALESCE(SUM(r.total),0) t FROM items i LEFT JOIN rentals r ON r.item_id=i.id GROUP BY i.id ORDER BY t DESC')->fetchAll();
if(isset($_GET['export'])){logact('export csv');header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="rekap_sewa.csv"');
$o=fopen('php://output','w');fputcsv($o,['Alat','Kategori','Jumlah Transaksi','Total Qty','Pendapatan']);
foreach($rows as $r){$n=preg_match('/^[=+\-@]/',$r['name'])?"'".$r['name']:$r['name'];fputcsv($o,[$n,$r['category'],$r['n'],$r['q'],$r['t']]);}exit;}
head('Laporan Rekap Penyewaan');echo '<p><a href="?export=csv">Ekspor CSV</a></p><table><tr><th>Alat</th><th>Kategori</th><th>Transaksi</th><th>Qty</th><th>Pendapatan</th></tr>';$tot=0;
foreach($rows as $r){$tot+=(int)$r['t'];echo '<tr><td>'.e($r['name']).'</td><td>'.e($r['category']).'</td><td>'.e($r['n']).'</td><td>'.e($r['q']).'</td><td>Rp'.number_format((int)$r['t'],0,',','.').'</td></tr>';}
echo '</table><p><b>Total pendapatan: Rp'.number_format($tot,0,',','.').'</b></p>';
