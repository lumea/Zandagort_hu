<?
include('csatlak.php');
$nem_szamit_aktivitasnak=1;include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$csat_mapping=array(0,-1,$adataim['szovetseg'],-500,-500,-500);
if ($adataim['szovetseg']<=0) $csat_mapping[2]=-500;//nemletezo csetszoba
$r=mysql_query('select cssz.id
from cset_szobak cssz
left join cset_szoba_user csszu on csszu.cset_szoba_id=cssz.id
where cssz.tulaj='.$uid.' or csszu.user_id='.$uid.'
group by cssz.id limit 3');
$cs=0;while($aux=mysql_fetch_array($r)) {
	$cs++;
	$csat_mapping[2+$cs]=-$aux[0];
}

?>
/*{"diplomata":<?
if ($adataim['karrier']==4 or ($adataim['karrier']==3 and $adataim['speci']==3)) echo 1;else echo 0;//diplomata vagy fantom
?>,"vendegsegek":<?
echo mysql2jsonmatrix('select sz.id,sz.nev
from szovetseg_vendegek szv
inner join szovetsegek sz on sz.id=szv.szov_id
where szv.user_id='.$uid.'
order by sz.nev,sz.id');
?>,"sajat_szobak":{"idk":<?
echo mysql2jsonarray('select id from cset_szobak where tulaj='.$uid.' order by id');
?>,"meghivok":<?
echo mysql2jsonmatrix('select sz.id,u.id,u.nev
from cset_szobak sz, cset_szoba_meghivok m, userek u
where sz.tulaj='.$uid.' and m.cset_szoba_id=sz.id and m.user_id=u.id
order by sz.id,u.nev,u.id');
?>,"tagok":<?
echo mysql2jsonmatrix('select sz.id,u.id,u.nev
from cset_szobak sz, cset_szoba_user szu, userek u
where sz.tulaj='.$uid.' and szu.cset_szoba_id=sz.id and szu.user_id=u.id
order by sz.id,u.nev,u.id');
?>},"meghivok":<?
echo mysql2jsonmatrix('select sz.id,u.nev
from cset_szobak sz, cset_szoba_meghivok m, userek u
where sz.tulaj!='.$uid.' and m.cset_szoba_id=sz.id and m.user_id='.$uid.' and sz.tulaj=u.id
order by sz.id');
?>,"tagsagok":<?
echo mysql2jsonmatrix('select sz.id,u.nev
from cset_szobak sz, cset_szoba_user szu, userek u
where sz.tulaj!='.$uid.' and szu.cset_szoba_id=sz.id and szu.user_id='.$uid.' and sz.tulaj=u.id
order by sz.id');
?>,"hivatalos_szobak":<?
echo mysql2jsonmatrix('select sz.id,sz.mikor,u.id as tulaj_id,u.nev as tulaj_nev
from cset_szobak sz
left join userek u on sz.tulaj=u.id
where sz.hivatalos=1
order by sz.id');
?>}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>