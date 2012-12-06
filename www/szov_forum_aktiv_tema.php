<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$_REQUEST['offset']=(int)$_REQUEST['offset'];
$tema=mysql2row('select * from szov_forum_temak where id='.$_REQUEST['id']);
$latod=false;$vendeg=false;
if ($tema) {
	if ($tema['szov_id']==$adataim['szovetseg'] and (!$tema['belso'] or $jogaim[1])) $latod=true;
	if (!$latod) if ($tema['vendeg']) {
		$aux=mysql2row('select * from szovetseg_vendegek where szov_id='.$tema['szov_id'].' and user_id='.$uid);
		if ($aux) {$latod=true;$vendeg=true;}
	}
}

if ($latod) {

$olvasatlan=0;
$er2=mysql_query('select uccso_komment from szov_forum_tema_olv where tema_id='.$tema['id'].' and user_id='.$uid);
$aux2=mysql_fetch_array($er2);
if (!$aux2 || $aux2[0]!=$tema['uccso_komment']) $olvasatlan=1;

$vanjoga_mod=$jogaim[9];
if ($vendeg) $vanjoga_mod=0;

mysql_query('insert into szov_forum_tema_olv (tema_id,user_id,uccso_komment) values('.$tema['id'].','.$uid.','.$tema['uccso_komment'].') on duplicate key update uccso_komment='.$tema['uccso_komment']);

?>
/*{"oke":1,"tema_cime":"<?
if ($tema['cim']) echo addslashes($tema['cim']);else echo '-';
?>","belso":<?
echo $tema['belso'];
?>,"vendeg":<?
echo $tema['vendeg'];
?>,"kommentek_szama":<?
echo $tema['kommentek_szama'];
?>,"olvasatlan":<?
echo $olvasatlan;
?>,"kommentek":<?
$er2=mysql_query('select
k.id,u.id,u.nev,k.mikor,k.mit
from szov_forum_kommentek k
left join userek u on k.ki=u.id
where k.tema_id='.$_REQUEST['id'].'
order by k.mikor desc limit '.$_REQUEST['offset'].',10') or hiba(__FILE__,__LINE__,mysql_error());
echo '[';
$i=0;while($aux2=mysql_fetch_array($er2)) {
	$i++;if ($i>1) echo ',';
	echo '[';
	echo $aux2[0].',';
	echo $aux2[1].',';
	echo json_encode($aux2[2]).',';
	echo json_encode($aux2[3]).',';
	echo json_encode(nl2br(htmlspecialchars($aux2[4]))).',';
	echo $vanjoga_mod;
	echo ']';
}
echo ']';
?>}*/
<?
} else {
?>
/*{"oke":0}*/
<?
}

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>