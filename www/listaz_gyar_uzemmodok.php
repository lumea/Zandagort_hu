<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['bid']=(int)$_REQUEST['bid'];
$res=mysql_query('select * from bolygok where id='.$_REQUEST['bid']) or hiba(__FILE__,__LINE__,mysql_error());
$bolygo=mysql_fetch_array($res);

$_REQUEST['gyid']=(int)$_REQUEST['gyid'];
$res=mysql_query('select * from gyarak where id='.$_REQUEST['gyid']) or hiba(__FILE__,__LINE__,mysql_error());
$gyar=mysql_fetch_array($res);

if ($bolygo['id']) if ($bolygo['tulaj']==$uid) {
?>
/*{"oke":1,"lista":[<?

$i=0;
$er=mysql_query('select e.*,gy.id as gyarrid from gyarak gy,eroforrasok e where gy.tipus='.$gyar['tipus'].' and gy.szint='.$gyar['szint'].' and gy.uzemmod=e.id order by e.id') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	if ($aux['tipus']==EROFORRAS_TIPUS_FAJ) {
		if ($aux['bolygo_osztaly']&pow(2,$bolygo['osztaly']-1)) {
			$i++;if ($i>1) echo ',';echo '['.$aux['id'].']';
		}
	} else {
		$er2=mysql_query('
select sum(if(uksz.szint>=gyksz.szint,0,1))
from gyar_kutatasi_szint gyksz, user_kutatasi_szint uksz
where gyksz.gyar_id='.$aux['gyarrid'].' and gyksz.kf_id=uksz.kf_id and uksz.user_id='.$uid.'
') or hiba(__FILE__,__LINE__,mysql_error());
		$aux2=mysql_fetch_array($er2);
		if (!$aux2 || $aux2[0]==0) {
			$i++;if ($i>1) echo ',';echo '['.$aux['id'].']';
		}
	}
}

?>]}*/
<?
} else {
?>
/*{"oke":0}*/
<?
}

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>