<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['hova']=(int)$_REQUEST['hova'];
mysql_query('delete from szovetseg_meghivas_kerelmek where ki='.$uid.' and hova='.$_REQUEST['hova']);

szovi_belepo_kilepo_uzenet($_REQUEST['hova']
,'Belépési kérelem visszavonva: '.$adataim['nev']
,$adataim['nev'].' visszavonta belépési kérelmét.'
,'Request for entry withdrawn: '.$adataim['nev']
,$adataim['nev'].' has withdrawn his/her entry application.');

kilep();
?>