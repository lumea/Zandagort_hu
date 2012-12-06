<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$regio=(int)$_REQUEST['regio'];

$res=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id'].' and letezik=1 and tulaj='.$uid);
$bolygo=mysql_fetch_array($res);
if (!$bolygo) kilep();

if ($regio==0) {//automata valaszto
	$regio=$adataim['aktualis_regio'];
}

?>
/*{"vagyon":<?
echo $adataim['vagyon'];
?>,"premium":<?
echo premium_szint();
?>,"auto_transz":<?
if (premium_szint()<2) echo '[]';else {
	echo mysql2jsonmatrix('select c.eroforras_id,c.darab,0,0,"",1,c.id,c.regio_slot from cron_tabla_eroforras_transzfer c where c.hova_bolygo_id=0 and c.honnan_bolygo_id='.$bolygo['id'].' order by c.eroforras_id,c.regio_slot,c.id');
}
?>,"kereskedo":<?
if ($adataim['karrier']==1 && $adataim['speci']==2) echo '1';else echo '0';
?>,"regiok_szama":<?
echo mysql2num('select count(1) from regiok');
?>,"elerheto_regiok":<?
echo mysql2jsonarray('select distinct regio from bolygok where tulaj='.$uid.' order by regio');
?>,"valasztott_regio":<?
echo $adataim['valasztott_regio'];
?>,"valasztott_regio2":<?
echo $adataim['valasztott_regio2'];
?>,"aktualis_regio":<?
echo $adataim['aktualis_regio'];
?>,"aktualis_regio2":<?
echo $adataim['aktualis_regio2'];
?>,"kovetkezo_regiovaltas_v2":<?
if (strtotime($adataim['uccso_regiovaltas'])+7200*60<=time()) echo 0;
else echo round((strtotime($adataim['uccso_regiovaltas'])+7200*60-time())/60);
?>,"piacok":<?
echo mysql2jsonmatrix('select termek_id,arfolyam from tozsdei_arfolyamok where regio='.$regio.' order by termek_id');
?>,"limitek":<?
echo mysql2jsonassoc('select termek_id,maximum,felhasznalt from user_veteli_limit where user_id='.$uid);
?>,"kovetkezo_napi_limit":<?
$hatralevo_perc=(20-date('H'))*60+(0-date('i'));
if ($hatralevo_perc<0) $hatralevo_perc+=1440;
echo $hatralevo_perc;
?>,"keszletek":<?
echo mysql2jsonassoc('
select e.id,be.db,-1 from eroforrasok e, bolygo_eroforras be
where be.bolygo_id='.$_REQUEST['bolygo_id'].' and be.eroforras_id=e.id and e.szallithato=1
union all
select 150,megoszthato_kp,kp from userek where id='.$uid.'
');
?>}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>