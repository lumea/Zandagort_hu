<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];


$er=mysql_query('select count(1) from csata_user where user_id='.$uid.' and csata_id='.$_REQUEST['id']);
$aux=mysql_fetch_array($er);

if ($aux[0]==0) {
?>
/*{"oke":0}*/
<?
kilep();
}

?>
/*{"oke":1,"flottak":[<?

//mysql_query('update csata_user set olvasott=1 where user_id='.$uid.' and csata_id='.$_REQUEST['id']);//mar akkor olvasotta valik, amikor a levelek.php kilistazza

$er=mysql_query('select csf.flotta_id,l.id,l.kep,l.cim'.$lang__lang.',csfh.ossz_hp_elotte,csfh.ossz_hp_utana
from '.$database_mmog_nemlog.'.hist_csata_flotta csf, '.$database_mmog_nemlog.'.hist_csata_flotta_hajo csfh, leirasok l
where csf.csata_id='.$_REQUEST['id'].' and csfh.csata_id='.$_REQUEST['id'].' and csf.flotta_id=csfh.flotta_id
and l.domen=2 and l.id=csfh.hajo_id and csfh.ossz_hp_elotte>0
order by csfh.flotta_id,csfh.hajo_id
') or hiba(__FILE__,__LINE__,mysql_error());
$flotta_id=0;
while($aux=mysql_fetch_array($er)) {
	if ($flotta_id!=$aux[0]) {
		if ($flotta_id>0) echo ']},';
		$flotta_id=$aux[0];
		echo '{"f":'.$flotta_id.',"h":[';
		$i=0;
	}
	$i++;if ($i>1) echo ',';
	echo '{';
	echo '"h":'.$aux[1].',';
	echo '"k":"'.$aux[2].'",';
	echo '"n":"'.$aux[3].'",';
	echo '"e":'.$aux[4].',';
	echo '"u":'.$aux[5].'';
	echo '}';
}
if ($flotta_id>0) echo ']}';

?>]}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>