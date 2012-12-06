<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($_REQUEST['mit']=='nev') mysql_query('update userek set szamlazasi_nev="'.sanitstr($_REQUEST['szoveg']).'" where id='.$uid);
if ($_REQUEST['mit']=='cim') mysql_query('update userek set szamlazasi_cim="'.sanitstr($_REQUEST['szoveg']).'" where id='.$uid);

kilep();
?>