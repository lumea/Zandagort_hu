<?
include('csatlak.php');
$nem_szamit_aktivitasnak=1;include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['felado_szuro']=(int)$_REQUEST['felado_szuro'];if (!in_array($_REQUEST['felado_szuro'],array(1,2,3))) $_REQUEST['felado_szuro']=1;
$szuro='';
if ($_REQUEST['felado_szuro']==1) $szuro.=' and felado>0';
if ($_REQUEST['felado_szuro']==2) $szuro.=' and felado=0';

$csatajelentesek_szama=mysql2num('select count(1) from csata_user where user_id='.$uid);
$olvasatlan_csatajelentesek_szama=mysql2num('select count(1) from csata_user where user_id='.$uid.' and olvasott=0');

?>
/*{"db":<?
$er=mysql_query('select count(1) from levelek where tulaj='.$uid.' and olvasott=0') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);echo $aux[0]+$olvasatlan_csatajelentesek_szama;
?>,"db_bontas":[<?
$er=mysql_query('select sum(if(felado>0,1,0)),sum(if(felado=0,1,0)),count(1) from levelek where tulaj='.$uid.' and olvasott=0') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
echo $aux[0].','.$aux[1].','.$olvasatlan_csatajelentesek_szama;
?>],"db_ossz":<?
$er=mysql_query('select count(1) from levelek where tulaj='.$uid.$szuro) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);echo $aux[0];
?>,"db_ossz_bontas":[<?
$er=mysql_query('select sum(if(felado>0,1,0)),sum(if(felado=0,1,0)),count(1) from levelek where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
echo $aux[0].','.$aux[1].','.$csatajelentesek_szama;
?>]}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>