<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

//if ($adataim['karrier']!=4) kilep($lang[$lang_lang]['kisphpk']['Csak a diplomata karriert választók nyithatnak chat szobát.']);
if ($adataim['karrier']!=4 and ($adataim['karrier']!=3 or $adataim['speci']!=3)) kilep($lang[$lang_lang]['kisphpk']['Csak a diplomata karriert választók és a fantomok nyithatnak chat szobát.']);

if ($_REQUEST['hiv']=='1') $hivatalos=1;else $hivatalos=0;
if ($hivatalos>0) if ($adataim['speci']!=1) kilep($lang[$lang_lang]['kisphpk']['Csak békebírók nyithatnak hivatalos chat szobát.']);

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

if ($cs==3) kilep();//nincs szabad hely

mysql_query('insert into cset_szobak (tulaj,hivatalos) values ('.$uid.','.$hivatalos.')');

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>