<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$er=mysql_query('select * from levelek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$level=mysql_fetch_array($er);

if ($level) if ($level['tulaj']==$uid) {

mysql_query('update levelek set olvasott=1 where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
?>
/*{"oke":1,"premium":<?
echo premium_szint();
?>,"ido":"<?=str_replace(' ','&nbsp;',substr($level['ido'],0,-3));?>","felado_id":<?=$level['felado'];?>,"felado_nev":<?
if ($level['felado']) {
	$er2=mysql_query('select nev from userek where id='.$level['felado']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux2=mysql_fetch_array($er2);
	echo json_encode($aux2['nev']);
} else echo ($lang_lang=='hu')?'"rendszer"':'"system"';
?>,"cimzettek":<?
$x='';
$er2=mysql_query('select u.id,u.nev from cimzettek c,userek u where c.level_id='.$level['id'].' and c.cimzett_tipus='.CIMZETT_TIPUS_USER.' and c.cimzett_id=u.id order by u.nev') or hiba(__FILE__,__LINE__,mysql_error());
$j=0;while($aux2=mysql_fetch_array($er2)) {
	if ($j) $x.=', ';$j++;
	//$x.=$aux2[0];
	$x.='<a href="" onclick="return user_katt('.$aux2[0].')">'.$aux2[1].'</a>';
}
$er2=mysql_query('select sz.id,sz.nev from cimzettek c,szovetsegek sz where c.level_id='.$level['id'].' and c.cimzett_tipus='.CIMZETT_TIPUS_SZOVETSEG.' and c.cimzett_id=sz.id order by sz.nev') or hiba(__FILE__,__LINE__,mysql_error());
while($aux2=mysql_fetch_array($er2)) {
	if ($j) $x.=', ';$j++;
	//$x.=$aux2[0];
	$x.='<a href="" onclick="return szovetseg_katt('.$aux2[0].')">'.$aux2[1].'</a>';
}
echo json_encode($x);
?>,"targy":<?=json_encode($level['targy']);?>,"uzenet":<?=json_encode($level['uzenet']);?>,"uzenet_br":<?
//echo json_encode(nl2br($level['uzenet']));
$uzenet_bb=nl2br($level['uzenet']);
$uzenet_bb=preg_replace('/\[img\](.*)\[\/img\]/iU','<img src="$1" alt="" />',$uzenet_bb);
echo json_encode($uzenet_bb);
?>,"mappa":<?=json_encode($level['mappa']);?>}*/
<?
} else {
?>
/*{"oke":0,"premium":<?
echo premium_szint();
?>}*/
<?
}
?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>