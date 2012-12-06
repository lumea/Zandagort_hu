<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['karrier']!=1) kilep();
if ($adataim['speci']!=2) kilep();
if (strtotime($adataim['uccso_regiovaltas'])+7200*60>time()) kilep();

$_REQUEST['regio1']=(int)$_REQUEST['regio1'];
$_REQUEST['regio2']=(int)$_REQUEST['regio2'];

$x1=mysql2num('select count(1) from bolygok where tulaj='.$uid.' and regio='.$_REQUEST['regio1']);
if ($x1>0) mysql_query('update userek set aktualis_regio='.$_REQUEST['regio1'].' where id='.$uid);

$x2=mysql2num('select count(1) from bolygok where tulaj='.$uid.' and regio='.$_REQUEST['regio2']);
if ($x2>0) mysql_query('update userek set aktualis_regio2='.$_REQUEST['regio2'].' where id='.$uid);

mysql_query('update userek set uccso_regiovaltas=now() where id='.$uid);

kilep();
?>