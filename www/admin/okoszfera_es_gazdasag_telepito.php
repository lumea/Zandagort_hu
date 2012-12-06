<?
include('../csatlak.php');
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;
set_time_limit(0);

//biztos, ami biztos
mysql_query('update bolygok set bolygo_id_mod=id%15');

//oko
function indulo_pop($tsz) {
	$x=8000000;
	for($i=0;$i<$tsz;$i++) $x/=5;
	return $x;
}
$indulo_pop_szorzo_tabla=array(0.84,1.8,0.945,0.81,1.3);
mysql_query('truncate bolygo_eroforras');
$er=mysql_query('select * from bolygok');
while($bolygo=mysql_fetch_array($er)) {
	$osztaly=1;for($i=1;$i<$bolygo['osztaly'];$i++) $osztaly*=2;
	$faj_kveri='select * from eroforrasok where tipus='.EROFORRAS_TIPUS_FAJ.' and bolygo_osztaly&'.$osztaly.'>0 order by trofikus_szint,nev';
	$indulo_pop_szorzo=$indulo_pop_szorzo_tabla[$bolygo['osztaly']-1];
	mysql_query('insert into bolygo_eroforras(bolygo_id,bolygo_id_mod,eroforras_id,db) values('.$bolygo['id'].','.($bolygo['id']%15).',0,1)');
	$er2=mysql_query($faj_kveri);
	while($aux=mysql_fetch_array($er2)) {
		$pop=$indulo_pop_szorzo*indulo_pop($aux['trofikus_szint'])/2500000*$bolygo['terulet'];
		mysql_query('insert into bolygo_eroforras(bolygo_id,bolygo_id_mod,eroforras_id,db) values('.$bolygo['id'].','.($bolygo['id']%15).','.$aux['id'].','.$pop.')');
	}
}
echo 'ecosystem installed<br />';

//gazdasag
mysql_query('truncate bolygo_ember');
mysql_query('truncate bolygo_gyar');
mysql_query('truncate bolygo_gyar_eroforras');
mysql_query('truncate bolygo_gyartipus_szabotazs');
mysql_query('insert into bolygo_ember (bolygo_id,pop) select id,0 from bolygok');
mysql_query('insert into bolygo_eroforras (bolygo_id,eroforras_id,db,bolygo_id_mod)
select b.id,e.id,0,b.bolygo_id_mod
from bolygok b, eroforrasok e
where e.tipus>1');
echo 'bolygo_reset start<br />';
$er=mysql_query('select * from bolygok');while($aux=mysql_fetch_array($er)) bolygo_reset($aux['id'],$aux['osztaly'],$aux['terulet']);
echo 'bolygo_reset finish<br />';

mysql_close($mysql_csatlakozas);
?>