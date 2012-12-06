<?
include('csatlak.php');
$nem_szamit_aktivitasnak=1;include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');
?>
/*{"db":<?
echo (int)mysql2num('select count(1) from
szov_forum_temak t
left join szov_forum_tema_olv o on o.user_id='.$uid.' and o.tema_id=t.id
where t.szov_id='.$adataim['szovetseg'].' and (o.uccso_komment is null or o.uccso_komment<>t.uccso_komment) and (t.belso=0 or '.$jogaim[1].'=1)');
?>,"cset":<?
if ($adataim['szovetseg']>0) {
	$er=mysql_query('select id from cset_hozzaszolasok where szov_id='.$adataim['szovetseg'].' order by id desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if (((int)$aux[0])>$adataim['uccso_cset_id']) echo 1;else echo 0;
} else echo 0;
?>}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>