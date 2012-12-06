<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['premium_szint']==2) kilep();

$mennyi_alap_ido=0;
if ($adataim['premium']==1) $mennyi_alap_ido=strtotime($szerver_varhato_vege)-time();
elseif (time()<strtotime($adataim['premium_alap'])) $mennyi_alap_ido=strtotime($adataim['premium_alap'])-time();
$mennyi_emelt_ido=round($mennyi_alap_ido/1.6);
$meddig_emelt=date('Y-m-d H:i:s',time()+$mennyi_emelt_ido);
mysql_query('update userek set premium=0,premium_alap="'.$meddig_emelt.'",premium_emelt="'.$meddig_emelt.'",premium_szint=2 where id='.$uid);

kilep();
?>