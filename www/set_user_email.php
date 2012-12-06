<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$adataim['admin']) kilep();

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select * from userek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$delikvens=mysql_fetch_array($er);
if (!$delikvens) kilep();


$uj_zanda_id=0;
/*
//ZandaNet
$mysql_csatlakozas_zandanet=mysql_connect('HOST','USER','PASSWORD');
mysql_select_db('mmog',$mysql_csatlakozas_zandanet) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('set names "utf8"',$mysql_csatlakozas_zandanet);

$er=mysql_query('select * from zandanet_server_accounts where server_prefix="'.$szerver_prefix.'" and user_id='.$delikvens['id'],$mysql_csatlakozas_zandanet) or hiba(__FILE__,__LINE__,mysql_error());
$hibas_server_account=mysql_fetch_array($er);
$regi_zanda_id=$hibas_server_account['zanda_id'];//12390

$er=mysql_query('select * from zandanet_users where email="'.sanitstr($_REQUEST['email']).'"',$mysql_csatlakozas_zandanet) or hiba(__FILE__,__LINE__,mysql_error());
$uj_emailhez_tartozo_zandanet_account=mysql_fetch_array($er);

if ($uj_emailhez_tartozo_zandanet_account) {
	$uj_zanda_id=$uj_emailhez_tartozo_zandanet_account['id'];
	mysql_query('update zandanet_server_accounts set zanda_id='.$uj_zanda_id.' where id='.$hibas_server_account['id'],$mysql_csatlakozas_zandanet) or hiba(__FILE__,__LINE__,mysql_error());
} else {
	mysql_query('update zandanet_users set email="'.sanitstr($_REQUEST['email']).'" where id='.$regi_zanda_id,$mysql_csatlakozas_zandanet) or hiba(__FILE__,__LINE__,mysql_error());
	$uj_zanda_id=$regi_zanda_id;
}

mysql_connect('localhost',$mysql_username,$mysql_password);//hogy ujra a helyi szerver legyen a default
*/

mysql_query('update userek set email="'.sanitstr($_REQUEST['email']).'",zanda_id='.$uj_zanda_id.' where id='.$delikvens['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update '.$database_mmog_nemlog.'.userek_ossz set email="'.sanitstr($_REQUEST['email']).'",zanda_id='.$uj_zanda_id.' where eredeti_id='.$delikvens['id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>