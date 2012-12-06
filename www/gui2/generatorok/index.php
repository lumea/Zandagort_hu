<?
include('../../csatlak.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

?>
GYÁRAK
<?
$r=mysql_query('select gyt.id,gyt.nev,l.kep from gyartipusok gyt, leirasok l where l.domen=1 and l.id=gyt.id order by gyt.iparag_sorszam,gyt.id');
while($aux=mysql_fetch_array($r)) {
?>
								<tr class="bolygo_gyar_tabla_sor tooltipped" id="bolygo_gyar_<?=$aux['id']?>"><td><img src="img/gyarikonok/<?=$aux['kep']?>_index.jpg" /></td><td><?=$aux['nev']?></td><td class="gyar_aktiv"></td><td class="gyar_inaktiv"></td></tr>
<?
}


?>
ERŐFORRÁSOK
<?
$r=mysql_query('select e.id,e.nev,l.kep from eroforrasok e, leirasok l where l.domen=2 and l.id=e.id and e.tipus not in (1,3) order by e.tipus,e.id');
while($aux=mysql_fetch_array($r)) {
?>
								<tr class="bolygo_eroforras_tabla_sor tooltipped" id="bolygo_eroforras_<?=$aux['id']?>"><td><img src="img/eroforrasikonok/<?=$aux['kep']?>_index.jpg" /></td><td><?=$aux['nev']?></td><td class="eroforras_keszlet"></td><td class="eroforras_delta"></td><td class="eroforras_netto"></td></tr>
<?
}






?>
---
