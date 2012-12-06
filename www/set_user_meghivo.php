<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$adataim['admin']) kilep();

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select id from userek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$meghivott=(int)$aux[0];

$er=mysql_query('select id from userek where nev="'.sanitstr($_REQUEST['nev']).'"') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$meghivo=(int)$aux[0];

if ($meghivott>0) /*if ($meghivo<$meghivott)*/ mysql_query('update userek set kin_keresztul_id='.$meghivo.' where id='.$meghivott) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>